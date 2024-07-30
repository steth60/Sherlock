@extends('layouts.auth')

@section('title', 'Two-Factor Authentication')

@section('content')
    <div class="mb-4">
        <i class="feather icon-shield auth-icon"></i>
    </div>
    <h3 class="mb-4">Two-Factor Authentication</h3>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('two-factor.challenge.totp.verify') }}" id="auth-form">
        @csrf
        <div class="form-group mb-3">
            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" placeholder="Enter 6-digit code" required autofocus>
        </div>

        <div class="form-group mb-4">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="remember_device" name="remember_device">
                <label class="custom-control-label" for="remember_device">Save this device for 90 days</label>
            </div>
        </div>

        <button type="button" class="btn btn-primary btn-block mb-4" id="verify-button">
            Verify and Login
        </button>

        <p class="text-center mb-0">
            <a href="#" id="toggle-recovery">Use Recovery Code</a>
        </p>
    </form>

    <form method="POST" action="{{ route('two-factor.challenge.totp.verify') }}" class="d-none" id="recovery-form">
        @csrf
        <div class="form-group mb-3">
            <input type="text" class="form-control @error('recovery_code') is-invalid @enderror" id="recovery_code" name="recovery_code" placeholder="Enter recovery code" required>
        </div>

        <div class="form-group mb-4">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="remember_device_recovery" name="remember_device">
                <label class="custom-control-label" for="remember_device_recovery">Save this device for 90 days</label>
            </div>
        </div>

        <button type="button" class="btn btn-primary btn-block mb-4" id="verify-recovery-button">
            Verify Recovery Code
        </button>

        <p class="text-center mb-0">
            <a href="#" id="toggle-code">Use Authentication Code</a>
        </p>
    </form>
@endsection

@section('modal')
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
        document.getElementById('auth-form').classList.add('d-none');
        document.getElementById('recovery-form').classList.remove('d-none');
    });

    document.getElementById('toggle-code').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('auth-form').classList.remove('d-none');
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
        $('#saveDeviceModal').modal('hide');
        document.getElementById('auth-form').submit();
    });
</script>
@endsection

@section('styles')
<style>
    .auth-content {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card {
        width: 100%;
        max-width: 400px;
    }
    .alert ul {
        list-style-type: none;
        padding-left: 0;
    }
    .auth-icon {
        font-size: 3rem;
        color: #5e72e4;
    }
    .form-control {
        height: auto;
        padding: 0.75rem 1rem;
    }
</style>
@endsection
