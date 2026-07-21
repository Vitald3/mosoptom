<div class="slider slider-{{ $module }}">
    <div class="owl-carousel">
        @foreach($sliders as $slider)
            <div class="item relative">
                @if(isset($slider['html']))
                    {{ $slider['html'] }}
                @else
                    <div class="slider_info">
                        @if(!empty($slider['title']) && !empty($slider['text']))
                            <div class="slider_description">
                                @if(!empty($slider['title']))
                                    <div class="slider_title">{{ $slider['title'] }}</div>
                                @endif
                                @if(!empty($slider['text']))
                                    <div class="slider_text">{{ $slider['text'] }}</div>
                                @endif
                            </div>
                        @endif
                        @if((!empty($slider['button']) && !empty($slider['button_href'])) || (!empty($slider['a']) && !empty($slider['a_href'])))
                            <div class="buttons">
                                @if(!empty($slider['button']) && !empty($slider['button_href']))
                                    <a class="slider_button" href="{{ $slider['button_href'] }}">{{ $slider['button'] }}</a>
                                @endif
                                @if(!empty($slider['a']) && !empty($slider['a_href']))
                                    <div><a class="slider_link" href="{{ $slider['a_href'] }}">{{ $slider['a'] }}</a></div>
                                @endif
                            </div>
                        @endif
                    </div>
                    <img class="slider_img" src="{{ $slider['image'] }}" alt="{{ !empty($slider['title']) ? $slider['title'] : ' ' }}" />
                @endif
            </div>
        @endforeach
    </div>
</div>