@extends('layouts.error')

@section('title', 'Page Not Found')

@section('content')
    <h1 class="text-white text-uppercase display-1 mb-3">404</h1>
    <h2 class="text-white text-uppercase mb-3">Page Not Found</h2>
    <h5 class="text-white font-weight-normal mb-4">Sorry, the page you are looking for could not be found.</h5>
    <a href="{{ url('/') }}" class="btn btn-primary mb-4">
        <i class="feather icon-home me-2"></i>Go to Homepage
    </a>
@endsection

@section('styles')

@endsection