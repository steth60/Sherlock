<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordChangeController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        $rules = [
            'password' => ['required', 'confirmed', Password::defaults()],
        ];

        if (!$user->force_password_change) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $request->validate($rules);

        $user->password = Hash::make($request->password);
        $user->force_password_change = false;
        $user->save();

        return redirect()->route('home')->with('status', 'Password changed successfully.');
    }
}