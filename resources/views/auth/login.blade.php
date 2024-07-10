@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
<h4 class="text-dark mb-5">Sign In</h4>

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="row">
        <div class="form-group col-md-12 mb-4">
            <input type="email" class="form-control input-lg @error('email') is-invalid @enderror" id="email" name="email" aria-describedby="emailHelp" placeholder="E-Mail Address" value="{{ old('email') }}" required autocomplete="email" autofocus>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group col-md-12">
            <input type="password" class="form-control input-lg @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required autocomplete="current-password">
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="col-md-12">
            <div class="d-flex my-2 justify-content-between">
                <div class="d-inline-block mr-3">
                    <label class="control control-checkbox">Remember me
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} />
                        <div class="control-indicator"></div>
                    </label>
                </div>

                @if (Route::has('password.request'))
                    <p><a class="text-blue" href="{{ route('password.request') }}">Forgot Your Password?</a></p>
                @endif
            </div>

            <button type="submit" class="btn btn-lg btn-primary btn-block mb-4">Sign In</button>

            <p>Don't have an account yet?
                <a class="text-blue" href="{{ route('register') }}">Sign Up</a>
            </p>
        </div>
    </div>
</form>
@endsection