<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', 'Sleek Dashboard - Free Bootstrap 4 Admin Dashboard Template and UI Kit')">
    <title>@yield('title', 'Sherlock')</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <!-- GOOGLE FONTS -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500|Poppins:400,500,600,700|Roboto:400,500" rel="stylesheet">
    <link href="https://cdn.materialdesignicons.com/4.4.95/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/sleek.css') }}" rel="stylesheet">

    <!-- SLEEK CSS -->
    <link id="sleek-css" rel="stylesheet" href="{{ asset('assets/css/sleek.css') }}">

    <!-- FAVICON -->
    <link href="{{ asset('assets/img/favicon.png') }}" rel="shortcut icon">

    @yield('styles')

    <script src="{{ asset('assets/plugins/nprogress/nprogress.js') }}"></script>
</head>

<body id="body">
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="row justify-content-center w-100">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                <div class="card">
                    <div class="card-header bg-primary">
                        <div class="app-brand">
                            <span class="brand-name">Axess Dashboard</span>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Javascript -->
    <script src="{{ asset('assets/plugins/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/sleek.js') }}"></script>
    <link href="{{ asset('assets/options/optionswitch.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/options/optionswitcher.js') }}"></script>
    @vite('resources/js/app.js')
    @yield('scripts')
</body>




</html>