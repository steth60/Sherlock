@extends('layouts.auth')

@section('content')

                <div class="card-header">{{ __('Setup TOTP Multi-Factor Authentication') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('two-factor.setup.post') }}">
                        @csrf

                        <div class="text-center mb-4">
                            <img src="data:image/png;base64,{{ $QR_Image }}" alt="QR Code" class="img-fluid mb-3">
                            <p class="text-muted">Scan this QR code with your authenticator app</p>
                            <input type="hidden" name="secret" value="{{ $secret }}">
                        </div>

                        <div class="form-group mb-4">
                            <label for="mfa_code" class="text-sm font-weight-bold text-muted mb-2">Authentication Code</label>
                            <input id="mfa_code" type="text" class="form-control @error('one_time_password') is-invalid @enderror" name="one_time_password" placeholder="Enter 6-digit code" required autofocus>
                            @error('one_time_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-block mb-4">
                            Enable TOTP MFA
                        </button>
                    </form>
                </div>

@endsection
