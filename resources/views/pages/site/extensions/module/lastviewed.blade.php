<div class="products_visited products_visited{{ $module }}">
    <div class="flex-2 flex-start mb25">
        <div class="h2">{{ __('locale.text_last_viewed') }}</div>
        <a href="{{ route(session('route_url') . '_last_viewed') }}" class="more">
            {{ __('locale.more') }}&nbsp;&nbsp;&nbsp;
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" width="63" height="8" viewBox="0 0 63 8" fill="none">
                    <path d="M0.5 6.5H61L55.5 1" stroke="#54B0AC" stroke-width="1.5"/>
                </svg>
            </span>
        </a>
    </div>
    <div class="flex">
        <div class="left_last">
            @if(count($products) > 3)
                <div class="flex-2">
                    <a href="#" class="last_prev">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="20" cy="20" r="20" fill="white"/>
                            <g clip-path="url(#clip0_432_54444)">
                                <path d="M16.5815 20C16.5815 19.7849 16.6637 19.5699 16.8275 19.4059L21.9873 14.2462C22.3155 13.9179 22.8477 13.9179 23.1758 14.2462C23.5039 14.5743 23.5039 15.1063 23.1758 15.4346L18.6101 20L23.1756 24.5654C23.5037 24.8936 23.5037 25.4256 23.1756 25.7537C22.8476 26.0821 22.3154 26.0821 21.9872 25.7537L16.8274 20.594C16.6635 20.43 16.5815 20.215 16.5815 20Z" fill="#54B0AC"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_432_54444">
                                    <rect width="12" height="12" fill="white" transform="translate(26 14) rotate(90)"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </a>
                    <a href="#" class="last_next">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" style="transform: rotate(180deg);">
                            <circle cx="20" cy="20" r="20" fill="white"/>
                            <g clip-path="url(#clip0_432_54444)">
                                <path d="M16.5815 20C16.5815 19.7849 16.6637 19.5699 16.8275 19.4059L21.9873 14.2462C22.3155 13.9179 22.8477 13.9179 23.1758 14.2462C23.5039 14.5743 23.5039 15.1063 23.1758 15.4346L18.6101 20L23.1756 24.5654C23.5037 24.8936 23.5037 25.4256 23.1756 25.7537C22.8476 26.0821 22.3154 26.0821 21.9872 25.7537L16.8274 20.594C16.6635 20.43 16.5815 20.215 16.5815 20Z" fill="#54B0AC"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_432_54444">
                                    <rect width="12" height="12" fill="white" transform="translate(26 14) rotate(90)"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </a>
                </div>
            @endif
            <span>{{ $text_last_wishlist }}</span>
        </div>
        <div class="last_right">
            <div class="owl-carousel">
                @foreach($products as $product)
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
                    <div class="flex flex-start mh150">
                        <a href="{{ $product->getSlug() }}" class="relative">
                            <img src="{{ resize_image($product->image, $width, $height) }}" width="{{ $width }}" height="{{ $height }}" alt="{!! $product->name !!}" class="border-24" />
                        </a>
                        <div class="caption relative">
                            <a href="#" class="product_wishlist product_wishlist-{{ $product->id }}" onclick="wishlist.add({{ $product->id }});return false;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="14" viewBox="0 0 17 14" fill="none">
                                    <path d="M15.1668 1.32286C13.4028 -0.440714 10.533 -0.440714 8.76946 1.32286L8.35983 1.73224L7.95045 1.32286C6.18688 -0.440953 3.3169 -0.440953 1.55332 1.32286C-0.174444 3.05063 -0.185664 5.78932 1.5273 7.69349C3.08965 9.42961 7.69742 13.1804 7.89292 13.3392C8.02564 13.4471 8.18534 13.4996 8.34408 13.4996C8.34933 13.4996 8.35458 13.4996 8.3596 13.4993C8.52383 13.507 8.68925 13.4506 8.82627 13.3392C9.02177 13.1804 13.63 9.42961 15.1928 7.69325C16.9056 5.78932 16.8944 3.05063 15.1668 1.32286ZM14.1282 6.73532C12.9101 8.08855 9.56173 10.8795 8.3596 11.8699C7.15746 10.8798 3.80983 8.08903 2.59194 6.73556C1.39697 5.40739 1.38575 3.51587 2.56592 2.3357C3.16866 1.7332 3.96021 1.43171 4.75177 1.43171C5.54332 1.43171 6.33488 1.73296 6.93761 2.3357L7.83802 3.2361C7.9452 3.34328 8.08031 3.40725 8.2221 3.42969C8.45222 3.47911 8.7019 3.41489 8.88093 3.23634L9.78182 2.3357C10.9875 1.13046 12.9488 1.1307 14.1537 2.3357C15.3339 3.51587 15.3227 5.40739 14.1282 6.73532Z" fill="#BED0D6"/>
                                </svg>
                            </a>
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
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>