@extends('_layout')

@section('title')
@if ( $error ) No Results
@elseif ($search && $search->title) Results for {{ $search->title }}
@elseif ($search && $search->uri) Results for {{ $search->uri }}
@else No Search
@endif
@endsection

@section('content')

@if ( $error )
  <section id="intro" class="wrapper style3 fullscreen">
    <div class="inner">
        <div class="alert alert-danger text-center" role="alert">
          <h1>{!! $error !!}</h1>
          <hr/>
          <h2>New search</h2>
          @component('component/search-form', ['newSearch' => true])@endcomponent
        </div>
    </div>
  </section>
@elseif ($search && null === $search->main) 
  <div class="alert alert-danger text-center" role="alert">
    <h1 class="bah">blah UNABLE</h1>
    <h2>Unable to discover the theme - is that a Wordpress site?</h2>
    <hr/>
    @component('component/search-form', ['newSearch' => true])@endcomponent
  </div>
@elseif ($search) 
  <section id="top" class="wrapper style1 fullscreen web-bg results">
    <div class="inner">
      <h1>What's The WP Theme Results</h1>
      <h2> @if ($search && $search->title) {{ $search->title }} @else Theme name not available @endif </h2>
      @if ($uri) <h4><a href="{{ $uri }}" target="_blank">{{ $uri }}</a></h4> @endif

      @if ($search && null === $search->main)
        <div class="alert alert-danger text-center" role="alert">
          <h2>Unable to discover the theme - is that a Wordpress site?</h2>
        </div>
      @endif

      @if ($search && $search->main)
        @component('component/theme-info', ['theme' => $search->main])
        @endcomponent
      @endif

      @if ($search && $search->child)
        <hr/>
        @component('component/theme-info', ['theme' => $search->child])
        @endcomponent
      @endif
    </div>
  </section>
  <section id="intro" class="wrapper style1-alt fullscreen">
    <div class="inner">
      <h1>New search</h1>
      @component('component/search-form')
      @endcomponent
    </div>
  </section>
@endif
  
  @include('parts/section-one')
  @include('parts/section-two')

@endsection
