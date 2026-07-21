@if(!empty($courier))
    <p style="margin-top: 30px;margin-bottom: 20px">{{ __('locale.text_checkout_17') }}</p>
    <div class="col-12">
        <div class="form_group">
            <input type="text" name="courier[address]" id="account_adres" value="{{ old('fields.shipping.address') }}" class="input" required>
            <label for="account_adres" class="required">{{ __('locale.text_checkout_18') }}</label>
        </div>
    </div>
    <div class="flex flex_m">
        <div class="form_group">
            <input type="text" name="courier[kv]" id="account_kv2" value="{{ old('fields.shipping.kv') }}" class="input" required>
            <label for="account_kv2" class="required">{{ __('locale.text_checkout_8') }}</label>
        </div>
        <div class="form_group">
            <select name="courier[courier]" onchange="set_shipping();" class="selectize" data-text="{{ __('locale.text_checkout_courier') }}">
                @foreach($courier as $t)
                    <option value="{{ str_slug($t['name']) }}"{{ old('fields.shipping.tk') == str_slug($t['name']) ? ' selected' : '' }}>{{ $t['name'] }}</option>
                @endforeach
            </select>
        </div>
        <a href="#" class="btn-default mb20 text-center mt30 col-12" data-target-modal="#maps">{{ __('locale.text_checkout_19') }}</a>
    </div>
    <div class="col-12">
        <div class="form_group">
            <input type="text" name="courier[comment]" id="account_coment2" value="{{ old('fields.shipping.comment') }}" class="input" required>
            <label for="account_coment2" class="required">{{ __('locale.text_checkout_9') }}</label>
        </div>
    </div>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=6b4e01ea-ffd7-46de-b8e1-8000c364d702"></script>
    <script>
        ymaps.ready(init);

        function init() {
            var myPlacemark,
                myMap = new ymaps.Map('map', {
                    center: [55.753994, 37.622093],
                    zoom: 9
                }, {
                    searchControlProvider: 'yandex#search'
                });

            // Слушаем клик на карте.
            myMap.events.add('click', function (e) {
                var coords = e.get('coords');

                // Если метка уже создана – просто передвигаем ее.
                if (myPlacemark) {
                    myPlacemark.geometry.setCoordinates(coords);
                }
                // Если нет – создаем.
                else {
                    myPlacemark = createPlacemark(coords);
                    myMap.geoObjects.add(myPlacemark);
                    // Слушаем событие окончания перетаскивания на метке.
                    myPlacemark.events.add('dragend', function () {
                        getAddress(myPlacemark.geometry.getCoordinates());
                    });
                }
                getAddress(coords);
            });

            // Создание метки.
            function createPlacemark(coords) {
                return new ymaps.Placemark(coords, {
                    iconCaption: '{{ __('locale.text_map_search') }}'
                }, {
                    preset: 'islands#violetDotIconWithCaption',
                    draggable: true
                });
            }

            // Определяем адрес по координатам (обратное геокодирование).
            function getAddress(coords) {
                myPlacemark.properties.set('iconCaption', 'поиск...');
                ymaps.geocode(coords).then(function (res) {
                    var firstGeoObject = res.geoObjects.get(0);

                    myPlacemark.properties
                        .set({
                            // Формируем строку с данными об объекте.
                            iconCaption: [
                                // Название населенного пункта или вышестоящее административно-территориальное образование.
                                firstGeoObject.getLocalities().length ? firstGeoObject.getLocalities() : firstGeoObject.getAdministrativeAreas(),
                                // Получаем путь до топонима, если метод вернул null, запрашиваем наименование здания.
                                firstGeoObject.getThoroughfare() || firstGeoObject.getPremise()
                            ].filter(Boolean).join(', '),
                            // В качестве контента балуна задаем строку с адресом объекта.
                            balloonContent: firstGeoObject.getAddressLine()
                        });

                    $('#account_adres').val(firstGeoObject.getAddressLine())
                });
            }
        }
    </script>
    <div class="modal" id="maps">
        <div class="modal-content" style="padding: 0;border-radius: 25px;overflow: hidden">
            <div id="map" style="width: 600px;height: 400px"></div>
        </div>
    </div>
@endif