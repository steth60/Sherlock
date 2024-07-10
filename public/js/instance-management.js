$(document).ready(function() {
    // Handle tab navigation
    $('#myTab a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    // Refresh console output
    function refreshConsole() {
        $.ajax({
            url: '/instances/' + instanceId + '/output',
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
                    _token: csrfToken
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
        ajaxAction('start', '/instances/' + instanceId + '/start', 'Instance started successfully.');
    });

    $('#stop-btn').on('click', function() {
        $('#confirmModalLabel').text('Confirm Stop');
        $('#confirmModalBody').text('Are you sure you want to stop this instance?');
        ajaxAction('stop', '/instances/' + instanceId + '/stop', 'Instance stopped successfully.');
    });

    $('#restart-btn').on('click', function() {
        $('#confirmModalLabel').text('Confirm Restart');
        $('#confirmModalBody').text('Are you sure you want to restart this instance?');
        ajaxAction('restart', '/instances/' + instanceId + '/restart', 'Instance restarted successfully.');
    });

    $('#delete-btn').on('click', function() {
        $('#confirmModalLabel').text('Confirm Delete');
        $('#confirmModalBody').text('Are you sure you want to delete this instance?');
        ajaxAction('delete', '/instances/' + instanceId + '/delete', 'Instance deleted successfully.');
    });

    $('#check-updates-btn').on('click', function() {
        $.ajax({
            url: '/instances/' + instanceId + '/check-updates',
            method: 'POST',
            data: {
                _token: csrfToken
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
        ajaxAction('confirm', '/instances/' + instanceId + '/confirm-updates', 'Updates pulled successfully.');
    });

    // Environment Variables Management
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
    const consoleOutput = document.getElementById('console-output');
    
    function appendToConsole(text) {
        consoleOutput.innerHTML += text + '\n';
        consoleOutput.scrollTop = consoleOutput.scrollHeight;
    }

    // Simulating console output (replace this with your actual console output logic)
    function simulateConsoleOutput() {
        const outputLines = [
            " "
        ];

        let lineIndex = 0;
        const outputInterval = setInterval(() => {
            if (lineIndex < outputLines.length) {
                appendToConsole(outputLines[lineIndex]);
                lineIndex++;
            } else {
                clearInterval(outputInterval);
            }
        }, 1000);
    }

    // Call this function when the page loads or when you want to refresh the console output
    simulateConsoleOutput();
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

    
});