@extends('layouts.contentLayoutMaster')
@section('title','Элементы')
@section('content')
    <section class="users-list-wrapper">
        <div class="users-list-filter px-1">
            <form action="{{ asset('admin/elements') }}" method="get">
                <div class="row border rounded py-2 mb-2" style="background: #fff">
                    <div class="col-12 col-sm-6 col-lg-6">
                        <label for="users-list-role">Название</label>
                        <fieldset class="form-group">
                            <input type="text" name="name" placeholder="Название" value="{{ $name }}" class="form-control" />
                        </fieldset>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-6 d-flex align-items-center" style="justify-content: space-between">
                        <button type="submit" style="width: 48%" class="btn btn-primary btn-block glow users-list-clear mb-0">Применить</button>
                        <a href="{{ asset('admin/elements') }}" style="margin-top:0;width: 48%" class="btn btn-primary btn-block glow users-list-clear mb-0">Очистить</a>
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
                        <form action="{{ asset('admin/elements_delete') . $params }}" method="post" class="table-responsive">
                            @csrf
                            <div class="col-12 col-sm-12 d-flex align-items-center" style="justify-content: space-between;margin-bottom: 15px">
                                @role('delete')
                                @if(!$elements->isEmpty())
                                    <button type="submit" class="btn btn-danger btn-block glow users-list-clear mb-0" style="width: 48%">Удалить</button>
                                @endif
                                @endrole
                                @role('create')
                                <a href="{{ asset('admin/element_add') . $params }}" class="btn btn-primary btn-block glow users-list-clear mb-0 mt-0" style="width: 48%">Создать</a>
                                @endrole
                            </div>
                            <table id="users-list-datatable" class="table">
                                <thead>
                                @php
                                    $colspan = 2;
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
                                        <a href="{{ $sort_name }}" class="{{ $sort == 'cd.name' ? $order : '' }}">Название</a>
                                    </th>
                                    <th>Параметры</th>
                                    @role('edit|content_edit')
                                    <th>Действие</th>
                                    @php
                                        $colspan += 1;
                                    @endphp
                                    @endrole
                                </tr>
                                </thead>
                                <tbody>
                                @if(!$elements->isEmpty())
                                    @foreach($elements as $element)
                                        <tr>
                                            @role('delete')
                                            <td><input type="checkbox" name="selected[]" value="{{ $element['id'] }}" /></td>
                                            @endrole
                                            <td>{{ $element['name'] }}</td>
                                            <td>
                                                @if(!empty($element['setting'][4]))
                                                    @foreach($element['setting'][4] as $key => $setting)
                                                        @if($key != 'children')
                                                            <div>{{ strpos($key, 'auto') !== false ? str_replace(':', ': ', $key) : $key . ': ' . $setting }}</div>
                                                        @endif
                                                    @endforeach
                                                @elseif(!empty($element['setting'][3]))
                                                    @foreach($element['setting'][3] as $key => $setting)
                                                        <div>{{ strpos($key, 'auto') !== false ? str_replace(':', ': ', $key) : $key . ': ' . $setting }}</div>
                                                    @endforeach
                                                @endif
                                            </td>
                                            @role('edit|content_edit')
                                            <td><a href="{{asset('admin/element/' . $element['id']) . $params}}"><i class="bx bx-edit-alt"></i></a></td>
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
                            {{ $elements->appends($params_array)->links() }}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection