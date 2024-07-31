@extends('layouts.auth')

@section('title', 'Forgotten Password')

@section('content')
    <div class="mb-4">
        <i class="feather icon-mail auth-icon"></i>
    </div>
    <h3 class="mb-4">{{ __('Reset Password') }}</h3>

    @if (session('status'))
        <div class="alert alert-success mb-4" role="alert">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="input-group mb-3">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="{{ __('E-Mail Address') }}">
        </div>

        <button type="submit" class="btn btn-primary shadow-2 mb-4">
            {{ __('Send Password Reset Link') }}
        </button>
    </form>

    <p class="mb-0 text-muted">Remember your password? <a href="{{ route('login') }}">Login</a></p>
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
</style>
@endsection