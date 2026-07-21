@extends('layouts.contentLayoutMaster')
@section('title','Валюта')
@section('content')
    <section class="users-list-wrapper">
        <div class="users-list-table">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger" role="alert" style="margin-bottom:20px"><strong>{{ session('error') }}</strong></div>
                        @endif
                        @if($errors->all())
                            <span class="btn btn-danger invalid-feedback" role="alert" style="margin-bottom:20px;display: block"><strong>Проверьте форму на наличие ошибок</strong></span>
                        @endif
                        @if(session('success'))
                            <div class="alert alert-success" role="alert" style="margin-bottom:20px"><strong>{{ session('success') }}</strong></div>
                        @endif
                        <form action="{{ asset('admin/currency_delete') . $params }}" method="post" class="table-responsive">
                            @csrf
                            <div class="col-12 col-sm-12 d-flex align-items-center" style="justify-content: space-between;margin-bottom: 15px">
                                @role('delete')
                                @if(!$currencies->isEmpty() && $currencies->count() > 1)
                                    <button type="submit" class="btn btn-danger btn-block glow users-list-clear mb-0" style="width: 48%">Удалить</button>
                                @endif
                                @endrole
                                @role('create')
                                <a href="{{ asset('admin/currency_add') . $params }}" class="btn btn-primary btn-block glow users-list-clear mb-0 mt-0" style="width: 48%">Создать</a>
                                @endrole
                            </div>
                            <table id="users-list-datatable" class="table">
                                <thead>
                                @php
                                    $colspan = 5;
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
                                        <a href="{{ $sort_name }}" class="{{ $sort == 'title' ? $order : '' }}">Название</a>
                                    </th>
                                    <th>
                                        <a href="{{ $sort_code }}" class="{{ $sort == 'code' ? $order : '' }}">Код</a>
                                    </th>
                                    <th>
                                        <a href="{{ $sort_value }}" class="{{ $sort == 'value' ? $order : '' }}">Значение</a>
                                    </th>
                                    <th>
                                        <a href="{{ $sort_updated_at }}" class="{{ $sort == 'updated_at' ? $order : '' }}">Дата обновления</a>
                                    </th>
                                    <th><a href="{{ $sort_status }}" class="{{ $sort == 'status' ? $order : '' }}">Статус</a></th>
                                    @role('edit|content_edit')
                                    <th>Действие</th>
                                    @php
                                        $colspan += 1;
                                    @endphp
                                    @endrole
                                </tr>
                                </thead>
                                <tbody>
                                @if(!$currencies->isEmpty())
                                    @foreach($currencies as $currency)
                                        <tr>
                                            @role('delete')
                                            <td><input type="checkbox" name="selected[]" value="{{ $currency['id'] }}" /></td>
                                            @endrole
                                            <td>{{ $currency['title'] }}</td>
                                            <td>{{ $currency['code'] }}</td>
                                            <td>{{ $currency['value'] }}</td>
                                            <td>{{ $currency['updated_at'] }}</td>
                                            <td>{{ $currency['status'] == 1 ? 'Включено' : 'Выключено' }}</td>
                                            @role('edit|content_edit')
                                            <td><a href="{{asset('admin/currency/' . $currency['id']) . $params}}"><i class="bx bx-edit-alt"></i></a></td>
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
                            {{ $currencies->appends($params_array)->links() }}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection