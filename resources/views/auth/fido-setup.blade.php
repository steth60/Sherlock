@extends('layouts.auth')

@section('title', 'Set Up FIDO MFA')

@section('content')
<h4 class="text-dark mb-5">Set Up FIDO/Physical Token MFA</h4>

<form method="POST" action="{{ route('fido.store') }}" id="fido-form">
    @csrf
    <button type="submit" class="btn btn-primary">Register FIDO Device</button>
</form>

<script>
    document.getElementById('fido-form').addEventListener('submit', function(event) {
        event.preventDefault();
        navigator.credentials.create({publicKey: {!! $json !!}}).then(function (credential) {
            fetch('{{ route('fido.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(credential)
            }).then(function (response) {
                if (response.ok) {
                    window.location.href = '{{ route('dashboard') }}';
                }
            });
        });
    });
</script>
@endsection
