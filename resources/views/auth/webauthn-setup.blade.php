@extends('layouts.auth')

@section('content')
<div class="container">
    <h2>Setup WebAuthn</h2>
    <button id="register">Register Security Key</button>
    <div id="status"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@laragear/webpass@2/dist/webpass.js" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        document.getElementById('register').addEventListener('click', async function() {
            const statusElement = document.getElementById('status');
            statusElement.innerText = 'Initiating WebAuthn registration...';
    
            if (Webpass.isUnsupported()) {
                statusElement.innerText = "Your browser doesn't support WebAuthn.";
                return;
            }
    
            try {
                const { success, error } = await Webpass.attest(
                    "{{ route('two-factor.setup.webauthn.options') }}",
                    "{{ route('two-factor.setup.webauthn.register') }}",
                    {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }
                );
    
                if (error) {
                    statusElement.innerText = 'Error: ' + error;
                } else if (success) {
                    statusElement.innerText = 'Registration successful';
                    // Optionally redirect or perform other actions on success
                    // window.location.replace("/dashboard");
                }
            } catch (error) {
                console.error('WebAuthn Error:', error);
                statusElement.innerText = 'Error: ' + (error.message || 'Unknown error occurred');
            }
        });
    });
    </script>
@endsection