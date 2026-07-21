<?php
	$discount = false;

	if (session('customer_id') || !session('settings.price_logged')) {
		if (isset($product->product_discount[0])) {
			$price = format_price($product->product_discount[0]->price, session('currency'));
			$discount = format_price($product->product_discount[$product->product_discount->count() - 1]->price, session('currency'));
		} else {
			$price = format_price($product->price, session('currency'));
		}
	} else {
		$price = false;
	}

	if ((session('customer_id') || !session('settings.price_logged')) && $product->product_special_one) {
		$special = format_price($product->product_special_one->price, session('currency'));
	} else {
		$special = false;
	}

	$width = isset($width) ? $width : 227;
	$height = isset($height) ? $height : 187;
?>
<div class="product">
    <a href="{{ $product->getSlug() }}" class="relative text-center">
        <img src="{{ resize_image($product->image, $width, $height) }}" width="{{ $width }}px" height="{{ $height }}px" alt="{!! $product->name !!}" class="border-24" />
    </a>
    <div class="caption">
        <div class="product_sku">
            {!! __('locale.sku') !!}{{ $product->model }}
            <a href="#" class="product_wishlist product_wishlist-{{ $product->id }}{{ isset($wishlist) ? ' active' : '' }}" onclick="wishlist.add({{ $product->id }});return false;">
                @if(isset($wishlist))
                    <svg width="17" height="14" viewBox="0 0 17 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.9168 1.30774C13.1528 -0.435676 10.283 -0.435676 8.51946 1.30774L8.10984 1.71244L7.70045 1.30774C5.93688 -0.435912 3.0669 -0.435912 1.30332 1.30774C-0.424444 3.01575 -0.435664 5.72314 1.2773 7.60554C2.83965 9.32182 7.44742 13.0298 7.64292 13.1867C7.77565 13.2933 7.93534 13.3453 8.09408 13.3453C8.09933 13.3453 8.10458 13.3453 8.1096 13.345C8.27383 13.3526 8.43925 13.2969 8.57627 13.1867C8.77177 13.0298 13.38 9.32182 14.9428 7.60531C16.6556 5.72314 16.6444 3.01575 14.9168 1.30774Z" fill="#54B0AC"/>
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="14" viewBox="0 0 17 14" fill="none">
                        <path d="M15.1668 1.32286C13.4028 -0.440714 10.533 -0.440714 8.76946 1.32286L8.35983 1.73224L7.95045 1.32286C6.18688 -0.440953 3.3169 -0.440953 1.55332 1.32286C-0.174444 3.05063 -0.185664 5.78932 1.5273 7.69349C3.08965 9.42961 7.69742 13.1804 7.89292 13.3392C8.02564 13.4471 8.18534 13.4996 8.34408 13.4996C8.34933 13.4996 8.35458 13.4996 8.3596 13.4993C8.52383 13.507 8.68925 13.4506 8.82627 13.3392C9.02177 13.1804 13.63 9.42961 15.1928 7.69325C16.9056 5.78932 16.8944 3.05063 15.1668 1.32286ZM14.1282 6.73532C12.9101 8.08855 9.56173 10.8795 8.3596 11.8699C7.15746 10.8798 3.80983 8.08903 2.59194 6.73556C1.39697 5.40739 1.38575 3.51587 2.56592 2.3357C3.16866 1.7332 3.96021 1.43171 4.75177 1.43171C5.54332 1.43171 6.33488 1.73296 6.93761 2.3357L7.83802 3.2361C7.9452 3.34328 8.08031 3.40725 8.2221 3.42969C8.45222 3.47911 8.7019 3.41489 8.88093 3.23634L9.78182 2.3357C10.9875 1.13046 12.9488 1.1307 14.1537 2.3357C15.3339 3.51587 15.3227 5.40739 14.1282 6.73532Z" fill="#BED0D6"/>
                    </svg>
                @endif
            </a>
        </div>
        <div class="product_name"><a href="{{ $product->getSlug() }}">{!! \Str::limit($product->name, 54, '...') !!}</a></div>
        @if($price)
            @if($discount)
                <div class="flex-2 flex-start">
            @endif
            <div class="price">
                @if($special)
                    <span class="price_old">{{ $price }}</span>
                    <span class="price_new">{{ $special }}</span>
                @else
                    <span class="price_new">{{ $price }}</span>
                @endif
            </div>
            @if($discount)
                    <span class="price_discount">{{ __('locale.text_filter_price_from') }} {{ $discount }}</span>
                </div>
            @endif
        @endif
        @isset($wishlist)
            <a href="#" onclick="wishlist.remove({{ $product->id }});return false;" class="product_wishlist-{{ $product->id }} wish_remove">
                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.4219 1.875H9.84375V1.40625C9.84375 0.63085 9.2129 0 8.4375 0H6.5625C5.7871 0 5.15625 0.63085 5.15625 1.40625V1.875H2.57812C1.93195 1.875 1.40625 2.4007 1.40625 3.04688V4.6875C1.40625 4.94637 1.61613 5.15625 1.875 5.15625H2.13117L2.53614 13.6606C2.57191 14.4117 3.18891 15 3.94078 15H11.0592C11.8111 15 12.4281 14.4117 12.4639 13.6606L12.8688 5.15625H13.125C13.3839 5.15625 13.5938 4.94637 13.5938 4.6875V3.04688C13.5938 2.4007 13.068 1.875 12.4219 1.875ZM6.09375 1.40625C6.09375 1.14779 6.30404 0.9375 6.5625 0.9375H8.4375C8.69596 0.9375 8.90625 1.14779 8.90625 1.40625V1.875H6.09375V1.40625ZM2.34375 3.04688C2.34375 2.91765 2.4489 2.8125 2.57812 2.8125H12.4219C12.5511 2.8125 12.6562 2.91765 12.6562 3.04688V4.21875C12.5118 4.21875 2.94237 4.21875 2.34375 4.21875V3.04688ZM11.5274 13.616C11.5155 13.8664 11.3098 14.0625 11.0592 14.0625H3.94078C3.69015 14.0625 3.48448 13.8664 3.47259 13.616L3.06973 5.15625H11.9303L11.5274 13.616Z" fill="#EB5757"/>
                    <path d="M7.5 13.125C7.75887 13.125 7.96875 12.9151 7.96875 12.6562V6.5625C7.96875 6.30363 7.75887 6.09375 7.5 6.09375C7.24113 6.09375 7.03125 6.30363 7.03125 6.5625V12.6562C7.03125 12.9151 7.2411 13.125 7.5 13.125Z" fill="#EB5757"/>
                    <path d="M9.84375 13.125C10.1026 13.125 10.3125 12.9151 10.3125 12.6562V6.5625C10.3125 6.30363 10.1026 6.09375 9.84375 6.09375C9.58488 6.09375 9.375 6.30363 9.375 6.5625V12.6562C9.375 12.9151 9.58485 13.125 9.84375 13.125Z" fill="#EB5757"/>
                    <path d="M5.15625 13.125C5.41512 13.125 5.625 12.9151 5.625 12.6562V6.5625C5.625 6.30363 5.41512 6.09375 5.15625 6.09375C4.89738 6.09375 4.6875 6.30363 4.6875 6.5625V12.6562C4.6875 12.9151 4.89735 13.125 5.15625 13.125Z" fill="#EB5757"/>
                </svg>
            </a>
        @endisset
    </div>
</div>