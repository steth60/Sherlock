@extends('layouts.app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/theme/darcula.min.css">
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
    #file-editor-container {
        margin-top: 20px;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    #file-editor {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    #editorTabs {
        background-color: #f8f9fa;
        padding: 10px 10px 0 10px;
    }
    #editorContent {
        padding: 15px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .close-tab {
        margin-left: 5px;
        font-weight: bold;
        cursor: pointer;
    }
    .icon-folder, .icon-editable-file, .icon-file {
        width: 1em;
        height: 1em;
        margin-right: 5px;
        fill: currentColor;
        flex-shrink: 0;
    }
    .folder-link, .file-link {
        display: flex;
        align-items: center;
    }
    .file-browser-breadcrumb {
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        padding: 10px 15px;
        margin-bottom: 15px;
        border-radius: 4px;
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
    .instance-card {
        margin-bottom: 20px;
    }
    .instance-card .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .instance-card .card-header h2 {
        margin: 0;
    }
    .instance-card .card-header .btn-edit {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
    }
    .instance-card .card-body {
        padding: 20px;
    }
    .instance-card .card-title {
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
    }
    .instance-card .card-text {
        margin-bottom: 1.5rem;
    }
    .instance-control .btn-group {
        margin-bottom: 15px;
    }
    .instance-control .progress {
        height: 20px;
        margin-bottom: 10px;
    }
    .instance-control .progress .progress-bar {
        transition: width 0.6s ease;
    }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <h1 class="mb-4">Instance Management</h1>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header card-header-border-bottom">
                            <h2>Instance Information</h2>
                            <a href="{{ route('instances.edit', $instance) }}" class="btn btn-primary btn-edit">Edit</a>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $instance->name }}</h5>
                            <p class="card-text">
                                <strong>GitHub URL:</strong> {{ $instance->github_url }}<br>
                                <strong>Start Command:</strong> {{ $instance->start_command }}<br>
                                <strong>Description:</strong> {{ $instance->description }}<br>
                                <strong>Status:</strong> <span id="instance-status" class="badge badge-primary">{{ $instance->status }}</span>
                            </p>
                            <div class="btn-group d-flex mb-3" role="group">
                                <button id="start-btn" class="btn btn-success flex-fill" {{ $instance->status === 'running' ? 'disabled' : '' }}>Start</button>
                                <button id="stop-btn" class="btn btn-warning flex-fill" {{ $instance->status !== 'running' ? 'disabled' : '' }}>Stop</button>
                                <button id="restart-btn" class="btn btn-info flex-fill" {{ $instance->status !== 'running' ? 'disabled' : '' }}>Restart</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header card-header-border-bottom">
                            <h2>Instance Metrics</h2>
                        </div>
                        <div class="card-body">
                            <div class="content">
                                <h6 class="text-uppercase">
                                    CPU Usage <span class="float-right" id="cpu-usage-text">0%</span>
                                </h6>
                                <div class="progress progress-xs">
                                    <div class="progress-bar active" id="cpu-progress" style="width: 0%;" role="progressbar"></div>
                                </div>
                                <h6 class="text-uppercase">
                                    Memory Usage <span class="float-right" id="memory-usage-text">0%</span>
                                </h6>
                                <div class="progress progress-xs">
                                    <div class="progress-bar progress-bar-warning" id="memory-progress" style="width: 0%;" role="progressbar"></div>
                                </div>
                                <h6 class="text-uppercase">
                                    Uptime <span class="float-right" id="uptime-text">0:00.00</span>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 col-lg-4 col-xl-4">
                    <div class="card widget-block p-4 rounded bg-white border">
                        <div class="card-block">
                            <h4 class="text-primary my-2" id="cpu-usage-text">0%</h4>
                            <p class="pb-3">CPU Usage</p>
                            <div class="progress my-2" style="height: 5px;">
                                <div id="cpu-progress" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4 col-xl-4">
                    <div class="card widget-block p-4 rounded bg-white border">
                        <div class="card-block">
                            <h4 class="text-primary my-2" id="memory-usage-text">0%</h4>
                            <p class="pb-3">Memory Usage</p>
                            <div class="progress my-2" style="height: 5px;">
                                <div id="memory-progress" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4 col-xl-4">
                    <div class="card widget-block p-4 rounded bg-white border">
                        <div class="card-block">
                            <h4 class="text-primary my-2" id="uptime-text">0:00.00</h4>
                            <p class="pb-3">Uptime</p>
                            <div class="progress my-2" style="height: 5px; visibility: hidden;">
                                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            

            
            
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header card-header-border-bottom">
                            Instance Management
                        </div>
                        <div class="list-group list-group-flush" id="instance-tabs">
                            <a href="#console" class="list-group-item list-group-item-action active" data-toggle="list">Console</a>
                            <a href="#detailed-usage" class="list-group-item list-group-item-action" data-toggle="list">Detailed Usage</a>
                            <a href="#scheduling" class="list-group-item list-group-item-action" data-toggle="list">Scheduling</a>
                            <a href="#activity-log" class="list-group-item list-group-item-action" data-toggle="list">Activity Log</a>
                            <a href="#env-variables" class="list-group-item list-group-item-action" data-toggle="list">Environment Variables</a>
                            <a href="#file-browser" class="list-group-item list-group-item-action" data-toggle="list">File Browser</a>
                            <a href="#notes" class="list-group-item list-group-item-action" data-toggle="list">Notes</a>
                            <a href="#update" class="list-group-item list-group-item-action" data-toggle="list">Update</a>
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
                        
                        @include('instances.partials.scheduling-tab')
                        
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
                        <div class="tab-pane fade" id="detailed-usage">
                            <div class="card card-default">
                                <div class="card-header card-header-border-bottom">
                                    <h2>Detailed CPU and Memory Usage</h2>
                                </div>
                                <div class="card-body">
                                    <canvas id="cpuChart"></canvas>
                                    <canvas id="memoryChart"></canvas>
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
                                    <form id="notes-form" class="mb-4">
                                        @csrf
                                        <div class="form-group">
                                            <label for="note-content">Add a note:</label>
                                            <textarea class="form-control" id="note-content" name="content" rows="3" maxlength="1000"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save Note</button>
                                    </form>
                        
                                    <div id="notes-container">
                                        <div class="list-group" id="saved-notes">
                                            @foreach($notes as $note)
                                            <div class="list-group-item flex-column align-items-start" data-note-id="{{ $note->id }}">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h5 class="mb-1">{{ $note->user->name }}</h5>
                                                    <small>{{ $note->created_at->diffForHumans() }}</small>
                                                </div>
                                                <p class="mb-1">{{ $note->content }}</p>
                                                <small class="text-muted">Created on {{ $note->created_at->format('Y-m-d H:i:s') }}</small>
                                                @if(Auth::id() === $note->user_id)
                                                    <button class="btn btn-sm btn-danger float-right delete-note mt-2">Delete</button>
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
                        </div>
                        
                        <div class="tab-pane fade" id="file-browser">
                            <div class="card card-default">
                                <div class="card-header card-header-border-bottom">
                                    <h2>File Browser</h2>
                                </div>
                                <div class="card-body">
                                    <div id="file-browser-content"></div>
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

</script>
<script src="{{ asset('./js/instance-management.js') }}"></script>
<script src="{{ asset('./js/schedule-management.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/theme/material-darker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/split.js/1.6.4/split.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/python/python.min.js"></script>
</body>

@endsection