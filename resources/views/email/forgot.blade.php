<h1>{{ __('locale.text_forgot_email_1') }}</h1>

<p>{{ __('locale.text_forgot_email_2') }}</p>
<a href="{{ route(session('route_url') . '_account_forgot', ['token' => $token]) }}">{{ __('locale.text_forgot_email_3') }}</a>