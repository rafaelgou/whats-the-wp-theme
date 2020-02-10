@extends('_layout')

@section('title', ' results: ' . $uri)

@section('content')
  <div class="jumbotron">
    <div class="container text-center">
      <h1 class="display-3">Altitude Results</h1>
    </div>
  </div>

  <h2 class="text-center"> @if ($search && $search->title) {{ $search->title }} @else Theme name not available @endif </h2>
  @if ($uri) <h4 class="text-center"><a href="{{ $uri }}" target="_blank">{{ $uri }}</a></h4> @endif

  <hr class="clearfix"/>

  @if ($error)
    <div class="alert alert-danger text-center" role="alert">
      <h2>{!! $error !!}</h2>
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

  @component('component/search-form', ['newSearch' => true])
  @endcomponent

  <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>

@endsection

@section('sidebar')
  @component('component/sidebar', ['topSearched' => $topSearched])
  @endcomponent
@endsection
