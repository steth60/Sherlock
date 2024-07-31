<div class="card">
    <div class="card-header">Two-Factor Authentication (MFA) Settings</div>
    <div class="card-body">
        @if(auth()->user()->two_factor_secret)
            <p>Two-factor authentication is currently enabled.</p>
            
            <h5 class="mt-4">Manage MFA Methods</h5>
            <form method="POST" action="{{ route('settings.mfa.add') }}" class="mb-3">
                @csrf
                <div class="form-group">
                    <label for="mfa_method">Add MFA Method</label>
                    <select class="form-control" id="mfa_method" name="mfa_method">
                        <option value="app">Authenticator App</option>
                        <option value="sms">SMS</option>
                        <option value="email">Email</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Add Method</button>
            </form>

            <form method="POST" action="{{ route('settings.mfa.remove') }}" class="mb-3">
                @csrf
                <div class="form-group">
                    <label for="remove_mfa_method">Remove MFA Method</label>
                    <select class="form-control" id="remove_mfa_method" name="remove_mfa_method">
                        <!-- Populate this with the user's current MFA methods -->
                        <option value="app">Authenticator App</option>
                        <option value="sms">SMS</option>
                        <option value="email">Email</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-danger">Remove Method</button>
            </form>

            <h5 class="mt-4">Backup Codes</h5>
            <p>If you lose access to your primary MFA method, you can use these backup codes to log in.</p>
            <form method="POST" action="{{ route('settings.mfa.regenerate') }}">
                @csrf
                <button type="submit" class="btn btn-warning">Regenerate Backup Codes</button>
            </form>
        @else
            <p>Two-factor authentication is currently not set up.</p>
            <a href="{{ route('two-factor.setup') }}" class="btn btn-primary">Set Up Two-Factor Authentication</a>
        @endif
    </div>
</div>