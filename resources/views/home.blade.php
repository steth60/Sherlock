@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="container">
    <h1 class="mb-4">Welcome, {{ Auth::user()->name }}</h1>

    <div class="row">
        <!-- IT Tools Section -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>IT Management Tools</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="mdi mdi-server mr-2"></i> Virtual Machines
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="mdi mdi-docker mr-2"></i> Docker Containers
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="mdi mdi-language-python mr-2"></i> Python Environments
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Departmental Tools Section -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Departmental Tools</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="mdi mdi-ticket mr-2"></i> Service Desk Tickets
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="mdi mdi-beach mr-2"></i> Holiday Booking
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="mdi mdi-file-document mr-2"></i> Document Management
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Upcoming Holidays Section -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Upcoming Holidays</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>July 25, 2024:</strong> Lorem ipsum dolor sit amet
                        </li>
                        <li class="mb-2">
                            <strong>August 5, 2024:</strong> Consectetur adipiscing elit
                        </li>
                        <li class="mb-2">
                            <strong>September 1, 2024:</strong> Sed do eiusmod tempor incididunt
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Pending Approvals Section (visible only to managers) -->

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Pending Approvals</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Holiday Request:</strong> Lorem ipsum (John Doe)
                            <a href="#" class="btn btn-sm btn-outline-primary float-right">Review</a>
                        </li>
                        <li class="mb-2">
                            <strong>Expense Claim:</strong> Dolor sit amet (Jane Smith)
                            <a href="#" class="btn btn-sm btn-outline-primary float-right">Review</a>
                        </li>
                        <li class="mb-2">
                            <strong>Document Approval:</strong> Consectetur adipiscing (Mike Johnson)
                            <a href="#" class="btn btn-sm btn-outline-primary float-right">Review</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
  
</script>
@endsection