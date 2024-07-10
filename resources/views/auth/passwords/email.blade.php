@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<h4 class="text-dark mb-5">Reset Password</h4>

@if (session('status'))
    <div class="alert alert-success mb-4" role="alert">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="row">
        <div class="form-group col-md-12 mb-4">
            <input type="email" class="form-control input-lg @error('email') is-invalid @enderror" id="email" name="email" placeholder="E-Mail Address" value="{{ old('email') }}" required autocomplete="email" autofocus>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="col-md-12">
            <button type="submit" class="btn btn-lg btn-primary btn-block mb-4">
                {{ __('Send Password Reset Link') }}
            </button>

            <p>Remember your password?
                <a class="text-blue" href="{{ route('login') }}">Sign In</a>
            </p>
        </div>
    </div>
</form>
@endsection