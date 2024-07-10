<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use App\Models\User;
use App\Models\TrustedDevice;
use Illuminate\Support\Facades\Auth;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MfaController extends Controller
{
    public function showSetupForm(Request $request)
    {
        $user = Auth::user();
        $google2fa = new Google2FA();

        // Generate the secret key
        $user->google2fa_secret = $google2fa->generateSecretKey();
        $user->save();

        // Generate the QR code URL
        $google2fa_url = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        // Generate QR code image with a smaller size
        $renderer = new ImageRenderer(
            new RendererStyle(200),  // Adjust size here
            new ImagickImageBackEnd()
        );
        $writer = new Writer($renderer);
        $QR_Image = base64_encode($writer->writeString($google2fa_url));

        return view('auth.two-factor-setup', ['QR_Image' => $QR_Image, 'secret' => $user->google2fa_secret]);
    }

    public function setupMfa(Request $request)
    {
        $user = Auth::user();
        $google2fa = new Google2FA();

        $request->validate([
            'one_time_password' => 'required|digits:6',
        ]);

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->one_time_password);

        if ($valid) {
            $user->two_factor_enabled = true;
            
            // Generate recovery codes
            $recoveryCodes = $this->generateRecoveryCodes();
            $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
            $user->save();

            return redirect()->route('two-factor.recovery-codes')->with('recovery_codes', $recoveryCodes);
        } else {
            return redirect()->route('two-factor.setup')->withErrors(['one_time_password' => 'Invalid authentication code.']);
        }
    }

    private function generateRecoveryCodes()
    {
        $recoveryCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = Str::random(10);
        }
        return $recoveryCodes;
    }

    public function showRecoveryCodes()
    {
        if (session()->has('recovery_codes')) {
            return view('settings.two-factor-recovery-codes', ['recovery_codes' => session('recovery_codes')]);
        }

        return redirect()->route('dashboard');
    }

    public function showChallenge()
    {
        return view('settings.two-factor-challenge');
    }

    public function verifyChallenge(Request $request)
    {
        $request->validate([
            'code' => 'required_without:recovery_code',
            'recovery_code' => 'required_without:code',
        ]);

        $user = Auth::user();
        $google2fa = new Google2FA();

        if ($request->has('code') && $google2fa->verifyKey($user->google2fa_secret, $request->input('code')) ||
            $request->has('recovery_code') && $this->verifyRecoveryCode($user, $request->input('recovery_code'))) {

            // Mark the 2FA as verified in the session
            $request->session()->put('auth.2fa.verified', true);

            if ($request->has('remember_device')) {
                $this->rememberDevice($request, $user);
            }

            return redirect()->intended('/home');
        }

        return redirect()->route('two-factor.challenge')->withErrors(['code' => 'The provided two-factor authentication code is incorrect.']);
    }

    private function verifyRecoveryCode($user, $recoveryCode)
    {
        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
        if (in_array($recoveryCode, $recoveryCodes)) {
            // Invalidate the used recovery code
            $recoveryCodes = array_diff($recoveryCodes, [$recoveryCode]);
            $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
            $user->save();
            return true;
        }
        return false;
    }

    private function rememberDevice($request, $user)
    {
        $deviceToken = Str::random(60);

        $trustedDevice = new TrustedDevice();
        $trustedDevice->user_id = $user->id;
        $trustedDevice->device_name = $request->header('User-Agent');
        $trustedDevice->device_token = $deviceToken;
        $trustedDevice->expires_at = Carbon::now()->addDays(90);
        $trustedDevice->save();

        // Set a cookie with the device token
        cookie()->queue(cookie('device_token', $deviceToken, 60 * 24 * 90));
    }

    public function disable(Request $request)
    {
        $user = Auth::user();
        $user->two_factor_enabled = false;
        $user->google2fa_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        return redirect()->route('profile.show')->with('status', 'Two Factor Authentication has been disabled.');
    }
}
