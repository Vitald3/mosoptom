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
    <link rel="stylesheet" href="{{ asset('css/site/style.css') }}" />
@endsection

@section('content')

@endsection

@section('page-scripts')
    <script src="{{ asset('js/site/jquery.js') }}"></script>
    <script src="{{ asset('js/site/main.js') }}"></script>
@endsection