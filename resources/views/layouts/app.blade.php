<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sherlock')</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500|Poppins:400,500,600,700|Roboto:400,500" rel="stylesheet">
    <link href="https://cdn.materialdesignicons.com/4.4.95/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="{{ asset('assets/plugins/simplebar/simplebar.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/nprogress/nprogress.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/options/optionswitch.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/sleek.css') }}" rel="stylesheet">
    @yield('styles')
</head>
<body class="header-fixed sidebar-fixed sidebar-dark header-light" id="body">
    <div id="toaster"></div>

    <div class="wrapper">
        @include('layouts.partials.sidebar')

        <div class="page-wrapper">
            <header class="main-header" id="header">
                @include('layouts.partials.navbar')
            </header>

            <div class="content-wrapper">
                <div class="content">
                    <!-- Breadcrumbs -->
                    <div class="breadcrumb-wrapper">
                        @include('layouts.partials.breadcrumbs')
                    </div>

                    @yield('content')
                </div>
            </div>

            <footer class="footer mt-auto">
                <div class="copyright bg-white">
                    <p>Copyright &copy; <span id="copy-year"></span> Axess System LTD</p>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/nprogress/nprogress.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/sleek.js') }}"></script>
    <script src="{{ asset('assets/options/optionswitcher.js') }}"></script>
    @yield('scripts')
    @vite('resources/js/app.js')

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        NProgress.configure({ showSpinner: false });
        NProgress.start();

        $(window).on('load', function() {
            NProgress.done();
        });

        $(document).ajaxStart(function() {
            NProgress.start();
        });

        $(document).ajaxStop(function() {
            NProgress.done();
        });

        document.getElementById("copy-year").innerHTML = new Date().getFullYear();
    </script>
</body>
</html>