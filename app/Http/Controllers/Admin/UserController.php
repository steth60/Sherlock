<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Invitation;
use App\Models\Group;
use App\Models\TrustedDevice;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        $invitations = Invitation::all();

        $totalUsers = $users->count();
        $activeUsers = $users->where('active', true)->count();
        $inactiveUsers = $users->where('active', false)->count();
        $unverifiedEmailUsers = User::whereNull('email_verified_at')->count();
        $mfaNotEnabledUsers = User::where('two_factor_enabled', false)->count();

        return view('admin.users.index', compact(
            'users', 
            'invitations',
            'totalUsers', 
            'activeUsers',
            'inactiveUsers',
            'unverifiedEmailUsers', 
            'mfaNotEnabledUsers'
        ));
    }

    public function show(User $user)
    {
        $groups = Group::all();
        $trustedDevices = TrustedDevice::where('user_id', $user->id)->get();

        return view('admin.users.profile', compact('user', 'groups', 'trustedDevices'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'department' => 'nullable|string|max:255',
            'active' => 'nullable|boolean'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'department' => $request->department,
            'active' => $request->active ? true : false
        ]);

        return redirect()->back()->with('status', 'User details updated successfully.');
    }

    public function removeMfa(Request $request, User $user)
    {
        $request->validate(['mfa_code' => 'required|string']);

        // Verify MFA code here

        $user->two_factor_enabled = false;
        $user->save();

        // Deauth the user's trusted devices
        TrustedDevice::where('user_id', $user->id)->delete();

        return redirect()->back()->with('status', 'MFA removed successfully.');
    }

    public function assignGroups(Request $request, User $user)
    {
        $request->validate(['groups' => 'array']);

        $user->groups()->sync($request->groups);

        return redirect()->back()->with('status', 'Groups assigned successfully.');
    }

    public function deauthDevice(Request $request, User $user, $deviceId)
    {
        // Assuming you have a relationship method 'trustedDevices' on User
        TrustedDevice::where('id', $deviceId)->delete();

        return redirect()->back()->with('status', 'Device deauthenticated successfully.');
    }

    public function disableUser(User $user)
    {
        $user->active = false;
        $user->save();

        return redirect()->back()->with('status', 'User disabled successfully.');
    }

    public function setTempPassword(Request $request, User $user)
    {
        $tempPassword = $this->generateTempPassword();

        $user->password = Hash::make($tempPassword);
        $user->force_password_change = true; // flag to force password change on next login
        $user->save();

        // Send email with temporary password
        Mail::send('emails.temp-password', ['user' => $user, 'tempPassword' => $tempPassword], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Your Temporary Password');
        });

        return response()->json([
            'status' => 'success',
            'tempPassword' => $tempPassword,
            'userName' => $user->name
        ]);
    }

    private function generateTempPassword()
    {
        return $this->randomString(6) . '-' . $this->randomString(6);
    }
    
    private function randomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->password = Hash::make($request->new_password);
        $user->force_password_change = false;
        $user->save();

        return redirect()->route('dashboard')->with('status', 'Password changed successfully');
    }
}
