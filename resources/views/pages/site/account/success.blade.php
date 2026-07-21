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
                <p>{{ __('locale.text_account_success_1') }}</p>
                <br>
                <p>{{ sprintf(__('locale.text_account_success_2'), route(session('route_url') . '_account'), route(session('route_url') . '_account_order'), route(session('route_url') . '_wishlist'), route(session('route_url') . '_account_newsletter'), route(session('route_url') . '_account_reviews')) }}</p>
                <br>
                <p>{{ __('locale.text_account_success_3') }}</p>
                <a href="{{ url('katalog') }}" class="mt50 btn-default">{{ __('locale.text_account_success_4') }}</a>
            </div>
        </div>
    </section>
@endsection