@extends('_layout')

@section('title', '')

@section('body_class', 'body-home')

@section('content')

  @component('component/search-form', ['title' => true])
  @endcomponent

@endsection

@section('sidebar')
  @component('component/sidebar', ['topSearched' => $topSearched])
  @endcomponent
@endsection
