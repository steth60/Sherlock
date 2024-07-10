<div class="card">
    <div class="card-header">Change Email</div>
    <div class="card-body">
        <form method="POST" action="{{ route('settings.change-email.update') }}">
            @csrf
            <div class="form-group">
                <label for="email">New Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="current-password">Current Password</label>
                <input type="password" class="form-control" id="current-password" name="current_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Change Email</button>
        </form>
    </div>
</div>