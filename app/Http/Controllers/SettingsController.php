<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        return view('settings.index', compact('trustedDevices'));
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

    public function addMfaMethod(Request $request)
    {
        // Add MFA method logic
    }

    public function removeMfaMethod(Request $request)
    {
        // Remove MFA method logic
    }

    public function regenerateBackupCodes(Request $request)
    {
        // Regenerate backup codes logic
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
        ]);

        $user = Auth::user();
        $user->backup_email = $request->backup_email;
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
}
