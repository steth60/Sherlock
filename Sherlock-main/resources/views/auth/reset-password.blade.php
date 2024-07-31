@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
    <div class="mb-4">
        <i class="feather icon-lock auth-icon"></i>
    </div>
    <h3 class="mb-4">{{ __('Reset Password') }}</h3>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ request()->token }}">

        <div class="form-group mb-3">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="feather icon-mail"></i>
                </span>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="{{ __('E-Mail Address') }}">
            </div>
        </div>

        <div class="form-group mb-3">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="feather icon-lock"></i>
                </span>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="{{ __('New Password') }}">
            </div>
        </div>

        <div class="form-group mb-4">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="feather icon-check"></i>
                </span>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('Confirm New Password') }}">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block mb-4">
            {{ __('Reset Password') }}
        </button>
    </form>

    <p class="text-center mb-0">Remembered your password? <a href="{{ route('login') }}">Login</a></p>
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
    .alert ul {
        list-style-type: none;
        padding-left: 0;
    }
    .auth-icon {
        font-size: 3rem;
        color: #5e72e4;
    }
    .form-control {
        height: auto;
        padding: 0.75rem 1rem;
    }
    .input-group-text {
        background-color: transparent;
        border-right: none;
    }
    .form-control {
        border-left: none;
    }
    .input-group:focus-within .input-group-text {
        border-color: #80bdff;
    }
</style>
@endsection