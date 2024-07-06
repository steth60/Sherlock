@extends('layouts.app')

@section('content')
    <h1>Create New Instance</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('instances.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" name="name" required>
        </div>
        <div class="form-group">
            <label for="github_url">GitHub URL</label>
            <input type="url" class="form-control" name="github_url" required>
        </div>
        <div class="form-group">
            <label for="start_command">Start Command</label>
            <input type="text" class="form-control" name="start_command" required>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
@endsection
