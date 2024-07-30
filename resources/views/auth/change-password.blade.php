@extends('layouts.auth')

@section('content')
    <div class="mb-4">
        <i class="feather icon-lock auth-icon"></i>
    </div>
    <h3 class="mb-4">Change Password</h3>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <div class="input-group mb-3">
            <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Current Password" required>
        </div>
        <div class="input-group mb-3">
            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password" required>
        </div>
        <div class="input-group mb-4">
            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" placeholder="Confirm New Password" required>
        </div>
        <button type="submit" class="btn btn-primary shadow-2 mb-4">Change Password</button>
    </form>
    <p class="mb-0 text-muted">Remember your password? <a href="{{ route('login') }}">Login</a></p>
@endsection