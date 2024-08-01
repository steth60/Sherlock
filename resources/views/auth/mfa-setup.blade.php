@extends('layouts.auth')

@section('content')

                <div class="card-header">{{ __('Setup Multi-Factor Authentication') }}</div>

                <div class="card-body">
                    <p>{{ __('Choose a method to setup multi-factor authentication for your account:') }}</p>
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <a href="{{ route('two-factor.setup.totp') }}" class="btn btn-primary btn-block">Setup TOTP MFA</a>
                        </li>
                        <li class="mb-3">
                            <a href="{{ route('two-factor.setup.email') }}" class="btn btn-primary btn-block">Setup Email MFA</a>
                        </li>
                        <li class="mb-3">
                            <a href="{{ route('two-factor.setup.webauthn') }}" class="btn btn-primary">Setup WebAuthn</a>
                        </li>
                    </ul>
                </div>
 
@endsection
