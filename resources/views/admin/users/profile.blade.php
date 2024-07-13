@extends('layouts.app')

@section('content')
<div class="container">
    <h1>User Profile: {{ $user->name }}</h1>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">User Details</h5>
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                        </div>
                        <div class="form-group">
                            <label for="department">Department</label>
                            <input type="text" class="form-control" id="department" name="department" value="{{ $user->department }}">
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="active" name="active" {{ $user->active ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Active</label>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Update Details</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Actions</h5>
                    <button class="btn btn-secondary mb-2" data-toggle="modal" data-target="#confirmTempPasswordModal">Set Temporary Password</button>
                    <button class="btn btn-danger mb-2" data-toggle="modal" data-target="#removeMfaModal">Remove MFA</button>
                </div>
            </div>
        </div>
    </div>

    <h5>Assign Groups</h5>
    <form action="{{ route('users.assignGroups', $user) }}" method="POST" class="mb-4">
        @csrf
        <div class="form-group">
            <select multiple class="form-control" name="groups[]">
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" {{ $user->groups->contains($group->id) ? 'selected' : '' }}>{{ $group->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-secondary">Assign Groups</button>
    </form>

    <h5>Trusted Devices</h5>
    <ul class="list-group mb-4">
        @foreach($trustedDevices as $device)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>{{ $device->device_name }} - {{ $device->created_at->format('Y-m-d H:i:s') }}</span>
                <button class="btn btn-danger btn-sm" onclick="showDeauthDeviceModal('{{ $user->id }}', '{{ $device->id }}')">Deauthenticate</button>
            </li>
        @endforeach
    </ul>

    <!-- Confirm Temporary Password Modal -->
    <div class="modal fade" id="confirmTempPasswordModal" tabindex="-1" aria-labelledby="confirmTempPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmTempPasswordModalLabel">Set Temporary Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to set a temporary password for {{ $user->name }}?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="setTempPassword()">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Display Temporary Password Modal -->
    <div class="modal fade" id="displayTempPasswordModal" tabindex="-1" aria-labelledby="displayTempPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="displayTempPasswordModalLabel">Temporary Password Set</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{ $user->name }}'s password has been reset.</p>
                    <p>Password: <strong id="generatedTempPassword"></strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Remove MFA Modal -->
    <div class="modal fade" id="removeMfaModal" tabindex="-1" aria-labelledby="removeMfaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('users.removeMfa', $user) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="removeMfaModalLabel">Remove MFA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="mfaCode">MFA Code</label>
                            <input type="text" class="form-control" id="mfaCode" name="mfa_code" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Remove MFA</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Deauth Device Modal -->
    <div class="modal fade" id="deauthDeviceModal" tabindex="-1" aria-labelledby="deauthDeviceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="deauthDeviceForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deauthDeviceModalLabel">Deauthenticate Device</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to deauthenticate this device?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Deauthenticate</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showDeauthDeviceModal(userId, deviceId) {
        var form = document.getElementById('deauthDeviceForm');
        form.action = '/admin/users/' + userId + '/deauth-device/' + deviceId;
        $('#deauthDeviceModal').modal('show');
    }

    function setTempPassword() {
        var url = '{{ route("users.setTempPassword", $user) }}';

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('generatedTempPassword').innerText = data.tempPassword;
                $('#confirmTempPasswordModal').modal('hide');
                $('#displayTempPasswordModal').modal('show');
            } else {
                alert('An error occurred.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
</script>
@endsection
