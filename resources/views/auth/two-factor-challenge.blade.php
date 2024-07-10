@extends('layouts.auth')

@section('title', 'Two-Factor Authentication')

@section('content')
<h4 class="text-dark mb-5">Two-Factor Authentication</h4>

<form method="POST" action="{{ route('two-factor.challenge.verify') }}" id="auth-form">
    @csrf

    <div class="row">
        <div class="form-group col-md-12 mb-4">
            <input type="text" class="form-control input-lg @error('code') is-invalid @enderror" id="code" name="code" placeholder="Enter 6-digit code" required autofocus>
            @error('code')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group col-md-12 mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember_device" id="remember_device">
                <label class="form-check-label" for="remember_device">
                    Save this device for 90 days
                </label>
            </div>
        </div>

        <div class="col-md-12">
            <button type="button" class="btn btn-lg btn-primary btn-block mb-4" id="verify-button">
                Verify and Login
            </button>

            <p class="text-center mb-0">
                <a href="#" class="text-blue" id="toggle-recovery">Use Recovery Code</a>
            </p>
        </div>
    </div>
</form>

<form method="POST" action="{{ route('two-factor.challenge.verify') }}" class="d-none" id="recovery-form">
    @csrf

    <div class="row">
        <div class="form-group col-md-12 mb-4">
            <input type="text" class="form-control input-lg @error('recovery_code') is-invalid @enderror" id="recovery_code" name="recovery_code" placeholder="Enter recovery code" required>
            @error('recovery_code')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group col-md-12 mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember_device" id="remember_device_recovery">
                <label class="form-check-label" for="remember_device_recovery">
                    Save this device for 90 days
                </label>
            </div>
        </div>

        <div class="col-md-12">
            <button type="button" class="btn btn-lg btn-primary btn-block mb-4" id="verify-recovery-button">
                Verify Recovery Code
            </button>

            <p class="text-center mb-0">
                <a href="#" class="text-blue" id="toggle-code">Use Authentication Code</a>
            </p>
        </div>
    </div>
</form>

<!-- Modal -->
<div class="modal fade" id="saveDeviceModal" tabindex="-1" role="dialog" aria-labelledby="saveDeviceModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="saveDeviceModalLabel">Save Device</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        You can manage your saved devices from the settings menu. Do you want to continue saving this device for 90 days?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="continue-button">Continue</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('toggle-recovery').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector('form').classList.add('d-none');
        document.getElementById('recovery-form').classList.remove('d-none');
    });

    document.getElementById('toggle-code').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector('form').classList.remove('d-none');
        document.getElementById('recovery-form').classList.add('d-none');
    });

    document.getElementById('verify-button').addEventListener('click', function() {
        if (document.getElementById('remember_device').checked) {
            $('#saveDeviceModal').modal('show');
        } else {
            document.getElementById('auth-form').submit();
        }
    });

    document.getElementById('verify-recovery-button').addEventListener('click', function() {
        if (document.getElementById('remember_device_recovery').checked) {
            $('#saveDeviceModal').modal('show');
        } else {
            document.getElementById('recovery-form').submit();
        }
    });

    document.getElementById('continue-button').addEventListener('click', function() {
        document.getElementById('auth-form').submit();
    });
</script>
@endsection
