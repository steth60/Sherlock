@extends('layouts.app')

@section('content')
    <h1>Running Instances</h1>
    <ul class="list-group mt-3">
        @foreach($runningInstances as $instance)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $instance->name }}</strong>
                    <div id="output-{{ $instance->id }}" class="output-console bg-dark text-white p-2 mt-2" style="height: 200px; overflow-y: scroll; white-space: pre;"></div>
                </div>
                <div>
                    <form action="{{ route('instances.stop', $instance) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning">Stop</button>
                    </form>
                    <form action="{{ route('instances.delete', $instance) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </div>
            </li>
        @endforeach
    </ul>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($runningInstances as $instance)
                setInterval(function() {
                    fetchOutput({{ $instance->id }});
                }, 5000); // Fetch output every 5 seconds
            @endforeach
        });

        function fetchOutput(instanceId) {
            fetch(`/instances/${instanceId}/output`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById(`output-${instanceId}`).textContent = data.output;
                });
        }
    </script>
@endsection
