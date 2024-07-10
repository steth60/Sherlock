@extends('layouts.auth')

@section('title', 'Sign Up')

@section('content')
<h4 class="text-dark mb-5">Sign Up</h4>

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="row">
        <div class="form-group col-md-12 mb-4">
            <input type="text" class="form-control input-lg @error('name') is-invalid @enderror" id="name" name="name" placeholder="Name" value="{{ old('name') }}" required autocomplete="name" autofocus>
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group col-md-12 mb-4">
            <input type="email" class="form-control input-lg @error('email') is-invalid @enderror" id="email" name="email" placeholder="E-Mail Address" value="{{ old('email') }}" required autocomplete="email">
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group col-md-12 mb-4">
            <input type="password" class="form-control input-lg @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required autocomplete="new-password">
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group col-md-12 mb-4">
            <input type="password" class="form-control input-lg" id="password-confirm" name="password_confirmation" placeholder="Confirm Password" required autocomplete="new-password">
        </div>

        <div class="col-md-12">
            <button type="submit" class="btn btn-lg btn-primary btn-block mb-4">Sign Up</button>

            <p>Already have an account?
                <a class="text-blue" href="{{ route('login') }}">Sign In</a>
            </p>
        </div>
    </div>
</form>
@endsection