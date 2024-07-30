@extends('layouts.app')
@section('title', 'Instance Dashboard')
@section('pageTitle', 'AInstance Dashboard')
@section('content')
<div class="container">
   
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Instances</h5>
                    <p class="card-text display-4">{{ $totalInstances }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Running Instances</h5>
                    <p class="card-text display-4">{{ $runningInstances }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Stopped Instances</h5>
                    <p class="card-text display-4">{{ $stoppedInstances }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <h2>Recent Instances</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentInstances as $instance)
                <tr>
                    <td>{{ $instance->name }}</td>
                    <td>{{ ucfirst($instance->status) }}</td>
                    <td>{{ $instance->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('instances.show', $instance) }}" class="btn btn-sm btn-primary">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <a href="{{ route('instances.create') }}" class="btn btn-success">Create New Instance</a>
    </div>
</div>
@endsection