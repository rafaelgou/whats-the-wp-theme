@extends('_layout')

@section('title', ' Theme: ' . $theme->name)

@section('content')
  <section id="top" class="wrapper style3 fullscreen fade-up">
    <div class="inner">
      <h1 class="h3 mb-3 font-weight-normal">{{ $theme->name }}</h1>

      @component('component/theme-info', ['theme' => $theme, 'standalone' => true])
      @endcomponent
    </div>
  </section>
  
  <section id="intro" class="wrapper style1 fullscreen fade-up">
    <div class="inner">
      @component('component/search-form', ['newSearch' => true])@endcomponent
    </div>
  </section>
  @include('parts/section-one')
  @include('parts/section-two')
@endsection

@section('sidebar')
  @component('component/sidebar')@endcomponent
@endsection
