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
    <link rel="stylesheet" href="{{ asset('assets/site/css/style.css') }}" />
@endsection

@section('content')
    <section id="content">
        <div class="container">
            @if($page_categories)
                <div class="h2">{{ __('locale.text_blog_category') }}</div>
                <div class="flex-3 wrap">
                    @foreach($page_categories as $category)
                        <div class="col-3">
                            <div class="page">
                                <a href="{{ $category['url'] }}" class="podcat">{{ $category['name'] }}</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="bloc_container">
                <h1 class="h2_zag">{{ $title }}</h1>
                <div class="s_blog pages">
                    @if(isset($pages))
                        @foreach($pages->groupBy(function($query) use($arr) {return $arr[Carbon\Carbon::parse($query->created_at)->format('n')];}) as $month => $chunk)
                            <div class="flex bloc_flex">
                                <div class="colon_mesec">
                                    {{ $month }}
                                </div>
                                <div class="colon_stati flex">
                                    @foreach($chunk as $page)
                                        <div class="colon2">
                                            <div class="data">{{ $page->str_date() }}</div>
                                            <a href="{{ $page->getSlug() }}" class="name_s">{{ $page->name }}</a>
                                            @if(!is_null($page->page_attribute))
                                                <div class="attributes">
                                                    @foreach($page->page_attribute as $key => $attribute)
                                                        <span class="flex"{!! isset($attribute['svg']) ? ' style="padding-left: 38px;background: url(\'' . $attribute['svg'] . '\') no-repeat left center"' : '' !!}>
                                                            <span>{{ $attribute['text'] }}</span>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if(!is_null($page->description))
                                                <div class="mini_descr">{{ \Str::limit(strip_tags($page->description), 300, '...') }}</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        @if(strip_tags($description))
                            <div class="description">
                                {!! $description !!}
                            </div>
                        @endif
                    @elseif(!$page_categories)
                        <p>{{ __('locale.text_blog_empty') }}</p>
                    @endif
                </div>
                @if($next)
                    <div class="text-center bottom"><a href="{{ $next }}" class="btn-default load_more">{{ __('locale.text_load_more') }}</a></div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('page-scripts')
    <script>
        $(document).on('click', '.load_more', function () {
            var self = $(this);
            var url = self.attr('href');

            self.addClass('clicked');
            setTimeout(function(){
                self.addClass('success');
            }, 300);


            $.ajax({
                url: url,
                type: 'get',
                dataType: 'html',
                success: function (html) {
                    self.removeClass('clicked');
                    setTimeout(function(){
                        self.removeClass('success');
                    }, 2000);
                    $('.pages').append($(html).find('.pages').html());
                    $('#content h1').text($(html).find('#content h1').text());

                    if ($(html).find('.bottom').length) {
                        $('.bottom').html($(html).find('.bottom').html());
                    } else {
                        $('.bottom').remove();
                    }

                    window.history.pushState('', '', url);
                }
            });

            return false;
        });
    </script>
@endsection