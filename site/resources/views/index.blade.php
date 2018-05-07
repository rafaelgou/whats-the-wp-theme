@extends('_layout')

@section('title', '')

@section('body_class', 'body-home')

@section('content')

  <div class="container">

    <div class="row">

      <form class="form-url" method="POST" action="/">
        @csrf
        <div class="text-center">
          <h1 class="h3 mb-3 font-weight-normal">Altitude</h1>
          <label for="inputUrl" class="sr-only">Site URL</label>
          <input type="url" id="inputUrl" name="uri" class="form-control" placeholder="http://example.com" required autofocus>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Discover theme!</button>
        </div>
      </form>
    </div>

  </div>

@endsection
