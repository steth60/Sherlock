<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" >
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Dashboard')</title>
    
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500|Poppins:400,500,600,700|Roboto:400,500" rel="stylesheet">
    <link href="https://cdn.materialdesignicons.com/4.4.95/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="{{ asset('assets/plugins/simplebar/simplebar.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/nprogress/nprogress.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/options/optionswitch.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/jvectormap/jquery-jvectormap-2.0.3.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/sleek.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/img/favicon.png') }}" rel="shortcut icon">
  
    @yield('styles')
    <script src="{{ asset('assets/plugins/nprogress/nprogress.js') }}"></script>
</head>
<body class="header-fixed sidebar-fixed sidebar-dark header-light" id="body">˙

    <div id="toaster"></div>

    <div class="wrapper">
        <!-- LEFT SIDEBAR -->
        @include('layouts.partials.sidebar')

        <!-- PAGE WRAPPER -->
        <div class="page-wrapper">
          <!-- Header -->
          <header class="main-header" id="header">
            @include('layouts.partials.navbar')
          </header>
          
          <!-- CONTENT WRAPPER -->
          <div class="content-wrapper">
            <div class="content">
              @yield('content')
            </div>
          </div>

          <!-- Footer -->
          <footer class="footer mt-auto">
            <div class="copyright bg-white">
              <p>
                Copyright &copy; <span id="copy-year"></span> Axess System LTD
              </p>
            </div>
            <script>
              var d = new Date();
              var year = d.getFullYear();
              document.getElementById("copy-year").innerHTML = year;
            </script>
          </footer>
        </div> <!-- End Page Wrapper -->
    </div> <!-- End Wrapper -->

    <!-- Javascript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/sleek.js') }}"></script>
    <script src="{{ asset('assets/options/optionswitcher.js') }}"></script>
    @yield('scripts')
</body>
</html>
