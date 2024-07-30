

            <form method="POST" action="{{ route('settings.change-email.update') }}">
                @csrf
                <div class="form-group mb-3">
                    <label for="email" class="form-label">New Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group mb-4">
                    <label for="current-password" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="current-password" name="current_password" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="feather icon-save mr-2"></i>Change Email
                </button>
            </form>
     