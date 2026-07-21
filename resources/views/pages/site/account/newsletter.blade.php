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
                <div class="h2">{{ __('locale.text_newsletter_2') }}</div>
                <div class="flex flex-start check">
                    <div class="custom_checkbox mt0">
                        <input type="checkbox" value="1" onchange="save(this.value);" id="sale_1" name="type"{{ in_array(1, $newsletter) ? ' checked' : '' }} />
                        <span></span>
                    </div>
                    <label for="sale_1">{{ __('locale.text_newsletter_3') }}</label>
                </div>
                <div class="flex flex-start">
                    <div class="custom_checkbox mt0">
                        <input type="checkbox" value="2" onchange="save(this.value);" id="sale_2" name="type"{{ in_array(2, $newsletter) ? ' checked' : '' }} />
                        <span></span>
                    </div>
                    <label for="sale_2">{{ __('locale.text_newsletter_4') }}</label>
                </div>
                <div class="flex flex-start mb30">
                    <div class="custom_checkbox mt0">
                        <input type="checkbox" value="3" onchange="save(this.value);" id="sale_3" name="type"{{ in_array(3, $newsletter) ? ' checked' : '' }} />
                        <span></span>
                    </div>
                    <label for="sale_3">{{ __('locale.text_newsletter_5') }}</label>
                </div>
                <p>{!! sprintf(__('locale.text_newsletter_6'), $customer['email'], $customer['email']) !!}</p>
                <p>{{ __('locale.text_newsletter_7') }}</p>
                <a href="#" class="mt50 send btn-default"{!! !$newsletter ? ' style="display: none"' : '' !!}>{{ __('locale.text_newsletter_8') }}</a>
            </div>
        </div>
    </section>
@endsection

@section('page-scripts')
    <script>
        function save(value) {
            $.ajax({
                url: '{{ route('newsletter_save') }}',
                data: 'type=' + value,
                dataType: 'json',
                type: 'POST',
                success: function() {
                    if ($('.check input:checked').length) {
                        $('.send').fadeIn();
                    } else {
                        $('.send').fadeOut();
                    }
                }
            });
        }

        $(document).on('click', '.send', function(){
            var self = $(this);

            $.ajax({
                url: '{{ route('newsletter_send') }}',
                dataType: 'json',
                type: 'POST',
                success: function(json) {
                    if (json.success) {
                        self.after('<div class="alert alert-success">' + json.success + '</div>');
                    } else {
                        self.after('<div class="alert alert-danger">' + json.error + '</div>');
                    }
                }
            });

            return false;
        });
    </script>
@endsection