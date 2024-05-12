<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>HGO Ticket Booking</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/vue-select@latest/dist/vue-select.css">
    {{-- <script src="https://www.google.com/recaptcha/api.js"></script> --}}
    {{-- <script src="https://www.google.com/recaptcha/api.js?render=6Lf5Z8wpAAAAAPR2ikZrfMVoxTw6YgGX6QDt3szj"></script> --}}
    @vite(['resources/js/index.js', 'resources/css/app.scss'])
  </head>

  <body>
    <div id="app"></div>
  </body>

</html>
