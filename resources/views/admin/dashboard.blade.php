@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Admin Dashboard</h1>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('resend_email'))
        <div class="alert alert-warning">
            This email already has an invitation. Would you like to resend it?
            <form action="{{ route('admin.resend-invite', session('invitation_id')) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-warning btn-sm">Resend Invite</button>
            </form>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text display-4">{{ $totalUsers }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Permissions</h5>
                    <p class="card-text display-4">{{ $totalPermissions }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Groups</h5>
                    <p class="card-text display-4">{{ $totalGroups }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Users Without Verified Email</h5>
                    <p class="card-text display-4">{{ $unverifiedEmailUsers }}</p>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#unverifiedEmailModal">View Users</button>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Users Without MFA Enabled</h5>
                    <p class="card-text display-4">{{ $mfaNotEnabledUsers }}</p>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#mfaNotEnabledModal">View Users</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Admin Pages</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Manage Users</a>
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">Manage Permissions</a>
                    <a href="{{ route('admin.groups.index') }}" class="btn btn-secondary">Manage Groups</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Invitation Form -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Invite User</h5>
                    <form action="{{ route('admin.invite') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="invitee_name">Invitee Name</label>
                            <input type="text" class="form-control" id="invitee_name" name="invitee_name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Invitee Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Invitation</button>
                    </form>
                </div>
            </div>
        </div>

    <form action="{{ route('admin.toggle-maintainer-mode') }}" method="POST">
        @csrf
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="maintainer_mode" name="maintainer_mode" {{ $isMaintainerMode ? 'checked' : '' }}>
            <label class="form-check-label" for="maintainer_mode">Enable Maintainer Mode</label>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

<!-- Unverified Email Users Modal -->
<div class="modal fade" id="unverifiedEmailModal" tabindex="-1" role="dialog" aria-labelledby="unverifiedEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="unverifiedEmailModalLabel">Users Without Verified Email</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="unverifiedEmailUsersContent">Loading...</div>
            </div>
        </div>
    </div>
</div>

<!-- MFA Not Enabled Users Modal -->
<div class="modal fade" id="mfaNotEnabledModal" tabindex="-1" role="dialog" aria-labelledby="mfaNotEnabledModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mfaNotEnabledModalLabel">Users Without MFA Enabled</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="mfaNotEnabledUsersContent">Loading...</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#unverifiedEmailModal').on('show.bs.modal', function () {
            $.get('{{ route("admin.unverified-email-users") }}', function(data) {
                var content = '<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Actions</th></tr></thead><tbody>';
                data.forEach(function(user) {
                    content += '<tr><td>' + user.id + '</td><td>' + user.name + '</td><td>' + user.email + '</td><td><a href="/users/' + user.id + '" class="btn btn-sm btn-primary">View</a></td></tr>';
                });
                content += '</tbody></table></div>';
                $('#unverifiedEmailUsersContent').html(content);
            });
        });

        $('#mfaNotEnabledModal').on('show.bs.modal', function () {
            $.get('{{ route("admin.mfa-not-enabled-users") }}', function(data) {
                var content = '<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Actions</th></tr></thead><tbody>';
                data.forEach(function(user) {
                    content += '<tr><td>' + user.id + '</td><td>' + user.name + '</td><td>' + user.email + '</td><td><a href="/users/' + user.id + '" class="btn btn-sm btn-primary">View</a></td></tr>';
                });
                content += '</tbody></table></div>';
                $('#mfaNotEnabledUsersContent').html(content);
            });
        });
    });
</script>
@endsection
