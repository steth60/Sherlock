<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use App\Models\Invitation;
use App\Models\Permission;
use App\Models\Group;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;



class AdminDashboardController extends Controller
{
    public function index()
    {
        $users = User::all();
        $invitations = Invitation::all();
        $totalUsers = $users->count();
        $totalPermissions = Permission::count();
        $totalGroups = Group::count();
        $activeUsers = $users->where('active', true)->count();
        $inactiveUsers = $users->where('active', false)->count();
        $unverifiedEmailUsers = $users->whereNull('email_verified_at')->count();
        $mfaNotEnabledUsers = $users->where('two_factor_enabled', false)->count();
        $isMaintainerMode = $this->getMaintainerMode();
    
        // New variables
        
       
        $serverLoad = sys_getloadavg()[0] * 100; // This gets the server load (you might need to adjust based on your hosting environment)
        //$databaseSize = \DB::select('SELECT pg_database_size(current_database()) as size')[0]->size / 1024 / 1024; // This is for PostgreSQL, adjust for your database
    
        return view('admin.dashboard', compact(
            'users', 
            'totalPermissions', 
            'totalGroups', 
            'invitations',
            'totalUsers', 
            'activeUsers',
            'inactiveUsers',
            'unverifiedEmailUsers', 
            'mfaNotEnabledUsers',
            'isMaintainerMode',
     
      
            'serverLoad',
        
        ));
    }


    public function unverifiedEmailUsers()
    {
        $users = User::whereNull('email_verified_at')->get();
        return response()->json($users);
    }

    public function mfaNotEnabledUsers()
    {
        $users = User::where('two_factor_enabled', 0)->get();
        return response()->json($users);
    }



    public function invite(Request $request)
{
    $request->validate([
        'invitee_name' => 'required|string|max:255',
        'email' => 'required|email'
    ]);

    // Check if the email already has an invitation
    $existingInvitation = Invitation::where('email', $request->email)->first();

    if ($existingInvitation) {
        if ($existingInvitation->isExpired() || !$existingInvitation->used) {
            // Option to resend the invitation if it is expired or not used
            return back()->with('status', 'This email already has an invitation. Would you like to resend it?')
                         ->with('resend_email', $request->email)
                         ->with('invitation_id', $existingInvitation->id);
        } else {
            // If the invitation has been used
            return back()->withErrors(['email' => 'This email is already in use.']);
        }
    }

    $invitationCode = strtoupper(Str::random(10));
    $expiresAt = Carbon::now()->addHours(48);

    $invitation = Invitation::create([
        'invitee_name' => $request->invitee_name,
        'email' => $request->email,
        'invitation_code' => $invitationCode,
        'expires_at' => $expiresAt,
    ]);

    try {
        Mail::send('emails.invitation', [
            'invitationCode' => $invitationCode,
            'inviteeName' => $request->invitee_name,
            'inviteeEmail' => $request->email,
        ], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('You are invited to join our platform');
        });

        Log::info('Invitation sent to ' . $request->email);

        return back()->with('status', 'Invitation sent successfully.');
    } catch (\Exception $e) {
        Log::error('Failed to send invitation to ' . $request->email . ': ' . $e->getMessage());

        return back()->withErrors(['email' => 'Failed to send invitation.']);
    }
}




    public function resendInvite(Request $request, $id)
    {
        $invitation = Invitation::findOrFail($id);
        $invitation->delete();

        $newInvitationCode = strtoupper(Str::random(10));
        $newExpiresAt = Carbon::now()->addHours(48);

        $newInvitation = Invitation::create([
            'invitee_name' => $invitation->invitee_name,
            'email' => $invitation->email,
            'invitation_code' => $newInvitationCode,
            'expires_at' => $newExpiresAt,
        ]);

        try {
            Mail::send('emails.invitation', [
                'invitationCode' => $newInvitationCode,
                'inviteeName' => $newInvitation->invitee_name,
                'inviteeEmail' => $newInvitation->email,
            ], function ($message) use ($newInvitation) {
                $message->to($newInvitation->email);
                $message->subject('Your Invitation to Join Our Platform');
            });

            Log::info('Invitation resent to ' . $newInvitation->email);

            return back()->with('status', 'Invitation resent successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to resend invitation to ' . $newInvitation->email . ': ' . $e->getMessage());

            return back()->withErrors(['email' => 'Failed to resend invitation.']);
        }
    }


    public function revokeInvite($id)
    {
        $invitation = Invitation::findOrFail($id);
        $invitation->delete();

        Log::info('Invitation revoked for ' . $invitation->email);

        return back()->with('status', 'Invitation revoked successfully.');
    }

    public function toggleMaintainerMode(Request $request)
    {
        $maintainerMode = Setting::updateOrCreate(
            ['key' => 'maintainer_mode'],
            ['value' => $request->input('maintainer_mode') ? '1' : '0']
        );

        return back()->with('status', 'Maintainer mode updated successfully.');
    }

    public function getMaintainerMode()
    {
        $setting = Setting::where('key', 'maintainer_mode')->first();
        return $setting ? $setting->value == '1' : false;
    }

}
