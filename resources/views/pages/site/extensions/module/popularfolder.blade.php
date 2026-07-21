<div class="popular_folder popular_folder{{ $module }}">
    <div class="h2">{{ $title }}</div>
    @isset($elements)
        <div class="owl-carousel elements">
            @foreach($elements as $element)
                <div class="item">
                    <a href="{{ $element['url'] }}" class="element relative">
                        <div class="element_name">{{ $element['name'] }}</div>
                        @if(!empty($element['text']))
                            <div class="element_text">{{ $element['text'] }}</div>
                        @endif
                        <img src="{{ $element['image'] }}" alt="{{ $element['name'] }}" />
                    </a>
                </div>
            @endforeach
        </div>
    @endif
    @isset($elements2)
        <div class="owl-carousel elements2">
            @foreach($elements2 as $element)
                <div class="item">
                    <a href="{{ $element['url'] }}" class="element relative">
                        <div class="element_name">{{ $element['name'] }}</div>
                        @if(!empty($element['text']))
                            <div class="element_text">{{ $element['text'] }}</div>
                        @endif
                        <img src="{{ $element['image'] }}" alt="{{ $element['name'] }}" />
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>