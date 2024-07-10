<h4 class="text-dark mb-5">Manage Trusted Devices</h4>

@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

<table class="table">
    <thead>
        <tr>
            <th scope="col">Device Name</th>
            <th scope="col">Expires At</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($trustedDevices as $device)
            <tr>
                <td>{{ $device->device_name }}</td>
                <td>{{ $device->expires_at }}</td>
                <td>
                    <form action="{{ route('settings.trusted-devices.destroy', $device->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                    </form>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#editDeviceModal{{ $device->id }}">Edit</button>
                    <form action="{{ route('settings.trusted-devices.renew', $device->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm">Renew</button>
                    </form>
                </td>
            </tr>

            <!-- Edit Device Modal -->
            <div class="modal fade" id="editDeviceModal{{ $device->id }}" tabindex="-1" role="dialog" aria-labelledby="editDeviceModalLabel{{ $device->id }}" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editDeviceModalLabel{{ $device->id }}">Edit Device</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('settings.trusted-devices.update', $device->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="device_name">Device Name</label>
                                    <input type="text" class="form-control" id="device_name" name="device_name" value="{{ $device->device_name }}" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </tbody>
</table>

<form id="save-device-form">
    @csrf
    <div class="form-group">
        <label for="device_name">Device Name</label>
        <input type="text" class="form-control" id="device_name" name="device_name" required>
    </div>
    <button type="submit" class="btn btn-primary">Save Current Device</button>
</form>

<script>
document.getElementById('save-device-form').addEventListener('submit', function(event) {
    event.preventDefault();

    let deviceName = document.getElementById('device_name').value;

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

