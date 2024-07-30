@extends('layouts.app')

@section('title', 'Instance Dashboard')
@section('pageTitle', 'Instance Dashboard')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/theme/darcula.min.css">
<style>
    /* Consolidated Styles */
    #console-container, #console-output {
        font-family: 'Courier New', Courier, monospace;
        border-radius: 5px;
        background-color: #2d2d2d;
    }

    #console-container {
        height: 400px;
        overflow-y: scroll;
    }

    #console-output {
        white-space: pre-wrap;
        word-wrap: break-word;
        margin: 0;
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

    .CodeMirror {
        height: calc(100vh - 200px);
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
    }

    @media (max-width: 768px) {
        .CodeMirror {
            height: calc(100vh - 250px);
        }
    }

    @media (max-width: 767px) {
        #editorTabs .nav-link {
            max-width: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    }

</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-4">


            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h1 class="m-b-10">{{ $instance->name }}</h1>
                            <p class="text-muted">Instance Management</p>
                            <h5 class="card-title">Quick Actions</h5>
                            <div class="btn-group" role="group">
                                <button id="start-btn" class="btn btn-success" {{ $instance->status === 'running' ? 'disabled' : '' }}>
                                    <i class="feather icon-play"></i> Start
                                </button>
                                <button id="stop-btn" class="btn btn-warning" {{ $instance->status !== 'running' ? 'disabled' : '' }}>
                                    <i class="feather icon-square"></i> Stop
                                </button>
                                <button id="restart-btn" class="btn btn-info" {{ $instance->status !== 'running' ? 'disabled' : '' }}>
                                    <i class="feather icon-refresh-cw"></i> Restart
                                </button>
                            </div>
                            <a href="{{ route('instances.edit', $instance) }}" class="btn btn-primary">
                                <i class="feather icon-edit"></i> Edit Instance
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Instance Info</h5>
                            <p><strong>GitHub URL:</strong> {{ $instance->github_url }}</p>
                            <p><strong>Start Command:</strong> {{ $instance->start_command }}</p>
                            <p><strong>Description:</strong> {{ $instance->description }}</p>
                            <p><strong>Status:</strong> <span id="instance-status" class="badge bg-primary">{{ $instance->status }}</span></p>
                        </div>


                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="text-primary" id="cpu-usage-text">0%</h4>
                            <p>CPU Usage</p>
                            <div class="progress">
                                <div id="cpu-progress" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="text-primary" id="memory-usage-text">0%</h4>
                            <p>Memory Usage</p>
                            <div class="progress">
                                <div id="memory-progress" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="text-primary" id="uptime-text">0:00.00</h4>
                            <p>Uptime</p>
                        </div>
                    </div>
                </div>
            </div>


            
            <div class="row">
                <div class="col-md-3">

                    <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <li><a href="#console" class="nav-link text-left active" data-bs-toggle="pill">Console</a></li>
                        <li><a href="#detailed-usage" class="nav-link text-left" data-bs-toggle="pill">Detailed Usage</a></li>
                        <li><a href="#scheduling" class="nav-link text-left"data-bs-toggle="pill">Scheduling</a></li>
                        <li><a href="#activity-log" class="nav-link text-left" data-bs-toggle="pill">Activity Log</a></li>
                        <li><a href="#env-variables" class="nav-link text-left" data-bs-toggle="pill">Environment Variables</a></li>
                        <li><a href="#file-browser" class="nav-link text-left" data-bs-toggle="pill">File Browser</a></li>
                        <li><a href="#notes" class="nav-link text-left"data-bs-toggle="pill">Notes</a></li>
                        <li><a href="#update" class="nav-link text-left" data-bs-toggle="pill">Update</a></li>
                        </ul>
               
                </div>
                
                <div class="col-md-9">
                    <div class="tab-content bg-transparent p-0 shadow-none">
                        <div class="tab-pane fade show active" id="console">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Console Output</h5>
                                </div>
                                <div class="card-body">
                                    <div id="console-container" class="bg-dark p-3 rounded">
                                        <pre id="console-output" class="text-white m-0"></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @include('instances.partials.scheduling-tab')
                        
                        <div class="tab-pane fade" id="env-variables">
                            
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Environment Variables</h5>
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
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#previewModal">Preview</button>
                                            <button type="submit" class="btn btn-success">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="detailed-usage">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Detailed CPU and Memory Usage</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="cpuChart"></canvas>
                                    <canvas id="memoryChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="update">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Check for Updates</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <button id="check-updates-btn" class="btn btn-primary">Check for Updates</button>
                                        <button id="rollback-btn" class="btn btn-danger">Rollback to Last Backup</button>
                                    </div>
                                    <div id="updates-result" class="mt-3" style="display: none;">
                                        <pre id="updates-diff" class="bg-light p-3"></pre>
                                        <button id="confirm-updates-btn" class="btn btn-success mt-2">Confirm Updates</button>
                                    </div>
                                    <div id="no-updates" class="mt-3" style="display: none;">
                                        No updates available.
                                    </div>
                                </div>
                                </div>
                        </div>
                        
                        
                        <div class="tab-pane fade" id="activity-log">

                                    <h5 class="card-title mb-0">Activity Log</h5>

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
                        
                        <div class="tab-pane fade" id="notes">

                                    <h5 class="card-title mb-0">Notes</h5>
                                <div class="card-body">
                                    <form id="notes-form" class="mb-4">
                                        @csrf
                                        <div class="form-group">
                                            <label for="note-content">Add a note:</label>
                                            <textarea class="form-control" id="note-content" name="content" rows="3" maxlength="1000"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save Note</button>
                                    </form>
                        
                                    <div id="notes-container">
                                        <div id="saved-notes">
                                            @foreach($notes as $note)
                                                <div class="list-group-item flex-column align-items-start" data-note-id="{{ $note->id }}">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h5 class="mb-1">{{ $note->user->name }}</h5>
                                                        <small>{{ $note->created_at->diffForHumans() }}</small>
                                                    </div>
                                                    <p class="mb-1">{{ $note->content }}</p>
                                                    <small class="text-muted">Created on {{ $note->created_at->format('Y-m-d H:i:s') }}</small>
                                                    @if(Auth::id() === $note->user_id)
                                                        <button class="btn btn-sm btn-danger float-right delete-note mt-2" data-toggle="modal" data-target="#deleteModal" data-note-id="{{ $note->id }}">Delete</button>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <nav aria-label="Notes pagination" class="mt-4">
                                            <ul class="pagination pagination-flat justify-content-center">
                                                {{ $notes->links() }}
                                            </ul>
                                        </nav>
                                    </div>
                                </div>

                        </div>
                        
                        <div class="tab-pane fade" id="file-browser">

                                    <h5 class="card-title mb-0">File Browser</h5>

                                <div class="card-body">
                                    <div id="file-browser-content"></div>
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

    function handleAjaxError(xhr, status, error) {
        console.error("An error occurred: " + error);
        if (xhr.responseJSON && xhr.responseJSON.errors) {
            toastr.error(xhr.responseJSON.errors.content[0]);
        } else {
            toastr.error("An error occurred: " + error);
        }
    }

    function refreshConsole() {
        $.ajax({
            url: '/instances/' + instanceId + '/output',
            method: 'GET',
            success: function (data) {
                const consoleOutput = document.getElementById('console-output');
                consoleOutput.textContent = data.output;
                scrollToBottom();
            },
            error: handleAjaxError
        });
    }

    function scrollToBottom() {
        const consoleContainer = document.getElementById('console-container');
        consoleContainer.scrollTop = consoleContainer.scrollHeight;
    }

    document.addEventListener('DOMContentLoaded', function() {
        setInterval(refreshConsole, 5000); // Refresh console every 5 seconds
    });
</script>

<script src="{{ asset('./js/instance-management.js') }}"></script>
<script src="{{ asset('./js/schedule-management.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/theme/material-darker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/split.js/1.6.4/split.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/python/python.min.js"></script>
@endsection
