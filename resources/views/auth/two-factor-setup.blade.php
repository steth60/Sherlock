@extends('layouts.auth')

@section('content')
<h4 class="text-dark mb-5">Setup Two Factor Authentication</h4>

@if (session('recovery_codes'))
    <div class="alert alert-success">
        <p>Two-Factor Authentication enabled successfully. Here are your recovery codes:</p>
        <ul>
            @foreach (session('recovery_codes') as $code)
                <li>{{ $code }}</li>
            @endforeach
        </ul>
        <p>Please save these codes in a safe place. You can use them to access your account if you lose access to your authentication device.</p>
    </div>
@endif

<form method="POST" action="{{ route('two-factor.setup.post') }}">
    @csrf

    <div class="text-center mb-5">
        <img src="data:image/png;base64,{{ $QR_Image }}" alt="QR Code" class="img-fluid mb-4">
        <p class="text-muted">Scan this QR code with your authenticator app</p>
        <input type="hidden" name="secret" value="{{ $secret }}">
    </div>

    <div class="form-group mb-4">
        <label for="mfa_code" class="text-sm font-weight-bold text-muted mb-2">Authentication Code</label>
        <input id="mfa_code" type="text" class="form-control form-control-lg @error('one_time_password') is-invalid @enderror" name="one_time_password" placeholder="Enter 6-digit code" required autofocus>
        @error('one_time_password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <button type="submit" class="btn btn-lg btn-primary btn-block mb-4">
        Enable Two Factor Authentication
    </button>
</form>
@endsection
