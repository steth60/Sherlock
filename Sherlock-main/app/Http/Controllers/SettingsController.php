<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;
use Illuminate\Validation\Rules\Password;
use App\Models\TrustedDevice;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $trustedDevices = $user->trustedDevices()->where('expires_at', '>', Carbon::now())->get();
        $theme_preference = $user->theme_preference;

        // Fetch active sessions
        $activeSessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($session) use ($user) {
                $trustedDevice = $user->trustedDevices()->where('device_token', $session->id)->first();
                return (object) [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'device_name' => $trustedDevice ? $trustedDevice->device_name : $this->getDeviceName($session->user_agent),
                    'last_activity' => \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                ];
            });

        return view('settings.index', compact('user', 'trustedDevices', 'theme_preference', 'activeSessions'));
    }

    public function showProfilePhotoSettings()
    {
        $user = Auth::user();
        return view('settings.profile-photo', compact('user'));
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'profile_icon' => 'nullable|string',
            'initial_color' => 'nullable|string',
        ]);
    
        $user = Auth::user();
    
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $path = $file->storeAs('profile-photos/' . $user->id, $file->getClientOriginalName(), 'public');
            $user->profile_photo = $path;
            $user->profile_photo_type = 'upload';
        } else if ($request->input('profile_icon')) {
            $user->profile_photo = 'icons/' . $request->input('profile_icon');
            $user->profile_photo_type = 'icon';
        } else if ($request->input('initial_color')) {
            $user->profile_photo = $request->input('initial_color');
            $user->profile_photo_type = 'initials';
        }
    
        $user->save();
    
        return redirect()->route('settings.index')->with('status', 'Profile photo updated successfully.');
    }
    
    

    public function uploadProfilePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = Auth::user();
        $image = $request->file('photo');
        $imageName = Str::random(10) . '.' . $image->getClientOriginalExtension();
        $imagePath = 'images/profile-photo/' . $user->id;

        // Store the image
        $image->storeAs('public/' . $imagePath, $imageName);

        // Update user profile photo path
        $user->profile_photo = $imagePath . '/' . $imageName;
        $user->save();

        return redirect()->route('settings.profile-photo')->with('status', 'Profile photo uploaded successfully.');
    }


    
    private function getDeviceName($userAgent)
    {
        // Implement your logic to extract device name from user agent
        // This is a simplified example
        if (strpos($userAgent, 'Windows') !== false) {
            return 'Windows PC';
        } elseif (strpos($userAgent, 'Macintosh') !== false) {
            return 'Mac PC';
        } elseif (strpos($userAgent, 'iPhone') !== false) {
            return 'iPhone';
        } elseif (strpos($userAgent, 'Android') !== false) {
            return 'Android Phone';
        }
        return 'Unknown Device';
    }

    // Theme Preference
    public function updateTheme(Request $request)
    {
        $request->validate([
            'theme_preference' => 'required|in:dark,light,system'
        ]);

        $user = Auth::user();
        $user->theme_preference = $request->theme_preference;
        $user->save();

        return redirect()->back()->with('success', 'Theme preference updated successfully.');
    }

    // Personal Info
    public function showPersonalInfoForm()
    {
        return view('settings.personal-info');
    }

    public function updatePersonalInfo(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'current_password' => 'required',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match.']);
        }

        $user->name = $request->name;
        $user->save();

        return redirect()->route('settings.index')->with('status', 'Personal information updated successfully.');
    }

    // Change Email
    public function showChangeEmailForm()
    {
        return view('settings.change-email');
    }

    public function changeEmail(Request $request)
    {
        $request->validate([
            'new_email' => 'required|email|unique:users,email',
            'current_password' => 'required',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match.']);
        }

        // Send verification email to new email address
        $token = \Str::random(60);
        $user->email_verification_token = $token;
        $user->new_email = $request->new_email;
        $user->save();

        Mail::to($user->new_email)->send(new EmailVerificationMail($token));

        return redirect()->route('settings.index')->with('status', 'Verification email sent to new address.');
    }

    // Change Password
    public function showChangePasswordForm()
    {
        return view('settings.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match.']);
        }

        // Check password history (last 5 passwords)
        $passwordHistory = $user->passwordHistories()->orderBy('created_at', 'desc')->take(5)->get();
        foreach ($passwordHistory as $oldPassword) {
            if (Hash::check($request->new_password, $oldPassword->password)) {
                return back()->withErrors(['new_password' => 'You cannot reuse one of your last 5 passwords.']);
            }
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        // Save password to history
        $user->passwordHistories()->create(['password' => Hash::make($request->new_password)]);

        return redirect()->route('settings.index')->with('status', 'Password changed successfully.');
    }

    // Manage MFA Settings
    public function showMfaSettings()
    {
        return view('settings.mfa');
    }

    // Update MFA settings
    public function updateMfaSettings(Request $request)
    {
        $user = Auth::user();
        
        $user->mfa_totp = $request->has('mfa_totp');
        $user->mfa_physical_key = $request->has('mfa_physical_key');
        $user->mfa_email = $request->has('mfa_email');
        
        $user->save();

        return redirect()->route('settings.index')->with('status', 'MFA settings updated successfully.');
    }


   // Update account recovery options
   public function updateAccountRecoveryOptions(Request $request)
   {
       $request->validate([
           'backup_email' => 'required|email',
           'recovery_phone' => 'required|string|max:15',
       ]);

       $user = Auth::user();
       $user->backup_email = $request->backup_email;
       $user->recovery_phone = $request->recovery_phone;
       $user->save();

       return redirect()->route('settings.index')->with('status', 'Account recovery options updated successfully.');
   }

   // Update login notifications settings
   public function updateLoginNotifications(Request $request)
   {
       $user = Auth::user();
       $user->login_notifications_enabled = $request->has('login_notifications_enabled');
       $user->save();

       return redirect()->route('settings.index')->with('status', 'Login notifications settings updated successfully.');
   }


    // Account Recovery Options
    public function showAccountRecoveryForm()
    {
        return view('settings.account-recovery');
    }

    public function updateAccountRecovery(Request $request)
    {
        $request->validate([
            'backup_email' => 'required|email',
            'recovery_phone' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->backup_email = $request->backup_email;
        $user->recovery_phone = $request->recovery_phone;
        $user->save();

        return redirect()->route('settings.index')->with('status', 'Account recovery options updated successfully.');
    }

    // MFA Token Reset
    public function showMfaResetForm()
    {
        return view('settings.mfa-reset');
    }

    public function resetMfa(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match.']);
        }

        // Reset MFA logic
    }

    // Trusted Devices
    public function storeTrustedDevice(Request $request)
    {
        $user = Auth::user();
        $deviceToken = Str::random(60);

        $trustedDevice = new TrustedDevice();
        $trustedDevice->user_id = $user->id;
        $trustedDevice->device_name = $request->input('device_name');
        $trustedDevice->device_token = $deviceToken;
        $trustedDevice->expires_at = Carbon::now()->addDays(90);
        $trustedDevice->save();

        return response()->json(['device_token' => $deviceToken]);
    }

    public function updateTrustedDevice(Request $request, $id)
    {
        $user = Auth::user();
        $trustedDevice = $user->trustedDevices()->findOrFail($id);

        $request->validate([
            'device_name' => 'required|string|max:255',
        ]);

        $trustedDevice->device_name = $request->input('device_name');
        $trustedDevice->save();

        return redirect()->route('settings.index')->with('status', 'Trusted device updated successfully.');
    }

    public function renewTrustedDevice($id)
    {
        $user = Auth::user();
        $trustedDevice = $user->trustedDevices()->findOrFail($id);

        $trustedDevice->expires_at = Carbon::now()->addDays(90);
        $trustedDevice->save();

        return redirect()->route('settings.index')->with('status', 'Trusted device renewed successfully.');
    }

    public function destroyTrustedDevice($id)
    {
        $user = Auth::user();
        $trustedDevice = $user->trustedDevices()->find($id);
        if ($trustedDevice) {
            $trustedDevice->delete();
        }

        return redirect()->route('settings.index')->with('status', 'Trusted device removed successfully.');
    }

    public function getActiveSessions()
    {
        $user = Auth::user();
        $activeSessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($session) use ($user) {
                $trustedDevice = $user->trustedDevices()->where('device_token', $session->id)->first();
                return (object) [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'device_name' => $trustedDevice ? $trustedDevice->device_name : $this->getDeviceName($session->user_agent),
                    'last_activity' => \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                ];
            });
    
        return view('settings.index', compact('activeSessions'));
    }

    

    public function logoutSession($session_id)
    {
        DB::table('sessions')->where('id', $session_id)->delete();
    
        // If the logged out session is the current session, log the user out
        if (session()->getId() == $session_id) {
            Auth::logout();
            return redirect()->route('login')->with('status', 'You have been logged out.');
        }
    
        return redirect()->route('settings.index')->with('status', 'Session logged out successfully.')->with('active_tab', 'active-sessions');
    }

}
