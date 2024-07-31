@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Update Manager</h1>

            <div class="card mb-3">
                <div class="card-body">
                    <p class="card-text">Current Version: <strong>{{ $currentVersion }}</strong></p>

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div id="update-section">
                        @if ($updateAvailable)
                            <p class="text-warning">A new version ({{ $latestVersion }}) is available!</p>
                            <form action="{{ route('admin.update.install') }}" method="POST">
                                @csrf
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="reseed" id="reseed">
                                    <label class="form-check-label" for="reseed">
                                        Reseed Database
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="clear_cache" id="clear_cache">
                                    <label class="form-check-label" for "clear_cache">
                                        Clear Caches
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">Install Update</button>
                            </form>
                        @else
                            <p class="text-success">No updates available.</p>
                        @endif
                    </div>

                    <button id="check-updates" class="btn btn-secondary mt-3">Check for Updates</button>
                </div>
            </div>

            <a href="{{ route('admin.update.logs') }}" class="btn btn-info">View Update Logs</a>
        </div>
    </div>
</div>

<div class="container mt-3">
    <div class="row">
        <div class="col-md-12">
            <div id="progress-container" class="progress" style="display:none;">
                <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div id="console-output" class="mt-3" style="display:none;">
                <pre id="console-text"></pre>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/7.0.3/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('#check-updates').addEventListener('click', function() {
            fetch("{{ route('admin.update.check') }}", {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.updateAvailable) {
                    document.getElementById('update-section').innerHTML = `
                        <p class="text-warning">A new version (${data.latestVersion}) is available!</p>
                        <form action="{{ route('admin.update.install') }}" method="POST">
                            @csrf
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="reseed" id="reseed">
                                <label class="form-check-label" for="reseed">
                                    Reseed Database
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="clear_cache" id="clear_cache">
                                <label class="form-check-label" for="clear_cache">
                                    Clear Caches
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Install Update</button>
                        </form>
                    `;
                } else {
                    document.getElementById('update-section').innerHTML = '<p class="text-success">No updates available.</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while checking for updates.');
            });
        });

        // Configure Laravel Echo
        Pusher.logToConsole = true;

        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env('PUSHER_APP_KEY') }}',
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            encrypted: true
        });

        // Listen for update progress events
        window.Echo.channel('update-progress')
            .listen('.update.progress', (e) => {
                let progressContainer = document.getElementById('progress-container');
                let progressBar = document.getElementById('progress-bar');
                let consoleOutput = document.getElementById('console-output');
                let consoleText = document.getElementById('console-text');

                if (!progressContainer.style.display || progressContainer.style.display === 'none') {
                    progressContainer.style.display = 'block';
                }

                if (!consoleOutput.style.display || consoleOutput.style.display === 'none') {
                    consoleOutput.style.display = 'block';
                }

                consoleText.innerHTML += e.message + '\n';
                consoleText.scrollTop = consoleText.scrollHeight;

                // Update the progress bar (dummy example, you might want to update it based on actual progress)
                let currentProgress = parseInt(progressBar.getAttribute('aria-valuenow'));
                let newProgress = Math.min(currentProgress + 10, 100);
                progressBar.style.width = newProgress + '%';
                progressBar.setAttribute('aria-valuenow', newProgress);
            });
    });
</script>
@endsection
