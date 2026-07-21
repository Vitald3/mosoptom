<div class="bestseller_module mt30 col-12 flex-2 bestseller_module{{ $module }}">
    <div class="new_left">
        <div class="h2">{{ $title }}</div>
        <a href="{{ route(session('route_url') . '_bestseller') }}" class="more">
            {{ __('locale.more') }}
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" width="63" height="8" viewBox="0 0 63 8" fill="none">
                    <path d="M0.5 6.5H61L55.5 1" stroke="#54B0AC" stroke-width="1.5"/>
                </svg>
            </span>
        </a>
        <div class="new_text">
            {{ __('locale.text_bestseller') }}
        </div>
    </div>
    <div class="new_right">
        <div class="products4 col-12 flex-2 flex-start wrap">
            @foreach($products as $product)
                @include('pages.site.product_item', ['product' => $product])
            @endforeach
        </div>
    </div>
</div>