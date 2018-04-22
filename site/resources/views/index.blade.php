<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Altitude</title>

        <!-- Fonts -->
        <link href="/css/app.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    </head>
    <body>
        <form class="form-url">
          <div class="text-center">
          <h1 class="h3 mb-3 font-weight-normal">Altitude</h1>
          <label for="inputUrl" class="sr-only">Site URL</label>
          <input type="url" id="inputUrl" name="url" class="form-control" placeholder="http://example.com" required autofocus>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Discover theme!</button>
          <p class="mt-5 mb-3 text-muted">Altitude &copy; 2018</p>
          </div>
        </form>
    </body>
</html>
