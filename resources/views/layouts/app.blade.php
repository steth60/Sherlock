<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sleek Dashboard - Free Bootstrap 4 Admin Dashboard Template and UI Kit. It is very powerful bootstrap admin dashboard, which allows you to build products like admin panels, content management systems and CRMs etc.">
  
    <title>@yield('title', 'Dashboard')</title>
    
    <!-- GOOGLE FONTS -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500|Poppins:400,500,600,700|Roboto:400,500" rel="stylesheet">
    <link href="https://cdn.materialdesignicons.com/4.4.95/css/materialdesignicons.min.css" rel="stylesheet">
  
    <!-- PLUGINS CSS STYLE -->
    <link href="{{ asset('assets/plugins/simplebar/simplebar.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/nprogress/nprogress.css') }}" rel="stylesheet">
  
    <!-- No Extra plugin used -->
    <link href="{{ asset('assets/plugins/jvectormap/jquery-jvectormap-2.0.3.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    
    <!-- SLEEK CSS -->
    <link id="sleek-css" rel="stylesheet" href="{{ asset('assets/css/sleek.css') }}">
  
    <!-- FAVICON -->
    <link href="{{ asset('assets/img/favicon.png') }}" rel="shortcut icon">
  
    @yield('styles')
    <script src="{{ asset('assets/plugins/nprogress/nprogress.js') }}"></script>
</head>
<body class="header-fixed sidebar-fixed sidebar-dark header-light" id="body">
    <script>
      NProgress.configure({ showSpinner: false });
      NProgress.start();
    </script>

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
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/sleek.js') }}"></script>
    <link href="{{ asset('assets/options/optionswitch.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/options/optionswitcher.js') }}"></script>
    @yield('scripts')
</body>
</html>
