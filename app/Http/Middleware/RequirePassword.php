<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RequirePassword
{
    public function handle($request, Closure $next)
    {
        if (!session()->has('auth.password_confirmed_at') || session('auth.password_confirmed_at') < now()->timestamp - config('auth.password_timeout', 900)) {
            return redirect()->route('password.confirm');
        }

        return $next($request);
    }
}
