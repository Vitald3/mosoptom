@extends('layouts.contentLayoutMaster')
@if($id)
    @section('title','Редактирование возврата')
@else
    @section('title','Создание возврата')
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
                                                <label>Номер заказа</label>
                                                <input type="text" name="order_id" class="form-control @error('order_id') is-invalid @enderror" placeholder="Номер заказа"
                                                       value="{{ old('order_id', $order_id) }}" required />
                                                @error('order_id')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="users-list-role">Дата</label>
                                            <fieldset class="form-group position-relative has-icon-left">
                                                <input type="text" name="updated_at" class="form-control pickadate" placeholder="Дата" value="{{ old('updated_at', date('Y-m-d', \strtotime($updated_at))) }}">
                                                <div class="form-control-position">
                                                    <i class='bx bx-calendar'></i>
                                                </div>
                                            </fieldset>
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
                                                <label>Имя</label>
                                                <input type="text" name="firstname" class="form-control @error('firstname') is-invalid @enderror" placeholder="Имя"
                                                       value="{{ old('firstname', $firstname) }}" required />
                                                @error('firstname')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Фамилия</label>
                                                <input type="text" name="lastname" class="form-control @error('lastname') is-invalid @enderror" placeholder="Фамилия"
                                                       value="{{ old('lastname', $lastname) }}" required />
                                                @error('lastname')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Email</label>
                                                <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email"
                                                       value="{{ old('email', $email) }}" required />
                                                @error('email')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Телефон</label>
                                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Телефон"
                                                       value="{{ old('phone', $phone) }}" required />
                                                @error('phone')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <h3>Информация о товаре</h3>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Товар</label>
                                                <input type="text" name="product" class="form-control @error('product_id') is-invalid @enderror" placeholder="Товар"
                                                       value="{{ old('product', $product) }}" required />
                                                <input type="hidden" name="product_id" value="{{ old('product_id', $product_id) }}" />
                                                @error('product_id')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Модель</label>
                                                <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" placeholder="Товар"
                                                       value="{{ old('model, $model') }}" required />
                                                @error('model')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Количество</label>
                                                <input type="text" name="quantity" class="form-control" placeholder="Количество" value="{{ old('quantity', $quantity) }}" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Комментарий</label>
                                                <textarea name="comment" class="form-control" placeholder="Комментарий">{{ old('comment', $comment) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Статус возврата</label>
                                            <select name="status" class="form-control">
                                                <option value="">Выберите статус</option>
                                                @foreach($statuses as $s)
                                                    <option value="{{ $s['id'] }}"{{ old('status', $status) == $s['id'] ? ' selected' : '' }}>{{ $s['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="history_tab" role="tabpanel" aria-labelledby="history">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Статус</th>
                                                <th>Дата</th>
                                                <th>Комментарий</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(!empty($get_history))
                                                @foreach($get_history as $history)
                                                    <tr>
                                                        <td>{{ $statuses[$history['status']]['name'] }}</td>
                                                        <td>{{ date('Y-m-d H:i', \strtotime($history['created_at'])) }}</td>
                                                        <td>{{ $history['comment'] }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="3">Нет данных</td>
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

@section('vendor-scripts')
    <script src="{{asset('assets/admin/js/picker.js')}}"></script>
    <script src="{{asset('assets/admin/js/picker.date.js')}}"></script>
@endsection

@section('page-scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(document).ready(function () {
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
                                    id: item['id'],
                                    model: item['model']
                                }
                            }));
                        }
                    });
                },
                select: function(event, ui) {
                    $('[name="product"]').val(ui.item.value);
                    $('[name="product_id"]').val(ui.item.id);
                    $('[name="model"]').val(ui.item.model);
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
                                    firstname: item['firstname'],
                                    lastname: item['lastname'],
                                    email: item['email'],
                                    phone: item['phone']
                                }
                            }));
                        }
                    });
                },
                select: function(event, ui) {
                    $('[name="customer"]').val(ui.item.value);
                    $('[name="customer_id"]').val(ui.item.id);
                    $('[name="firstname"]').val(ui.item.firstname);
                    $('[name="lastname"]').val(ui.item.lastname);
                    $('[name="email"]').val(ui.item.email);
                    $('[name="phone"]').val(ui.item.phone);
                }
            });

            $('.pickadate').pickadate({
                format: 'yyyy-mm-dd'
            });
        });
    </script>
@endsection