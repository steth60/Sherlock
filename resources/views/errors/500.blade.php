<!-- resources/views/errors/500.blade.php -->
@extends('layouts.auth')

@section('title', 'Server Error')

@section('content')
<div class="container text-center">
    <h1>500</h1>
    <h2>Server Error</h2>
    <p>Sorry, something went wrong on our end. Please try again later.</p>
    <a href="{{ url('/') }}" class="btn btn-primary">Go to Homepage</a>
</div>
@endsection
