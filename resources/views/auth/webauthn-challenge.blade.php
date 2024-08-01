@extends('layouts.auth')

@section('content')
<div class="container">
    <h2>WebAuthn Challenge</h2>
    <button id="authenticate">Authenticate with Security Key</button>
    <div id="status"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@laragear/webpass@2/dist/webpass.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('authenticate').addEventListener('click', async function() {
        try {
            const { user, error } = await Webpass.assert(
                "{{ route('two-factor.challenge.webauthn.options') }}",
                "{{ route('two-factor.challenge.webauthn.login') }}"
            );

            if (error) {
                document.getElementById('status').innerText = 'Error: ' + error;
            } else {
                document.getElementById('status').innerText = 'Authentication successful';
                window.location.href = "{{ route('home') }}";
            }
        } catch (error) {
            document.getElementById('status').innerText = 'Error: ' + error.message;
        }
    });
});
</script>
@endsection