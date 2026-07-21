@extends('layouts.contentLayoutMaster')
@if($id)
    @section('title','Редактирование заказа')
@else
    @section('title','Создание заказа')
@endif
@section('vendor-styles')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/pickadate.css') }}">
@endsection

@section('content')
    <section class="users-edit">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    @if(session('error'))
                        <span class="btn btn-danger invalid-feedback" role="alert" style="margin-bottom:20px;display: block"><strong>{{ session('error') }}</strong></span>
                    @endif
                    @if($errors->all())
                        <span class="btn btn-danger invalid-feedback" role="alert" style="margin-bottom:20px;display: block"><strong>Проверьте форму на наличие ошибок</strong></span>
                    @endif
                    <form action="{{ $action }}" method="post" novalidate>
                        @csrf
                        @if($id)
                            <input type="hidden" name="id" value="{{ $id }}" />
                        @endif
                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <ul class="nav nav-tabs nav-fill" id="myTab2" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="content" data-toggle="tab" href="#content_tab" role="tab" aria-controls="content_tab" aria-selected="true">
                                            Данные клиента
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="products" data-toggle="tab" href="#products_tab" role="tab" aria-controls="products_tab" aria-selected="true">
                                            Товары
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="shippings" data-toggle="tab" href="#shippings_tab" role="tab" aria-controls="shippings_tab" aria-selected="true">
                                            Адрес и доставка
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="totals" data-toggle="tab" href="#totals_tab" role="tab" aria-controls="totals_tab" aria-selected="true">
                                            Итого
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content pt-1">
                                    <div class="tab-pane active" id="content_tab" role="tabpanel" aria-labelledby="content">
                                        <div class="form-group">
                                            <div class="controls">
                                                <label class="required">Валюта</label>
                                                <select name="currency_code" class="form-control" onchange="update_currency(this.value);">
                                                    <option value="">Выберите валюту</option>
                                                    @foreach($currencies as $currency)
                                                        <option value="{{ $currency['code'] }}"{{ $currency['code'] == old('currency_code', $currency_code) ? ' selected' : '' }}>{{ $currency['title'] }}</option>
                                                    @endforeach
                                                </select>
                                                @error('currency_code')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Клиент</label>
                                                <input type="text" name="customer" class="form-control" placeholder="Клиент" value="{{ old('customer', $customer) }}">
                                                <input type="hidden" name="customer_id" value="{{ old('customer_id', $customer_id) }}" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Группа клиентов</label>
                                                <select name="customer_group_id" class="form-control">
                                                    <option value="">Выберите группу клиентов</option>
                                                    @foreach($customer_groups as $customer_group)
                                                        <option value="{{ $customer_group['id'] }}"{{ old('customer_group_id', $customer_group_id) == $customer_group['id'] ? ' selected' : '' }}>{{ $customer_group['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls flex">
                                                <div class="radio">
                                                    <input type="radio" onchange="$('.type1').fadeOut();" id="individ" name="type" class="form-control" value="0"{!! !$type ? ' checked' : '' !!} />
                                                    <label for="individ">Физическое лицо</label>
                                                </div>
                                                <div class="radio">
                                                    <input type="radio" onchange="$('.type1').fadeIn();" id="uri" name="type" class="form-control" value="1"{!! $type == 1 ? ' checked' : '' !!} />
                                                    <label for="uri">Юридическое лицо</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group type1"{!! !$type ? ' style="display: none"' : '' !!}>
                                            <div class="controls">
                                                <label>Компания</label>
                                                <input type="text" name="company" class="form-control" placeholder="Компания" value="{{ old('company', $company) }}" />
                                            </div>
                                        </div>
                                        <div class="form-group type1"{!! !$type ? ' style="display: none"' : '' !!}>
                                            <div class="controls">
                                                <label>ИНН</label>
                                                <input type="text" name="inn" class="form-control" placeholder="ИНН" value="{{ old('inn', $inn) }}" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Имя</label>
                                                <input type="text" name="firstname" class="form-control @error('firstname') is-invalid @enderror" placeholder="Имя"
                                                       value="{{ old('firstname', $firstname) }}" required>
                                                @error('firstname')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Фамилия</label>
                                                <input type="text" name="lastname" class="form-control @error('lastname') is-invalid @enderror" placeholder="Фамилия"
                                                       value="{{ old('lastname', $lastname) }}" required>
                                                @error('lastname')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Email</label>
                                                <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email"
                                                       value="{{ old('email', $email) }}" required>
                                                @error('email')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Телефон</label>
                                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Телефон"
                                                       value="{{ old('phone', $phone) }}" required>
                                                @error('phone')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="products_tab" role="tabpanel" aria-labelledby="products">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Товар</th>
                                                <th>Модель</th>
                                                <th>Количество</th>
                                                <th>Цена</th>
                                                <th>Всего</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody id="add_products">
                                            @if(!empty($products))
                                                @foreach($products as $product_row => $product)
                                                    <tr id="product_row{{ $product_row }}">
                                                        <td>
                                                            {{ $product['name'] }}<br />
                                                            <input type="hidden" name="product[{{ $product_row }}][product_id]" value="{{ $product['product_id'] }}" />
                                                            @foreach($product['options'] as $option)
                                                                - <small>{{ $option['name'] }}: {{ $option['value'] }}</small><br />
                                                                @if(in_array($option['type'], ['select', 'radio', 'color']))
                                                                    <input type="hidden" name="product[{{ $product_row }}][option][{{ $option['option_id'] }}][{{ $option['product_option_id'] }}]" value="{{ $option['product_option_value_id'] }}" />
                                                                @endif
                                                                @if($option['type'] === 'checkbox')
                                                                    <input type="hidden" name="product[{{ $product_row }}][option][{{ $option['option_id'] }}][{{ $option['product_option_id'] }}][]" value="{{ $option['product_option_value_id'] }}" />
                                                                @endif
                                                                @if(in_array($option['type'], ['text', 'textarea', 'date', 'datetime', 'time']))
                                                                    <input type="hidden" name="product[{{ $product_row }}][option][{{ $option['option_id'] }}][{{ $option['product_option_id'] }}]" value="{{ $option['value'] }}" />
                                                                @endif
                                                            @endforeach
                                                        </td>
                                                        <td>{{ $product['model'] }}</td>
                                                        <td><input onchange="cart_update();" type="number" name="product[{{ $product_row }}][quantity]" value="{{ $product['quantity'] }}" placeholder="Количество" class="form-control" /></td>
                                                        <td>{{ $product['price'] }}</td>
                                                        <td>{{ $product['total'] }}</td>
                                                        <td><a href="#" class="btn btn-danger" onclick="$('#product_row{{ $product_row }}').remove();cart_update();return false;">&times;</a></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                        <hr>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Выберите товар</label>
                                                <input type="text" name="product" class="form-control" placeholder="Выберите товар" value="{{ old('product') }}">
                                                <input type="hidden" name="product_id" value="{{ old('product_id') }}" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Количество</label>
                                                <input type="text" name="quantity" class="form-control" placeholder="Количество" value="{{ old('quantity', 1) }}">
                                            </div>
                                        </div>
                                        <div id="option"></div>
                                        <div class="form-group">
                                            <a href="#" class="btn btn-primary add_product">Добавить товар</a>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="shippings_tab" role="tabpanel" aria-labelledby="shippings">
                                        <div class="form-group">
                                            <label class="required">Метод доставки</label>
                                            <div class="input-group">
                                                <select name="shipping_method" class="form-control" onchange="shipping_save()">
                                                    <option value="">Выберите</option>
                                                    @foreach($shipping_methods as $sm)
                                                        <optgroup label="{{ $sm['title'] }}">
                                                            @if($sm['quote'])
                                                                @foreach($sm['quote'] as $quote)
                                                                    <option value="{{ $sm['code'] . '.' . $quote['code'] }}"{{ old('shipping_method', $shipping_method) == $sm['code'] . '.' . $quote['code'] ? ' selected' : '' }}>{{ $quote['name'] }}</option>
                                                                @endforeach
                                                            @else
                                                                <option value="{{ $sm['code'] }}"{{ old('shipping_method', $shipping_method) == $sm['code'] ? ' selected' : '' }}>{{ $sm['title'] }}</option>
                                                            @endif
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('shipping_method')
                                            <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Адрес (Страна, город, улица, дом)</label>
                                                <input type="text" name="fields[address]" class="form-control" placeholder="Адрес (Страна, город, улица, дом)" value="{{ old('fields.address', !empty($fields['address']) ? $fields['address'] : '') }}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Квартира или офис</label>
                                                <input type="text" name="fields[kv]" class="form-control" placeholder="Квартира или офис" value="{{ old('fields.kv', !empty($fields['kv']) ? $fields['kv'] : '') }}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Комментарий к доставке</label>
                                                <input type="text" name="fields[comment]" class="form-control" placeholder="Комментарий к доставке" value="{{ old('fields.comment', !empty($fields['comment']) ? $fields['comment'] : '') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="totals_tab" role="tabpanel" aria-labelledby="totals">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Товар</th>
                                                <th>Модель</th>
                                                <th>Количество</th>
                                                <th>Цена</th>
                                                <th>Всего</th>
                                            </tr>
                                            </thead>
                                            <tbody id="total">
                                            @foreach($products as $product)
                                                <tr>
                                                    <td>
                                                        {{ $product['name'] }}<br />
                                                        @foreach($product['options'] as $option)
                                                            - <small>{{ $option['name'] }}: {{ $option['value'] }}</small><br />
                                                        @endforeach
                                                    </td>
                                                    <td>{{ $product['model'] }}</td>
                                                    <td>{{ $product['quantity'] }}</td>
                                                    <td>{{ $product['price'] }}</td>
                                                    <td>{{ $product['total'] }}</td>
                                                </tr>
                                            @endforeach
                                            @foreach($totals as $total)
                                                <tr id="{{ $total['code'] }}">
                                                    <td colspan="4">{{ $total['title'] }}</td>
                                                    <td>{{ $total['value'] }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <hr>
                                        <legend>Детали заказа</legend>
                                        <div class="form-group">
                                            <label class="required">Метод оплаты</label>
                                            <div class="input-group">
                                                <select name="payment_method" class="form-control" onchange="payment_save()">
                                                    <option value="">Выберите</option>
                                                    @foreach($payment_methods as $pm)
                                                        <option value="{{ $pm['code'] }}"{{ old('payment_method', $payment_method) == $pm['code'] ? ' selected' : '' }}>{{ $pm['title'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('payment_method')
                                            <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Купон</label>
                                                <div class="input-group">
                                                    <input type="text" name="coupon" class="form-control" placeholder="Купон" value="{{ old('coupon', $coupon) }}">
                                                    <a href="#" class="btn btn-primary" onclick="coupon_save();return false;">Применить</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Баллы</label>
                                                <div class="input-group">
                                                    <input type="text" name="reward" class="form-control" placeholder="Баллы" value="{{ old('reward', $reward) }}">
                                                    <a href="#" class="btn btn-primary" onclick="reward_save();return false;">Применить</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Комментарий</label>
                                                <textarea name="comment" class="form-control" placeholder="Комментарий">{{ old('comment', $comment) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <div class="checkbox">
                                                    <input type="checkbox" name="notify" id="notify" value="1" />
                                                    <label for="notify">Уведомить клиента</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="required">Статус заказа</label>
                                            <select name="order_status_id" class="form-control">
                                                <option value="">Выберите статус</option>
                                                @foreach($statuses as $s)
                                                    <option value="{{ $s['id'] }}"{{ old('order_status_id', $order_status_id) == $s['id'] ? ' selected' : '' }}>{{ $s['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('order_status_id')
                                            <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                @role('edit|create')
                                <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Сохранить</button>
                                @endrole
                                <a href="{{ url()->previous() }}" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Назад</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page-scripts')
    <script src="{{asset('assets/admin/js/picker.js')}}"></script>
    <script src="{{asset('assets/admin/js/picker.date.js')}}"></script>
    <script src="{{asset('assets/admin/js/picker.time.js')}}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        function getProducts(json) {
            var html = '';

            for (var i in json) {
                html += '<tr id="product_row' + i + '">';
                html += '  <td>';
                html += json[i]['name'] + '<br />';
                html += '<input type="hidden" name="product[' + i + '][product_id]" value="' + json[i]['product_id'] + '" />';

                for(var j in json[i]['options']) {
                    var option = json[i]['options'][j];

                    html += ' - <small>' + option['name'] + ': ' + option['value'] + '</small><br />';

                    if (option['type'] === 'select' || option['type'] === 'radio' || option['type'] === 'color') {
                        html += ' <input type="hidden" name="product[' + i + '][option][' + option['option_id'] + '][' + option['product_option_id'] + ']" value="' + option['product_option_value_id'] + '" />';
                    }

                    if (option['type'] === 'checkbox') {
                        html += '<input type="hidden" name="product[' + i + '][option][' + option['option_id'] + '][' + option['product_option_id'] + '][]" value="' + option['product_option_value_id'] + '" />';
                    }

                    if (option['type'] === 'text' || option['type'] === 'textarea' || option['type'] === 'date' || option['type'] === 'datetime' || option['type'] === 'time') {
                        html += '<input type="hidden" name="product[' + i + '][option][' + option['option_id'] + '][' + option['product_option_id'] + ']" value="' + option['value'] + '" />';
                    }
                }

                html +='</td>';
                html +='<td>' + json[i]['model'] + '</td>';
                html +='<td><input onchange="cart_update();" type="number" name="product[' + i + '][quantity]" value="' + json[i]['quantity'] + '" placeholder="Количество" class="form-control" /></td>';
                html +='<td>' + json[i]['price'] + '</td>';
                html +='<td>' + json[i]['total'] + '</td>';
                html +='<td><a href="#" class="btn btn-danger" onclick="$(\'#product_row' + i + '\').remove();cart_update();return false;">&times;</a></td>';
                html +='</tr>';
            }

            $('#add_products').html(html);
        }

        function cart_update() {
            var product = [];

            $('#add_products input').each(function(){
                product.push($(this).attr('name') + '=' + $(this).val())
            });

            $.ajax({
                url: '{{ route('api_cart_add') }}',
                type: 'post',
                dataType: 'json',
                data: '_token={{ csrf_token() }}' + '&' + product.join('&'),
                success: function (json) {
                    update_methods();
                    getProducts(json);
                }
            });
        }

        function getTotals(json) {
            var html = '';

            for(var i in json['products']) {
                html += '<tr>';
                html += '  <td>' + json['products'][i]['name'] + '<br />';

                for (var j in json['products'][i]['options']) {
                    html += '- <small>' + json['products'][i]['options'][j]['name'] + ': ' + json['products'][i]['options'][j]['value'] + '</small><br/>';
                }

                html += '</td>';
                html += '<td>' + json['products'][i]['model'] + '</td>';
                html += '<td>' + json['products'][i]['quantity'] + '</td>';
                html += '<td>' + json['products'][i]['price'] + '</td>';
                html += '<td>' + json['products'][i]['total'] + '</td>';
                html += '</tr>';
            }

            for (var i in json['totals']) {
                html += '<tr>';
                html += '<td colspan="4">' + json['totals'][i]['title'] + '</td>';
                html += '<td>' + json['totals'][i]['value'] + '</td>';
                html += '</tr>';
            }

            $('#total').html(html);
        }

        function coupon_save() {
            var coupon = $('input[name="coupon"]').val();

            $.ajax({
                url: '{{ route('api_coupon') }}',
                type: 'post',
                dataType: 'json',
                data: '_token={{ csrf_token() }}' + '&coupon=' + coupon,
                success: function (json) {
                    $('form .invalid-feedback, .coupon_alert').remove();

                    if (json.error) {
                        $('input[name="coupon"]').parent().after('<span class="btn btn-danger invalid-feedback" role="alert" style="display: block"><strong>' + json.error + '</strong></span>');
                    } else {
                        $('input[name="coupon"]').parent().after(' <div class="coupon_alert alert alert-success" role="alert" style="margin-top: 5px"><strong>Купон успешно применен</strong></div>');

                        setTimeout(function(){
                            $('.coupon_alert').remove();
                        }, 3000);

                        getTotals(json);
                    }
                }
            });
        }

        function reward_save() {
            var reward = $('input[name="reward"]').val();

            $.ajax({
                url: '{{ route('api_reward') }}',
                type: 'post',
                dataType: 'json',
                data: '_token={{ csrf_token() }}' + '&reward=' + reward,
                success: function (json) {
                    $('form .invalid-feedback, .reward_alert').remove();

                    if (json.error) {
                        $('input[name="reward"]').parent().after('<span class="btn btn-danger invalid-feedback" role="alert" style="display: block"><strong>' + json.error + '</strong></span>');
                    } else {
                        $('input[name="reward"]').parent().after(' <div class="reward_alert alert alert-success" role="alert" style="margin-top: 5px"><strong>Баллы успешно применены</strong></div>');

                        setTimeout(function(){
                            $('.reward_alert').remove();
                        }, 3000);

                        getTotals(json);
                    }
                }
            });
        }

        function shipping_save() {
            var shipping_method = $('select[name="shipping_method"]').val();

            $.ajax({
                url: '{{ route('api_shipping') }}',
                type: 'post',
                dataType: 'json',
                data: '_token={{ csrf_token() }}' + '&shipping_method=' + shipping_method,
                success: function (json) {
                    $('form .invalid-feedback, .ship_alert').remove();

                    if (json.error) {
                        $('select[name="shipping_method"]').parent().after('<span class="btn btn-danger invalid-feedback" role="alert" style="display: block"><strong>' + json.error + '</strong></span>');
                    } else {
                        $('select[name="shipping_method"]').parent().after(' <div class="ship_alert alert alert-success" role="alert" style="margin-top: 5px"><strong>Метод доставки успешно изменен</strong></div>');

                        setTimeout(function(){
                            $('.ship_alert').remove();
                        }, 3000);

                        getTotals(json);
                    }
                }
            });
        }

        function payment_save() {
            var payment_method = $('select[name="payment_method"]').val();

            $.ajax({
                url: '{{ route('api_payment') }}',
                type: 'post',
                dataType: 'json',
                data: '_token={{ csrf_token() }}' + '&payment_method=' + payment_method,
                success: function (json) {
                    $('form .invalid-feedback, .pay_alert').remove();

                    if (json.error) {
                        $('select[name="payment_method"]').parent().after('<span class="btn btn-danger invalid-feedback" role="alert" style="display: block"><strong>' + json.error + '</strong></span>');
                    } else {
                        $('select[name="payment_method"]').parent().after(' <div class="pay_alert alert alert-success" role="alert" style="margin-top: 5px"><strong>Метод оплаты успешно изменен</strong></div>');

                        setTimeout(function(){
                            $('.pay_alert').remove();
                        }, 3000);
                    }
                }
            });
        }

        function update_methods() {
            $.post('{{ route('api_methods') }}', '&_token={{ csrf_token() }}', function() {
                shipping_save();
                payment_save();
            });
        }

        function update_currency(val) {
            if (val) {
                $.post('{{ route('api_currency') }}', '&_token={{ csrf_token() }}&currency_code=' + val, function () {
                    cart_update();
                });
            }
        }

        @if($id)
        if ($('#add_products input').length) cart_update();
        if ($('input[name="coupon"]').val() && !$('#coupon').length) coupon_save();
        if ($('input[name="reward"]').val() && !$('#reward').length) reward_save();
        if ($('select[name="payment_method"]').val() && !$('#payment').length) payment_save();
        @endif

        $(document).on('click', '.add_product', function(){
            var product_id = $('[name="product_id"]').val();
            var quantity = $('[name="quantity"]').val();
            var option = [];
            $('.text-danger').remove();

            if (product_id !== '') {
                if ($('#option').text()) {
                    $('#option input[type="text"], #option input[type="hidden"], #option input[type="number"], #option input[type="radio"]:checked, #option input[type="checkbox"]:checked, #option select, #option textarea').each(function(){
                        option.push($(this).attr('name') + '=' + $(this).val());
                    });

                    if (option) option = '&' + option.join('&');
                }

                if (!option) option = '';

                $.ajax({
                    url: '{{ route('api_cart_add') }}',
                    type: 'post',
                    dataType: 'json',
                    data: 'product_id=' + product_id + '&quantity=' + quantity + option + '&_token={{ csrf_token() }}',
                    success: function (json) {
                        if (!json.error) {
                            getProducts(json);

                            $('#option').html('');
                            $('[name="product"]').val('');
                            $('[name="product_id"]').val('');
                            $('[name="quantity"]').val('');
                        } else {
                            for (i in json.error.option) {
                                $('#input-option' + i).after('<div class="text-danger">' + json.error.option[i] + '</div>');
                            }
                        }
                    }
                });
            } else {
                alert('Выберите товар')
            }

            return false;
        });

        $(document).ready(function () {
            $('[name="product"]').autocomplete({
                source: function(request, response) {
                    $.ajax( {
                        url: '{{ asset('admin/product_autocomplete') }}',
                        dataType: "json",
                        data: {
                            term: request.term,
                            option: 1
                        },
                        success: function(data) {
                            response($.map(data, function(item) {
                                return {
                                    value: item['name'],
                                    id: item['id'],
                                    option: (typeof item['product_option'] !== "undefined" ? item['product_option'] : false)
                                }
                            }));
                        }
                    });
                },
                select: function(event, ui) {
                    $('[name="product"]').val(ui.item.value);
                    $('[name="product_id"]').val(ui.item.id);

                    if (ui.item.option) {
                        html  = '<fieldset>';
                        html += '  <legend>Выберите опцию</legend>';

                        for (i = 0; i < ui.item.option.length; i++) {
                            var option = ui.item.option[i];

                            if (option['type'] === 'select') {
                                html += '<div class="form-group">';
                                html += '  <label class="' + (option['required'] ? ' required' : '') + '" for="input-option' + option['id'] + '">' + option['name'] + '</label>';
                                html += '  <div class="controls">';
                                html += '    <select name="option[' + option['option_id'] + '][' + option['id'] + ']" id="input-option' + option['id'] + '" class="form-control">';
                                html += '      <option value="">Выбрать</option>';

                                for (j = 0; j < option['product_option_values'].length; j++) {
                                    var option_value = option['product_option_values'][j];

                                    html += '<option value="' + option_value['id'] + '">' + option_value['name'];

                                    if (option_value['price']) {
                                        html += ' (' + option_value['price'] + ')';
                                    }

                                    html += '</option>';
                                }

                                html += '    </select>';
                                html += '  </div>';
                                html += '</div>';
                            }

                            if (option['type'] === 'radio' || option['type'] === 'color') {
                                html += '<div class="form-group">';
                                html += '  <label class="' + (option['required'] ? ' required' : '') + '" for="input-option' + option['id'] + '">' + option['name'] + '</label>';
                                html += '  <div class="controls">';
                                html += '    <select name="option[' + option['option_id'] + '][' + option['id'] + ']" id="input-option' + option['id'] + '" class="form-control">';
                                html += '      <option value="">Выбрать</option>';

                                for (j = 0; j < option['product_option_values'].length; j++) {
                                    var option_value = option['product_option_values'][j];

                                    html += '<option value="' + option_value['id'] + '">' + option_value['name'];

                                    if (option_value['price']) {
                                        html += ' (' + option_value['price'] + ')';
                                    }

                                    html += '</option>';
                                }

                                html += '    </select>';
                                html += '  </div>';
                                html += '</div>';
                            }

                            if (option['type'] === 'checkbox') {
                                html += '<div class="form-group">';
                                html += '  <label class="' + (option['required'] ? ' required' : '') + '">' + option['name'] + '</label>';
                                html += '  <div class="controls" id="input-option' + option['id'] + '">';

                                for (j = 0; j < option['product_option_values'].length; j++) {
                                    var option_value = option['product_option_values'][j];

                                    html += '<div class="checkbox">';

                                    html += '  <input type="checkbox" name="option[' + option['option_id'] + '][' + option['id'] + '][]" id="input-option' + option['id'] + '-' + option_value['id'] + '" value="' + option_value['id'] + '" /> <label style="margin-left: 0;padding-left: 30px;margin-right: 30px" for="input-option' + option['id'] + '-' + option_value['id'] + '">' + option_value['name'] + '</label>';

                                    if (option_value['price']) {
                                        html += ' (' + option_value['price'] + ')';
                                    }

                                    html += '  </label>';
                                    html += '</div>';
                                }

                                html += '  </div>';
                                html += '</div>';
                            }

                            if (option['type'] === 'text') {
                                html += '<div class="form-group">';
                                html += '  <label class="' + (option['required'] ? ' required' : '') + '" for="input-option' + option['id'] + '">' + option['name'] + '</label>';
                                html += '  <div class="controls"><input type="text" name="option[' + option['option_id'] + '][' + option['id'] + ']" value="' + option['value'] + '" id="input-option' + option['id'] + '" class="form-control" /></div>';
                                html += '</div>';
                            }

                            if (option['type'] === 'textarea') {
                                html += '<div class="form-group">';
                                html += '  <label class="' + (option['required'] ? ' required' : '') + '" for="input-option' + option['id'] + '">' + option['name'] + '</label>';
                                html += '  <div class="controls"><textarea name="option[' + option['option_id'] + '][' + option['id'] + ']" rows="5" id="input-option' + option['id'] + '" class="form-control">' + option['value'] + '</textarea></div>';
                                html += '</div>';
                            }

                            if (option['type'] === 'date') {
                                html += '<div class="form-group">';
                                html += '  <label class="' + (option['required'] ? ' required' : '') + '" for="input-option' + option['id'] + '">' + option['name'] + '</label>';
                                html += '  <fieldset class="position-relative has-icon-left">';
                                html += '    <input type="text" name="option[' + option['option_id'] + '][' + option['id'] + ']" class="form-control pickadate" placeholder="' + option['name'] + '" id="input-option' + option['id'] + '" >';
                                html += '    <div class="form-control-position">';
                                html += '    <i class="bx bx-calendar"></i>';
                                html += '    </div>';
                                html += '  </fieldset>';
                                html += '</div>';
                            }

                            if (option['type'] === 'datetime') {
                                html += '<div class="form-group">';
                                html += '  <label class="' + (option['required'] ? ' required' : '') + '" for="input-option' + option['id'] + '">' + option['name'] + '</label>';
                                html += '  <fieldset class="position-relative has-icon-left">';
                                html += '    <input type="text" name="option[' + option['option_id'] + '][' + option['id'] + ']" class="form-control pickadatetime" placeholder="' + option['name'] + '" id="input-option' + option['id'] + '" >';
                                html += '    <div class="form-control-position">';
                                html += '    <i class="bx bx-calendar"></i>';
                                html += '    </div>';
                                html += '  </fieldset>';
                                html += '</div>';
                            }

                            if (option['type'] === 'time') {
                                html += '<div class="form-group">';
                                html += '  <label class="' + (option['required'] ? ' required' : '') + '" for="input-option' + option['id'] + '">' + option['name'] + '</label>';
                                html += '  <fieldset class="position-relative has-icon-left">';
                                html += '    <input type="text" name="option[' + option['option_id'] + '][' + option['id'] + ']" class="form-control pickatime" placeholder="' + option['name'] + '" id="input-option' + option['id'] + '" >';
                                html += '    <div class="form-control-position">';
                                html += '    <i class="bx bx-calendar"></i>';
                                html += '    </div>';
                                html += '  </fieldset>';
                                html += '</div>';
                            }
                        }

                        html += '</fieldset>';

                        $('#option').html(html);

                        $('.pickadate').pickadate({
                            format: 'yyyy-mm-dd'
                        });

                        $('.pickatime').pickatime({
                            format: 'T!ime selected: h:i a',
                            formatLabel: 'HH:i a',
                            formatSubmit: 'HH:i',
                            hiddenPrefix: 'prefix__',
                            hiddenSuffix: '__suffix'
                        });

                        $('.pickadatetime').pickadate({
                            format: 'yyyy-mm-dd HH:i'
                        });
                    } else {
                        $('#option').html('');
                    }
                }
            });

            $('[name="customer"]').autocomplete({
                source: function(request, response) {
                    $.ajax( {
                        url: '{{ asset('admin/customer_autocomplete') }}',
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response($.map(data, function(item) {
                                return {
                                    value: item['customer'],
                                    id: item['id'],
                                    email: item['email'],
                                    phone: item['id'],
                                    firstname: item['lastname'],
                                    lastname: item['lastname'],
                                    customer_group_id: item['customer_group_id']
                                }
                            }));
                        }
                    });
                },
                select: function(event, ui) {
                    $('[name="customer"]').val(ui.item.value);
                    $('[name="customer_id"]').val(ui.item.id);
                    $('[name="email"]').val(ui.item.email);
                    $('[name="phone"]').val(ui.item.phone);
                    $('[name="firstname"]').val(ui.item.firstname);
                    $('[name="lastname"]').val(ui.item.lastname);
                    $('[name="customer_group_id"]').val(ui.item.customer_group_id);
                }
            });
        });
    </script>
@endsection