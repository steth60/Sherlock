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
                <button id="add-schedule-btn" class="btn btn-primary mb-3">Add New Schedule</button>
                <div id="schedules-container">
                    @if($instance->schedules->isNotEmpty())
                        @foreach ($instance->schedules as $schedule)
                            <div class="card mb-3">
                                <div class="card-header">
                                    {{ $schedule->description ?? $schedule->action }}
                                    <span class="float-right">
                                        <button class="btn btn-sm btn-secondary edit-schedule-btn" data-id="{{ $schedule->id }}">Edit</button>
                                        <button class="btn btn-sm btn-danger delete-schedule-btn" data-id="{{ $schedule->id }}">Delete</button>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <p><strong>Action:</strong> {{ ucfirst($schedule->action) }}</p>
                                    <p><strong>Months:</strong> {{ implode(', ', $schedule->months) }}</p>
                                    <p><strong>Days:</strong> {{ implode(', ', $schedule->days) }}</p>
                                    <p><strong>Hours:</strong> {{ implode(', ', $schedule->hours) }}</p>
                                    <p><strong>Minutes:</strong> {{ implode(', ', $schedule->minutes) }}</p>
                                    <p><strong>Enabled:</strong> {{ $schedule->enabled ? 'Yes' : 'No' }}</p>
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

    <!-- Schedule Modal -->
    <div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog" aria-labelledby="scheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scheduleModalLabel">Add New Schedule</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="schedule-form">
                        @csrf
                        <div id="wizard-step-1">
                            <h5>Select Schedule Time</h5>
                            <div class="form-group">
                                <label>Months</label>
                                <div id="months">
                                    @for($i = 1; $i <= 12; $i++)
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="months[]" value="{{ $i }}"> {{ $i }}
                                        </label>
                                    @endfor
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Days</label>
                                <div id="days">
                                    @for($i = 1; $i <= 31; $i++)
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="days[]" value="{{ $i }}"> {{ $i }}
                                        </label>
                                    @endfor
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Hours</label>
                                <div id="hours">
                                    @for($i = 0; $i <= 23; $i++)
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="hours[]" value="{{ $i }}"> {{ $i }}
                                        </label>
                                    @endfor
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Minutes</label>
                                <div id="minutes">
                                    @for($i = 0; $i <= 59; $i++)
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="minutes[]" value="{{ $i }}"> {{ $i }}
                                        </label>
                                    @endfor
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" id="next-step">Next</button>
                        </div>
                        <div id="wizard-step-2" style="display:none;">
                            <h5>Confirm Schedule</h5>
                            <p id="schedule-summary"></p>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <input type="text" class="form-control" id="description" name="description" placeholder="Description (optional)">
                            </div>
                            <button type="button" class="btn btn-secondary" id="previous-step">Previous</button>
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Handle tab navigation
        $('#myTab a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });

        // Refresh console output
        function refreshConsole() {
            $.ajax({
                url: '/instances/{{ $instance->id }}/output',
                method: 'GET',
                success: function (data) {
                    document.getElementById('console-output').textContent = data.output;
                },
                error: function(xhr, status, error) {
                    console.error("An error occurred: " + error);
                    toastr.error("An error occurred while fetching console output: " + error);
                }
            });
        }
        setInterval(refreshConsole, 5000); // Refresh console every 5 seconds

        function updateInstanceStatus(status) {
            document.getElementById('instance-status').textContent = status;
        }

        function showNotification(message, type) {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            switch (type) {
                case 'success':
                    toastr.success(message);
                    break;
                case 'warning':
                    toastr.warning(message);
                    break;
                case 'error':
                    toastr.error(message);
                    break;
                default:
                    toastr.info(message);
            }
        }

        // AJAX actions with confirmation modals
        function ajaxAction(action, url, successMessage) {
            $('#confirmModal').modal('show');
            $('#confirmModalConfirm').off('click').on('click', function() {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        if (data.status === 'success') {
                            updateInstanceStatus(data.instance.status);
                            refreshConsole();
                            showNotification(successMessage, 'success');
                        } else {
                            showNotification(data.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("An error occurred: " + error);
                        showNotification("An error occurred: " + error, 'error');
                    }
                });
                $('#confirmModal').modal('hide');
            });
        }

        $('#start-btn').on('click', function() {
            $('#confirmModalLabel').text('Confirm Start');
            $('#confirmModalBody').text('Are you sure you want to start this instance?');
            ajaxAction('start', '/instances/{{ $instance->id }}/start', 'Instance started successfully.');
        });

        $('#stop-btn').on('click', function() {
            $('#confirmModalLabel').text('Confirm Stop');
            $('#confirmModalBody').text('Are you sure you want to stop this instance?');
            ajaxAction('stop', '/instances/{{ $instance->id }}/stop', 'Instance stopped successfully.');
        });

        $('#restart-btn').on('click', function() {
            $('#confirmModalLabel').text('Confirm Restart');
            $('#confirmModalBody').text('Are you sure you want to restart this instance?');
            ajaxAction('restart', '/instances/{{ $instance->id }}/restart', 'Instance restarted successfully.');
        });

        $('#delete-btn').on('click', function() {
            $('#confirmModalLabel').text('Confirm Delete');
            $('#confirmModalBody').text('Are you sure you want to delete this instance?');
            ajaxAction('delete', '/instances/{{ $instance->id }}/delete', 'Instance deleted successfully.');
        });

        $('#check-updates-btn').on('click', function() {
            $.ajax({
                url: '{{ route('instances.check.updates', $instance) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.status === 'up-to-date') {
                        $('#no-updates').show();
                        $('#updates-result').hide();
                    } else if (data.status === 'updates-available') {
                        $('#updates-diff').text(data.diff);
                        $('#updates-result').show();
                        $('#no-updates').hide();
                    } else {
                        showNotification(data.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("An error occurred: " + error);
                    showNotification("An error occurred: " + error, 'error');
                }
            });
        });

        $('#confirm-updates-btn').on('click', function() {
            $('#confirmModalLabel').text('Confirm Update');
            $('#confirmModalBody').text('Are you sure you want to apply these updates?');
            ajaxAction('confirm', '{{ route('instances.confirm.updates', $instance) }}', 'Updates pulled successfully.');
        });

        // Environment Variables Management
        const envContent = @json($envContent);
        const envVariablesContainer = document.getElementById('env-variables-container');
        const addVariableBtn = document.getElementById('add-variable-btn');
        const addCommentBtn = document.getElementById('add-comment-btn');
        const previewModal = document.getElementById('previewModal');
        const envPreview = document.getElementById('env-preview');

        function addEnvRow(type, key = '', value = '', comment = '') {
            const row = document.createElement('div');
            row.className = 'form-group row';

            if (type === 'variable') {
                row.innerHTML = `
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="env[][key]" placeholder="Variable" value="${key}">
                    </div>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="env[][value]" placeholder="Value" value="${value}">
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-danger remove-row">Remove</button>
                    </div>
                    <input type="hidden" name="env[][type]" value="variable">
                `;
            } else {
                row.innerHTML = `
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="env[][comment]" placeholder="Comment/Header" value="${comment}">
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-danger remove-row">Remove</button>
                    </div>
                    <input type="hidden" name="env[][type]" value="comment">
                `;
            }

            envVariablesContainer.appendChild(row);
        }

        function populateEnvFields() {
            const lines = envContent.split('\n');
            lines.forEach(line => {
                if (line.startsWith('#')) {
                    addEnvRow('comment', '', '', line.slice(1).trim());
                } else if (line.includes('=')) {
                    const [key, value] = line.split('=');
                    addEnvRow('variable', key.trim(), value.trim());
                }
            });
        }

        function generateEnvPreview() {
            const rows = document.querySelectorAll('#env-variables-container .form-group.row');
            let previewContent = '';
            rows.forEach(row => {
                const type = row.querySelector('input[name="env[][type]"]').value;
                if (type === 'variable') {
                    const key = row.querySelector('input[name="env[][key]"]').value;
                    const value = row.querySelector('input[name="env[][value]"]').value;
                    previewContent += `${key}=${value}\n`;
                } else {
                    const comment = row.querySelector('input[name="env[][comment]"]').value;
                    previewContent += `# ${comment}\n`;
                }
            });
            envPreview.textContent = previewContent;
        }

        addVariableBtn.addEventListener('click', () => addEnvRow('variable'));
        addCommentBtn.addEventListener('click', () => addEnvRow('comment'));

        envVariablesContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-row')) {
                e.target.closest('.form-group.row').remove();
            }
        });

        $('#previewModal').on('show.bs.modal', generateEnvPreview);

        populateEnvFields();

        // Scheduling modal and wizard
        $('#add-schedule-btn').click(function() {
            $('#schedule-form')[0].reset();
            $('#wizard-step-1').show();
            $('#wizard-step-2').hide();
            $('#scheduleModal').modal('show');
        });

        $('#next-step').click(function() {
            $('#wizard-step-1').hide();
            $('#wizard-step-2').show();
            updateScheduleSummary();
        });

        $('#previous-step').click(function() {
            $('#wizard-step-2').hide();
            $('#wizard-step-1').show();
        });

        function updateScheduleSummary() {
            let selectedMonths = $('#months input:checked').map(function() { return $(this).val(); }).get();
            let selectedDays = $('#days input:checked').map(function() { return $(this).val(); }).get();
            let selectedHours = $('#hours input:checked').map(function() { return $(this).val(); }).get();
            let selectedMinutes = $('#minutes input:checked').map(function() { return $(this).val(); }).get();

            let summary = 'Every ';
            if (selectedMonths.length) {
                summary += selectedMonths.join(', ') + ' ';
            }
            if (selectedDays.length) {
                summary += selectedDays.join(', ') + ' ';
            }
            if (selectedHours.length) {
                summary += 'at ' + selectedHours.join(':') + ' ';
            }
            if (selectedMinutes.length) {
                summary += ':' + selectedMinutes.join(', ');
            }

            $('#schedule-summary').text(summary);
        }

        $('#schedule-form').submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: '{{ route('schedules.store', $instance) }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    toastr.success('Schedule created successfully');
                    $('#scheduleModal').modal('hide');
                    location.reload(); // Reload the page to reflect changes
                },
                error: function(xhr, status, error) {
                    console.error("An error occurred: " + error);
                    toastr.error("An error occurred: " + error);
                }
            });
        });

        $('.trigger-now-btn').click(function() {
            const scheduleId = $(this).data('id');
            toastr.info('Starting scheduled task...');
            $.ajax({
                url: `/schedules/${scheduleId}/trigger-now`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success('Scheduled task completed successfully');
                        location.reload(); // Reload to update instance status
                    } else {
                        toastr.error('Failed to trigger: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('An error occurred: ' + error);
                    console.error("An error occurred: " + error);
                }
            });
        });

        // Handle Edit Schedule
        $('.edit-schedule-btn').click(function() {
            const scheduleId = $(this).data('id');
            $.ajax({
                url: `/schedules/${scheduleId}/edit`,
                method: 'GET',
                success: function(data) {
                    $('#scheduleModalLabel').text('Edit Schedule');
                    $('#wizard-step-1').show();
                    $('#wizard-step-2').hide();
                    $('#schedule-form').attr('action', `/schedules/${scheduleId}`);
                    $('#schedule-form').append('<input type="hidden" name="_method" value="PUT">');
                    // Populate form with existing data
                    data.schedule.months.forEach(month => {
                        $(`#months input[value="${month}"]`).prop('checked', true);
                    });
                    data.schedule.days.forEach(day => {
                        $(`#days input[value="${day}"]`).prop('checked', true);
                    });
                    data.schedule.hours.forEach(hour => {
                        $(`#hours input[value="${hour}"]`).prop('checked', true);
                    });
                    data.schedule.minutes.forEach(minute => {
                        $(`#minutes input[value="${minute}"]`).prop('checked', true);
                    });
                    $('#description').val(data.schedule.description);
                    $('#scheduleModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error("An error occurred: " + error);
                    toastr.error("An error occurred while fetching schedule data: " + error);
                }
            });
        });

        // Handle Delete Schedule
        $('.delete-schedule-btn').click(function() {
            const scheduleId = $(this).data('id');
            if (confirm('Are you sure you want to delete this schedule?')) {
                $.ajax({
                    url: `/schedules/${scheduleId}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success('Schedule deleted successfully');
                        location.reload(); // Reload the page to reflect changes
                    },
                    error: function(xhr, status, error) {
                        console.error("An error occurred: " + error);
                        toastr.error("An error occurred while deleting the schedule: " + error);
                    }
                });
            }
        });
    });
</script>
@endsection
