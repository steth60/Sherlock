<div class="container">
    <div class="card">
        <div class="card-header">
            <h5><i class="feather icon-shield text-c-blue wid-20"></i><span class="p-l-5">Security Settings</span></h5>
        </div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Manage Trusted Devices -->
            <h5 class="mb-4">Recognized Devices</h5>
            @foreach ($trustedDevices as $device)
                <div class="media mb-2">
                    <i class="feather icon-{{ $device->type == 'desktop' ? 'monitor' : ($device->type == 'tablet' ? 'tablet' : 'smartphone') }} f-20 h3 me-3 wid-30 text-center"></i>
                    <div class="media-body">
                        <div class="float-end">
                            <div class="{{ $device->is_active ? 'text-success' : 'text-muted' }} d-inline-block me-2">
                                <i class="fas fa-circle f-10 me-2"></i>
                                {{ $device->is_active ? 'Current Active' : 'Active ' . $device->last_active }}
                            </div>
                            <a href="#" class="text-danger" data-bs-toggle="modal" data-bs-target="#removeDeviceModal{{ $device->id }}"><i class="feather icon-x-circle"></i></a>
                            <a href="#" class="text-info ms-2" data-bs-toggle="modal" data-bs-target="#editDeviceModal{{ $device->id }}"><i class="feather icon-edit"></i></a>
                        </div>
                        <span class="font-weight-bold text-truncate" style="max-width: 200px;" title="{{ $device->device_name }}">{{ $device->device_name }}</span>
                        <span class="text-muted">| {{ $device->location }}</span>
                    </div>
                </div>

                <!-- Remove Device Modal -->
                <div class="modal fade" id="removeDeviceModal{{ $device->id }}" tabindex="-1" aria-labelledby="removeDeviceModalLabel{{ $device->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="removeDeviceModalLabel{{ $device->id }}">Remove Device</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to remove this device?
                            </div>
                            <div class="modal-footer">
                                <form action="{{ route('settings.trusted-devices.destroy', $device->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Remove</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Device Modal -->
                <div class="modal fade" id="editDeviceModal{{ $device->id }}" tabindex="-1" aria-labelledby="editDeviceModalLabel{{ $device->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editDeviceModalLabel{{ $device->id }}">Edit Device Name</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('settings.trusted-devices.update', $device->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label for="device_name{{ $device->id }}">Device Name</label>
                                        <input type="text" class="form-control" id="device_name{{ $device->id }}" name="device_name" value="{{ $device->device_name }}" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3">Save changes</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Active Sessions -->
            <hr>
            <h5 class="mb-3">Active Sessions</h5>
            @foreach ($activeSessions as $session)
                <div class="media mb-2">
                    <i class="feather icon-monitor f-20 h3 me-3 wid-30 text-center text-success"></i>
                    <div class="media-body">
                        <div class="float-end">
                            <a href="#" class="text-muted badge badge-light-danger" data-bs-toggle="modal" data-bs-target="#logoutSessionModal{{ $session->id }}">Logout</a>
                        </div>
                        <span class="font-weight-bold text-truncate" style="max-width: 200px;" title="{{ $session->device_name }}">{{ $session->device_name }}</span>
                        <span class="text-muted">| Last Active: {{ $session->last_activity }}</span>
                        <div><small class="text-muted">{{ $session->user_agent }}</small></div>
                    </div>
                </div>

                <!-- Logout Session Modal -->
                <div class="modal fade" id="logoutSessionModal{{ $session->id }}" tabindex="-1" aria-labelledby="logoutSessionModalLabel{{ $session->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="logoutSessionModalLabel{{ $session->id }}">Logout Session</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to logout from this session?
                            </div>
                            <div class="modal-footer">
                                <form action="{{ route('settings.active-sessions.logout', $session->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Logout</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Add New Device -->
            <hr>
            <h5 class="mb-4">Add New Device</h5>
            <form id="save-device-form">
                @csrf
                <div class="form-group">
                    <label for="new_device_name">Device Name</label>
                    <input type="text" class="form-control" id="new_device_name" name="device_name" required>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Save Current Device</button>
            </form>

            <!-- Enable/Disable MFA -->
            <hr>
            <h5>Multi-Factor Authentication</h5>
            <form action="{{ route('settings.mfa.update') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="two_factor_enabled">Enable TOTP</label>
                    <input type="checkbox" id="two_factor_enabled" name="two_factor_enabled" {{ Auth::user()->two_factor_enabled ? 'checked' : '' }}>
                </div>
                <div class="form-group">
                    <label for="two_factor_email_enabled">Enable Email MFA</label>
                    <input type="checkbox" id="two_factor_email_enabled" name="two_factor_email_enabled" {{ Auth::user()->two_factor_email_enabled ? 'checked' : '' }}>
                </div>
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>

            <!-- Account Recovery Options -->
            <hr>
            <h5 class="mb-4">Account Recovery Options</h5>
            <form action="{{ route('settings.account-recovery.update') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="backup_email">Backup Email</label>
                    <input type="email" class="form-control" id="backup_email" name="backup_email" value="{{ $user->backup_email }}" required>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Update Recovery Options</button>
            </form>

            <!-- Login Notifications -->
            <hr>
            <h5 class="mb-4">Login Notifications</h5>
            <form action="{{ route('settings.login-notifications.update') }}" method="POST">
                @csrf
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="login_notifications_enabled" name="login_notifications_enabled" {{ Auth::user()->login_notifications_enabled ? 'checked' : '' }}>
                    <label class="form-check-label" for="login_notifications_enabled">Enable Email Notifications for New Logins</label>
                </div>
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
            
        </div>
    </div>
</div>

<script>
document.getElementById('save-device-form').addEventListener('submit', function(event) {
    event.preventDefault();

    let deviceName = document.getElementById('new_device_name').value;

    fetch('{{ route('settings.trusted-devices.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ device_name: deviceName })
    })
    .then(response => response.json())
    .then(data => {
        document.cookie = `device_token=${data.device_token};path=/;max-age=${60 * 60 * 24 * 90}`;
        window.location.reload();
    })
    .catch(error => console.error('Error:', error));
});
</script>
