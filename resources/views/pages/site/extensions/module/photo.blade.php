<div class="photo photo-{{ $module }}">
    <div class="container">
        <div class="row">
            <div class="flex">
                <div class="title">{{ $title }}</div>
            </div>
            <div class="owl-carousel popup-gallery">
                @foreach($images as $image)
                    <div class="item bor">
                        <a href="{{ $image['popup'] }}"><img src="{{ $image['image'] }}" alt="{{ $image['alt'] }}" title="{{ $image['alt'] }}" /></a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>