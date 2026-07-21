<div class="flex-2">
    <div class="post_left">
        <div class="text_product">{!! $count !!}</div>
        @if($products)
            <div class="production overflow-y">
                @foreach($products as $product)
                    <div class="flex mh150">
                        <a href="{{ $product['url'] }}" class="relative">
                            <img src="{{ $product['image'] }}" class="border-24" />
                        </a>
                        <div class="caption relative">
                            <a href="#" class="product_wishlist product_wishlist-{{ $product['id'] }}" onclick="wishlist.add({{ $product['id'] }});return false;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="14" viewBox="0 0 17 14" fill="none">
                                    <path d="M15.1668 1.32286C13.4028 -0.440714 10.533 -0.440714 8.76946 1.32286L8.35983 1.73224L7.95045 1.32286C6.18688 -0.440953 3.3169 -0.440953 1.55332 1.32286C-0.174444 3.05063 -0.185664 5.78932 1.5273 7.69349C3.08965 9.42961 7.69742 13.1804 7.89292 13.3392C8.02564 13.4471 8.18534 13.4996 8.34408 13.4996C8.34933 13.4996 8.35458 13.4996 8.3596 13.4993C8.52383 13.507 8.68925 13.4506 8.82627 13.3392C9.02177 13.1804 13.63 9.42961 15.1928 7.69325C16.9056 5.78932 16.8944 3.05063 15.1668 1.32286ZM14.1282 6.73532C12.9101 8.08855 9.56173 10.8795 8.3596 11.8699C7.15746 10.8798 3.80983 8.08903 2.59194 6.73556C1.39697 5.40739 1.38575 3.51587 2.56592 2.3357C3.16866 1.7332 3.96021 1.43171 4.75177 1.43171C5.54332 1.43171 6.33488 1.73296 6.93761 2.3357L7.83802 3.2361C7.9452 3.34328 8.08031 3.40725 8.2221 3.42969C8.45222 3.47911 8.7019 3.41489 8.88093 3.23634L9.78182 2.3357C10.9875 1.13046 12.9488 1.1307 14.1537 2.3357C15.3339 3.51587 15.3227 5.40739 14.1282 6.73532Z" fill="#BED0D6"/>
                                </svg>
                            </a>
                            <div class="product_name"><a href="{{ $product['url'] }}">{!! $product['name'] !!}</a></div>
                            @if($product['price'])
                                @if($product['discount'])
                                    <div class="flex-2 flex-start">
                                        @endif
                                        <div class="price">
                                            @if($product['special'])
                                                <span class="price_old">{{ $product['price'] }}</span>
                                                <span class="price_new">{{ $product['special'] }}</span>
                                            @else
                                                <span class="price_new">{{ $product['price'] }}</span>
                                            @endif
                                        </div>
                                        @if($product['discount'])
                                            <span class="price_discount">от {{ $product['discount'] }}</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <a href="{{ route(session('lang') . '_search', ['search' => $search]) }}" class="more">
                {{ __('locale.more') }}
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="63" height="8" viewBox="0 0 63 8" fill="none">
                        <path d="M0.5 6.5H61L55.5 1" stroke="#54B0AC" stroke-width="1.5"/>
                    </svg>
                </span>
            </a>
        @endif
    </div>
    <div class="post_right">
        @if($categories)
            <div class="text_product">{{ __('locale.text_search_count') }}</div>
            <ul class="list-un-styled categories_input overflow-y">
                @foreach($categories as $category)
                    <li><a href="#" data-text="{{ strip_tags($category['name']) }}">{!! $category['name'] !!}</a></li>
                @endforeach
            </ul>
        @endif
    </div>
</div>