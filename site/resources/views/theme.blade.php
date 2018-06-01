@extends('_layout')

@section('title', ' Theme: ' . $theme->name)

@section('content')
  <div class="jumbotron">
    <div class="container text-center">
      <h1 class="display-3">{{ $theme->name }}</h1>
    </div>
  </div>

  @component('component/theme-info', ['theme' => $theme, 'standalone' => true])
  @endcomponent

  @component('component/search-form', ['newSearch' => true])
  @endcomponent

  <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>

@endsection

@section('sidebar')
  @component('component/sidebar', ['topSearched' => $topSearched])
  @endcomponent
@endsection
