@extends('layouts.base')
@section('meta')
    <title>{{ $meta_title }}</title>
    @if($meta_description)
        <meta name="description" content="{{ $meta_description }}" />
    @endif
    @if($meta_keywords)
        <meta name="keywords" content="{{ $meta_keywords }}" />
    @endif
@endsection
@section('page-styles')
    <link rel="stylesheet" href="{{ asset('assets/site/css/account/account.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/site/css/media/account.css') }}" />
@endsection
@section('content')
    <section id="success" class="container">
        <div class="account_container flex-2">
            <div class="account_left">
                @include('pages.site.account.menu')
            </div>
            <div class="account_right">
                <h1>{{ $title }}</h1>
                {!! $text !!}
            </div>
        </div>
    </section>
@endsection