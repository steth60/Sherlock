@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Admin Dashboard</h1>

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
                    <p class="card-text display-4">{{ $unverifiedEmails }}</p>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#unverifiedEmailModal">View Users</button>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Users Without MFA Enabled</h5>
                    <p class="card-text display-4">{{ $mfaNotEnabled }}</p>
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
