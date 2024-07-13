<!-- resources/views/errors/403.blade.php -->
@extends('layouts.auth')

@section('title', 'Unauthorized')

@section('content')
<div class="container text-center">
    <h1>403</h1>
    <h2>Unauthorized</h2>
    <p>Sorry, you do not have permission to access this page.</p>
    <a href="{{ url('/') }}" class="btn btn-primary">Go to Homepage</a>
</div>
@endsection
