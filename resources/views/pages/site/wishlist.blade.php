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
        <div class="account_container flex-2 flex-start">
            <div class="account_left">
                @include('pages.site.account.menu')
            </div>
            <div class="account_right">
                <h1>{{ $title }}</h1>
                @if($products->isEmpty())
                    <div class="search_empty">
                        <div class="write_title">{{ __('locale.text_wishlist_2') }}</div>
                        <p style="color: #ACBDC0">{{ __('locale.text_wishlist_3') }}</p>
                    </div>
                @else
                    <div class="products flex-2 flex-start wrap">
                        @foreach($products as $product)
                            <div class="col-3 mb30">
                                @include('pages.site.product_item', ['product' => $product, 'wishlist' => true])
                            </div>
                        @endforeach
                    </div>
                    {!! $products->links() !!}
                @endif
            </div>
        </div>
    </section>
@endsection