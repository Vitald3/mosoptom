@extends('layouts.base')
@section('meta')
  <title>{{ __('locale.404') }}</title>
@endsection
@section('page-styles')
  <link rel="stylesheet" href="{{ asset('assets/site/css/style.css') }}" />
@endsection

@section('content')
  <section class="container" style="padding-bottom: 15px">
    <h1 class="h1">{{ __('locale.404') }}</h1>
    <p class="f14">
      {{ __('locale.404text') }}
    </p>
    <a href="{{ url('') }}" class="btn-default">{{ __('locale.continue') }}</a>
  </section>
@endsection