@extends('layouts.app')

@section('title', 'Holiday Management')

@section('styles')
<link href="{{ asset('assets/plugins/fullcalendar/core-4.3.1/main.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/plugins/fullcalendar/daygrid-4.3.0/main.min.css') }}" rel="stylesheet">
<style>
    .dashboard-card {
        height: 100%;
    }
    .fc-past-event {
        opacity: 0.7;
    }
    .fc-current-event {
        border: 2px solid #007bff;
    }
    .fc-future-event {
        /* Add any specific styling for future events if needed */
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Holiday Management</h1>

    <!-- Dashboard -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h5 class="card-title">Annual Leave</h5>
                    <p class="card-text">Remaining: <span id="annualLeaveRemaining">15</span> days</p>
                    <p class="card-text">Used: <span id="annualLeaveUsed">10</span> days</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h5 class="card-title">Sick Leave</h5>
                    <p class="card-text">Taken: <span id="sickLeaveTaken">3</span> days</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h5 class="card-title">Upcoming Leave</h5>
                    <p class="card-text">Next: <span id="nextLeave">July 15-22, 2024</span></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h5 class="card-title">Actions</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-add-event">Book Holiday</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div class="card">
        <div class="card-body">
            <div class="full-calendar">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="modal-add-event" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEventModalLabel">Book Holiday</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addEventForm">
                    <div class="mb-3">
                        <label for="eventTitle" class="form-label">Event Title</label>
                        <input type="text" class="form-control" id="eventTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="eventStart" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="eventStart" required>
                    </div>
                    <div class="mb-3">
                        <label for="eventEnd" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="eventEnd" required>
                    </div>
                    <div class="mb-3">
                        <label for="eventDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="eventDescription" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveEvent">Save Event</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('assets/plugins/fullcalendar/core-4.3.1/main.min.js') }}"></script>
<script src="{{ asset('assets/plugins/fullcalendar/daygrid-4.3.0/main.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: [ 'dayGrid' ],
        defaultView: 'dayGridMonth',
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,dayGridDay'
        },
        eventRender: function(info) {
            var ntoday = moment().format('YYYYMMDD');
            var eventStart = moment(info.event.start).format('YYYYMMDD');
            info.el.setAttribute("title", info.event.extendedProps.description);
            info.el.setAttribute("data-toggle", "tooltip");
            if (eventStart < ntoday){
                info.el.classList.add("fc-past-event");
            } else if (eventStart == ntoday){
                info.el.classList.add("fc-current-event");
            } else {
                info.el.classList.add("fc-future-event");
            }
        },
        events: [
            {
                title: 'All Day Event',
                description: 'description for All Day Event',
                start: '2024-07-15',
                end: '2024-07-22'
            }
            // more events here
        ]
    });
    calendar.render();

    // Handle saving new events
    document.getElementById('saveEvent').addEventListener('click', function() {
        var title = document.getElementById('eventTitle').value;
        var start = document.getElementById('eventStart').value;
        var end = document.getElementById('eventEnd').value;
        var description = document.getElementById('eventDescription').value;

        if (title && start && end) {
            calendar.addEvent({
                title: title,
                start: start,
                end: end,
                description: description,
                allDay: true
            });

            // Close the modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('modal-add-event'));
            modal.hide();

            // Clear the form
            document.getElementById('addEventForm').reset();
        } else {
            alert('Please fill in all required fields.');
        }
    });
});
</script>
@endsection