@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
    <div class="mb-4">
        <i class="feather icon-log-in auth-icon"></i>
    </div>
    <h3 class="mb-4">Sign In</h3>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="input-group mb-3">
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="E-Mail Address" value="{{ old('email') }}" required autocomplete="email" autofocus>
        </div>

        <div class="input-group mb-4">
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required autocomplete="current-password">
        </div>

        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="custom-control-label" for="remember">Remember me</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block mb-4">Sign In</button>

        <div class="text-center">
            @if (Route::has('password.request'))
                <p class="mb-2"><a href="{{ route('password.request') }}">Forgot Your Password?</a></p>
            @endif
        </div>
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
</style>
@endsection