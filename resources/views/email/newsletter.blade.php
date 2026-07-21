<p>{{ __('email.text_newsletter') }}</p>
<p>{{ __('email.text_newsletter_2') }}</p>

<ul>
    @foreach($newsletter as $n)
        @if($n == 1)
            <li>{{ __('locale.text_newsletter_3') }}</li>
        @elseif($n == 2)
            <li>{{ __('locale.text_newsletter_4') }}</li>
        @else
            <li>{{ __('locale.text_newsletter_5') }}</li>
        @endif
    @endforeach
</ul>