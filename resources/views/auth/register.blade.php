@extends('layouts.auth')

@section('title', 'Sign Up')

@section('content')
<h4 class="text-dark mb-5">Sign Up</h4>

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="row">
        <!-- Name -->
        <div class="form-group col-md-12 mb-4">
            <label for="name">Name</label>
            <input type="text" class="form-control input-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ request('invitee_name') ?? old('name') }}" readonly required autofocus>
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="form-group col-md-12 mb-4">
            <label for="email">Email</label>
            <input type="email" class="form-control input-lg @error('email') is-invalid @enderror" id="email" name="email" value="{{ request('email') ?? old('email') }}" required>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group col-md-12 mb-4">
            <label for="password">Password</label>
            <input type="password" class="form-control input-lg @error('password') is-invalid @enderror" id="password" name="password" required>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group col-md-12 mb-4">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" class="form-control input-lg" id="password_confirmation" name="password_confirmation" required>
        </div>

        <!-- Invitation Code -->
        <div class="form-group col-md-12 mb-4">
            <label for="invitation_code">Invitation Code</label>
            <input type="text" class="form-control input-lg @error('invitation_code') is-invalid @enderror" id="invitation_code" name="invitation_code" value="{{ request('invitation_code') ?? old('invitation_code') }}" readonly required>
            @error('invitation_code')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Hidden fields to ensure the name and email are submitted -->
        <input type="hidden" name="invitee_name" value="{{ request('invitee_name') ?? old('invitee_name') }}">
        <input type="hidden" name="email" value="{{ request('email') ?? old('email') }}">

        <div class="col-md-12">
            <button type="submit" class="btn btn-lg btn-primary btn-block mb-4">Register</button>

            <p>Already have an account?
                <a class="text-blue" href="{{ route('login') }}">Sign In</a>
            </p>
        </div>
    </div>
</form>
@endsection
