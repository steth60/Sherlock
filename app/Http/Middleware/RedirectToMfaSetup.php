<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectToMfaSetup
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($user && !$user->two_factor_enabled && !$request->is('two-factor-setup')) {
            return redirect()->route('two-factor.setup');
        }

        return $next($request);
    }
}
