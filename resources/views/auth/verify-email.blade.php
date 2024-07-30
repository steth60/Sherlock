@extends('layouts.auth')

@section('title', 'Verify Your Email Address')

@section('content')
    <div class="mb-4">
        <i class="feather icon-mail auth-icon"></i>
    </div>
    <h3 class="mb-4">{{ __('Verify Your Email Address') }}</h3>

    @if (session('resent'))
        <div class="alert alert-success mb-4" role="alert">
            {{ __('A fresh verification link has been sent to your email address.') }}
        </div>
    @endif

    <p class="mb-4">{{ __('Before proceeding, please check your email for a verification link.') }}</p>

    <p class="mb-4">{{ __('If you did not receive the email') }},</p>

    <form id="resendForm" method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-primary btn-block mb-4" id="resendButton">
            {{ __('Request another verification email') }}
        </button>
    </form>

    <p id="retryMessage" class="text-muted mb-4"></p>

    <p class="text-center mb-0">
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            {{ __('Logout') }}
        </a>
    </p>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('resendForm');
    const button = document.getElementById('resendButton');
    const retryMessage = document.getElementById('retryMessage');
    let retryAfter = {{ $retryAfter ?? 0 }};

    function updateRetryMessage() {
        if (retryAfter > 0) {
            button.disabled = true;
            retryMessage.textContent = `You can request a new verification email in ${retryAfter} seconds.`;
            retryAfter--;
            setTimeout(updateRetryMessage, 1000);
        } else {
            button.disabled = false;
            retryMessage.textContent = 'You can request a new verification email now.';
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