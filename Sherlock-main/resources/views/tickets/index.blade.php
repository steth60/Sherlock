@extends('layouts.app')

@section('content')
    
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>


    <style>
        .ghost-row {
            background-color: #f0f0f0;
            text-align: center;
            padding: 10px;
            font-style: italic;
        }
        .filter-sidebar {
            position: fixed;
            top: 0;
            right: 0;
            width: 300px;
            height: 100%;
            background-color: #f8f9fa;
            border-left: 1px solid #dee2e6;
            z-index: 1040;
            display: none;
        }
        .filter-sidebar.show {
            display: block;
        }
        .filter-sidebar-header {
            background-color: #343a40;
            color: white;
            padding: 15px;
        }
        .filter-sidebar-body {
            padding: 15px;
            overflow-y: auto;
            height: calc(100% - 56px);
        }
        .filter-toggle-btn {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1050;
        }
    </style>

    <div class="container">

            <button class="btn btn-primary" onclick="toggleFilterSidebar()">Filters</button>

        <div class="row">
            <div class="col-md-12">
                <h1>Tickets List</h1>
                <p>Query: {{ $query }}</p>
                <p>Order Type: {{ $orderType }}</p>
                <p>Page: {{ $page }}</p>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Subject</th>
                            <th>Type</th>
                            <th>Contact</th>
                            <th>State</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Created Date</th>
                            <th>Last Update</th>
                            <th>Assigned to</th>
                            <th>Company</th>
                            <th>Category</th>
                            <th>Sub-Category</th>
                        </tr>
                    </thead>
                    <tbody id="ticket-list">
                        @foreach ($tickets['tickets'] as $ticket)
                        <tr>
                            <td>{{ $ticket['id'] }}</td>
                            <td>{{ $ticket['subject'] }}</td>
                            <td>{{ $ticket['type'] }}</td>
                            <td>{{ $ticket['requester'] }}</td>
                            <td>{{ $ticket['status'] }}</td>
                            <td>{{ $ticket['status'] }}</td>
                            <td>{{ $ticket['priority'] }}</td>
                            <td>{{ $ticket['created_at'] }}</td>
                            <td>{{ $ticket['updated_at'] }}</td>
                            <td>{{ $ticket['group'] }} / {{ $ticket['responder'] }}</td>
                            <td>{{ $ticket['department'] }}</td>
                            <td>{{ $ticket['category'] }}</td>
                            <td>{{ $ticket['sub_category'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="loading" class="text-center" style="display: none;">
                    <p>Loading more tickets...</p>
                </div>
                <div id="ghost-row" class="ghost-row" style="display: none;">
                    Loading more tickets...
                </div>
            </div>
        </div>
    </div>

    <div class="filter-sidebar" id="filter-sidebar">
        <div class="filter-sidebar-header">
            <h2>Filters</h2>
            <button type="button" class="close" aria-label="Close" onclick="toggleFilterSidebar()">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="filter-sidebar-body">
            <form id="filter-form" method="POST" action="{{ route('tickets.saveFilter') }}">
                @csrf
                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select id="priority" name="filter[priority][]" class="form-control select2" multiple>
                        <option value="1" {{ in_array(1, $filter['priority'] ?? []) ? 'selected' : '' }}>Low</option>
                        <option value="2" {{ in_array(2, $filter['priority'] ?? []) ? 'selected' : '' }}>Medium</option>
                        <option value="3" {{ in_array(3, $filter['priority'] ?? []) ? 'selected' : '' }}>High</option>
                        <option value="4" {{ in_array(4, $filter['priority'] ?? []) ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="filter[status][]" class="form-control select2" multiple>
                        <option value="2" {{ in_array(2, $filter['status'] ?? []) ? 'selected' : '' }}>Open</option>
                        <option value="3" {{ in_array(3, $filter['status'] ?? []) ? 'selected' : '' }}>Pending</option>
                        <option value="4" {{ in_array(4, $filter['status'] ?? []) ? 'selected' : '' }}>Resolved</option>
                        <option value="5" {{ in_array(5, $filter['status'] ?? []) ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="department_id">Department</label>
                    <select id="department_id" name="filter[department_id][]" class="form-control select2" multiple>
                        @foreach($departments as $department)
                            @if(is_array($department))
                                <option value="{{ $department['id'] ?? '' }}" 
                                    {{ in_array($department['id'] ?? '', $filter['department_id'] ?? []) ? 'selected' : '' }}>
                                    {{ $department['name'] ?? 'Unknown Department' }}
                                </option>
                            @elseif(is_object($department))
                                <option value="{{ $department->id ?? '' }}" 
                                    {{ in_array($department->id ?? '', $filter['department_id'] ?? []) ? 'selected' : '' }}>
                                    {{ $department->name ?? 'Unknown Department' }}
                                </option>
                            @else
                                <option value="">Unknown Department</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="group_id">Group</label>
                    <select id="group_id" name="filter[group_id][]" class="form-control select2" multiple>
                        @foreach($groups as $group)
                            @if(is_array($group))
                                <option value="{{ $group['id'] ?? '' }}" 
                                    {{ in_array($group['id'] ?? '', $filter['group_id'] ?? []) ? 'selected' : '' }}>
                                    {{ $group['name'] ?? 'Unknown Group' }}
                                </option>
                            @elseif(is_object($group))
                                <option value="{{ $group->id ?? '' }}" 
                                    {{ in_array($group->id ?? '', $filter['group_id'] ?? []) ? 'selected' : '' }}>
                                    {{ $group->name ?? 'Unknown Group' }}
                                </option>
                            @else
                                <option value="">Unknown Group</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="agent_id">Agent</label>
                    <select id="agent_id" name="filter[agent_id][]" class="form-control select2" multiple>
                        @foreach($agents as $agent)
                            @if(is_array($agent))
                                <option value="{{ $agent['id'] ?? '' }}" 
                                    {{ in_array($agent['id'] ?? '', $filter['agent_id'] ?? []) ? 'selected' : '' }}>
                                    {{ ($agent['first_name'] ?? '') . ' ' . ($agent['last_name'] ?? '') }}
                                </option>
                            @elseif(is_object($agent))
                                <option value="{{ $agent->id ?? '' }}" 
                                    {{ in_array($agent->id ?? '', $filter['agent_id'] ?? []) ? 'selected' : '' }}>
                                    {{ ($agent->first_name ?? '') . ' ' . ($agent->last_name ?? '') }}
                                </option>
                            @else
                                <option value="">Unknown Agent</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="time_frame">Time Frame</label>
                    <select id="time_frame" name="filter[time_frame]" class="form-control">
                        <option value="">--Select Time Frame--</option>
                        <option value="today" {{ isset($filter['time_frame']) && $filter['time_frame'] == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ isset($filter['time_frame']) && $filter['time_frame'] == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="this_week" {{ isset($filter['time_frame']) && $filter['time_frame'] == 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="last_week" {{ isset($filter['time_frame']) && $filter['time_frame'] == 'last_week' ? 'selected' : '' }}>Last Week</option>
                        <option value="this_month" {{ isset($filter['time_frame']) && $filter['time_frame'] == 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_month" {{ isset($filter['time_frame']) && $filter['time_frame'] == 'last_month' ? 'selected' : '' }}>Last Month</option>
                        <option value="last_3_months" {{ isset($filter['time_frame']) && $filter['time_frame'] == 'last_3_months' ? 'selected' : '' }}>Last 3 Months</option>
                        <option value="last_6_months" {{ isset($filter['time_frame']) && $filter['time_frame'] == 'last_6_months' ? 'selected' : '' }}>Last 6 Months</option>
                        <option value="custom" {{ isset($filter['time_frame']) && $filter['time_frame'] == 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                </div>

                <div id="custom_time_frame" style="display: none;">
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" id="start_date" name="filter[start_date]" class="form-control" value="{{ $filter['start_date'] ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" id="end_date" name="filter[end_date]" class="form-control" value="{{ $filter['end_date'] ?? '' }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Apply Filters</button>
            </form>
        </div>
    </div>

    
    @endsection

    @section('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            var page = {{ $page }};
            var orderType = '{{ $orderType }}';
            var query = '{{ $query }}';
            var loading = false;

            function handleTimeFrameChange() {
                var timeFrame = $('#time_frame').val();
                var customTimeFrame = $('#custom_time_frame');
                customTimeFrame.toggle(timeFrame === 'custom');
            }

            handleTimeFrameChange();

            $('#time_frame').on('change', handleTimeFrameChange);

            function loadMoreTickets() {
                if (loading) return;

                loading = true;
                $('#loading').show();
                $('#ghost-row').show();

                $.ajax({
                    url: "{{ route('tickets.more') }}",
                    type: 'GET',
                    data: {
                        order_type: orderType,
                        page: page + 1,
                        query: query
                    },
                    success: function(data) {
                        $('#ticket-list').append(data);
                        page++;
                        loading = false;
                        $('#loading').hide();
                        $('#ghost-row').hide();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading more tickets:", error);
                        loading = false;
                        $('#loading').hide();
                        $('#ghost-row').hide();
                    }
                });
            }

            $(window).scroll(function() {
                if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                    loadMoreTickets();
                }
            });

            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error applying filters:", error);
                    }
                });
            });
        });

        function toggleFilterSidebar() {
            $('#filter-sidebar').toggleClass('show');
        }
    </script>
@endsection
