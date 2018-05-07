<!doctype html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Altitude @yield('title')</title>

    <!-- Fonts -->
    <link href="/css/app.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.1.1/minty/bootstrap.min.css" rel="stylesheet" type="text/css">

  </head>
  <body class="@yield('body_class')">

    @yield('content')

    <nav class="navbar fixed-bottom navbar-light bg-light">
      <p class="text-center">Altitude &copy; 2018</p>
    </nav>

    <div class="clearfix" />
  </body>
</html>
