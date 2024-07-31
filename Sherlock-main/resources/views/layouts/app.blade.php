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

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/prism-coy.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500|Poppins:400,500,600,700|Roboto:400,500" rel="stylesheet">
    <link href="https://cdn.materialdesignicons.com/4.4.95/css/materialdesignicons.min.css" rel="stylesheet">

    <link rel="stylesheet" class="layout-css" href="">
    @yield('styles')
</head>
<body class="layout-6" style="background: rgb(207, 46, 46);">



    
    @include('layouts.partials.nav')
    @include('layouts.partials.header')
    


    <div class="pcoded-main-container">
        <div class="pcoded-wrapper">
            <div class="pcoded-content">
                <div class="pcoded-inner-content">
                    @include('layouts.partials.breadcrumbs')
                    <div class="main-body">
                        <div class="page-wrapper">
                            @yield('content')

                   
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.partials.footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js" integrity="sha512-TPh2Oxlg1zp+kz3nFA0C5vVC6leG/6mm1z9+mA81MI5eaUVqasPLO8Cuk4gMF4gUfP5etR73rgU/8PNMsSesoQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js" integrity="sha512-ykZ1QQr0Jy/4ZkvKuqWn4iF3lqPZyij9iRv6sGqLRdTPkY69YX6+7wvVGmsdBbiIfN/8OdsI7HABjvEok6ZopQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('assets/js/plugins/prism.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    
    <script src="{{ asset('assets/js/pcoded.js') }}"></script>
   
    @yield('scripts')
    @vite('resources/js/app.js')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        document.getElementById("copy-year").innerHTML = new Date().getFullYear();
    </script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var userThemePreference = '{{ Auth::user()->theme_preference }}';

    function applyTheme(theme) {
        if (theme === "dark") {
            applyDarkMode();
        } else if (theme === "light") {
            removeDarkMode();
        } else {
            applySystemDefault();
        }
    }

    function applyDarkMode() {
        document.querySelector(".pcoded-navbar").classList.add("navbar-dark");
        document.querySelector("link.layout-css").setAttribute('href', "{{ asset('assets/css/layouts/dark.css') }}");
        document.body.classList.add("dark-mode");
    }

    function removeDarkMode() {
        document.querySelector(".pcoded-navbar").classList.remove("navbar-dark");
        document.querySelector("link.layout-css").setAttribute('href', '');
        document.body.classList.remove("dark-mode");
    }

    function applySystemDefault() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            applyDarkMode();
        } else {
            removeDarkMode();
        }
    }

    applyTheme(userThemePreference);

    document.getElementById('theme_preference').addEventListener('change', function(event) {
        var selectedTheme = event.target.value;
        applyTheme(selectedTheme);

        $.ajax({
            url: '{{ route("settings.updateTheme") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                theme_preference: selectedTheme
            },
            success: function(response) {
                toastr.success('Theme preference updated successfully.');
            },
            error: function(xhr, status, error) {
                console.error("An error occurred: " + error);
                toastr.error('Failed to update theme preference.');
            }
        });
    });
});

</script>


</html>
