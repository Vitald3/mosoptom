@extends('layouts.contentLayoutMaster')
@section('title','Транспортными компаниями')

@section('content')
    <section class="users-edit">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    @if(session('error'))
                        <span class="btn btn-danger invalid-feedback" role="alert" style="margin-bottom:20px;display: block"><strong>{{ session('error') }}</strong></span>
                    @endif
                    <form action="{{ $action }}" method="post" novalidate>
                        @csrf
                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Название ТК</th>
                                        <th>Стоимость доставки</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody id="add_tk">
									<?php $tk_row = 0; ?>
                                    @if(!empty($setting['tk']) && old('setting.tk', $setting['tk']))
                                        @foreach(old('setting.tk', $setting['tk']) as $tk)
                                            <tr id="tk_row{{ $tk_row }}">
                                                <td><input type="text" name="setting[tk][{{ $tk_row }}][name]" value="{{ $tk['name'] }}" placeholder="Название ТК" class="form-control" /></td>
                                                <td><input type="text" name="setting[tk][{{ $tk_row }}][cost]" value="{{ $tk['cost'] }}" placeholder="Стоимость доставки" class="form-control" /></td>
                                                <td><a href="#" class="btn btn-danger" onclick="$('#tk_row{{ $tk_row }}').remove();return false;">&times;</a></td>
                                            </tr>
											<?php $tk_row++; ?>
                                        @endforeach
                                    @endif
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="3">
                                            <a href="#" class="btn btn-primary" onclick="addTk();return false;">Добавить</a>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Порядок сортировки</label>
                                        <input type="text" name="setting[sort_order]" class="form-control" placeholder="Порядок сортировки" value="{{ old('setting.sort_order', (!empty($setting['sort_order']) ? $setting['sort_order'] : 0)) }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Статус</label>
                                    <select name="setting[status]" class="form-control">
                                        <option value="1"{{ old('setting.status', (!empty($setting['status']) ? $setting['status'] : 0)) ? ' selected' : '' }}>Включено</option>
                                        <option value="0"{{ old('setting.status', (!empty($setting['status']) ? $setting['status'] : 0)) == 0 || !old('setting.status', (!empty($setting['status']) ? $setting['status'] : 0)) ? ' selected' : '' }}>Выключено</option>
                                    </select>
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
    <script>
        var tk_row = {{ $tk_row }};

        function addTk() {
            var html = '<tr id="tk_row' + tk_row + '">' +
                '  <td><input type="text" name="setting[tk][' + tk_row + '][name]" placeholder="Название ТК" class="form-control" /></td>' +
                '  <td><input type="text" name="setting[tk][' + tk_row + '][cost]" placeholder="Стоимость доставки" class="form-control" /></td>' +
                '  <td><a href="#" class="btn btn-danger" onclick="$(\'#tk_row' + tk_row + '\').remove();return false;">&times;</a></td>' +
                '</tr>';

            $('#add_tk').append(html);
            tk_row++;
        }
    </script>
@endsection