@extends('layouts.auth')

@section('content')

                <div class="card-header">{{ __('Setup Email Multi-Factor Authentication') }}</div>

                <div class="card-body">
                    <form id="sendMfaForm" method="POST" action="{{ route('two-factor.challenge.email.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-block mb-4" id="sendMfaButton">
                            Send MFA Code
                        </button>
                    </form>

                    <p id="retryMessage" class="text-muted mb-4"></p>

                    <form method="POST" action="{{ route('two-factor.challenge.email.verify') }}">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="email_mfa_code" class="text-sm font-weight-bold text-muted mb-2">Enter MFA Code</label>
                            <input id="email_mfa_code" type="text" class="form-control @error('email_mfa_code') is-invalid @enderror" name="email_mfa_code" placeholder="Enter the code sent to your email" required>
                            @error('email_mfa_code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-block mb-4">
                            Enable Email MFA
                        </button>
                    </form>
                </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('sendMfaForm');
    const button = document.getElementById('sendMfaButton');
    const retryMessage = document.getElementById('retryMessage');
    let retryAfter = {{ $retryAfter ?? 0 }};

    function updateRetryMessage() {
        if (retryAfter > 0) {
            button.disabled = true;
            retryMessage.textContent = `You can request a new code in ${retryAfter} seconds.`;
            retryAfter--;
            setTimeout(updateRetryMessage, 1000);
        } else {
            button.disabled = false;
            retryMessage.textContent = 'You can request a new code now.';
        }
    }

    updateRetryMessage();

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.retry_after) {
                retryAfter = data.retry_after;
                updateRetryMessage();
            }
            alert(data.message);
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});
</script>
@endsection