<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Writer;
use App\Models\User;
use App\Models\TrustedDevice;
use App\Models\WebauthnCredential;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\EmailMfaCode;
use lbuchs\WebAuthn\WebAuthn;
use lbuchs\WebAuthn\WebAuthnException;

class MfaController extends Controller
{
    protected $webauthn;

    public function __construct()
    {
        $rpName = config('webauthn.rp.name', 'My Application');
        $rpId = config('webauthn.rp.id', 'example.com');
        $this->webauthn = new WebAuthn($rpName, $rpId);
    }

    public function showSetupForm(Request $request)
    {
        return view('auth.mfa-setup');
    }

    public function showTotpSetupForm(Request $request)
    {
        $user = $request->user();
        $google2fa = new Google2FA();

        if (!$user->google2fa_secret) {
            $user->google2fa_secret = $google2fa->generateSecretKey();
            $user->save();
        }

        $google2fa_url = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new ImagickImageBackEnd()
        );
        $writer = new Writer($renderer);
        $QR_Image = base64_encode($writer->writeString($google2fa_url));

        return view('auth.totp-setup', [
            'QR_Image' => $QR_Image,
            'secret' => $user->google2fa_secret,
        ]);
    }

    public function showEmailSetupForm(Request $request)
    {
        return view('auth.email-setup');
    }

    public function setupMfa(Request $request)
    {
        $user = $request->user();
        $google2fa = new Google2FA();

        $request->validate([
            'one_time_password' => 'required|digits:6',
        ]);

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->one_time_password);

        if ($valid) {
            $user->two_factor_enabled = true;
            
            $recoveryCodes = $this->generateRecoveryCodes();
            $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
            $user->save();

            return redirect()->route('two-factor.recovery-codes')->with('recovery_codes', $recoveryCodes);
        } else {
            return redirect()->route('two-factor.setup.totp')->withErrors(['one_time_password' => 'Invalid authentication code.']);
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
            return view('auth.two-factor-recovery-codes', ['recovery_codes' => session('recovery_codes')]);
        }

        return redirect()->route('home');
    }

    public function showChallenge()
    {
        return view('auth.two-factor-challenge');
    }

    public function verifyChallenge(Request $request)
    {
        $request->validate([
            'code' => 'required_without:recovery_code',
            'recovery_code' => 'required_without:code',
        ]);

        $user = $request->user();
        $google2fa = new Google2FA();

        if ($request->has('code') && $google2fa->verifyKey($user->google2fa_secret, $request->input('code')) ||
            $request->has('recovery_code') && $this->verifyRecoveryCode($user, $request->input('recovery_code'))) {

            $request->session()->put('auth.2fa.verified', true);

            if ($request->has('remember_device')) {
                $this->rememberDevice($request, $user);
            }

            return redirect()->intended('/home');
        }

        return redirect()->route('two-factor.challenge.totp')->withErrors(['code' => 'The provided two-factor authentication code is incorrect.']);
    }

    private function verifyRecoveryCode($user, $recoveryCode)
    {
        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
        if (in_array($recoveryCode, $recoveryCodes)) {
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

        cookie()->queue(cookie('device_token', $deviceToken, 60 * 24 * 90));
    }

    public function disable(Request $request)
    {
        $user = $request->user();
        $user->two_factor_enabled = false;
        $user->google2fa_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        return redirect()->route('profile.show')->with('status', 'Two Factor Authentication has been disabled.');
    }

    public function disableEmailMfa(Request $request)
    {
        $user = $request->user();
        $user->two_factor_email_enabled = false;
        $user->email_mfa_code = null;
        $user->email_mfa_code_expires_at = null;
        $user->save();

        return redirect()->route('profile.show')->with('status', 'Email MFA has been disabled.');
    }

    public function setupEmailMfa(Request $request)
    {
        $user = $request->user();
        $user->two_factor_email_enabled = true;
        $user->save();

        return redirect()->back()->with('status', 'Email MFA enabled successfully.');
    }

    public function sendEmailMfaCode(Request $request)
    {
        $user = $request->user();
        $key = 'email_mfa_send:' . $user->id;

        if (RateLimiter::tooManyAttempts($key, 1)) {
            $retryAfter = RateLimiter::availableIn($key);
            return response()->json([
                'message' => "Please wait before requesting another code.",
                'retry_after' => $retryAfter
            ], 429);
        }

        $this->sendNewMfaCode($user);
        RateLimiter::hit($key, 30); // 5 minutes

        return response()->json([
            'message' => 'MFA code sent to your email.',
            'retry_after' => 30
        ]);
    }

    private function sendNewMfaCode($user)
    {
        $code = rand(100000, 999999);
        $user->email_mfa_code = $code;
        $user->email_mfa_code_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        Mail::to($user->email)->send(new EmailMfaCode($code));
    }

    public function verifyEmailMfaCode(Request $request)
    {
        $request->validate([
            'email_mfa_code' => 'required|integer',
        ]);

        $user = $request->user();
        if ($user->email_mfa_code == $request->email_mfa_code && Carbon::now()->lessThan($user->email_mfa_code_expires_at)) {
            $user->two_factor_email_enabled = true;
            $user->save();

            $request->session()->put('auth.2fa.verified', true);
            return redirect()->intended(config('fortify.home'))->with('status', 'Email MFA enabled successfully.');
        }

        return back()->withErrors(['email_mfa_code' => 'The provided code is incorrect or has expired.']);
    }

    public function showEmailChallenge(Request $request)
    {
        $user = $request->user();
        $key = 'email_mfa_send:' . $user->id;

        // If it's the first attempt after login, clear the rate limiter
        if (!$request->session()->has('mfa_challenge_started')) {
            RateLimiter::clear($key);
            $request->session()->put('mfa_challenge_started', true);
        }

        $retryAfter = RateLimiter::availableIn($key);

        if ($retryAfter === 0) {
            $this->sendNewMfaCode($user);
            RateLimiter::hit($key, 30); // 5 minutes
            $retryAfter = 30;
        }

        return view('auth.email-mfa-challenge', [
            'retryAfter' => $retryAfter
        ]);
    }

    // WebAuthn Methods
    public function showWebauthnSetupForm(Request $request)
    {
        $user = $request->user();
        $webAuthn = new WebAuthn(
            config('app.name'),
            request()->getHost(),
            ['none', 'packed', 'tpm', 'android-key', 'android-safetynet', 'fido-u2f', 'apple']
        );
    
        $createArgs = $webAuthn->getCreateArgs(
            $user->id,
            $user->email,
            $user->name,
            60,
            false,
            'preferred',
            null,
            $user->webauthnCredentials->pluck('credential_id')->toArray()
        );
    
        // Encode binary data to base64
        $createArgs->publicKey->challenge = base64_encode($createArgs->publicKey->challenge);
        $createArgs->publicKey->user->id = base64_encode($createArgs->publicKey->user->id);
        if (isset($createArgs->publicKey->excludeCredentials)) {
            foreach ($createArgs->publicKey->excludeCredentials as &$credential) {
                $credential->id = base64_encode($credential->id);
            }
        }
    
        $request->session()->put('webauthnChallenge', $webAuthn->getChallenge());
    
        return view('auth.webauthn-setup', [
            'createArgs' => json_encode($createArgs)
        ]);
    }

    public function setupWebauthn(Request $request)
    {
        $user = $request->user();
        $webAuthn = new WebAuthn(
            config('app.name'),
            request()->getHost(),
            ['none', 'packed', 'tpm', 'android-key', 'android-safetynet', 'fido-u2f', 'apple']
        );

        try {
            $data = $webAuthn->processCreate(
                $request->input('clientDataJSON'),
                $request->input('attestationObject'),
                session('webauthnChallenge'),
                false,
                true,
                false
            );

            WebauthnCredential::create([
                'user_id' => $user->id,
                'credential_id' => $data->credentialId,
                'public_key' => $data->credentialPublicKey,
                'type' => $data->attestationFormat,
                'counter' => $data->signatureCounter,
            ]);

            return redirect()->route('two-factor.recovery-codes')->with('status', 'WebAuthn setup complete');
        } catch (\Exception $e) {
            return back()->withErrors(['webauthn' => $e->getMessage()]);
        }
    }

    public function showWebauthnChallenge(Request $request)
    {
        $user = $request->user();
        $webAuthn = new WebAuthn(
            config('app.name'),
            request()->getHost(),
            ['none', 'packed', 'tpm', 'android-key', 'android-safetynet', 'fido-u2f', 'apple']
        );

        $getArgs = $webAuthn->getGetArgs(
            $user->webauthnCredentials->pluck('credential_id')->toArray()
        );

        $request->session()->put('webauthnChallenge', $webAuthn->getChallenge());

        return view('auth.webauthn-challenge', [
            'getArgs' => json_encode($getArgs)
        ]);
    }


    public function verifyWebauthn(Request $request)
    {
        $user = $request->user();
        $webAuthn = new WebAuthn(
            config('app.name'),
            request()->getHost(),
            ['none', 'packed', 'tpm', 'android-key', 'android-safetynet', 'fido-u2f', 'apple']
        );

        try {
            $credential = $user->webauthnCredentials()->where('credential_id', $request->input('id'))->firstOrFail();

            $result = $webAuthn->processGet(
                $request->input('clientDataJSON'),
                $request->input('authenticatorData'),
                $request->input('signature'),
                $credential->public_key,
                session('webauthnChallenge'),
                $credential->counter,
                false,
                true
            );

            if ($result) {
                $credential->counter = $webAuthn->getSignatureCounter();
                $credential->save();

                $request->session()->put('auth.2fa.verified', true);
                return redirect()->intended(config('fortify.home'))->with('status', 'WebAuthn authentication successful');
            }
        } catch (\Exception $e) {
            return back()->withErrors(['webauthn' => $e->getMessage()]);
        }

        return back()->withErrors(['webauthn' => 'WebAuthn authentication failed']);
    }
}
