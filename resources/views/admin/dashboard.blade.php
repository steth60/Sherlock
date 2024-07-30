@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">

        <!-- Main content -->
        <main class="ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Admin Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                        <span data-feather="calendar"></span>
                        This week
                    </button>
                </div>
            </div>

            @include('admin.partials.alerts')

            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Total Users</h5>
                            <p class="card-text display-4">{{ $totalUsers }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Total Permissions</h5>
                            <p class="card-text display-4">{{ $totalPermissions }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Total Groups</h5>
                            <p class="card-text display-4">{{ $totalGroups }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Active Sessions</h5>
                            <p class="card-text display-4"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">User Statistics</h5>
                            <canvas id="userStatsChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Recent Activity</h5>
                            <ul class="list-group list-group-flush">

                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Invite User</h5>
                            <form action="{{ route('admin.invite') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="invitee_name" class="form-label">Invitee Name</label>
                                    <input type="text" class="form-control" id="invitee_name" name="invitee_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Invitee Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Send Invitation</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">System Status</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Maintainer Mode
                                    <span class="badge bg-{{ $isMaintainerMode ? 'success' : 'danger' }} rounded-pill">{{ $isMaintainerMode ? 'Enabled' : 'Disabled' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Server Load
                                    <span class="badge bg-primary rounded-pill">{{ $serverLoad }}%</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Database Size
                                    <span class="badge bg-info rounded-pill">0 MB</span>
                                </li>
                            </ul>
                            <div class="mt-3">
                                <form action="{{ route('admin.toggle-maintainer-mode') }}" method="POST">
                                    @csrf
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="maintainer_mode" name="maintainer_mode" {{ $isMaintainerMode ? 'checked' : '' }}>
                                        <label class="form-check-label" for="maintainer_mode">Toggle Maintainer Mode</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-2">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@include('admin.partials.modals')

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // User Statistics Chart
        const ctx = document.getElementById('userStatsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                datasets: [{
                    label: 'New Users',
                    data: [65, 59, 80, 81, 56, 55, 40],
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Modal Functionality
        const unverifiedEmailModal = document.getElementById('unverifiedEmailModal');
        unverifiedEmailModal.addEventListener('show.bs.modal', function () {
            fetch('{{ route("admin.unverified-email-users") }}')
                .then(response => response.json())
                .then(data => {
                    // Populate modal content
                });
        });

        const mfaNotEnabledModal = document.getElementById('mfaNotEnabledModal');
        mfaNotEnabledModal.addEventListener('show.bs.modal', function () {
            fetch('{{ route("admin.mfa-not-enabled-users") }}')
                .then(response => response.json())
                .then(data => {
                    // Populate modal content
                });
        });
    });
</script>
@endsection