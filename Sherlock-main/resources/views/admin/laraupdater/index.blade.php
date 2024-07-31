@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4 text-white">Update Manager</h1>

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
                            <form action="{{ route('admin.update.install') }}" method="GET">
                                <button type="submit" class="btn btn-primary">Install Update</button>
                            </form>
                        @else
                            <p class="text-success">No updates available.</p>
                        @endif
                    </div>

                    <button id="check-updates" class="btn btn-secondary mt-3">Check for Updates</button>
                </div>
            </div>


        </div>
    </div>


<script>
    $(document).ready(function() {
        $('#check-updates').on('click', function() {
            $.ajax({
                url: '{{ route('admin.update.check') }}',
                method: 'GET',
                success: function(response) {
                    if (response.updateAvailable) {
                        $('#update-section').html(`
                            <p class="text-warning">A new version (${response.latestVersion}) is available!</p>
                            <form action="{{ route('admin.update.install') }}" method="GET">
                                <button type="submit" class="btn btn-primary">Install Update</button>
                            </form>
                        `);
                    } else {
                        $('#update-section').html('<p class="text-success">No updates available.</p>');
                    }
                },
                error: function() {
                    alert('Error checking for updates.');
                }
            });
        });
    });
</script>
@endsection
