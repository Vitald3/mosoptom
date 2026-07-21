<div class="products_module flex-2 products_module{{ $module }}">
    <div class="new_left">
        <div class="h2">{{ $title }}</div>
        @if($link)
            <a href="{{ $link }}" class="more">
                {{ __('locale.more') }}
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="63" height="8" viewBox="0 0 63 8" fill="none">
                        <path d="M0.5 6.5H61L55.5 1" stroke="#54B0AC" stroke-width="1.5"/>
                    </svg>
                </span>
            </a>
        @endif
        @if($text)
            <div class="new_text">
                {{ $text }}
            </div>
        @endif
    </div>
    <div class="new_right">
        <div class="owl-carousel">
            @foreach($products as $product)
                @include('pages.site.product_item', ['product' => $product])
            @endforeach
        </div>
    </div>
</div>