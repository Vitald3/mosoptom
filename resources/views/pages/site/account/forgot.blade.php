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
            <form action="{{ route(session('route_url') . '_account_post_forgot') }}" method="post" enctype="multipart/form-data" class="validate_js account_right" novalidate style="width: 50%;margin: auto">
                <h1>{{ $title }}</h1>
                <div class="form_group">
                    <input type="password" name="password" value="{{ old('password') }}" id="account_password" class="input" required />
                    <label for="account_password" class="required">{{ __('locale.text_login_popup_v2') }}</label>
                </div>
                <input type="hidden" name="email" value="{{ $email_form }}" />
                <input type="hidden" name="token" value="{{ old('token', $codes) }}" />
                <a href="#" class="mt50 send btn-default">{{ __('locale.text_forgot_get2') }}</a>
            </form>
        </div>
    </section>
@endsection

@section('page-scripts')
    <script>
        $(document).on('click', '.send', function(){
            var self = $(this);
            $('.alert').remove();

            $.ajax({
                url: '{{ route(session('route_url') . '_account_post_forgot') }}',
                data: $('#success form').serialize(),
                dataType: 'json',
                type: 'POST',
                success: function(json) {
                    if (json.redirect) {
                        location = json.redirect;
                    } else {
                        self.after('<div class="alert alert-danger" style="margin-top: 15px">' + json.error + '</div>');
                    }
                }
            });

            return false;
        });
    </script>
@endsection