@extends('layouts.auth')

@section('title', 'Setup Two-Factor Authentication')

@section('content')
<div class="mb-4">
    <i class="feather icon-shield auth-icon"></i>
</div>
<h3 class="mb-4">Setup Two-Factor Authentication</h3>

@if (session('recovery_codes'))
    <div class="alert alert-success mb-4">
        <p>Two-Factor Authentication enabled successfully. Here are your recovery codes:</p>
        <ul class="list-unstyled mb-3">
            @foreach (session('recovery_codes') as $code)
                <li><code>{{ $code }}</code></li>
            @endforeach
        </ul>
        <p class="mb-0">Please save these codes in a safe place. You can use them to access your account if you lose access to your authentication device.</p>
    </div>
@endif

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="totp-tab" data-toggle="tab" href="#totp" role="tab" aria-controls="totp" aria-selected="true">TOTP MFA</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false">Email MFA</a>
    </li>
</ul>
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="totp" role="tabpanel" aria-labelledby="totp-tab">
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
                Enable Two-Factor Authentication
            </button>
        </form>
    </div>
    <div class="tab-pane fade" id="email" role="tabpanel" aria-labelledby="email-tab">
        <form method="POST" action="{{ route('two-factor-email.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary btn-block mb-4">
                Send MFA Code
            </button>
        </form>

        <form method="POST" action="{{ route('two-factor-email.verify') }}">
            @csrf
            <div class="form-group mb-4">
                <label for="email_mfa_code" class="text-sm font-weight-bold text-muted mb-2">Enter MFA Code</label>
                <input id="email_mfa_code" type="text" class="form-control @error('email_mfa_code') is-invalid @enderror" name="email_mfa_code" placeholder="Enter the code sent to your email" required>
                @error('email_mfa_code')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary btn-block mb-4">
                Enable Email MFA
            </button>
        </form>
    </div>
</div>
@endsection

@section('styles')
<style>
    .auth-content {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card {
        width: 100%;
        max-width: 400px;
    }
    .auth-icon {
        font-size: 3rem;
        color: #5e72e4;
    }
    .form-control {
        height: auto;
        padding: 0.75rem 1rem;
    }
    .alert ul {
        background-color: #f8f9fa;
        border-radius: 0.25rem;
        padding: 1rem;
    }
    .alert ul li {
        margin-bottom: 0.5rem;
    }
    .alert ul li:last-child {
        margin-bottom: 0;
    }
</style>
@endsection

@section('scripts')
<script>
    $(function () {
        $('#myTab a:first-child').tab('show'); // Select first tab
    });
</script>
@endsection
