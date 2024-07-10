<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EnsureMfaEnabled
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $trustedDevice = $this->isTrustedDevice($request, $user);

        Log::info('EnsureMfaEnabled Middleware executed', [
            'user' => $user,
            'google2fa_secret' => $user ? $user->google2fa_secret : null,
            'session' => $request->session()->all(),
            'trusted_device' => $trustedDevice,
        ]);

        if ($user && !empty($user->google2fa_secret) && !$request->session()->has('auth.2fa.verified') && !$trustedDevice) {
            return redirect()->route('two-factor.challenge');
        }

        if ($user && empty($user->google2fa_secret)) {
            return redirect()->route('two-factor.setup');
        }

        return $next($request);
    }

    private function isTrustedDevice($request, $user)
    {
        $deviceToken = $request->cookie('device_token');
        if ($deviceToken) {
            $trustedDevice = $user->trustedDevices()->where('device_token', $deviceToken)->where('expires_at', '>', Carbon::now())->first();
            return $trustedDevice !== null;
        }
        return false;
    }
}
