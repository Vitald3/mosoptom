<p>{{ __('email.text_v1') }}</p>
<p>{{ sprintf(__('email.text_v2'), route(session('route_url') . '_account')) }}</p>

<p>{{ __('email.text_v3') }}</p>

<ul>
    <li>{{ sprintf(__('email.text_v4'), $email, $phone) }}</li>
    <li>{{ sprintf(__('email.text_v5'), $password) }}</li>
</ul>

<p>{{ __('email.text_v6') }}</p>