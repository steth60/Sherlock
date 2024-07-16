@extends('layouts.auth')

@section('content')
<h4 class="text-dark mb-5">Recovery Codes</h4>

<div class="alert alert-success">
    <p>Two-Factor Authentication enabled successfully. Here are your recovery codes:</p>
    <ul id="recovery-codes">
        @foreach ($recovery_codes as $code)
            <li>{{ $code }}</li>
        @endforeach
    </ul>
    <p>Please save these codes in a safe place. You can use them to access your account if you lose access to your authentication device.</p>
</div>

<div class="mb-4">
    <button class="btn btn-secondary" onclick="printRecoveryCodes()">Print</button>
    <button class="btn btn-secondary" onclick="copyToClipboard()">Copy</button>
    <button class="btn btn-secondary" onclick="downloadRecoveryCodes()">Download</button>
</div>

<form method="GET" action="{{ route('home') }}">
    <button type="submit" class="btn btn-primary">Next</button>
</form>

<script>
    function printRecoveryCodes() {
        var content = document.getElementById('recovery-codes').innerHTML;
        var myWindow = window.open('', '', 'width=600,height=400');
        myWindow.document.write('<html><head><title>Print</title></head><body>' + content + '</body></html>');
        myWindow.document.close();
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }

    function copyToClipboard() {
        var text = Array.from(document.querySelectorAll('#recovery-codes li')).map(li => li.textContent).join('\n');
        navigator.clipboard.writeText(text).then(function() {
            alert('Recovery codes copied to clipboard');
        }, function(err) {
            console.error('Could not copy text: ', err);
        });
    }

    function downloadRecoveryCodes() {
        var text = Array.from(document.querySelectorAll('#recovery-codes li')).map(li => li.textContent).join('\n');
        var blob = new Blob([text], { type: 'text/plain' });
        var url = window.URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'recovery-codes.txt';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
    }
</script>
@endsection
