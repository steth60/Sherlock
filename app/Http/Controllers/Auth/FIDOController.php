<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Webauthn\Webauthn;
use Webauthn\PublicKeyCredentialCreationOptions;

class FIDOController extends Controller
{
    public function create()
    {
        $user = auth()->user();

        $creationOptions = (new PublicKeyCredentialCreationOptions)
            ->setUser(new UserEntity($user->email, $user->id, $user->name));

        $json = app(Webauthn::class)->getCreateArgs($creationOptions);

        return view('auth.fido-setup', compact('json'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        app(Webauthn::class)->doRegister($request->json()->all(), $user);

        $user->two_factor_confirmed_at = now();
        $user->save();

        return redirect()->route('dashboard')->with('status', 'FIDO/physical token MFA enabled.');
    }

    public function verify(Request $request)
    {
        $user = auth()->user();

        app(Webauthn::class)->doLogin($request->json()->all(), $user);

        return redirect()->route('dashboard');
    }
}
