<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ config('dashboard.favIcon') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">


    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/public/style.css') }}" />


</head>
<body class="login_body">
    <div id="app">
        <header>
            @yield('header')
        </header>
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const inputGroups = document.querySelectorAll(".input_box_wrap");

    inputGroups.forEach(group => {
      const openEye = group.querySelector(".open-eye");
      const closeEye = group.querySelector(".close-eye");
      const inputBox = group.querySelector(".passIcon");

      if (openEye && closeEye && inputBox) {
        openEye.addEventListener("click", function () {
          inputBox.setAttribute("type", "text");
          openEye.style.display = "none";
          closeEye.style.display = "block";
        });

        closeEye.addEventListener("click", function () {
          inputBox.setAttribute("type", "password");
          openEye.style.display = "block";
          closeEye.style.display = "none";
        });
      }
    });
  });
</script>
