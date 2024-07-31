@extends('layouts.app')

@section('content')   <h1>Update Manager</h1>
    
<h1>Update Logs</h1>
    
    @foreach ($logs as $log)
        <div>
            <strong>{{ $log->date }}</strong>: {{ $log->message }}
        </div>
    @endforeach

    <a href="/update">Back to Update Manager</a>
@endsection