@extends('layouts.contentLayoutMaster')
@if($id)
    @section('title','Редактирование купона')
@else
    @section('title','Создание купона')
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
                                <ul class="nav nav-tabs nav-fill" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="content" data-toggle="tab" href="#content_tab" role="tab" aria-controls="content_tab" aria-selected="true">
                                            Общие
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="history" data-toggle="tab" href="#history_tab" role="tab" aria-controls="history_tab" aria-selected="true">
                                            История
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content pt-1">
                                    <div class="tab-pane active" id="content_tab" role="tabpanel" aria-labelledby="content">
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Название</label>
                                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Название"
                                                       value="{{ old('name', $name) }}" required>
                                                @error('name')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Код</label>
                                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" placeholder="Код"
                                                       value="{{ old('code', $code) }}" required>
                                                @error('code')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Тип</label>
                                            <select name="type" class="form-control">
                                                <option value="P"{{ old('type', $type) == 'P' ? ' selected' : '' }}>Процент</option>
                                                <option value="F"{{ old('type', $type) == 'F' ? ' selected' : '' }}>Фиксированное число</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Скидка</label>
                                                <input type="text" name="discount" class="form-control @error('discount') is-invalid @enderror" placeholder="Скидка"
                                                       value="{{ old('discount', $discount) }}" required>
                                                @error('discount')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Итого в корзине (после этой суммы купон вступает в силу)</label>
                                                <input type="text" name="total" class="form-control" placeholder="Скидка" value="{{ old('total', $total) }}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label class="required">Вход клиента</label>
                                                <div style="display: flex;align-items: center">
                                                    <div class="radio">
                                                        <input id="rating1" type="radio" name="logged" class="form-control" value="1"{{ old('logged', $logged) == 1 ? ' checked' : '' }}>
                                                        <label for="rating1">Да</label>
                                                    </div>
                                                    <div class="radio" style="margin-left: 6px">
                                                        <input id="rating2" type="radio" name="logged" class="form-control" value="0"{{ old('logged', $logged) == 0 || !old('logged', $logged) ? ' checked' : '' }}>
                                                        <label for="rating2">Нет</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="input-product">Товары</label>
                                            <div class="controls">
                                                <input type="text" name="product" placeholder="Товары" id="input-product" class="form-control">
                                                <div id="coupon-product" class="well well-sm" style="height: 150px; overflow: auto;">
                                                    @foreach($coupon_products as $coupon_product)
                                                        <div id="coupon-product{{ $coupon_product['product_id'] }}">
                                                            <i class="minus">-</i> {{ $coupon_product['name'] }}
                                                            <input type="hidden" name="coupon_product[]" value="{{ $coupon_product['product_id'] }}" />
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="input-product">Категории</label>
                                            <div class="controls">
                                                <input type="text" name="category" placeholder="Категории" id="input-category" class="form-control">
                                                <div id="coupon-category" class="well well-sm" style="height: 150px; overflow: auto;">
                                                    @foreach($coupon_categories as $coupon_category)
                                                        <div id="coupon-category{{ $coupon_category['category_id'] }}">
                                                            <i class="minus">-</i> {{ $coupon_category['name'] }}
                                                            <input type="hidden" name="coupon_category[]" value="{{ $coupon_category['category_id'] }}" />
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Дата начала</label>
                                            <fieldset class="position-relative has-icon-left">
                                                <input type="text" name="date_start" value="{{ old('date_start', $date_start) }}" class="form-control pickadate" placeholder="Дата начала">
                                                <div class="form-control-position">
                                                    <i class="bx bx-calendar"></i>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="form-group">
                                            <label>Дата окончания</label>
                                            <fieldset class="position-relative has-icon-left">
                                                <input type="text" name="date_end" value="{{ old('date_end', $date_end) }}" class="form-control pickadate" placeholder="Дата окончания">
                                                <div class="form-control-position">
                                                    <i class="bx bx-calendar"></i>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Использований на купон</label>
                                                <input type="text" name="uses_total" class="form-control" placeholder="Использований на купон" value="{{ old('uses_total', $uses_total) }}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Использований на клиента</label>
                                                <input type="text" name="uses_customer" class="form-control" placeholder="Использований на клиента" value="{{ old('uses_customer', $uses_customer) }}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Статус</label>
                                            <select name="status" class="form-control">
                                                <option value="1"{{ old('status', $status) == 1 ? ' selected' : '' }}>Включено</option>
                                                <option value="0"{{ old('status', $status) == 0 || !old('status', $status) ? ' selected' : '' }}>Выключено</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="history_tab" role="tabpanel" aria-labelledby="history">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Номер заказа</th>
                                                <th>Покупатель</th>
                                                <th>Сумма</th>
                                                <th>Дата добавления</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(!empty($history))
                                                @foreach($history as $coupon_history)
                                                    <tr>
                                                        <td><a href="{{ url('admin/order/' . $coupon_history['order_id']) }}">#{{ $coupon_history['order_id'] }}</a></td>
                                                        <td><a href="{{ url('admin/customer/' . $coupon_history['customer_id']) }}">#{{ $coupon_history['customer'] }}</a></td>
                                                        <td>{{ $coupon_history['amount'] }}</td>
                                                        <td>{{ date('Y-m-d', \strtotime($coupon_history['created_at'])) }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="4">Нет данных</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
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
        $(document).ready(function () {
            $('.pickadate').pickadate({
                format: 'yyyy-mm-dd'
            });

            $('[name="product"]').autocomplete({
                source: function(request, response) {
                    $.ajax( {
                        url: '{{ asset('admin/product_autocomplete') }}',
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response($.map(data, function(item) {
                                return {
                                    value: item['name'],
                                    id: item['id']
                                }
                            }));
                        }
                    });
                },
                select: function(event, ui) {
                    $('[name="product"]').val('');

                    $('#coupon-product' + ui.item.id).remove();

                    $('#coupon-product').append('<div id="coupon-product' + ui.item.id + '"><i class="minus">-</i> ' + ui.item.value + '<input type="hidden" name="coupon_product[]" value="' + ui.item.id + '" /></div>');
                }
            });

            $('[name="category"]').autocomplete({
                source: function(request, response) {
                    $.ajax( {
                        url: '{{ asset('admin/category_autocomplete') }}',
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                select: function(event, ui) {
                    $('[name="category"]').val('');

                    $('#coupon-category' + ui.item.id).remove();

                    $('#coupon-category').append('<div id="coupon-category' + ui.item.id + '"><i class="minus">-</i> ' + ui.item.value + '<input type="hidden" name="coupon_category[]" value="' + ui.item.id + '" /></div>');
                }
            });

            $('#coupon-product').delegate('.minus', 'click', function() {
                $(this).parent().remove();
            });

            $('#coupon-category').delegate('.minus', 'click', function() {
                $(this).parent().remove();
            });
        });
    </script>
@endsection