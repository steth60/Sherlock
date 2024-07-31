<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EnsureMfaEnabled
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        $trustedDevice = $this->isTrustedDevice($request, $user);

        if ($user && !$request->session()->has('auth.2fa.verified') && !$trustedDevice) {
            if ($user->google2fa_secret) {
                return redirect()->route('two-factor.challenge.totp');
            }
            if ($user->two_factor_email_enabled) {
                return redirect()->route('two-factor.challenge.email');
            }
        }

        if ($user && !$user->google2fa_secret && !$user->two_factor_email_enabled) {
            return redirect()->route('two-factor.setup');
        }

        return $next($request);
    }

    private function isTrustedDevice($request, $user)
    {
        if (!$user) {
            return false;
        }

        $deviceToken = $request->cookie('device_token');
        if ($deviceToken) {
            $trustedDevice = $user->trustedDevices()->where('device_token', $deviceToken)->where('expires_at', '>', Carbon::now())->first();
            return $trustedDevice !== null;
        }
        return false;
    }
}