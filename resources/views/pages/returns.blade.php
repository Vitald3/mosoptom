@extends('layouts.contentLayoutMaster')
@section('title','Возвраты')

@section('vendor-styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/pickadate.css') }}">
@endsection

@section('content')
    <section class="users-list-wrapper">
        <div class="users-list-filter px-1">
            <form action="{{ asset('admin/returns') }}" method="get">
                <div class="row breturn rounded py-2 mb-2" style="background: #fff">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="users-list-verified">Статус</label>
                        <fieldset class="form-group">
                            <select name="status" class="form-control">
                                <option value="">Выберите статус</option>
                                <option value="1"{{ $status == 1 ? ' selected' : '' }}>Включено</option>
                                <option value="0"{{ $status != '' && $status == 0 ? ' selected' : '' }}>Выключено</option>
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="users-list-role">Дата</label>
                        <fieldset class="form-group position-relative has-icon-left">
                            <input type="text" name="created_at" class="form-control pickadate" placeholder="Дата" value="{{ $created_at }}">
                            <div class="form-control-position">
                                <i class='bx bx-calendar'></i>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="users-list-role">Клиент</label>
                        <fieldset class="form-group">
                            <input type="text" name="customer" placeholder="Клиент" value="{{ $customer }}" class="form-control" />
                        </fieldset>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="users-list-role">Товар</label>
                        <fieldset class="form-group">
                            <input type="text" name="product" placeholder="Товар" value="{{ $product }}" class="form-control" />
                        </fieldset>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="users-list-role">Модель</label>
                        <fieldset class="form-group">
                            <input type="text" name="model" placeholder="Модель" value="{{ $model }}" class="form-control" />
                        </fieldset>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-9 d-flex align-items-center" style="justify-content: space-between">
                        <button type="submit" style="width: 48%" class="btn btn-primary btn-block glow users-list-clear mb-0">Применить</button>
                        <a href="{{ asset('admin/returns') }}" style="margin-top:0;width: 48%" class="btn btn-primary btn-block glow users-list-clear mb-0">Очистить</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="users-list-table">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger" role="alert" style="margin-bottom:20px"><strong>{{ session('error') }}</strong></div>
                        @endif
                        @if(session('success'))
                            <div class="alert alert-success" role="alert" style="margin-bottom:20px"><strong>{{ session('success') }}</strong></div>
                        @endif
                        @if($errors->all())
                            <span class="btn btn-danger invalid-feedback" role="alert" style="margin-bottom:20px;display: block"><strong>Проверьте форму на наличие ошибок</strong></span>
                        @endif
                        <form action="{{ asset('admin/return_delete') . $params }}" method="post" class="table-responsive">
                            @csrf
                            <div class="col-12 col-sm-12 d-flex align-items-center" style="justify-content: space-between;margin-bottom: 15px">
                                @role('delete')
                                @if(!$returns->isEmpty())
                                    <button type="submit" class="btn btn-danger btn-block glow users-list-clear mb-0" style="width: 48%">Удалить</button>
                                @endif
                                @endrole
                                @role('create')
                                <a href="{{ asset('admin/return_add') . $params }}" class="btn btn-primary btn-block glow users-list-clear mb-0 mt-0" style="width: 48%">Создать</a>
                                @endrole
                            </div>
                            <table id="users-list-datatable" class="table">
                                <thead>
                                @php
                                    $colspan = 7;
                                @endphp
                                <tr>
                                    @role('delete')
                                    <th>
                                        <input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" />
                                    </th>
                                    @php
                                        $colspan += 1;
                                    @endphp
                                    @endrole
                                    <th>
                                        <a href="{{ $sort_id }}" class="{{ $sort == 'id' ? $order : '' }}">№</a>
                                    </th>
                                    <th>
                                        <a href="{{ $sort_order_id }}" class="{{ $sort == 'order_id' ? $order : '' }}">Номер заказа</a>
                                    </th>
                                    <th>
                                        <a href="{{ $sort_customer }}" class="{{ $sort == 'customer' ? $order : '' }}">Клиент</a>
                                    </th>
                                    <th>
                                        <a href="{{ $sort_product }}" class="{{ $sort == 'product' ? $order : '' }}">Товар</a>
                                    </th>
                                    <th>
                                        <a href="{{ $sort_model }}" class="{{ $sort == 'model' ? $order : '' }}">Модель</a>
                                    </th>
                                    <th>
                                        <a href="{{ $sort_created_at }}" class="{{ $sort == 'returns.updated_at' ? $order : '' }}">Дата</a>
                                    </th>
                                    <th><a href="{{ $sort_status }}" class="{{ $sort == 'st.name' ? $order : '' }}">Статус</a></th>
                                    @role('edit|content_edit')
                                    <th>Действие</th>
                                    @php
                                        $colspan += 1;
                                    @endphp
                                    @endrole
                                </tr>
                                </thead>
                                <tbody>
                                @if(!$returns->isEmpty())
                                    @foreach($returns as $return)
                                        <tr>
                                            @role('delete')
                                            <td><input type="checkbox" name="selected[]" value="{{ $return['id'] }}" /></td>
                                            @endrole
                                            <td>{{ $return['id'] }}</td>
                                            <td>
                                                @if($return['order_id'])
                                                    <a href="{{ url('admin/order/' . $return['order_id']) }}">{{ $return['order_id'] }}</a>
                                                @else
                                                    {{ $return['order_id'] }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($return['customer_id'])
                                                    <a href="{{ url('admin/customer/' . $return['customer_id']) }}">{{ $return['customer'] }}</a>
                                                @else
                                                    {{ $return['customer'] }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($return['product_id'])
                                                    <a href="{{ url('admin/product/' . $return['product_id']) }}">{{ $return['product'] }}</a>
                                                @else
                                                    {{ $return['product'] }}
                                                @endif
                                            </td>
                                            <td>{{ $return['model'] }}</td>
                                            <td>{{ date('Y-m-d', \strtotime($return['updated_at'])) }}</td>
                                            <td>{{ $return['status'] }}</td>
                                            @role('edit|content_edit')
                                            <td><a href="{{asset('admin/return/' . $return['id']) . $params}}"><i class="bx bx-edit-alt"></i></a></td>
                                            @endrole
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="{{ $colspan }}">Нет данных</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                            {{ $returns->appends($params_array)->links() }}
                        </form>
                    </div>
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
    <script>
        $(document).ready(function(){
            $('.pickadate').pickadate({
                format: 'yyyy-mm-dd'
            });
        });
    </script>
@endsection