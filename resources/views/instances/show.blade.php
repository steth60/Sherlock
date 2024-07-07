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
                <strong>Description:</strong> {{ $instance->description }}<br>
                <strong>Status:</strong> <span id="instance-status">{{ $instance->status }}</span>
            </p>
            <a href="{{ route('instances.edit', $instance) }}" class="btn btn-primary">Edit</a>
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
        <li class="nav-item">
            <a class="nav-link" id="update-tab" data-toggle="tab" href="#update" role="tab" aria-controls="update" aria-selected="false">Update</a>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="control" role="tabpanel" aria-labelledby="control-tab">
            <div class="mt-3">
                <button id="start-btn" class="btn btn-success">Start</button>
                <button id="stop-btn" class="btn btn-warning">Stop</button>
                <button id="restart-btn" class="btn btn-info">Restart</button>
                <button id="delete-btn" class="btn btn-danger">Delete</button>
            </div>
        </div>
        <div class="tab-pane fade" id="console" role="tabpanel" aria-labelledby="console-tab">
            <div class="mt-3">
                <pre id="console-output" class="bg-dark text-white p-3" style="height: 400px; overflow-y: scroll;">{{ $output }}</pre>
            </div>
        </div>
        <div class="tab-pane fade" id="scheduling" role="tabpanel" aria-labelledby="scheduling-tab">
            <div class="mt-3">
                <h5>Scheduling</h5>
                <!-- Assuming you have a button or an element where the instance ID can be added -->
<button id="add-schedule-btn" data-instance-id="{{ $instance->id }}" class="btn btn-primary">Add Schedule</button>

                <div id="schedules-container">
                    @if($instance->schedules->isNotEmpty())
                        @foreach ($instance->schedules as $schedule)
                            <div class="card mb-3">
                                <div class="card-header">
                                    {{ $schedule->description ?? $schedule->action }}
                                    <span class="float-right">
                                    <button type="button" class="btn btn-secondary edit-schedule-btn" data-id="{{ $schedule->id }}">Edit Schedule</button>
                                    <button class="btn btn-danger delete-schedule-btn" data-id="{{ $schedule->id }}">Delete</button>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <p><strong>Action:</strong> {{ ucfirst($schedule->action) }}</p>
                                    <p><strong>Months:</strong> {{ implode(', ', $schedule->months) }}</p>
                                    <p><strong>Days:</strong> {{ implode(', ', $schedule->days) }}</p>
                                    <p><strong>Hours:</strong> {{ implode(', ', $schedule->hours) }}</p>
                                    <p><strong>Minutes:</strong> {{ implode(', ', $schedule->minutes) }}</p>
                                    <p><strong>Enabled:</strong> {{ $schedule->enabled ? 'Yes' : 'No' }}</p>
                                    {{ $schedule->getNextRunTime() }}
                                    <button class="btn btn-sm btn-info trigger-now-btn" data-id="{{ $schedule->id }}">Trigger Now</button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p>No schedules found.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="env-variables" role="tabpanel" aria-labelledby="env-variables-tab">
            <div class="mt-3">
                <h5>Environment Variables</h5>
                <form id="env-form" action="{{ route('instances.update.env', $instance) }}" method="POST">
                    @csrf
                    <div id="env-variables-container">
                        <!-- Existing environment variables will be populated here by JavaScript -->
                    </div>
                    <button type="button" class="btn btn-secondary" id="add-variable-btn">Add Variable</button>
                    <button type="button" class="btn btn-secondary" id="add-comment-btn">Add Comment/Header</button>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#previewModal">Preview</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </form>
            </div>
        </div>
        <div class="tab-pane fade" id="update" role="tabpanel" aria-labelledby="update-tab">
            <div class="mt-3">
                <h5>Check for Updates</h5>
                <button id="check-updates-btn" class="btn btn-primary">Check for Updates</button>
                <div id="updates-result" class="mt-3" style="display: none;">
                    <pre id="updates-diff" class="bg-light p-3"></pre>
                    <button id="confirm-updates-btn" class="btn btn-success">Confirm Updates</button>
                </div>
                <div id="no-updates" class="mt-3" style="display: none;">
                    No updates available.
                </div>
            </div>
        </div>
    </div>

    <!-- Modals for confirmations -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="confirmModalBody">
                    Are you sure you want to perform this action?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmModalConfirm">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Preview Environment Variables</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <pre id="env-preview" class="bg-light p-3"></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



        <!-- Include the modal -->
        @include('schedules.schedule_modal')
        @endsection


        @section('scripts')
<script>
    // Define global variables
    const instanceId = {{ $instance->id }};
    const csrfToken = '{{ csrf_token() }}';
    const envContent = @json($envContent);
</script>
<script src="{{ asset('js/instance-management.js') }}"></script>
<script src="{{ asset('js/schedule-management.js') }}"></script>
@endsection


