@extends('layouts.auth')

@section('title', '2FA Setup - Confirm Password')

@section('content')
    <div class="mb-4">
        <i class="feather icon-shield auth-icon"></i>
    </div>
    <h3 class="mb-4">{{ __('Confirm Password') }}</h3>

    <p class="text-muted mb-4">{{ __('Please confirm your password to continue to 2FA setup.') }}</p>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="feather icon-lock"></i></span>
            </div>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="{{ __('Password') }}">
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block shadow-2 mb-4">
                {{ __('Confirm Password') }}
            </button>
        </div>

        @if (Route::has('password.request'))
            <p class="mb-0 text-muted text-center">
                <a href="{{ route('password.request') }}">
                    {{ __('Forgot Your Password?') }}
                </a>
            </p>
        @endif
    </form>
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
    .input-group-text {
        background-color: #f8f9fe;
        border-right: none;
    }
    .form-control {
        border-left: none;
    }
    .form-control:focus {
        box-shadow: none;
        border-color: #ced4da;
    }
</style>
@endsection