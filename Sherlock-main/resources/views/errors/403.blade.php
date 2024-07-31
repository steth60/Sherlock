@extends('layouts.offline')

@section('title', 'Unauthorized')

@section('content')
    <h1 class="text-white text-uppercase display-1 mb-3">403</h1>
    <h2 class="text-white text-uppercase mb-3">Unauthorized</h2>
    <h5 class="text-white font-weight-normal mb-4">Sorry, you do not have permission to access this page.</h5>
    <a href="{{ url('/') }}" class="btn btn-primary mb-4">
        <i class="feather icon-home me-2"></i>Go to Homepage
    </a>
@endsection
