@extends('layouts.auth')

@section('title', 'WebAuthn Challenge')

@section('content')
    <div>
        <h3>Authenticate with WebAuthn</h3>
        <button id="login">Login with Security Key</button>
        <div id="status"></div>
    </div>

    <script>
        document.getElementById('login').addEventListener('click', function() {
            let getArgs = {!! $getArgs !!};

            navigator.credentials.get(getArgs).then((assertion) => {
                let authenticatorData = new Uint8Array(assertion.response.authenticatorData).reduce((data, byte) => data + String.fromCharCode(byte), '');
                let clientDataJSON = new Uint8Array(assertion.response.clientDataJSON).reduce((data, byte) => data + String.fromCharCode(byte), '');
                let signature = new Uint8Array(assertion.response.signature).reduce((data, byte) => data + String.fromCharCode(byte), '');
                let userHandle = new Uint8Array(assertion.response.userHandle).reduce((data, byte) => data + String.fromCharCode(byte), '');

                fetch('{{ route('two-factor.challenge.webauthn.post') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id: assertion.id,
                        rawId: new Uint8Array(assertion.rawId).reduce((data, byte) => data + String.fromCharCode(byte), ''),
                        type: assertion.type,
                        authenticatorData: btoa(authenticatorData),
                        clientDataJSON: btoa(clientDataJSON),
                        signature: btoa(signature),
                        userHandle: btoa(userHandle)
                    })
                }).then((response) => {
                    if (response.ok) {
                        document.getElementById('status').innerText = 'Authentication successful';
                    } else {
                        document.getElementById('status').innerText = 'Authentication failed';
                    }
                });
            }).catch((error) => {
                document.getElementById('status').innerText = 'Error: ' + error;
            });
        });
    </script>
@endsection
