<div class="flex">
    <div class="left_cart">
        <div class="cart_adres">
            {!! __('locale.text_checkout_10') !!}
        </div>
        <div class="grafic">
            <div class="flex_grafic">
                {!! __('locale.text_checkout_11') !!}
            </div>
            <div class="flex_grafic">
                {!! __('locale.text_checkout_12') !!}
            </div>
            <div class="flex_grafic">
                {!! __('locale.text_checkout_14') !!}
            </div>
        </div>
        <div class="p_cart">{{ __('locale.text_checkout_15') }}</div>
    </div>
    <div class="right_cart popup-gallery">
        <div id="map2" style="width: 100%; height: 400px; padding: 0; margin: 0"></div>
        <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=b07ff572-e76f-4d53-ab0f-3501f7c39090"></script>
        <script>
            ymaps.ready(function () {
                var myMap = new ymaps.Map('map2', {
                        center: [55.784745, 37.739624],
                        zoom: 18
                    }, {
                        searchControlProvider: 'yandex#search'
                    }),

                    MyIconContentLayout = ymaps.templateLayoutFactory.createClass(
                        '<div style="color: #FFFFFF; font-weight: bold;">$[properties.iconContent]</div>'
                    ),

                    myPlacemark = new ymaps.Placemark(myMap.getCenter(), {
                        hintContent: 'г.Москва, ул. Зверинецкая 44/33',
                        balloonContent: 'г.Москва, ул. Зверинецкая 44/33'
                    }, {
                        iconLayout: 'default#image',
                        iconImageHref: 'images/myIcon.gif',
                        iconImageSize: [30, 42],
                        iconImageOffset: [-5, -38]
                    }),

                    myPlacemarkWithContent = new ymaps.Placemark([55.784745, 37.739624], {
                        hintContent: 'г.Москва, ул. Зверинецкая 44/33',
                        balloonContent: 'г.Москва, ул. Зверинецкая 44/33',
                        iconContent: '12'
                    }, {
                        iconLayout: 'default#imageWithContent',
                        iconImageHref: '/public/storage/photos/1/favicon.ico',
                        iconImageSize: [48, 48],
                        iconImageOffset: [-24, -24],
                        iconContentOffset: [15, 15],
                        iconContentLayout: MyIconContentLayout
                    });

                myMap.geoObjects
                    .add(myPlacemark)
                    .add(myPlacemarkWithContent);
            });
        </script>
        <span>{{ __('locale.text_checkout_16') }}</span>
    </div>
</div>