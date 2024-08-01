<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\Server;
use Webauthn\PublicKeyCredentialUserEntity;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\AttestationStatement\AttestationObject;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\PublicKeyCredentialLoader;
use Webauthn\PublicKeyCredentialDescriptor;

class WebauthnController extends Controller
{
    protected $server;

    public function __construct()
    {
        $rpEntity = new PublicKeyCredentialRpEntity(config('webauthn.rp.name'), config('webauthn.rp.id'));
        $this->server = new Server($rpEntity);
    }

    public function loginOptions(Request $request)
    {
        $user = User::where('email', $request->email)->first(); // Modify to get the user by email or other identifier
        $credentialDescriptors = $user->webauthnCredentials->map(function ($credential) {
            return new PublicKeyCredentialDescriptor($credential->type, $credential->credential_id);
        })->toArray();

        $publicKeyCredentialRequestOptions = $this->server->generatePublicKeyCredentialRequestOptions(
            config('webauthn.timeout'),
            $credentialDescriptors
        );

        session(['publicKeyCredentialRequestOptions' => $publicKeyCredentialRequestOptions]);

        return response()->json($publicKeyCredentialRequestOptions);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first(); // Modify to get the user by email or other identifier
        $publicKeyCredentialRequestOptions = session('publicKeyCredentialRequestOptions');

        $authenticatorAssertionResponse = new AuthenticatorAssertionResponse(
            base64_decode($request->input('authenticatorData')),
            base64_decode($request->input('clientDataJSON')),
            base64_decode($request->input('signature')),
            base64_decode($request->input('userHandle'))
        );

        $publicKeyCredentialLoader = new PublicKeyCredentialLoader();
        $publicKeyCredential = $publicKeyCredentialLoader->loadArray([
            'id' => $request->input('id'),
            'rawId' => base64_decode($request->input('rawId')),
            'type' => $request->input('type'),
            'response' => [
                'authenticatorData' => base64_decode($request->input('authenticatorData')),
                'clientDataJSON' => base64_decode($request->input('clientDataJSON')),
                'signature' => base64_decode($request->input('signature')),
                'userHandle' => base64_decode($request->input('userHandle'))
            ]
        ]);

        $this->server->loadAndCheckAssertionResponse(
            $authenticatorAssertionResponse,
            $publicKeyCredentialRequestOptions,
            $user->webauthnCredentials->toArray()
        );

        Auth::login($user);

        return redirect()->intended(config('fortify.home'));
    }
}

