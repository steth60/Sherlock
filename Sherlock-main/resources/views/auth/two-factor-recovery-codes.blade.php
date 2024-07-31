@extends('layouts.auth')

@section('title', 'Recovery Codes')

@section('content')
    <div class="mb-4">
        <i class="feather icon-key auth-icon"></i>
    </div>
    <h3 class="mb-4">Recovery Codes</h3>

    <div class="alert alert-success mb-4">
        <p>Two-Factor Authentication enabled successfully. Here are your recovery codes:</p>
        <ul id="recovery-codes" class="list-unstyled">
            @foreach ($recovery_codes as $code)
                <li><code>{{ $code }}</code></li>
            @endforeach
        </ul>
        <p class="mb-0">Please save these codes in a safe place. You can use them to access your account if you lose access to your authentication device.</p>
    </div>

    <div class="mb-4">
        <button class="btn btn-outline-secondary mr-2" onclick="printRecoveryCodes()">
            <i class="feather icon-printer mr-2"></i>Print
        </button>
        <button class="btn btn-outline-secondary mr-2" onclick="copyToClipboard()">
            <i class="feather icon-copy mr-2"></i>Copy
        </button>
        <button class="btn btn-outline-secondary" onclick="downloadRecoveryCodes()">
            <i class="feather icon-download mr-2"></i>Download
        </button>
    </div>

    <form method="GET" action="{{ route('home') }}">
        <button type="submit" class="btn btn-primary btn-block">Next</button>
    </form>
@endsection

@section('scripts')
<script>
    function printRecoveryCodes() {
        var content = document.getElementById('recovery-codes').innerHTML;
        var myWindow = window.open('', '', 'width=600,height=400');
        myWindow.document.write('<html><head><title>Recovery Codes</title></head><body>' + content + '</body></html>');
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
        max-width: 500px;
    }
    .auth-icon {
        font-size: 3rem;
        color: #5e72e4;
    }
    #recovery-codes {
        background-color: #f8f9fa;
        border-radius: 0.25rem;
        padding: 1rem;
    }
    #recovery-codes li {
        margin-bottom: 0.5rem;
    }
    #recovery-codes li:last-child {
        margin-bottom: 0;
    }
</style>
@endsection