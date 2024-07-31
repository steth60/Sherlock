$(document).ready(function() {
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

    // Wizard navigation
    $('#next-step').click(function() {
        $('#wizard-step-1').hide();
        $('#wizard-step-2').show();
    });

    $('#previous-step').click(function() {
        $('#wizard-step-2').hide();
        $('#wizard-step-1').show();
    });

    // Update summary text on input change
    $('#months input, #days input, #hours input, #minutes input').change(function() {
        updateScheduleSummary();
    });

    // AJAX form submission
    $('#schedule-form').submit(function(e) {
        e.preventDefault();

        let scheduleId = $('#schedule-id').val();
        let instanceId = $('#add-schedule-btn').data('instance-id');
        let url = scheduleId ? '/schedules/' + scheduleId : '/instances/' + instanceId + '/schedules';
        let method = scheduleId ? 'PUT' : 'POST';
        let data = $(this).serialize();

        if (!scheduleId) {
            data += '&instance_id=' + instanceId;
        }

        $.ajax({
            url: url,
            method: 'POST', // Use POST for both creation and updating, Laravel handles the method override
            data: data + (scheduleId ? '&_method=PUT' : ''),
            success: function(response) {
                toastr.success('Schedule saved successfully. Next run time: ' + response.next_run_time);
                $('#scheduleModal').modal('hide');
                location.reload(); // Reload the page to reflect changes
            },
            error: function(xhr, status, error) {
                console.error("An error occurred: " + error);
                toastr.error("An error occurred: " + error);
            }
        });
    });

    // Open modal for creating a new schedule
    $('#add-schedule-btn').click(function() {
        resetScheduleForm();
        $('#wizard-step-1').show();
        $('#wizard-step-2').hide();
        $('#scheduleModalLabel').text('Add New Schedule');
        $('#schedule-id').val('');
        $('#scheduleModal').modal('show');
    });

    // Function to reset the schedule form
    function resetScheduleForm() {
        $('#schedule-form')[0].reset();
        $('#months input, #days input, #hours input, #minutes input').prop('checked', false);
        $('#schedule-summary').text('');
    }

    // Open modal for editing an existing schedule
    $('.edit-schedule-btn').click(function(e) {
        e.preventDefault(); // Prevent default link behavior
        const scheduleId = $(this).data('id');
        $.ajax({
            url: `/schedules/${scheduleId}/edit`,
            method: 'GET',
            success: function(data) {
                resetScheduleForm(); // Reset the form before populating it with new data
                $('#scheduleModalLabel').text('Edit Schedule');
                $('#wizard-step-1').show();
                $('#wizard-step-2').hide();
                $('#schedule-id').val(scheduleId);

                // Check the corresponding checkboxes
                if (data.schedule.months) {
                    data.schedule.months.forEach(month => {
                        $(`#months input[value="${month}"]`).prop('checked', true);
                    });
                }
                if (data.schedule.days) {
                    data.schedule.days.forEach(day => {
                        $(`#days input[value="${day}"]`).prop('checked', true);
                    });
                }
                if (data.schedule.hours) {
                    data.schedule.hours.forEach(hour => {
                        $(`#hours input[value="${hour}"]`).prop('checked', true);
                    });
                }
                if (data.schedule.minutes) {
                    data.schedule.minutes.forEach(minute => {
                        $(`#minutes input[value="${minute}"]`).prop('checked', true);
                    });
                }

                $('#action').val(data.schedule.action);
                $('#description').val(data.schedule.description);
                $('#enabled').prop('checked', data.schedule.enabled);

                updateScheduleSummary();
                $('#scheduleModal').modal('show');
                $('#next-run-time').text('Next run time: ' + data.next_run_time);
            },
            error: function(xhr, status, error) {
                console.error("An error occurred: " + error);
                toastr.error("An error occurred while fetching schedule data: " + error);
            }
        });
    });

    // Trigger Now button
    $('.trigger-now-btn').click(function() {
        const scheduleId = $(this).data('id');
        toastr.info('Starting scheduled task...');
        $.ajax({
            url: `/schedules/${scheduleId}/trigger-now`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
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

    // Handle Delete Schedule
    $('.delete-schedule-btn').click(function() {
        const scheduleId = $(this).data('id');
        if (confirm('Are you sure you want to delete this schedule?')) {
            $.ajax({
                url: `/schedules/${scheduleId}`,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
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

    // Initialize summary text
    updateScheduleSummary();
});
