@extends('layouts.auth')

@section('title', 'Set Up MFA')

@section('content')
<h4 class="text-dark mb-5">Set Up Multi-Factor Authentication</h4>

<form method="POST" action="{{ route('mfa.setup') }}">
    @csrf

    <div class="form-group">
        <label for="mfa_type">Choose MFA Method</label>
        <select name="mfa_type" id="mfa_type" class="form-control">
            <option value="google2fa">Google Authenticator</option>
            <option value="fido">FIDO/Physical Token</option>
            <option value="email">Email Token</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Continue</button>
</form>
@endsection
