@extends('layouts.contentLayoutMaster')
@section('title','Популярные разделы')
@section('vendor-styles')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection
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
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Название</label>
                                        <input type="text" name="setting[name]" class="form-control @error('setting.name') is-invalid @enderror" placeholder="Название"
                                               value="{{ old('setting.name', !empty($setting['name']) ? $setting['name'] : '') }}" required>
                                        @error('setting.name')
                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                @if (!$langs->isEmpty())
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
                                                        <label>Заголовок</label>
                                                        <input type="text" name="setting[title][{{ $l['code'] }}]" class="form-control @error('setting.title') is-invalid @enderror" placeholder="Заголовок"
                                                               value="{{ old('setting.title.' . $l['code'], !empty($setting['title'][$l['code']]) ? $setting['title'][$l['code']] : '') }}" required>
                                                        @error('setting.title')
                                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <br>
                                    <hr>
                                    <br>
                                @endif
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Первый ряд</label>
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Категория</th>
                                                <th>Текст</th>
                                                <th>Изображение блока</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody id="add_elements">
											<?php $element_row = 0; ?>
                                            @if(old('setting.elements', !empty($setting['elements']) ? $setting['elements'] : []))
                                                @foreach((array)old('setting.elements', $setting['elements']) as $element)
                                                    <tr id="element_row{{ $element_row }}">
                                                        <td>
                                                            <input type="text" name="setting[elements][{{ $element_row }}][name]" value="{{ $element['name'] }}" placeholder="Начните писать название" class="cat form-control @error('setting.elements.' . $element_row . '.category_id') is-invalid @enderror" />
                                                            <input type="hidden" name="setting[elements][{{ $element_row }}][category_id]" value="{{ $element['category_id'] }}" />
                                                            @error('setting.elements.' . $element_row . '.category_id')
                                                            <span class="invalid-feedback" role="alert" style="display: block"><strong>Выберите категорию</strong></span>
                                                            @enderror
                                                        </td>
                                                        <td>
                                                            <textarea name="setting[elements][{{ $element_row }}][text]" class="form-control">{{ $element['text'] }}</textarea>
                                                        </td>
                                                        <td>
                                                            <a href="#" class="event_file" data-input="#element-image-{{ $element_row }}">
                                                                <img src="{{ asset($element['image'] ? $element['image'] : 'assets/admin/img/no_image.png') }}" width="150px" />
                                                                <input id="element-image-{{ $element_row }}" type="hidden" name="setting[elements][{{ $element_row }}][image]" value="{{ $element['image'] }}" />
                                                            </a>
                                                        </td>
                                                        <td><a href="#" class="btn btn-danger" onclick="$('#element_row{{ $element_row }}').remove();return false;">&times;</a></td>
                                                    </tr>
													<?php $element_row++; ?>
                                                @endforeach
                                            @endif
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td colspan="5">
                                                    <a href="#" class="btn btn-primary" onclick="addElement(1);return false;">Добавить блок</a>
                                                </td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Второй ряд</label>
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Товар</th>
                                                <th>Текст</th>
                                                <th>Изображение блока</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody id="add_elements2">
											<?php $element_row2 = 0; ?>
                                            @if((array)old('setting.elements2', !empty($setting['elements2']) ? $setting['elements2'] : []))
                                                @foreach((array)old('setting.elements2', $setting['elements2']) as $element)
                                                    <tr id="element_row-{{ $element_row2 }}">
                                                        <td>
                                                            <input type="text" name="setting[elements2][{{ $element_row2 }}][name]" value="{{ $element['name'] }}" placeholder="Начните писать название" class="product form-control @error('setting.elements2.' . $element_row2 . '.product_id') is-invalid @enderror" />
                                                            <input type="hidden" name="setting[elements2][{{ $element_row2 }}][product_id]" value="{{ $element['product_id'] }}" />
                                                            @error('setting.elements2.' . $element_row2 . '.product_id')
                                                            <span class="invalid-feedback" role="alert" style="display: block"><strong>Выберите товар</strong></span>
                                                            @enderror
                                                        </td>
                                                        <td>
                                                            <textarea name="setting[elements2][{{ $element_row2 }}][text]" class="form-control">{{ $element['text'] }}</textarea>
                                                        </td>
                                                        <td>
                                                            <a href="#" class="event_file" data-input="#element-image2-{{ $element_row2 }}">
                                                                <img src="{{ asset($element['image'] ? $element['image'] : 'assets/admin/img/no_image.png') }}" width="150px" />
                                                                <input id="element-image2-{{ $element_row2 }}" type="hidden" name="setting[elements2][{{ $element_row2 }}][image]" value="{{ $element['image'] }}" />
                                                            </a>
                                                        </td>
                                                        <td><a href="#" class="btn btn-danger" onclick="$('#element_row-{{ $element_row2 }}').remove();return false;">&times;</a></td>
                                                    </tr>
													<?php $element_row2++; ?>
                                                @endforeach
                                            @endif
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td colspan="5">
                                                    <a href="#" class="btn btn-primary" onclick="addElement(2);return false;">Добавить блок</a>
                                                </td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Статус</label>
                                    <select name="setting[status]" class="form-control">
                                        <option value="1"{{ old('setting.status', !empty($setting['status'])) ? ' selected' : '' }}>Включено</option>
                                        <option value="0"{{ !old('setting.status', !empty($setting['status'])) ? ' selected' : '' }}>Выключено</option>
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
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        var element_row = {{ $element_row }};
        var element_row2 = {{ $element_row2 }};

        function addElement(e) {
            if (e === 1) {
                var row = element_row;
                var element = 'elements';
            } else {
                var row = element_row2;
                var element = 'elements2';
            }

            var html = '<tr id="element_row' + (e === 2 ? '-' : '') + row + '">';
            html += '   <td>';
            html += '    <input type="text" name="setting[' + element + '][' + row + '][name]" placeholder="Начните писать название" class="' + (e === 2 ? 'product' : 'cat') + ' form-control" />';
            html += '    <input type="hidden" name="setting[' + element + '][' + row + '][' + (e === 2 ? 'product' : 'category') + '_id]" />';
            html += '   </td>';
            html += '   <td>';
            html += '    <textarea name="setting[' + element + '][' + row + '][text]" class="form-control"></textarea>';
            html += '   </td>';
            html += '   <td>';
            html += '    <a href="#" class="event_file" data-input="#element-image' + (e === 2 ? '2' : '') + '-' + row + '">';
            html += '        <img src="{{ asset('assets/admin/img/no_image.png') }}" width="150px" />';
            html += '        <input id="element-image' + (e === 2 ? '2' : '') + '-' + row + '" type="hidden" name="setting[' + element + '][' + row + '][image]" />';
            html += '    </a>';
            html += '   </td>';
            html += '   <td><a href="#" class="btn btn-danger" onclick="$(\'#element_row' + (e === 2 ? '-' : '') + row + '\').remove();return false;">&times;</a></td>';
            html += '</tr>';

            if (e === 1) {
                $('#add_elements').append(html);
                element_row++;
                autocomplete_category();
            } else {
                $('#add_elements2').append(html);
                element_row2++;
                autocomplete_product();
            }
        }

        function autocomplete_category() {
            $('.cat').each(function() {
                $(this).autocomplete({
                    source: function (request, response) {
                        $.ajax({
                            url: '{{ asset('admin/category_autocomplete') }}',
                            dataType: "json",
                            data: {
                                term: request.term
                            },
                            success: function (data) {
                                response(data);
                            }
                        });
                    },
                    select: function (event, ui) {
                        $(event).val(ui.item.name);
                        $(event.target).next().val(ui.item.id);
                    }
                });
            });
        }

        function autocomplete_product() {
            $('.product').each(function() {
                $(this).autocomplete({
                    source: function (request, response) {
                        $.ajax({
                            url: '{{ asset('admin/product_autocomplete') }}',
                            dataType: "json",
                            data: {
                                term: request.term
                            },
                            success: function (data) {
                                response($.map(data, function(item) {
                                    return {
                                        value: item['name'],
                                        id: item['id']
                                    }
                                }));
                            }
                        });
                    },
                    select: function (event, ui) {
                        $(event).val(ui.item.name);
                        $(event.target).next().val(ui.item.id);
                    }
                });
            });
        }

        $(document).ready(function() {
            autocomplete_category();
            autocomplete_product();
        });
    </script>
@endsection