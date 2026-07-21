<div class="news news-{{ $module }}">
    <div class="container">
        <div class="row">
            <div class="flex">
                <div class="title">{{ __('locale.v4') }}</div>
                @if(count($news) > 3)
                    <div class="arrows flex">
                        <a href="#" class="prev">
                            <svg width="18" height="12" viewBox="0 0 18 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.469669 6.53033C0.176777 6.23744 0.176777 5.76256 0.469669 5.46967L5.24264 0.6967C5.53553 0.403807 6.01041 0.403807 6.3033 0.6967C6.59619 0.989594 6.59619 1.46447 6.3033 1.75736L2.06066 6L6.3033 10.2426C6.59619 10.5355 6.59619 11.0104 6.3033 11.3033C6.01041 11.5962 5.53553 11.5962 5.24264 11.3033L0.469669 6.53033ZM18 6.75L1 6.75L1 5.25L18 5.25L18 6.75Z" fill="#CB8734"/>
                            </svg>
                        </a>
                        <span>1/{{ count($news) }}</span>
                        <a href="#" class="next">
                            <svg width="18" height="12" viewBox="0 0 18 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.5303 6.53033C17.8232 6.23744 17.8232 5.76256 17.5303 5.46967L12.7574 0.6967C12.4645 0.403807 11.9896 0.403807 11.6967 0.6967C11.4038 0.989594 11.4038 1.46447 11.6967 1.75736L15.9393 6L11.6967 10.2426C11.4038 10.5355 11.4038 11.0104 11.6967 11.3033C11.9896 11.5962 12.4645 11.5962 12.7574 11.3033L17.5303 6.53033ZM-6.55671e-08 6.75L17 6.75L17 5.25L6.55671e-08 5.25L-6.55671e-08 6.75Z" fill="#CB8734"/>
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
            <div class="owl-carousel">
                @foreach($news as $n)
                    <div class="item">
                        <div class="image">
                            @if(!empty($n['category']))
                                <div class="label">{{ $n['category'] }}</div>
                            @endif
                            <a href="{{ $n['url'] }}"><img src="{{ $n['image'] }}" alt="{{ $n['name'] }}" /></a>
                        </div>
                        <a href="{{ $n['url'] }}" class="caption_name">{{ $n['name'] }}</a>
                        @if(!empty($n['short']))
                            <div class="caption">{{ $n['short'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>