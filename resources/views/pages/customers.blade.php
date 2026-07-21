@extends('layouts.contentLayoutMaster')
@section('title','Пользователи')
@section('vendor-styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/pickadate.css') }}">
@endsection
@section('content')
    <section class="users-list-wrapper">
        <div class="users-list-filter px-1">
            <form action="{{ asset('admin/customers') }}" method="get">
                <div class="row bcustomer rounded py-2 mb-2" style="background: #fff">
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
                        <label for="users-list-verified">Подтвержденный</label>
                        <fieldset class="form-group">
                            <select name="approved" class="form-control">
                                <option value="">Выбрать</option>
                                <option value="1"{{ $approved == 1 ? ' selected' : '' }}>Да</option>
                                <option value="0"{{ $approved != '' && $approved == 0 ? ' selected' : '' }}>Нет</option>
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
                        <label for="users-list-role">Имя пользователя</label>
                        <fieldset class="form-group">
                            <input type="text" name="name" placeholder="Имя пользователя" value="{{ $name }}" class="form-control" />
                        </fieldset>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="users-list-role">Email</label>
                        <fieldset class="form-group">
                            <input type="text" name="email" placeholder="Email" value="{{ $email }}" class="form-control" />
                        </fieldset>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="users-list-verified">Группа пользователя</label>
                        <fieldset class="form-group">
                            <select name="customer_group" class="form-control">
                                <option value="">Выберите группы клиентов</option>
                                @foreach($customer_groups as $cg)
                                    <option value="{{ $cg->id }}"{{ $cg->id == $customer_group ? ' selected' : '' }}>{{ $cg->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="users-list-role">Ip</label>
                        <fieldset class="form-group">
                            <input type="text" name="ip" placeholder="Ip" value="{{ $ip }}" class="form-control" />
                        </fieldset>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-center" style="justify-content: space-between">
                        <button type="submit" style="width: 48%" class="btn btn-primary btn-block glow users-list-clear mb-0">Применить</button>
                        <a href="{{ asset('admin/customers') }}" style="margin-top:0;width: 48%" class="btn btn-primary btn-block glow users-list-clear mb-0">Очистить</a>
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
                        <form action="{{ asset('admin/customer_delete') . $params }}" method="post" class="table-responsive">
                            @csrf
                            <div class="col-12 col-sm-12 d-flex align-items-center" style="justify-content: space-between;margin-bottom: 15px">
                                @role('delete')
                                @if(!$customers->isEmpty())
                                    <button type="submit" class="btn btn-danger btn-block glow users-list-clear mb-0" style="width: 48%">Удалить</button>
                                @endif
                                @endrole
                                @role('create')
                                <a href="{{ asset('admin/customer_add') . $params }}" class="btn btn-primary btn-block glow users-list-clear mb-0 mt-0" style="width: 48%">Создать</a>
                                @endrole
                            </div>
                            <table id="users-list-datatable" class="table">
                                <thead>
                                @php
                                    $colspan = 6;
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
                                        <a href="{{ $sort_name }}" class="{{ $sort == 'name' ? $order : '' }}">Имя</a>
                                    </th>
                                    <th>
                                        <a href="{{ $sort_email }}" class="{{ $sort == 'customers.email' ? $order : '' }}">Email</a>
                                    </th>
                                    <th>
                                        <a href="{{ $sort_group }}" class="{{ $sort == 'customer_group' ? $order : '' }}">Группа покупателей</a>
                                    </th>
                                    <th>
                                        <a href="{{ $sort_created_at }}" class="{{ $sort == 'customers.created_at' ? $order : '' }}">Дата</a>
                                    </th>
                                    <th>
                                        <a href="{{ $sort_ip }}" class="{{ $sort == 'customers.ip' ? $order : '' }}">Ip</a>
                                    </th>
                                    <th><a href="{{ $sort_status }}" class="{{ $sort == 'customers.status' ? $order : '' }}">Статус</a></th>
                                    @role('edit|content_edit')
                                    <th>Действие</th>
                                    @php
                                        $colspan += 1;
                                    @endphp
                                    @endrole
                                </tr>
                                </thead>
                                <tbody>
                                @if(!$customers->isEmpty())
                                    @foreach($customers as $customer)
                                        <tr>
                                            @role('delete')
                                            <td><input type="checkbox" name="selected[]" value="{{ $customer['id'] }}" /></td>
                                            @endrole
                                            <td>{{ $customer['name'] }}</td>
                                            <td>{{ $customer['email'] }}</td>
                                            <td>{{ $customer->customer_group_description['name'] }}</td>
                                            <td>{{ date('Y-m-d', \strtotime($customer['created_at'])) }}</td>
                                            <td>{{ $customer['ip'] }}</td>
                                            <td>{{ $customer['status'] == 1 ? 'Включено' : 'Выключено' }}</td>
                                            @role('edit|content_edit')
                                            <td>
                                                <a href="{{asset('admin/customer/' . $customer['id']) . $params}}"><i class="bx bx-edit-alt"></i></a>
                                                <a href="{{asset('admin/customer_login/' . $customer['id']) . $params}}" style="padding-left: 10px" target="_blank"><i class="bx bx-key"></i></a>
                                            </td>
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
                            {{ $customers->appends($params_array)->links() }}
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