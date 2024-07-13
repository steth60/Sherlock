@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Users Overview</h1>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text display-4">{{ $totalUsers }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Active Users</h5>
                    <p class="card-text display-4">{{ $activeUsers }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Inactive Users</h5>
                    <p class="card-text display-4">{{ $inactiveUsers }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Users without Verified Email</h5>
                    <p class="card-text display-4">{{ $unverifiedEmailUsers }}</p>
                    <button class="btn btn-secondary mt-2" data-bs-toggle="modal" data-bs-target="#unverifiedEmailUsersModal">View Users</button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Users without MFA</h5>
                    <p class="card-text display-4">{{ $mfaNotEnabledUsers }}</p>
                    <button class="btn btn-secondary mt-2" data-bs-toggle="modal" data-bs-target="#mfaNotEnabledUsersModal">View Users</button>
                </div>
            </div>
        </div>
    </div>

    <h2 class="mt-4">User List</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <a href="{{ route('users.show', $user) }}" class="btn btn-primary btn-sm">View Profile</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Unverified Email Users Modal -->
    <div class="modal fade" id="unverifiedEmailUsersModal" tabindex="-1" aria-labelledby="unverifiedEmailUsersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unverifiedEmailUsersModalLabel">Users without Verified Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('admin.unverified-email-users')
                </div>
            </div>
        </div>
    </div>

    <!-- MFA Not Enabled Users Modal -->
    <div class="modal fade" id="mfaNotEnabledUsersModal" tabindex="-1" aria-labelledby="mfaNotEnabledUsersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mfaNotEnabledUsersModalLabel">Users without MFA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('admin.mfa-not-enabled-users')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
