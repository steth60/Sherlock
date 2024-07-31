<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->force_password_change && !$request->is('user/password-change*')) {
            return redirect()->route('password.change');
        }

        return $next($request);
    }
}