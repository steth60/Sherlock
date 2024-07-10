@extends('layouts.app')
@section('styles')
<style>
    #console-container {
        font-family: 'Courier New', Courier, monospace;
        border-radius: 5px;
    }
    #console-output {
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    #console-output::-webkit-scrollbar {
        width: 10px;
    }
    #console-output::-webkit-scrollbar-track {
        background: #343a40;
    }
    #console-output::-webkit-scrollbar-thumb {
        background: #6c757d;
    }
    #console-output::-webkit-scrollbar-thumb:hover {
        background: #5a6268;
    }
</style>
@endsection
@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <h1 class="mb-4">Instance Details</h1>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header card-header-border-bottom">
                            <h2>Instance Information</h2>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $instance->name }}</h5>
                            <p class="card-text">
                                <strong>GitHub URL:</strong> {{ $instance->github_url }}<br>
                                <strong>Start Command:</strong> {{ $instance->start_command }}<br>
                                <strong>Description:</strong> {{ $instance->description }}<br>
                                <strong>Status:</strong> <span id="instance-status" class="badge badge-primary">{{ $instance->status }}</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header card-header-border-bottom">
                            <h2>Instance Control</h2>
                        </div>
                        <div class="card-body">
                            <div class="btn-group d-flex" role="group">
                                <button id="start-btn" class="btn btn-success flex-fill">Start</button>
                                <button id="stop-btn" class="btn btn-warning flex-fill">Stop</button>
                                <button id="restart-btn" class="btn btn-info flex-fill">Restart</button>
                                <button id="delete-btn" class="btn btn-danger flex-fill">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="card sticky-top">
                        <div class="card-header card-header-border-bottom">
                            Instance Management
                        </div>
                        <div class="list-group list-group-flush" id="instance-tabs">
                            <a href="#console" class="list-group-item list-group-item-action active" data-toggle="list">Console</a>
                            <a href="#scheduling" class="list-group-item list-group-item-action" data-toggle="list">Scheduling</a>
                            <a href="#env-variables" class="list-group-item list-group-item-action" data-toggle="list">Environment Variables</a>
                            <a href="#update" class="list-group-item list-group-item-action" data-toggle="list">Update</a>
                            <a href="#activity-log" class="list-group-item list-group-item-action" data-toggle="list">Activity Log</a>
                            <a href="#notes" class="list-group-item list-group-item-action" data-toggle="list">Notes</a>
                            <a href="#file-browser" class="list-group-item list-group-item-action" data-toggle="list">File Browser</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-9">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="console">
                            <div class="card card-default">
                                <div class="card-header card-header-border-bottom">
                                    <h2>Console Output</h2>
                                </div>
                                <div class="card-body">
                                    <div id="console-container" class="bg-dark p-2" style="height: 400px; overflow: hidden;">
                                        <pre id="console-output" class="text-white m-0 pl-2" style="height: 100%; overflow-y: scroll;"></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="scheduling">
                            <div class="card card-default">
                                <div class="card-header card-header-border-bottom">
                                    <h2>Scheduling</h2>
                                </div>
                                <div class="card-body">
                                    <button id="add-schedule-btn" data-instance-id="{{ $instance->id }}" class="btn btn-primary mb-3">Add Schedule</button>
                                    <div id="schedules-container">
                                        @if($instance->schedules->isNotEmpty())
                                            @foreach ($instance->schedules as $schedule)
                                                <div class="card mb-3">
                                                    <div class="card-header">
                                                        {{ $schedule->description ?? $schedule->action }}
                                                        <div class="float-right">
                                                            <button type="button" class="btn btn-sm btn-secondary edit-schedule-btn" data-id="{{ $schedule->id }}">Edit</button>
                                                            <button class="btn btn-sm btn-danger delete-schedule-btn" data-id="{{ $schedule->id }}">Delete</button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <p><strong>Action:</strong> {{ ucfirst($schedule->action) }}</p>
                                                        <p><strong>Schedule:</strong> {{ $schedule->getFormattedSchedule() }}</p>
                                                        <p><strong>Enabled:</strong> {{ $schedule->enabled ? 'Yes' : 'No' }}</p>
                                                        <p><strong>Next Run:</strong> {{ $schedule->getNextRunTime() }}</p>
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
                        </div>
                        
                        <div class="tab-pane fade" id="env-variables">
                            <div class="card card-default">
                                <div class="card-header card-header-border-bottom">
                                    <h2>Environment Variables</h2>
                                </div>
                                <div class="card-body">
                                    <form id="env-form" action="{{ route('instances.update.env', $instance) }}" method="POST">
                                        @csrf
                                        <div id="env-variables-container">
                                            <!-- Existing environment variables will be populated here by JavaScript -->
                                        </div>
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-secondary" id="add-variable-btn">Add Variable</button>
                                            <button type="button" class="btn btn-secondary" id="add-comment-btn">Add Comment/Header</button>
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#previewModal">Preview</button>
                                            <button type="submit" class="btn btn-success">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="update">
                            <div class="card card-default">
                                <div class="card-header card-header-border-bottom">
                                    <h2>Check for Updates</h2>
                                </div>
                                <div class="card-body">
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

                        <div class="tab-pane fade" id="activity-log">
                            <div class="card card-default">
                                <div class="card-header card-header-border-bottom">
                                    <h2>Activity Log</h2>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <strong>2023-07-10 15:30:00</strong> - Instance started
                                        </li>
                                        <li class="list-group-item">
                                            <strong>2023-07-10 14:45:00</strong> - Environment variable APP_DEBUG updated
                                        </li>
                                        <li class="list-group-item">
                                            <strong>2023-07-10 12:00:00</strong> - Scheduled task "Daily Backup" executed
                                        </li>
                                        <li class="list-group-item">
                                            <strong>2023-07-09 23:00:00</strong> - Instance stopped
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="notes">
                            <div class="card card-default">
                                <div class="card-header card-header-border-bottom">
                                    <h2>Notes</h2>
                                </div>
                                <div class="card-body">
                                    <form id="notes-form">
                                        <div class="form-group">
                                            <label for="note-content">Add a note:</label>
                                            <textarea class="form-control" id="note-content" rows="3"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save Note</button>
                                    </form>
                                    <hr>
                                    <h5>Saved Notes:</h5>
                                    <ul class="list-group" id="saved-notes">
                                        <li class="list-group-item">
                                            <strong>2023-07-10</strong>: Updated PHP version to 8.1
                                        </li>
                                        <li class="list-group-item">
                                            <strong>2023-07-08</strong>: Added new API endpoint for user authentication
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="file-browser">
                            <div class="card card-default">
                                <div class="card-header card-header-border-bottom">
                                    <h2>File Browser</h2>
                                </div>
                                <div class="card-body">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                                            <li class="breadcrumb-item"><a href="#">public</a></li>
                                            <li class="breadcrumb-item active" aria-current="page">css</li>
                                        </ol>
                                    </nav>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Size</th>
                                                <th>Last Modified</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><i class="fa fa-folder"></i> images</td>
                                                <td>-</td>
                                                <td>2023-07-10 10:00:00</td>
                                                <td>
                                                    <button class="btn btn-sm btn-info">Open</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><i class="fa fa-file"></i> style.css</td>
                                                <td>15 KB</td>
                                                <td>2023-07-09 14:30:00</td>
                                                <td>
                                                    <button class="btn btn-sm btn-info">Edit</button>
                                                    <button class="btn btn-sm btn-danger">Delete</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><i class="fa fa-file"></i> custom.css</td>
                                                <td>8 KB</td>
                                                <td>2023-07-08 09:15:00</td>
                                                <td>
                                                    <button class="btn btn-sm btn-info">Edit</button>
                                                    <button class="btn btn-sm btn-danger">Delete</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modals -->
            @include('instances.modals.confirm_modal')
            @include('instances.modals.preview_modal')
            @include('schedules.schedule_modal')
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const instanceId = {{ $instance->id }};
    const csrfToken = '{{ csrf_token() }}';
    const envContent = @json($envContent);

    $(document).ready(function() {
        function setActiveTab(tabId) {
            $('#instance-tabs a').removeClass('active');
            $('#instance-tabs a[href="' + tabId + '"]').addClass('active');
            $('.tab-pane').removeClass('show active');
            $(tabId).addClass('show active');
            localStorage.setItem('lastInstanceTab', tabId);
        }

        $('#instance-tabs a').on('click', function (e) {
            e.preventDefault();
            setActiveTab($(this).attr('href'));
        });

        var lastTab = localStorage.getItem('lastInstanceTab');
        if (lastTab) {
            setActiveTab(lastTab);
        }
    });
</script>
<script src="{{ asset('./js/instance-management.js') }}"></script>
<script src="{{ asset('./js/schedule-management.js') }}"></script>
@endsection