@extends('layouts.app')

@section('title', 'Holiday Management - Admin')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Holiday Management - Admin Dashboard</h1>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pending Requests</h5>
                    <p class="card-text">5 requests awaiting approval</p>
                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pendingRequestsModal">View Requests</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Team Calendar</h5>
                    <p class="card-text">View team's holiday schedule</p>
                    <a href="#" class="btn btn-primary" id="viewTeamCalendar">View Calendar</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Leave Allowance</h5>
                    <p class="card-text">Manage employee allowances</p>
                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#leaveAllowanceModal">Manage Allowances</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Reports</h5>
                    <p class="card-text">Generate leave reports</p>
                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reportsModal">Generate Reports</a>
                </div>
            </div>
        </div>
    </div>

    <div id="adminCalendar"></div>
</div>

<!-- Pending Requests Modal -->
<div class="modal fade" id="pendingRequestsModal" tabindex="-1" aria-labelledby="pendingRequestsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pendingRequestsModalLabel">Pending Leave Requests</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>John Doe</td>
                            <td>Annual Leave</td>
                            <td>2024-07-15</td>
                            <td>2024-07-22</td>
                            <td>
                                <button class="btn btn-sm btn-success">Approve</button>
                                <button class="btn btn-sm btn-danger">Reject</button>
                            </td>
                        </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Leave Allowance Modal -->
<div class="modal fade" id="leaveAllowanceModal" tabindex="-1" aria-labelledby="leaveAllowanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leaveAllowanceModalLabel">Manage Leave Allowances</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Annual Leave</th>
                            <th>Sick Leave</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>John Doe</td>
                            <td><input type="number" class="form-control" value="25"></td>
                            <td><input type="number" class="form-control" value="10"></td>
                            <td><button class="btn btn-sm btn-primary">Update</button></td>
                        </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Reports Modal -->
<div class="modal fade" id="reportsModal" tabindex="-1" aria-labelledby="reportsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportsModalLabel">Generate Reports</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="reportType" class="form-label">Report Type</label>
                        <select class="form-select" id="reportType">
                            <option value="leaveBalance">Leave Balance</option>
                            <option value="leaveUsage">Leave Usage</option>
                            <option value="sickLeave">Sick Leave Patterns</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="reportPeriod" class="form-label">Period</label>
                        <select class="form-select" id="reportPeriod">
                            <option value="lastMonth">Last Month</option>
                            <option value="lastQuarter">Last Quarter</option>
                            <option value="yearToDate">Year to Date</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Add JavaScript for handling modal interactions and calendar view
</script>
@endsection