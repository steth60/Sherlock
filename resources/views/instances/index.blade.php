@extends('layouts.app')

@section('content')
    <h1>Instances</h1>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
sdsdsd
    <a href="{{ route('instances.create') }}" class="btn btn-primary">Create New Instance</a>
    <ul class="list-group mt-3">
        @foreach($instances as $instance)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <a href="{{ route('instances.show', $instance) }}">{{ $instance->name }}</a> - {{ $instance->status }}
                <div>
                    <form action="{{ route('instances.start', $instance) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">Start</button>
                    </form>
                    <form action="{{ route('instances.stop', $instance) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning">Stop</button>
                    </form>
                    <form action="{{ route('instances.restart', $instance) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-info">Restart</button>
                    </form>
                    <form action="{{ route('instances.delete', $instance) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </div>
            </li>
        @endforeach
    </ul>
@section('scripts')

@endsection
