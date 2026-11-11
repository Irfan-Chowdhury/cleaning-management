<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/css/style.css') }}">

    <!-- Google Tag Manager -->
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;
        f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-TVW3BGC5');
    </script>
    <!-- End Google Tag Manager -->

    @stack('styles')
</head>
<body>
    <div class="dashboard-wrapper">
        @include('components.sidebar')

        <div class="dashboard-main">
            {{-- @include('components.under-construction-banner') --}}

            @include('components.header')

            <main class="dashboard-content">
                {{-- Dynamic page content --}}
                @yield('content')
            </main>
        </div>
    </div>

    <div class="sidebar-overlay" aria-hidden="true"></div>

    <div id="global-success-message" data-message="{{ session('success') }}" hidden></div>
    <div id="global-error-message" data-message="{{ session('error') ?? session('invite_error') ?? ($errors->any() ? $errors->first() : null) }}" hidden></div>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TVW3BGC5" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.AppFlash = {
            success: @json(session('success')),
            error: @json(session('error') ?? session('invite_error') ?? ($errors->any() ? $errors->first() : null))
        };
    </script>
    <script src="{{ asset('public/assets/js/main.js') }}?v={{ filemtime(public_path('assets/js/main.js')) }}"></script>

    @stack('scripts')
</body>
</html>
