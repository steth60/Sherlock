@extends('layouts.auth')

@section('title', 'Setup WebAuthn')

@section('content')
    <div>
        <h3>Setup WebAuthn</h3>
        <p>Follow the instructions to set up your security key.</p>
        <button id="register">Register Security Key</button>
        <div id="status"></div>
        <div id="diagnostics"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const registerButton = document.getElementById('register');
            const statusDiv = document.getElementById('status');
            const diagnosticsDiv = document.getElementById('diagnostics');

            function updateDiagnostics(message) {
                diagnosticsDiv.innerHTML += message + '<br>';
            }

            updateDiagnostics('Browser: ' + navigator.userAgent);
            updateDiagnostics('Protocol: ' + window.location.protocol);
            updateDiagnostics('Host: ' + window.location.host);

            if (window.isSecureContext === false) {
                updateDiagnostics('WARNING: Not in a secure context. WebAuthn requires HTTPS or localhost.');
            }

            if (!window.PublicKeyCredential) {
                updateDiagnostics('window.PublicKeyCredential is not available.');
                statusDiv.textContent = 'WebAuthn is not supported in this browser. Please try a different browser.';
                registerButton.disabled = true;
            } else {
                updateDiagnostics('window.PublicKeyCredential is available.');
                PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable()
                    .then((available) => {
                        if (available) {
                            updateDiagnostics('Platform authenticator is available.');
                        } else {
                            updateDiagnostics('Platform authenticator is not available.');
                        }
                    })
                    .catch(error => {
                        updateDiagnostics('Error checking platform authenticator: ' + error);
                    });
            }

            registerButton.addEventListener('click', async function() {
                try {
                    let createArgs = {!! $createArgs !!};

                    updateDiagnostics('CreateArgs received: ' + JSON.stringify(createArgs));

                    createArgs.publicKey.challenge = Uint8Array.from(atob(createArgs.publicKey.challenge), c => c.charCodeAt(0));
                    createArgs.publicKey.user.id = Uint8Array.from(atob(createArgs.publicKey.user.id), c => c.charCodeAt(0));
                    if (createArgs.publicKey.excludeCredentials) {
                        createArgs.publicKey.excludeCredentials = createArgs.publicKey.excludeCredentials.map(cred => {
                            cred.id = Uint8Array.from(atob(cred.id), c => c.charCodeAt(0));
                            return cred;
                        });
                    }

                    updateDiagnostics('Calling navigator.credentials.create()');
                    const credential = await navigator.credentials.create({ publicKey: createArgs.publicKey });

                    updateDiagnostics('Credential created successfully');

                    const attestationObject = btoa(String.fromCharCode.apply(null, new Uint8Array(credential.response.attestationObject)));
                    const clientDataJSON = btoa(String.fromCharCode.apply(null, new Uint8Array(credential.response.clientDataJSON)));
                    const rawId = btoa(String.fromCharCode.apply(null, new Uint8Array(credential.rawId)));

                    updateDiagnostics('Sending data to server');
                    const response = await fetch('{{ route('two-factor.setup.webauthn.post') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id: credential.id,
                            rawId: rawId,
                            type: credential.type,
                            attestationObject: attestationObject,
                            clientDataJSON: clientDataJSON
                        })
                    });

                    if (response.ok) {
                        statusDiv.textContent = 'Registration successful';
                    } else {
                        const errorData = await response.json();
                        statusDiv.textContent = 'Registration failed: ' + (errorData.message || 'Unknown error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    statusDiv.textContent = 'Error: ' + (error.message || 'Unknown error occurred');
                    updateDiagnostics('Error: ' + error.toString());
                }
            });
        });
    </script>
@endsection