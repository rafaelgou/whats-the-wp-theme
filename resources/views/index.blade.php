@extends('_layout')

@section('title', "What's the WP theme")

@section('body_class', 'body-home')

@section('content')

  <!-- Intro -->
  <section id="intro" class="wrapper style1 web-bg fullscreen fade-up">
    <div class="inner">
      <h1>What's the WP theme?</h1>
      @component('component/search-form')@endcomponent
    </div>
  </section>

  @include('parts/section-one')
  @include('parts/section-two')

@endsection
