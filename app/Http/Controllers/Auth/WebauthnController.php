<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Laragear\WebAuthn\Http\Requests\AttestedRequest;
use Laragear\WebAuthn\Http\Requests\AttestationRequest;
use Laragear\WebAuthn\Http\Requests\AssertedRequest;
use Laragear\WebAuthn\Http\Requests\AssertionRequest;

class WebAuthnController extends Controller
{
    public function showSetupForm()
    {
        return view('auth.webauthn-setup');
    }

    public function getRegisterOptions(AttestationRequest $request)
    {
        return $request->toCreate();
    }

    public function register(AttestedRequest $request)
    {
        $request->save();
        return response()->json(['message' => 'WebAuthn setup complete']);
    }

    public function showChallenge()
    {
        return view('auth.webauthn-challenge');
    }

    public function getLoginOptions(AssertionRequest $request)
    {
        return $request->toVerify();
    }

    public function login(AssertedRequest $request)
    {
        if ($request->login()) {
            $request->session()->put('auth.2fa.verified', true);
            return response()->json(['message' => 'WebAuthn authentication successful']);
        }
        return response()->json(['error' => 'WebAuthn authentication failed'], 400);
    }
}