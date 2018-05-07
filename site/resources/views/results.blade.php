@extends('_layout')

@section('title', ' results: ' . $uri)

@section('content')
  <div class="jumbotron">
    <div class="container text-center">
      <h1 class="display-3">Altitude Results</h1>
    </div>
  </div>

  <div class="container">

    <div class="row">
      <div class="col col-lg-12 text-center">
        <h2> @if ($search && $search->title) {{ $search->title }} @else Theme name not available @endif </h2>
        @if ($uri) <h4><a href="{{ $uri }}" target="_blank">{{ $uri }}</a></h4> @endif
      </div>
    </div>

    <hr class="clearfix"/>

    @if ($error)
      <div class="alert alert-danger text-center" role="alert">
        <h2>{{ $error }}</h2>
      </div>
    @else
      @if (null !== $search && $search->main)
        @component('component/theme-info', ['theme' => $search->main])
        @endcomponent
      @endif

      @if (null === $search && null === $search->main)
        <div class="alert alert-danger text-center" role="alert">
          <h2>Unable to discover the theme - is that a Wordpress site?</h2>
        </div>
      @endif

      @if (null !== $search && $search->child)
        @component('component/theme-info', ['theme' => $search->child])
        @endcomponent
      @endif
    @endif


    <div class="row">
      <div class="col col-lg-12">
        <form class="form-url" method="POST" action="/">
          @csrf
          <div class="text-center">
            <h2 class="h3 mb-3 font-weight-normal">New Search</h2>
            <label for="inputUrl" class="sr-only">Site URL</label>
            <input type="url" id="inputUrl" name="uri" class="form-control" placeholder="http://example.com" value="{{ $uri }}" required>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Discover theme!</button>
          </div>
        </form>
      </div>
      <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
    </div>

  </div>

@endsection
