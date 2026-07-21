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

@section('content')
    <section class="container" id="content">
        <div class="row">
            <div class="flex-2 wrap">
                @if($categories)
                    <ul class="col-12">
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ $category['url'] }}" class="f14"><b>{{ $category['name'] }}</b></a>
                                @if($category['children'])
                                    <ul>
                                        @foreach($category['children'] as $children)
                                            <li>
                                                <a href="{{ $children['url'] }}" class="f14">{{ $children['name'] }}</a>
                                                @if($children['children'])
                                                    <ul>
                                                        @foreach($category['children'] as $children2)
                                                            <li>
                                                                <a href="{{ $children2['url'] }}" class="f14">{{ $children2['name'] }}</a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
                @if($page_categories)
                    <ul class="col-12">
                        @foreach($page_categories as $category)
                            <li>
                                <a href="{{ $category['url'] }}" class="f14"{!! $category['children'] || $category['pages'] ? ' style="font-weight:bold"' : '' !!}>{{ $category['name'] }}</a>
                                @if($category['pages'])
                                    <ul>
                                        @foreach($category['pages'] as $page)
                                            <li>
                                                <a href="{{ $page['url'] }}" class="f14">{{ $page['name'] }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                @if($category['children'])
                                    <ul>
                                        @foreach($category['children'] as $children)
                                            <li>
                                                <a href="{{ $children['url'] }}" class="f14"{!! $children['children'] || $children['pages'] ? ' style="font-weight:bold"' : '' !!}>{{ $children['name'] }}</a>
                                                @if($children['pages'])
                                                    <ul>
                                                        @foreach($children['pages'] as $page2)
                                                            <li>
                                                                <a href="{{ $page2['url'] }}" class="f14">{{ $page2['name'] }}</a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                                @if($children['children'])
                                                    <ul>
                                                        @foreach($category['children'] as $children2)
                                                            <li>
                                                                <a href="{{ $children2['url'] }}" class="f14"{!! $children2['children'] || $children2['pages'] ? ' style="font-weight:bold"' : '' !!}>{{ $children2['name'] }}</a>
                                                                @if($children2['pages'])
                                                                    <ul>
                                                                        @foreach($children2['pages'] as $page3)
                                                                            <li>
                                                                                <a href="{{ $page3['url'] }}" class="f14">{{ $page3['name'] }}</a>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </section>
@endsection