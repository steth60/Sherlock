$(document).ready(function() {
    /**
     * Table of Contents:
     * 1. Initialization and Setup
     * 2. UI Helpers
     * 3. AJAX Helpers
     * 4. Instance Management
     * 5. Console Output
     * 6. Metrics and Charts
     * 7. Environment Variables Management
     * 8. Notes Management
     * 9. File Browser and Editor
     */

    // 1. Initialization and Setup
    
    // Handle tab navigation
    $('#myTab a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    // Initialize toastr options
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

    // 2. UI Helpers

  
    function updateInstanceStatus(status) {
        document.getElementById('instance-status').textContent = status;
    }

    function showNotification(message, type) {
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

    function setLoadingState(button, isLoading) {
        if (isLoading) {
            button.data('original-text', button.html());
            button.html('<span class="spinner-border spinner-border-sm"></span> Loading...');
            button.prop('disabled', true);
        } else {
            button.html(button.data('original-text'));
            button.prop('disabled', false);
        }
    }

    // 3. AJAX Helpers

    function handleAjaxError(xhr, status, error) {
        console.error("An error occurred: " + error);
        if (xhr.responseJSON && xhr.responseJSON.errors) {
            toastr.error(xhr.responseJSON.errors.content[0]);
        } else {
            toastr.error("An error occurred: " + error);
        }
    }

    function ajaxRequest(url, method, data, successCallback, errorCallback) {
        $.ajax({
            url: url,
            method: method,
            data: data,
            success: successCallback,
            error: errorCallback || handleAjaxError
        });
    }

    function ajaxAction(action, url, successMessage, finalCallback) {
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
                        fetchUsage();
                        showNotification(successMessage, 'success');
                    } else {
                        showNotification(data.message, 'error');
                    }
                    if (finalCallback) finalCallback();
                },
                error: function(xhr, status, error) {
                    handleAjaxError(xhr, status, error);
                    if (finalCallback) finalCallback();
                }
            });
            $('#confirmModal').modal('hide');
        });
    }

    // 3. AJAX Helpers

    function handleAjaxError(xhr, status, error) {
        console.error("An error occurred: " + error);
        if (xhr.responseJSON && xhr.responseJSON.errors) {
            toastr.error(xhr.responseJSON.errors.content[0]);
        } else {
            toastr.error("An error occurred: " + error);
        }
    }

    function ajaxRequest(url, method, data, successCallback, errorCallback) {
        $.ajax({
            url: url,
            method: method,
            data: data,
            success: successCallback,
            error: errorCallback || handleAjaxError
        });
    }

    function ajaxAction(action, url, successMessage, finalCallback) {
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
                        fetchUsage();
                        showNotification(successMessage, 'success');
                    } else {
                        showNotification(data.message, 'error');
                    }
                    if (finalCallback) finalCallback();
                },
                error: function(xhr, status, error) {
                    handleAjaxError(xhr, status, error);
                    if (finalCallback) finalCallback();
                }
            });
            $('#confirmModal').modal('hide');
        });
    }

    // 4. Instance Management

    $('#start-btn').on('click', function() {
        $('#confirmModalLabel').text('Confirm Start');
        $('#confirmModalBody').text('Are you sure you want to start this instance?');
        setLoadingState($(this), true);
        ajaxAction('start', '/instances/' + instanceId + '/start', 'Instance started successfully.', function() {
            setLoadingState($('#start-btn'), false);
        });
    });

    $('#stop-btn').on('click', function() {
        $('#confirmModalLabel').text('Confirm Stop');
        $('#confirmModalBody').text('Are you sure you want to stop this instance?');
        setLoadingState($(this), true);
        ajaxAction('stop', '/instances/' + instanceId + '/stop', 'Instance stopped successfully.', function() {
            setLoadingState($('#stop-btn'), false);
        });
    });

    $('#restart-btn').on('click', function() {
        $('#confirmModalLabel').text('Confirm Restart');
        $('#confirmModalBody').text('Are you sure you want to restart this instance?');
        setLoadingState($(this), true);
        ajaxAction('restart', '/instances/' + instanceId + '/restart', 'Instance restarted successfully.', function() {
            setLoadingState($('#restart-btn'), false);
        });
    });

    $('#delete-btn').on('click', function() {
        $('#confirmModalLabel').text('Confirm Delete');
        $('#confirmModalBody').text('Are you sure you want to delete this instance?');
        setLoadingState($(this), true);
        ajaxAction('delete', '/instances/' + instanceId + '/delete', 'Instance deleted successfully.', function() {
            setLoadingState($('#delete-btn'), false);
        });
    });

    $('#check-updates-btn').on('click', function() {
        ajaxRequest(
            '/instances/' + instanceId + '/check-updates',
            'POST',
            { _token: csrfToken },
            function(data) {
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
            }
        );
    });

    $('#confirm-updates-btn').on('click', function() {
        $('#confirmModalLabel').text('Confirm Update');
        $('#confirmModalBody').text('Are you sure you want to apply these updates?');
        ajaxAction('confirm', '/instances/' + instanceId + '/confirm-updates', 'Updates pulled successfully.');
    });

    // 5. Console Output

    function refreshConsole() {
        $.ajax({
            url: '/instances/' + instanceId + '/output',
            method: 'GET',
            success: function (data) {
                document.getElementById('console-output').textContent = data.output;
            },
            error: handleAjaxError
        });
    }
    setInterval(refreshConsole, 5000); // Refresh console every 5 seconds

    // 6. Metrics and Charts

    let initialUptime = null;
    let startTime = null;
    let uptimeInterval = null;

    function parseUptime(uptime) {
        const parts = uptime.split(':');
        if (parts.length !== 2) {
            return null;
        }
        const secondsParts = parts[1].split('.');
        if (secondsParts.length !== 2) {
            return null;
        }
        return {
            hours: parseInt(parts[0], 10),
            minutes: parseInt(secondsParts[0], 10),
            seconds: parseInt(secondsParts[1], 10)
        };
    }

    function formatUptime({ hours, minutes, seconds }) {
        return `${hours}:${minutes.toString().padStart(2, '0')}.${seconds.toString().padStart(2, '0')}`;
    }

    function startUptimeCounter(uptime) {
        const uptimeParts = parseUptime(uptime);
        if (!uptimeParts) {
            console.error("Invalid uptime format:", uptime);
            return;
        }
        const now = new Date();

        startTime = new Date(
            now.getFullYear(),
            now.getMonth(),
            now.getDate(),
            now.getHours() - uptimeParts.hours,
            now.getMinutes() - uptimeParts.minutes,
            now.getSeconds() - uptimeParts.seconds
        );

        if (uptimeInterval) {
            clearInterval(uptimeInterval);
        }

    function updateUptimeCounter() {
        const now = new Date();
        const diff = now - startTime;
        const uptimeDate = new Date(diff);

        const uptimeFormatted = formatUptime({
            hours: uptimeDate.getUTCHours(),
            minutes: uptimeDate.getUTCMinutes(),
            seconds: uptimeDate.getUTCSeconds()
        });

            $('#uptime-text').text(uptimeFormatted);
        }

            uptimeInterval = setInterval(updateUptimeCounter, 1000);
            updateUptimeCounter(); // Initial update
        }

    function updateProgressBar(element, value) {
        element.style.width = value + '%';
        element.classList.remove('bg-success', 'bg-warning', 'bg-danger');
        if (value < 40) {
            element.classList.add('bg-success');
        } else if (value < 80) {
            element.classList.add('bg-warning');
        } else {
            element.classList.add('bg-danger');
        }
    }

    function fetchUsage() {
        console.log("Fetching usage data...");
        $.ajax({
            url: '/instances/' + instanceId + '/metrics',
            method: 'GET',
            success: function(data) {
                console.log("Fetched data:", data);
    
                const cpuUsage = data.cpu.length > 0 ? data.cpu[data.cpu.length - 1] : 0;
                const memoryUsage = data.memory.length > 0 ? data.memory[data.memory.length - 1] : 0;
                const uptime = data.uptime;
    
                $('#cpu-usage-text').text(cpuUsage + '%');
                updateProgressBar(document.getElementById('cpu-progress'), cpuUsage);
    
                $('#memory-usage-text').text(memoryUsage + '%');
                updateProgressBar(document.getElementById('memory-progress'), memoryUsage);
    
                if (uptime !== initialUptime && uptime !== '0:00.00') {
                    initialUptime = uptime;
                    startUptimeCounter(uptime);
                }
    
                if (data.cpu.length > 0 || data.memory.length > 0) {
                    updateCharts(data.cpu, data.memory);
                }
            },
            error: function(xhr, status, error) {
                console.error("An error occurred: " + error);
            }
        });
    }
    

    function updateCharts(cpuData, memoryData) {
        if (!cpuChart || !memoryChart) {
            console.error('Charts are not initialized');
            return;
        }
    
        const now = new Date().toLocaleTimeString();
    
    // Update CPU chart
    //cpuChart.data.labels.push(now);
    cpuChart.data.datasets[0].data.push(cpuData.length > 0 ? cpuData[cpuData.length - 1] : 0);
    if (cpuChart.data.labels.length > 20) {
        cpuChart.data.labels.shift();
        cpuChart.data.datasets[0].data.shift();
    }
    cpuChart.update();

    // Update Memory chart
    memoryChart.data.labels.push(now);
    memoryChart.data.datasets[0].data.push(memoryData.length > 0 ? memoryData[memoryData.length - 1] : 0);
    if (memoryChart.data.labels.length > 20) {
        memoryChart.data.labels.shift();
        memoryChart.data.datasets[0].data.shift();
    }
    memoryChart.update();

    }
    

    $(document).ready(function() {
        initializeCharts();
        fetchUsage(); // Initial fetch
        setInterval(fetchUsage, 5000); // Fetch every 5 seconds
    });

    let currentStatus = '{{ $instance->status }}';

    function updateButtons(status) {
        if (status === 'running') {
            $('#start-btn').prop('disabled', true);
            $('#stop-btn').prop('disabled', false);
            $('#restart-btn').prop('disabled', false);
        } else {
            $('#start-btn').prop('disabled', false);
            $('#stop-btn').prop('disabled', true);
            $('#restart-btn').prop('disabled', true);
        }
    }

    function checkInstanceStatus() {
        if (currentStatus === 'stopped') {
            return;
        }

        $.ajax({
            url: '/instances/' + instanceId + '/status',
            method: 'GET',
            success: function(data) {
                if (data.status !== currentStatus) {
                    currentStatus = data.status;
                    $('#instance-status').text(currentStatus);
                    updateButtons(currentStatus);

                    if (currentStatus === 'stopped') {
                        showNotification('The instance has stopped.', 'warning');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error("An error occurred while checking the instance status: " + error);
            }
        });
    }

    setInterval(checkInstanceStatus, 5000);
    $(document).ready(function() {
        updateButtons(currentStatus);  // Initialize button states based on current status
    });

    let cpuChart, memoryChart;

    function initializeCharts() {
        const ctxCpu = document.getElementById('cpuChart').getContext('2d');
        cpuChart = new Chart(ctxCpu, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'CPU Usage',
                    data: [],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    fill: false
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        const ctxMemory = document.getElementById('memoryChart').getContext('2d');
        memoryChart = new Chart(ctxMemory, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Memory Usage',
                    data: [],
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1,
                    fill: false
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }

    $(document).ready(function() {
        initializeCharts();
        fetchUsage(); // Initial fetch
        setInterval(fetchUsage, 5000); // Fetch every 5 seconds
    });





    // 7. Environment Variables Management

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
                    <input type="text" class="form-control" name="env[][key]" placeholder="Variable" value="${key}" required>
                </div>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="env[][value]" placeholder="Value" value="${value}" required>
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-danger remove-row">Remove</button>
                </div>
                <input type="hidden" name="env[][type]" value="variable">
            `;
        } else {
            row.innerHTML = `
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="env[][comment]" placeholder="Comment/Header" value="${comment}" required>
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
    
    // 8. Notes Management
    
    $('#notes-form').on('submit', function(e) {
        e.preventDefault();
        var content = $('#note-content').val();
        
        $.ajax({
            url: '/instances/' + instanceId + '/notes',
            method: 'POST',
            data: {
                content: content,
                _token: csrfToken
            },
            success: function(data) {
                var newNote = `
                    <div class="list-group-item flex-column align-items-start" data-note-id="${data.id}">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">${data.user_name}</h5>
                            <small>Just now</small>
                        </div>
                        <p class="mb-1">${$('<div>').text(data.content).html()}</p>
                        <small class="text-muted">Created on ${data.created_at}</small>
                        <button class="btn btn-sm btn-danger float-right delete-note mt-2">Delete</button>
                    </div>`;
                $('#saved-notes').prepend(newNote);
                $('#note-content').val('');
            },
            error: handleAjaxError
        });
    });
    
    // Handle note deletion
    $('#saved-notes').on('click', '.delete-note', function() {
        var noteItem = $(this).closest('.list-group-item');
        var noteId = noteItem.data('note-id');
    
        $.ajax({
            url: '/notes/' + noteId,
            method: 'DELETE',
            data: {
                _token: csrfToken
            },
            success: function() {
                noteItem.remove();
            },
            error: handleAjaxError
        });
    });
    
    // Handle pagination
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        fetchNotes(page);
    });
    
    function fetchNotes(page) {
        $.ajax({
            url: '/instances/' + instanceId + '/notes?page=' + page,
            success: function(data) {
                $('#notes-container').html(data);
            }
        });
    }
    
    // 9. File Browser and Editor
    
    var openFiles = {};
    
    // Load file browser
    function loadFileBrowser(path = '', sort = 'name', order = 'asc') {
        $.ajax({
            url: `/instances/${instanceId}/files`,
            data: { path: path, sort: sort, order: order },
            success: function(data) {
                $('#file-browser-content').html(data);
                updateOpenFilesList();
            },
            error: handleAjaxError
        });
    }
    
    // Handle breadcrumb navigation
    $(document).on('click', '.breadcrumb-item a, .folder-link', function(e) {
        e.preventDefault();
        const path = $(this).data('path');
        loadFileBrowser(path);
    });
    
    // Handle sorting
    $(document).on('click', '.sort-link', function(e) {
        e.preventDefault();
        const sort = $(this).data('sort');
        const order = $(this).hasClass('asc') ? 'desc' : 'asc';
        loadFileBrowser('', sort, order);
    });
    
    // Handle file viewing
    $(document).on('click', '.file-link', function(e) {
        e.preventDefault();
        const filePath = $(this).data('path');
        openFile(filePath);
    });
    
    function openFile(filePath) {
        if (openFiles[filePath]) {
            showFileEditor(filePath);
            return;
        }
    
        $.ajax({
            url: `/instances/${instanceId}/files/view`,
            data: { file: filePath },
            success: function(data) {
                const tabId = `file-${filePath.replace(/[^a-zA-Z0-9]/g, '-')}`;
                const tabTitle = filePath.split('/').pop();
    
                openFiles[filePath] = {
                    content: data.content,
                    tabId: tabId
                };
    
                // Create new tab
                $('#editorTabs').append(`
                    <li class="nav-item">
                        <a class="nav-link" id="${tabId}-tab" data-toggle="tab" href="#${tabId}" role="tab">
                            ${tabTitle} <span class="close-tab mdi mdi-close"></span>
                        </a>
                    </li>
                `);
                $('#editorContent').append(`
                    <div class="tab-pane fade" id="${tabId}" role="tabpanel">
                        <textarea id="editor-${tabId}" class="code-editor">${data.content}</textarea>
                        <div class="mt-3">
                            <button class="btn btn-primary save-file" data-file="${filePath}">Save Changes</button> 
                            <button class="btn btn-secondary close-file" data-file="${filePath}">Close</button>
                        </div>
                    </div>
                `);
    
                const editor = CodeMirror.fromTextArea(document.getElementById(`editor-${tabId}`), {
                    lineNumbers: true,
                    mode: 'javascript', // You can set the mode based on file extension
                    theme: 'darcula',
                    matchBrackets: true,
                    autoCloseBrackets: true
                });
    
                openFiles[filePath].editor = editor;
    
                updateOpenFilesList();
                showFileEditor(filePath);
            },
            error: handleAjaxError
        });
    }
    
    function showFileEditor(filePath) {
        const fileInfo = openFiles[filePath];
        $(`#${fileInfo.tabId}-tab`).tab('show');
        $('#file-browser-container').hide();
        $('#file-editor-container').show();
        fileInfo.editor.refresh();
    }
    
    function updateOpenFilesList() {
        const $openFilesList = $('#open-files ul');
        $openFilesList.empty();
        $.each(openFiles, (filePath, fileInfo) => {
            const fileName = filePath.split('/').pop();
            $openFilesList.append(`<li class="list-group-item"><a href="#" class="open-file" data-path="${filePath}"><span class="mdi mdi-file-document"></span> ${fileName}</a></li>`);
        });
    }
    
    // Handle file saving
    $(document).on('click', '.save-file', function() {
        const filePath = $(this).data('file');
        const content = openFiles[filePath].editor.getValue();
        $.ajax({
            url: `/instances/${instanceId}/files/update`,
            method: 'POST',
            data: { 
                file: filePath, 
                content: content,
                _token: csrfToken
            },
            success: function() {
                toastr.success('File saved successfully');
                openFiles[filePath].content = content;
            },
            error: handleAjaxError
        });
    });
    
    // Handle closing files
    $(document).on('click', '.close-file, .close-tab', function(e) {
        e.preventDefault();
        e.stopPropagation();
        let filePath = $(this).data('file');
        if (!filePath) {
            filePath = $(this).closest('.nav-item').find('a').attr('href').substring(1).replace('file-', '').replace(/-/g, '/');
        }
        closeFile(filePath);
    });
    
    function closeFile(filePath) {
        const fileInfo = openFiles[filePath];
        $(`#${fileInfo.tabId}-tab`).remove();
        $(`#${fileInfo.tabId}`).remove();
        delete openFiles[filePath];
        updateOpenFilesList();
    
        if (Object.keys(openFiles).length === 0) {
            $('#file-browser-container').show();
            $('#file-editor-container').hide();
        } else {
            $('#editorTabs a:first').tab('show');
        }
    }
    
    // Handle back to file browser button
    $(document).on('click', '#back-to-browser', function() {
        $('#file-browser-container').show();
        $('#file-editor-container').hide();
    });
    
    // Handle clicking on open files list
    $(document).on('click', '.open-file', function(e) {
        e.preventDefault();
        const filePath = $(this).data('path');
        showFileEditor(filePath);
    });
    
    // Load initial file browser content
    loadFileBrowser();
    });