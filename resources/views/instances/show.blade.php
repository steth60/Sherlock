@extends('layouts.app')

@section('content')
    <h1>Instance Details</h1>

    <div class="card mb-3">
        <div class="card-header">
            Instance Information
        </div>
        <div class="card-body">
            <h5 class="card-title">{{ $instance->name }}</h5>
            <p class="card-text">
                <strong>GitHub URL:</strong> {{ $instance->github_url }}<br>
                <strong>Start Command:</strong> {{ $instance->start_command }}<br>
                <strong>Status:</strong> {{ $instance->status }}
            </p>
        </div>
    </div>

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="control-tab" data-toggle="tab" href="#control" role="tab" aria-controls="control" aria-selected="true">Control</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="console-tab" data-toggle="tab" href="#console" role="tab" aria-controls="console" aria-selected="false">Console</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="scheduling-tab" data-toggle="tab" href="#scheduling" role="tab" aria-controls="scheduling" aria-selected="false">Scheduling</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="env-variables-tab" data-toggle="tab" href="#env-variables" role="tab" aria-controls="env-variables" aria-selected="false">Environment Variables</a>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="control" role="tabpanel" aria-labelledby="control-tab">
            <div class="mt-3">
                <form action="{{ route('instances.start', $instance) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">Start</button>
                </form>
                <form action="{{ route('instances.stop', $instance) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning">Stop</button>
                </form>
                <form action="{{ route('instances.restart', $instance) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-info">Restart</button>
                </form>
                <form action="{{ route('instances.delete', $instance) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
        <div class="tab-pane fade" id="console" role="tabpanel" aria-labelledby="console-tab">
            <div class="mt-3">
                <pre id="console-output" class="bg-dark text-white p-3" style="height: 400px; overflow-y: scroll;">{{ $output }}</pre>
            </div>
        </div>
        <div class="tab-pane fade" id="scheduling" role="tabpanel" aria-labelledby="scheduling-tab">
            <div class="mt-3">
                <!-- Scheduling content will go here -->
            </div>
        </div>
        <div class="tab-pane fade" id="env-variables" role="tabpanel" aria-labelledby="env-variables-tab">
            <div class="mt-3">
                <!-- Environment variables content will go here -->
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#myTab a').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });

            function refreshConsole() {
                $.ajax({
                    url: '{{ route('instances.output', $instance) }}',
                    method: 'GET',
                    success: function (data) {
                        $('#console-output').text(data.output);
                    }
                });
            }

            setInterval(refreshConsole, 5000); // Refresh console every 5 seconds
        });
    </script>
@endsection
