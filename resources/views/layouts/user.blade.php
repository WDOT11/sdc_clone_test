<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('site-title', config('app.name', 'Laravel'))</title>
    <link rel="icon" type="image/x-icon" href="{{ config('dashboard.favIcon') }}">

    <!-- @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @yield('style')
     -->
    <!-- Fonts -->
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
    @yield('style')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://unicons.iconscout.com/release/v3.0.6/css/line.css">
    <!-- Boxiocns CDN Link -->
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400..700&display=swap" rel="stylesheet" />
    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('css/user/style.css') }}" />
    <!-- chat cdn link -->

    <!-- Scripts -->
    @php
        $authUser = auth()->check()
            ? auth()
                ->user()
                ->only(['first_name', 'last_name', 'full_name', 'email', 'phone'])
            : null;

        if ($authUser && auth()->user()->profile_img_media_id) {
            $authUser['profile_image'] = auth()->user()->profileImage();
        }
        if($authUser && auth()->user()->role_id){
            $authUser['role_for'] = auth()->user()->roleFor();
        }
    @endphp

    <script>
        window.authUser = @json($authUser);
        window.originalSwitchId = @json(session('admin_user_id'));
    </script>
    <!-- @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @yield('style') -->
</head>

<body @if(session('admin_user_id')) class="admin-switched" @endif>
<!-- <body> -->
    <!-- Main Content -->
    <div id="app" class="content">
        <user-layout-sidebar></user-layout-sidebar>
        <section class="home-section">
            <user-layout-header></user-layout-header>
            @yield('content')
            <user-layout-footer></user-layout-footer>
        </section>
        <app-component />
    </div>
    <div class="notifoveropen2"></div>
    <div id="user_ajax_loader" class="user_ajax_loader">
        <div class="ajax_loader_inner">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" width="150" height="150" style="shape-rendering: auto; display: block; background: transparent;"><g><circle fill="#003cbb" r="10" cy="50" cx="84">
            <animate begin="0s" keySplines="0 0.5 0.5 1" values="10;0" keyTimes="0;1" calcMode="spline" dur="0.25s" repeatCount="indefinite" attributeName="r"/>
            <animate begin="0s" values="#003cbb;#bfd9ff;#66a3ff;#0055fa;#003cbb" keyTimes="0;0.25;0.5;0.75;1" calcMode="discrete" dur="1s" repeatCount="indefinite" attributeName="fill"/>
        </circle><circle fill="#003cbb" r="10" cy="50" cx="16">
        <animate begin="0s" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" values="0;0;10;10;10" keyTimes="0;0.25;0.5;0.75;1" calcMode="spline" dur="1s" repeatCount="indefinite" attributeName="r"/>
        <animate begin="0s" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" values="16;16;16;50;84" keyTimes="0;0.25;0.5;0.75;1" calcMode="spline" dur="1s" repeatCount="indefinite" attributeName="cx"/>
        </circle><circle fill="#0055fa" r="10" cy="50" cx="50">
        <animate begin="-0.25s" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" values="0;0;10;10;10" keyTimes="0;0.25;0.5;0.75;1" calcMode="spline" dur="1s" repeatCount="indefinite" attributeName="r"/>
        <animate begin="-0.25s" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" values="16;16;16;50;84" keyTimes="0;0.25;0.5;0.75;1" calcMode="spline" dur="1s" repeatCount="indefinite" attributeName="cx"/>
        </circle><circle fill="#66a3ff" r="10" cy="50" cx="84">
        <animate begin="-0.5s" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" values="0;0;10;10;10" keyTimes="0;0.25;0.5;0.75;1" calcMode="spline" dur="1s" repeatCount="indefinite" attributeName="r"/>
        <animate begin="-0.5s" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" values="16;16;16;50;84" keyTimes="0;0.25;0.5;0.75;1" calcMode="spline" dur="1s" repeatCount="indefinite" attributeName="cx"/>
        </circle><circle fill="#bfd9ff" r="10" cy="50" cx="16">
        <animate begin="-0.75s" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" values="0;0;10;10;10" keyTimes="0;0.25;0.5;0.75;1" calcMode="spline" dur="1s" repeatCount="indefinite" attributeName="r"/>
        <animate begin="-0.75s" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" values="16;16;16;50;84" keyTimes="0;0.25;0.5;0.75;1" calcMode="spline" dur="1s" repeatCount="indefinite" attributeName="cx"/>
        </circle><g/></g><!-- [ldio] generated by https://loading.io --></svg>
        </div>
    </div>
    <div class="tooltip-window"></div>
    <script type="text/javascript" src="{{ asset('js/jquery.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://js.stripe.com/v3/"></script>


    <!-- {{-- <script src="script.js"></script> --}} -->
     <script src="{{ asset('js/propper.js') }}"></script>
    <script src="{{ asset('js/user/script.js') }}"></script>
    <script type="text/javascript">
        jQuery(document).ready(function () {
    // Show dropdown on hover
    jQuery(".profile_box").hover(
        function () {
            jQuery(".Adrop_outer").stop(true, true).show();
        },
        function () {
            // Use a timeout to prevent flickering when moving to dropdown
            setTimeout(function () {
                if (!jQuery(".Adrop_outer").is(":hover")) {
                    jQuery(".Adrop_outer").hide();
                }
            }, 200);
        }
    );

    // Keep dropdown open when hovering over it
    jQuery(".Adrop_outer").hover(
        function () {
            jQuery(this).stop(true, true).show();
        },
        function () {
            jQuery(this).hide();
        }
    );

    // Toggle dropdown on click
    jQuery(".profile_box").click(function (event) {
        event.stopPropagation(); // Prevent click from propagating to document
        jQuery(".Adrop_outer").toggle(); // Toggle dropdown
    });

    // Hide dropdown when clicking anywhere else
    jQuery(document).click(function () {
        jQuery(".Adrop_outer").hide();
    });

    // Prevent hiding when clicking inside the dropdown
    jQuery(".Adrop_outer").click(function (event) {
        event.stopPropagation();
    });
});

        jQuery(document).ready(function($) {
            var offset = $(".home-content").offset();
            checkOffset();

            $(window).scroll(function() {
                checkOffset();
            });

            function checkOffset() {
                if ($(document).scrollTop() > offset.top) {
                    $(".home-content").addClass("fixedtop");
                } else {
                    $(".home-content").removeClass("fixedtop");
                }
            }
        });
    </script>
    @yield('script')
</body>

</html>
