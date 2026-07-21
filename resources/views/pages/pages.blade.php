@extends('layouts.contentLayoutMaster')
@section('title','Статьи')
@section('vendor-styles')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection
@section('content')
    <section class="users-list-wrapper">
        <div class="users-list-filter px-1">
            <form action="{{ asset('admin/pages') }}" method="get">
                <div class="row border rounded py-2 mb-2" style="background: #fff">
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
                        <label for="users-list-role">Название</label>
                        <fieldset class="form-group">
                            <input type="text" name="name" placeholder="Название" value="{{ $name }}" class="form-control" />
                        </fieldset>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="users-list-role">Категория</label>
                        <fieldset class="form-group">
                            <input type="text" name="category" placeholder="Название" value="{{ $category }}" class="form-control" />
                            <input type="hidden" name="category_id" value="{{ $category_id }}" />
                        </fieldset>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-center" style="justify-content: space-between">
                        <button type="submit" style="width: 48%" class="btn btn-primary btn-block glow users-list-clear mb-0">Применить</button>
                        <a href="{{ asset('admin/pages') }}" style="margin-top:0;width: 48%" class="btn btn-primary btn-block glow users-list-clear mb-0">Очистить</a>
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
                        <form action="{{ asset('admin/page_delete') . $params }}" method="post" class="table-responsive">
                            @csrf
                            <div class="col-12 col-sm-12 d-flex align-items-center" style="justify-content: space-between;margin-bottom: 15px">
                                @role('delete')
                                @if(!$pages->isEmpty())
                                    <button type="submit" class="btn btn-danger btn-block glow users-list-clear mb-0" style="width: 48%">Удалить</button>
                                @endif
                                @endrole
                                @role('create')
                                <a href="{{ asset('admin/page_add') . $params }}" class="btn btn-primary btn-block glow users-list-clear mb-0 mt-0" style="width: 48%">Создать</a>
                                @endrole
                            </div>
                            <table id="users-list-datatable" class="table">
                                <thead>
                                @php
                                    $colspan = 4;
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
                                        <a href="{{ $sort_name }}" class="{{ $sort == 'pd.name' ? $order : '' }}">Название</a>
                                    </th>
                                    <th>
                                        <a href="{{ $sort_sort }}" class="{{ $sort == 'pages.sort' ? $order : '' }}">Порядок сортировки</a>
                                    </th>
                                    <th>Категория</th>
                                    <th><a href="{{ $sort_status }}" class="{{ $sort == 'pages.status' ? $order : '' }}">Статус</a></th>
                                    @role('edit|content_edit')
                                    <th>Действие</th>
                                    @php
                                        $colspan += 1;
                                    @endphp
                                    @endrole
                                </tr>
                                </thead>
                                <tbody>
                                @if(!$pages->isEmpty())
                                    @foreach($pages as $page)
                                        <tr>
                                            @role('delete')
                                            <td><input type="checkbox" name="selected[]" value="{{ $page['id'] }}" /></td>
                                            @endrole
                                            <td>{{ $page['name'] }}</td>
                                            <td>{{ $page['sort'] }}</td>
                                            <td>
                                                @foreach($categories as $key => $category)
                                                    @if($page['parent_id'] == $key)
                                                        {{ $category }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>{{ $page['status'] == 1 ? 'Включено' : 'Выключено' }}</td>
                                            @role('edit|content_edit')
                                            <td><a href="{{asset('admin/page/' . $page['id']) . $params}}"><i class="bx bx-edit-alt"></i></a></td>
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
                            {{ $pages->appends($params_array)->links() }}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page-scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(document).ready(function () {
            $('[name="category"]').autocomplete({
                source: function(request, response) {
                    $.ajax( {
                        url: '{{ asset('admin/page_category_autocomplete') }}',
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
                    $('[name="category"]').val(ui.item.value);
                    $('[name="category_id"]').val(ui.item.id);
                }
            });
        });
    </script>
@endsection