@extends('layouts.contentLayoutMaster')
@if($id)
    @section('title','Редактирование опции')
@else
    @section('title','Создание опции')
@endif
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
                                <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                                    @foreach($langs as $key => $l)
                                        <li class="nav-item">
                                            <a class="nav-link{{ $key == 0 ? ' active' : '' }}" id="label-{{ $l['language_id'] }}" data-toggle="tab" href="#lid-{{ $l['language_id'] }}" role="tab" aria-controls="lid-{{ $l['language_id'] }}" aria-selected="true">
                                                {{ $l['name'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="tab-content pt-1">
                                    @foreach($langs as $key => $l)
                                        <div class="tab-pane{{ $key == 0 ? ' active' : '' }}" id="lid-{{ $l['language_id'] }}" role="tabpanel" aria-labelledby="label-{{ $l['language_id'] }}">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label>Название</label>
                                                    <input type="text" name="meta[{{ $l['code'] }}][name]" class="form-control @error('meta.' . $l['code'] . '.name') is-invalid @enderror" placeholder="Название"
                                                           value="{{ old('meta.' . $l['code'] . '.name', !empty($meta[$l['code']]['name']) ? $meta[$l['code']]['name'] : '') }}" required>
                                                    @error('meta.' . $l['code'] . '.name')
                                                    <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Тип</label>
                                        <select name="type" class="type form-control @error('type') is-invalid @enderror" required @error('type')data-validation-required-message="{{ $message }}"@enderror>
                                            <option value="checkbox"{{ isset($type) && $type == 'checkbox' ? ' selected' : '' }}>Флажек</option>
                                            <option value="radio"{{ isset($type) && $type == 'radio' ? ' selected' : '' }}>Кнопка</option>
                                            <option value="select"{{ isset($type) && $type == 'select' ? ' selected' : '' }}>Список</option>
                                            <option value="color"{{ isset($type) && $type == 'color' ? ' selected' : '' }}>Цвет</option>
                                            <option value="text"{{ isset($type) && $type == 'text' ? ' selected' : '' }}>Текстовое поле</option>
                                            <option value="textarea"{{ isset($type) && $type == 'textarea' ? ' selected' : '' }}>Многострочное текстовое поле</option>
                                            <option value="date"{{ isset($type) && $type == 'date' ? ' selected' : '' }}>Дата</option>
                                            <option value="time"{{ isset($type) && $type == 'time' ? ' selected' : '' }}>Время</option>
                                            <option value="datetime"{{ isset($type) && $type == 'datetime' ? ' selected' : '' }}>Дата и время</option>
                                        </select>
                                        @error('type')
                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Порядок сортировки</label>
                                        <input type="text" name="sort_order" class="form-control" placeholder="Порядок сортировки" value="{{ old('sort_order', $sort_order) }}">
                                    </div>
                                </div>
                                <div class="form-group values"{!! isset($type) && ($type !== 'select' && $type !== 'radio' && $type !== 'checkbox' && $type !== 'color') ? ' style="display: none"' : '' !!}>
                                    <label>Значения опций</label>
                                    <hr>
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>Название значения</th>
                                            <th class="image_option"{!! isset($type) && $type === 'select' ? ' style="display: none"' : '' !!}>Изображение</th>
                                            <th>Порядок сортировки</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody id="add_options">
                                        <?php $option_value_row = 0; ?>
                                        @if(!empty($option_values))
                                            @foreach($option_values as $value)
                                                <tr id="option_value_row{{ $option_value_row }}">
                                                    <td>
                                                        @foreach($langs as $lang)
                                                            <input type="text" name="option_values[{{ $option_value_row }}][option_value_description][{{ $lang->code }}][name]" value="{{ !empty($value['description'][$lang->code]) ? $value['description'][$lang->code]['name'] : old('filter_values.' . $value_row . '.description.' . $lang->code . '.name') }}" class="form-control" placeholder="Название значения" />
                                                        @endforeach
                                                    </td>
                                                    <td class="image_option"{!! isset($type) && $type === 'select' ? ' style="display: none"' : '' !!}>
                                                        <div class="preview">
                                                            <a href="#" class="event_file" data-input="#attribute_image-{{ $option_value_row }}">
                                                                <img src="{{ asset($value['image'] ? $value['image'] : 'assets/admin/img/no_image.png') }}" />
                                                                <input id="attribute_image-{{ $option_value_row }}" type="hidden" name="option_values[{{ $option_value_row }}][image]" value="{{ $value['image'] }}" />
                                                            </a>
                                                        </div>
                                                    </td>
                                                    <td><input type="text" name="option_values[{{ $option_value_row }}][sort_order]" value="{{ $value['sort_order'] }}" class="form-control" placeholder="Порядок сортировки" /></td>
                                                    <td>
                                                        <input type="hidden" name="option_values[{{ $option_value_row }}][id]" value="{{ $value['id'] }}" />
                                                        <a href="#" class="btn btn-danger" onclick="$('#option_value_row{{ $option_value_row }}').remove();return false;">&times;</a>
                                                    </td>
                                                </tr>
                                                <?php $option_value_row++; ?>
                                            @endforeach
                                        @endif
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="{{ isset($type) && $type !== 'select' ? '4' : '3' }}" class="colspan_option"><a href="#" class="btn btn-primary" onclick="addOptionValue();return false;">Добавить значение</a></td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="form-group">
                                    <label>Статус</label>
                                    <select name="status" class="form-control">
                                        <option value="1"{{ $status == 1 ? ' selected' : '' }}>Включено</option>
                                        <option value="0"{{ $status == 0 || !$status ? ' selected' : '' }}>Выключено</option>
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
        var option_value_row = {{ $option_value_row }};

        $(document).on('change', '.type', function(){
            if ($(this).val() !== 'select' && $(this).val() !== 'radio' && $(this).val() !== 'checkbox' && $(this).val() !== 'color') {
                $('.values').fadeOut();
            } else {
                $('.values').fadeIn();
            }

            if ($(this).val() === 'select') {
                $('.colspan_option').attr('colspan', 3);
                $('.image_option').fadeOut(0);
            } else {
                $('.colspan_option').attr('colspan', 4);
                $('.image_option').fadeIn(0);
            }
        });

        function addOptionValue() {
            var type = $('.type').val();

            var html = '<tr id="option_value_row' + option_value_row + '">' +
                '  <td>';
            @foreach($langs as $lang)
                html += ' <input type="text" name="option_values[' + option_value_row + '][option_value_description][{{ $lang->code }}][name]" class="form-control" placeholder="Название значения" />';
            @endforeach
                html += '</td>' +
                '  <td' + (type === 'select' ? ' style="display: none"' : '') + ' class="image_option">' +
                '    <div class="preview">' +
                '      <a href="#" class="event_file" data-input="#attribute_image-' + option_value_row + '">' +
                '        <img src="{{ asset('assets/admin/img/no_image.png') }}" />' +
                '        <input id="attribute_image-' + option_value_row + '" type="hidden" name="option_values[' + option_value_row + '][image]" />' +
                '      </a>' +
                '    </div>' +
                '  </td>' +
                '  <td><input type="text" name="option_values[' + option_value_row + '][sort_order]" class="form-control" placeholder="Порядок сортировки" /></td>' +
                '  <td><input type="hidden" name="option_values[' + option_value_row + '][id]"><a href="#" class="btn btn-danger" onclick="$(\'#option_value_row' + option_value_row + '\').remove();return false;">&times;</a></td>' +
                '</tr>';

            $('#add_options').append(html);

            if (type === 'select') {
                $('.colspan_option').attr('colspan', 3);
                $('.image_option').fadeOut(0);
            } else {
                $('.colspan_option').attr('colspan', 4);
                $('.image_option').fadeIn(0);
            }

            option_value_row++;
        }
    </script>
@endsection