@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Reset Multi-Factor Authentication</h2>
    <form method="POST" action="{{ route('mfa.reset.post') }}">
        @csrf
        <div class="mb-3">
            <label for="code" class="form-label">Authentication Code</label>
            <input id="code" type="text" class="form-control" name="code" required autofocus autocomplete="one-time-code">
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Reset MFA</button>
        </div>
    </form>
</div>
@endsection
