@extends('layouts.auth')

@section('title', 'Sign Up')

@section('content')
    <div class="mb-4">
        <i class="feather icon-user-plus auth-icon"></i>
    </div>
    <h3 class="mb-4">Sign Up</h3>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group mb-3">
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ request('invitee_name') ?? old('name') }}" readonly required autofocus placeholder="Name">
        </div>

        <div class="form-group mb-3">
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ request('email') ?? old('email') }}" readonly required autofocus placeholder="Email">
        </div>

        <div class="form-group mb-3">
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Password">
        </div>

        <div class="form-group mb-3">
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Confirm Password">
        </div>

        <div class="form-group mb-4">
            <input type="text" class="form-control @error('invitation_code') is-invalid @enderror" id="invitation_code" name="invitation_code" value="{{ request('invitation_code') ?? old('invitation_code') }}" readonly required placeholder="Invitation Code">
        </div>

        <input type="hidden" name="invitee_name" value="{{ request('invitee_name') ?? old('invitee_name') }}">
        <input type="hidden" name="email" value="{{ request('email') ?? old('email') }}">

        <button type="submit" class="btn btn-primary btn-block mb-4">Register</button>

        <p class="text-center mb-0">Already have an account? <a href="{{ route('login') }}">Sign In</a></p>
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
    .form-control {
        height: auto;
        padding: 0.75rem 1rem;
    }
</style>
@endsection