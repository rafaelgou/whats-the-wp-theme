<!doctype html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Altitude @yield('title')</title>

    <!-- Fonts -->
    <link href="//fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <link href="//stackpath.bootstrapcdn.com/bootswatch/4.1.1/minty/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
    <link href="/css/app.css" rel="stylesheet" type="text/css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  </head>
  <body class="@yield('body_class')">

    <div class="container">
      <div class="row">
        <div class="col-md-9">
          @yield('content')
        </div>
        <div class="col-md-3">
          @yield('sidebar')
        </div>
      </div>
    </div>

    <nav class="navbar fixed-bottom navbar-light bg-light">
      <p class="text-center">Altitude &copy; 2018</p>
    </nav>

    <div class="clearfix" />
  </body>
</html>
