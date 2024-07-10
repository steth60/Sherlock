<div class="card">
    <div class="card-header">Account Recovery Options</div>
    <div class="card-body">
        <form method="POST" action="{{ route('settings.account-recovery.update') }}">
            @csrf
            <div class="form-group">
                <label for="backup_email">Backup Email</label>
                <input type="email" class="form-control" id="backup_email" name="backup_email" value="{{ auth()->user()->backup_email }}" required>
            </div>
            <div class="form-group">
                <label for="security_question">Security Question</label>
                <select class="form-control" id="security_question" name="security_question">
                    <option>What was the name of your first pet?</option>
                    <option>In what city were you born?</option>
                    <option>What is your mother's maiden name?</option>
                </select>
            </div>
            <div class="form-group">
                <label for="security_answer">Security Answer</label>
                <input type="text" class="form-control" id="security_answer" name="security_answer" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Recovery Options</button>
        </form>
    </div>
</div>