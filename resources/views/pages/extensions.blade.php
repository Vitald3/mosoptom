@extends('layouts.contentLayoutMaster')
@section('title','Модули')
@section('content')
    <section class="users-list-wrapper">
        <div class="users-list-filter px-1">
            <div class="row border rounded py-2 mb-2" style="background: #fff">
                <div class="col-12 col-xs-12">
                    <label for="users-list-verified">Тип</label>
                    <fieldset class="form-group">
                        <select class="form-control" onchange="location=this.value">
                            <option value="{{ url('admin/extensions/module') }}"{{ $type == 'module' ? ' selected' : '' }}>Модули</option>
                            <option value="{{ url('admin/extensions/shipping') }}"{{ $type == 'shipping' ? ' selected' : '' }}>Доставка</option>
                            <option value="{{ url('admin/extensions/payment') }}"{{ $type == 'payment' ? ' selected' : '' }}>Оплата</option>
                            <option value="{{ url('admin/extensions/total') }}"{{ $type == 'total' ? ' selected' : '' }}>Учитывать в заказе</option>
                        </select>
                    </fieldset>
                </div>
            </div>
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
                        <div class="table-responsive">
                            @csrf
                            <table id="users-list-datatable" class="table">
                                <thead>
                                @php
                                    $colspan = 2;
                                @endphp
                                <tr>
                                    <th>Название</th>
                                    @if($type !== 'module')
                                    <th>Порядок сортировки</th>
                                    @endif
                                    <th>Статус</th>
                                    @role('edit|content_edit')
                                    <th style="text-align: right">Действие</th>
                                    @php
                                        $colspan += 1;
                                    @endphp
                                    @endrole
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($extensions))
                                    @foreach($extensions as $key => $extension)
                                        <tr>
                                            <td>{{ $extension['name'] }}</td>
                                            @if($type !== 'module')
                                                <td>{{ $extension['sort_order'] }}</td>
                                            @endif
                                            <td>{{ !$extension['modules']->isEmpty() || $extension['active'] ? 'Включено' : 'Выключено' }}</td>
                                            @role('edit|content_edit')
                                            <td style="text-align: right">
                                                <a href="{{ $extension['url'] }}" class="btn btn-primary"><i class="bx bx-{{ $extension['type'] == 'module' || !$extension['active'] ? 'plus' : 'edit-alt' }}"></i></a>
                                                @if($extension['active'])
                                                    <a href="{{asset('admin/extension/' . $type . '/' . $key . '/delete')}}" class="btn btn-danger">&times;</a>
                                                @endif
                                            </td>
                                            @endrole
                                        </tr>
                                        @if(!$extension['modules']->isEmpty())
                                            @foreach($extension['modules'] as $module)
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;<i class="bx bx-folder-open"></i>&nbsp;&nbsp;&nbsp;{{ $module['name'] }}</td>
                                                    <td>{{ $module['status'] == 1 ? 'Включено' : 'Выключено' }}</td>
                                                    @role('edit|content_edit')
                                                    <td style="text-align: right">
                                                        <a href="{{asset('admin/extension/' . $type . '/' . $key . '/edit/' . $module['id'])}}" class="btn btn-primary"><i class="bx bx-edit-alt"></i></a>
                                                        <a href="{{asset('admin/extension/copy/' . $type . '/' . $module['id'])}}" class="btn btn-primary"><i class="bx bx-folder-open"></i></a>
                                                        <a href="{{asset('admin/extension/' . $type . '/' . $key . '/delete/' . $module['id'])}}" class="btn btn-danger">&times;</a>
                                                    </td>
                                                    @endrole
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="{{ $colspan }}">Нет данных</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection