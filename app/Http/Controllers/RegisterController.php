<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\RegisterResponse;

class RegisteredUserController extends Controller
{
    public function store(Request $request): RegisterResponse
    {
        $this->validate($request, [
            'invitee_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'invitation_code' => 'required|string|exists:invitations,invitation_code'
        ]);

        // Verify the invitation
        $invitation = Invitation::where('invitation_code', $request->invitation_code)->first();

        if ($invitation->isExpired() || $invitation->used || $invitation->email !== $request->email || $invitation->invitee_name !== $request->invitee_name) {
            return back()->withErrors(['invitation_code' => 'The invitation code is invalid, has expired, or the details do not match.']);
        }

        // Create the user
        $user = User::create([
            'name' => $request->invitee_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Delete the invitation
        $invitation->delete();

        return app(RegisterResponse::class);
    }
}
