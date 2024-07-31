<?php

namespace App\Actions;

use App\Models\User;
use App\Models\Invitation;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateUserWithInvitation implements CreatesNewUsers
{
    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'invitation_code' => ['required', 'string', 'exists:invitations,invitation_code,used,0'],
        ])->validate();

        $invitation = Invitation::where('invitation_code', $input['invitation_code'])->firstOrFail();
        $invitation->update(['used' => true]);

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
