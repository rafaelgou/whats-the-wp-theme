<!doctype html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Wordpress theme discover tool">
    <title>@yield('title')What's the WP Theme</title>
    <!-- Fonts -->
    <link href="//fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
    <link href="/css/app.css" rel="stylesheet" type="text/css">
  </head>
  <body class="is-preload @yield('body_class')">

    @component('component/sidebar')@endcomponent

		<!-- Wrapper -->
    <div id="wrapper">

      @yield('content')

    </div><!-- wrapper -->

		<!-- Footer -->
    <footer id="footer" class="wrapper style1-alt">
      <div class="inner">
        <ul class="menu">
          <li>&copy; 2020. All rights reserved.</li>
          <li>Design: <a href="https://html5up.net" target="_blank">HTML5 UP</a></li>
          <li>Created by: <a href="https://me.rgou.net" target="_blank">Rafael Goulart</a></li>
          <li><a href="https://git.rgou.net/rafaelgou/whats-the-wp-theme" target="_blank"><i class="fab fa-github"></i> Github</a></li>
          <li><a href="https://github.com/rafaelgou/whats-the-wp-theme" target="_blank"><i class="fab fa-git"></i> Personal GIT repository</a></li>
        </ul>
      </div>
    </footer>
		<script src="js/app.js"></script>    
  </body>
</html>
