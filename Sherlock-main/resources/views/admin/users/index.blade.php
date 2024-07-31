@extends('layouts.app')
@section('title', 'User Managment')

@section('content')
<div class="container">
    <h1>Users Overview</h1>

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

    <div class="row mb-4">
        <!-- Your existing cards here -->
    </div>

    <h2 class="mt-4">User List</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Expiration</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>Active</td>
                    <td>N/A</td>
                    <td>
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-primary btn-sm">View Profile</a>
                    </td>
                </tr>
            @endforeach
            @foreach($invitations as $invitation)
                <tr>
                    <td>{{ $invitation->invitee_name }}</td>
                    <td>{{ $invitation->email }}</td>
                    <td>{{ $invitation->isExpired() ? 'Expired' : 'Pending' }}</td>
                    <td>{{ $invitation->expires_at->format('Y-m-d H:i:s') }}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#resendInviteModal" data-id="{{ $invitation->id }}" data-email="{{ $invitation->email }}">Resend Invite</button>
                        <form action="{{ route('admin.revoke-invite', $invitation->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Revoke Invite</button>
                        </form>
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
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
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
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('admin.mfa-not-enabled-users')
                </div>
            </div>
        </div>
    </div>

    <!-- Resend Invite Modal -->
    <div class="modal fade" id="resendInviteModal" tabindex="-1" aria-labelledby="resendInviteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resendInviteModalLabel">Resend Invitation</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="resendInviteForm" method="POST" action="">
                    @csrf
                    <div class="modal-body">
                        <p>Are you sure you want to resend the invitation to <span id="invite-email"></span>?</p>
                        <input type="hidden" name="invitation_id" id="invitation-id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Resend Invite</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('#resendInviteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var id = button.data('id'); // Extract info from data-* attributes
            var email = button.data('email');

            var modal = $(this);
            modal.find('.modal-title').text('Resend Invitation');
            modal.find('#invite-email').text(email);
            modal.find('#invitation-id').val(id);

            var form = modal.find('#resendInviteForm');
            form.attr('action', '{{ url("admin/resend-invite") }}/' + id);
        });
    });
</script>
@endsection
