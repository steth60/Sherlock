<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@yield('title', 'Sherlock')</title>
    <!-- Favicon icon -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sherlock')</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <!-- fontawesome icon -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome/css/fontawesome-all.min.css') }}">
    <!-- animation css -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/animate.min.css') }}">
    <!-- vendor css -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    @yield('styles')
</head>

<body>
    <!-- [ offline-ui ] start -->
    <div class="auth-wrapper offline">
        <div class="offline-wrapper">
            <img src="{{ asset('assets/images/error/sparcle-1.png') }}" alt="User-Image" class="img-fluid s-img-1">
            <img src="{{ asset('assets/images/error/sparcle-2.png') }}" alt="User-Image" class="img-fluid s-img-2">
            <div class="container off-main">
                <div class="row justify-content-center">
                    <div class="col-6">
                        <div class="text-center">
                            <div class="moon"></div>
                            <img src="{{ asset('assets/images/error/ship.svg') }}" alt="" class="img-fluid boat-img">
                        </div>
                    </div>
                </div>
                <div class="row m-0 justify-content-center off-content">
                    <div class="col-sm-12 p-0">
                        <div class="text-center">
                            @yield('content')
                        </div>
                    </div>
                    <div class="sark">
                        <img src="{{ asset('assets/images/error/sark.svg') }}" alt="" class="img-fluid img-sark">
                        <div class="bubble"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ offline-ui ] end -->

    @yield('scripts')
</body>
</html>