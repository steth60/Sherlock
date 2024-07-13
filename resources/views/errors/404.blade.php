<!-- resources/views/errors/404.blade.php -->
@extends('layouts.auth')

@section('title', 'Page Not Found')

@section('content')
<div class="container text-center">
    <h1>404</h1>
    <h2>Page Not Found</h2>
    <p>Sorry, the page you are looking for could not be found.</p>
    <a href="{{ url('/') }}" class="btn btn-primary">Go to Homepage</a>
</div>
@endsection
