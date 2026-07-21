@extends('layouts.contentLayoutMaster')
@section('title','Конструтор форм модуль')
@section('page-styles')
    <style>
        .table {
            table-layout: fixed;
        }
        td.placeholder {
            width: 356px;
        }
        .placeholder.not > .reletive {display: none}
        .table td {
            text-align: center;
        }
        .reletive {
            position: relative;
        }
        img.flag {
            position: absolute;
            top: 8px;
            right: 12px;
            width: 24px;
            height: 24px;
        }
        .flex {
            display: flex;
            align-items: center;
        }
        .add_element {
            opacity: .7;
        }
        .add_element.active, .add_element:hover {
            opacity: 1!important;
        }
        ol.sortable {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .sortable ol {list-style: none}
        .menu-item-handle {
            width: 100%;
            z-index: 1;
            cursor: move;
            display: table;
            min-height: 40px;
            position: relative;
            background: #dfe2e4;
            border-radius: 4px;
            padding: 10px 10px 10px 60px;
            margin: 4px 0
        }
        .sortable-placeholder {
            border-width: 1px;
            border-style: dashed;
            margin-bottom: 20px;
            min-height: 60px;
            width: 100%;
            margin-top: 13px;
        }
        #preview {
            width: 100%;
            border: 0;
            margin: 22px 0;
            border-radius: 4px;
            min-height: 400px;
            background: #dfe2e4;
            padding: 10px;
        }
        .menu-item-handle textarea, .menu-item-handle input {
            display: table-cell;
            vertical-align: middle;
            height: 40px;
            border: 1px solid #dfe2e4;
            border-radius: 0;
        }
        .menu-item-handle a {
            display: table-cell;
            vertical-align: middle;
        }
        .menu-item-handle span {
            display: table-cell;
            vertical-align: middle;
            background: #5a8dee;
            color: #fff;
            text-align: center;
            border-radius: 4px;
            width: 30%;
            font-size: 15px;
        }
    </style>
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
                        @if($id)
                            <input type="hidden" name="id" value="{{ $id }}" />
                            <input type="hidden" name="setting[data][id]" value="{{ $id }}" />
                        @endif
                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Название</label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Название"
                                               value="{{ old('name', $name) }}" required>
                                        @error('name')
                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Тема сообщения в письме на почту</label>
                                        <input type="text" name="setting[data][subject]" value="{{ !empty($setting['data']['subject']) ? $setting['data']['subject'] : old('setting.data.subject') }}" class="form-control @error('setting.data.subject') is-invalid @enderror" placeholder="Тема сообщения в письме на почту" required />
                                        @error('setting.data.subject')
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
                                                        <textarea name="setting[data][title][{{ $l['code'] }}]" class="form-control @error('setting.data.title.' . $l['code']) is-invalid @enderror" placeholder="Заголовок" required>{{ !empty($setting['data']['title'][$l['code']]) ? $setting['data']['title'][$l['code']] : old('setting.data.title.' . $l['code']) }}</textarea>
                                                        @error('setting.data.title.' . $l['code'])
                                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Текст под заголовком</label>
                                                        <input type="text" name="setting[data][text][{{ $l['code'] }}]" value="{{ old('setting.data.text.' . $l['code'], !empty($setting['data']['text'][$l['code']]) ? $setting['data']['text'][$l['code']] : '') }}" class="form-control" placeholder="Текст под заголовком" />
                                                        @error('setting.data.text.' . $l['code'])
                                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Текст кнопки формы</label>
                                                        <input type="text" name="setting[data][text_button][{{ $l['code'] }}]" value="{{ old('setting.data.text_button.' . $l['code'], !empty($setting['data']['text_button'][$l['code']]) ? $setting['data']['text_button'][$l['code']] : '') }}" class="form-control @error('setting.data.text_button.' . $l['code']) is-invalid @enderror" placeholder="Текст кнопки" required />
                                                        @error('setting.data.text_button.' . $l['code'])
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
                                        <label>Изображение</label>
                                        <div class="preview">
                                            <a href="#" class="event_file not_remove" data-input="#image">
                                                <img src="{{ asset(old('setting.data.image', (!empty($setting['data']['image']) ? $setting['data']['image'] : 'assets/admin/img/no_image.png'))) }}" />
                                                <input id="image" type="hidden" name="setting[data][image]" value="{{ old('setting.data.image', (!empty($setting['data']['image']) ? $setting['data']['image'] : '')) }}" />
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="head">
                                    <div style="padding: 10px 10px 10px 60px">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <td>Тип поля</td>
                                                <td>Placeholder</td>
                                                <td>Заголовок</td>
                                                <td>Обязательное</td>
                                                <td>Действие</td>
                                            </tr>
                                            </thead>
                                            <tfoot>
                                            <tr>
                                                <td colspan="5" style="border: 0" class="text-right"><a href="#" class="btn btn-primary" onclick="addField();return false;">Добавить поле</a></td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <ol class="sortable">
                                        <?php $key = 0; ?>
                                        @if (!empty($setting['data']['fields']))
                                            @foreach($setting['data']['fields'] as $data)
                                                <li id="menuItem_{{ $key }}">
                                                    <div class="menu-item-handle tfoot">
                                                        <table class="table">
                                                            <tr>
                                                                <td>
                                                                    <select name="setting[data][fields][{{ $key }}][type]" data-field="[type]" class="form-control">
                                                                        <option value="select"{{ $data['type'] == 'select' ? ' selected' : '' }}>Список</option>
                                                                        <option value="text"{{ $data['type'] == 'text' ? ' selected' : '' }}>Текстовое поле</option>
                                                                        <option value="textarea"{{ $data['type'] == 'textarea' ? ' selected' : '' }}>Текстовый блок</option>
                                                                        <option value="tel"{{ $data['type'] == 'tel' ? ' selected' : '' }}>Телефон</option>
                                                                        <option value="number"{{ $data['type'] == 'number' ? ' selected' : '' }}>Число</option>
                                                                        <option value="email"{{ $data['type'] == 'email' ? ' selected' : '' }}>Email</option>
                                                                    </select>
                                                                </td>
                                                                <td class="placeholder">
                                                                    @if($data['type'] === 'select')
                                                                        <a href="#" class="btn btn-primary values" onclick="addValues(this, {{ $key }});return false;">Добавить значение</a>
                                                                        @if(!empty($data['values']))
                                                                            @foreach($data['values'] as $key2 => $value)
                                                                                <div class="flex" id="values-{{ $key }}-{{ $key2 }}" style="align-items: inherit;margin-top: 15px">
                                                                                    <div>
                                                                                        @foreach($langs as $lang)
                                                                                            <div class="reletive">
                                                                                                <input type="text" name="setting[data][fields][{{ $key }}][values][{{ $key2 }}][{{ $lang->code }}]" data-field="[values][{{ $key2 }}][{{ $lang->code }}]" value="{{ !empty($value[$lang->code]) ? $value[$lang->code] : '' }}" class="form-control" placeholder="Значение" />
                                                                                                <img src="{{ asset($lang['image']) }}" class="flag" />
                                                                                            </div>
                                                                                        @endforeach
                                                                                    </div>
                                                                                    <a href="#" style="display: flex" class="btn btn-danger flex" onclick="$('#values-{{ $key }}-{{ $key2 }}').remove();return false;">&times;</a>
                                                                                </div>
                                                                            @endforeach
                                                                        @endif
                                                                    @else
                                                                        @foreach($langs as $lang)
                                                                            <div class="reletive">
                                                                                <input type="text" name="setting[data][fields][{{ $key }}][placeholder][{{ $lang->code }}]" data-field="[placeholder][{{ $lang->code }}]" value="{{ !empty($data['placeholder'][$lang->code]) ? $data['placeholder'][$lang->code] : '' }}" class="form-control" placeholder="Placeholder" />
                                                                                <img src="{{ asset($lang['image']) }}" class="flag" />
                                                                            </div>
                                                                        @endforeach
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @foreach($langs as $lang)
                                                                        <div class="reletive">
                                                                            <input type="text" name="setting[data][fields][{{ $key }}][title][{{ $lang->code }}]" data-field="[title][{{ $lang->code }}]" value="{{ !empty($data['title'][$lang->code]) ? $data['title'][$lang->code] : '' }}" class="form-control" placeholder="Заголовок" />
                                                                            <img src="{{ asset($lang['image']) }}" class="flag" />
                                                                        </div>
                                                                    @endforeach
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" name="setting[data][fields][{{ $key }}][required]" data-field="[required]" value="1"{{ isset($data['required']) ? ' checked' : '' }} />
                                                                </td>
                                                                <td><a href="#" class="btn btn-danger" onclick="$('#menuItem_{{ $key }}').remove();return false;">&times;</a></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </li>
                                                <?php $key++; ?>
                                            @endforeach
                                        @endif
                                    </ol>
                                </div>

                                <div class="form-group">
                                    <label>Статус</label>
                                    <select name="status" class="form-control">
                                        <option value="1"{{ old('status', $status) == 1 ? ' selected' : '' }}>Включено</option>
                                        <option value="0"{{ old('status', $status) == 0 || !old('status', $status) ? ' selected' : '' }}>Выключено</option>
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
    <script src="{{asset('assets/admin/js/sortable.js')}}"></script>

    <script>
        var x = {{ $key }};

        function addValues(self, x) {
            var x2 = $(self).parent().find('.flex').length;
            x2++;

            var html = '<div class="flex" id="values-' + x + '-' + x2 + '" style="margin-top: 15px;align-items: inherit"><div>';
            @foreach($langs as $lang)
                html += '  <div class="reletive"><input type="text" name="setting[data][fields][' + x + '][values][' + x2 + '][{{ $lang->code }}]" data-field="[values][' + x2 + '][{{ $lang->code }}]" class="form-control" placeholder="Значение" /><img src="{{ asset($lang['image']) }}" class="flag" /></div>';
            @endforeach

                html += '  </div><a href="#" style="display: flex" class="btn btn-danger flex" onclick="$(\'#values-' + x + '-' + x2 + '\').remove();return false;">&times;</a></div>';

            $(self).parent().append(html);
        }

        function select_type(val) {
            if ($(val).val() !== 'select') {
                $(val).closest('.table').find('.values').fadeOut(0);
            } else {
                $(val).closest('.table').find('.values').fadeIn(0);
            }
        }

        function addField() {
            x++;

            var html = '<li id="menuItem_' + x + '">' +
                '<div class="menu-item-handle tfoot">' +
                '    <table class="table">' +
                '        <tr>' +
                '            <td>' +
                '                <select name="setting[data][fields][' + x + '][type]" data-field="[type]" onchange="select_type(this);" class="form-control">' +
                '                    <option value="select">Список</option>' +
                '                    <option value="text">Текстовое поле</option>' +
                '                    <option value="textarea">Текстовый блок</option>' +
                '                    <option value="tel">Телефон</option>' +
                '                    <option value="number">Число</option>' +
                '                    <option value="email">Email</option>' +
                '                </select>' +
                '            </td>' +
                '            <td class="placeholder">' +
                '                <a href="#" class="btn btn-primary values" onclick="addValues(this, ' + x + ');return false;">Добавить значение</a>' +
                '                <div class="flex" id="values-' + x + '-0" style="margin-top: 15px;align-items: inherit"><div>';
            @foreach($langs as $lang)
                html += '        <div class="reletive"><input type="text" name="setting[data][fields][' + x + '][values][0][{{ $lang->code }}]" data-field="[values][0][{{ $lang->code }}]" class="form-control" placeholder="Значение" /><img src="{{ asset($lang['image']) }}" class="flag" /></div>';
            @endforeach
                html += '        </div><a href="#" style="display: flex" class="btn btn-danger flex" onclick="$(\'#values-' + x + '-0\').remove();return false;">&times;</a>' +
                '                </div>' +
                '            </td>' +
                '            <td>';
            @foreach($langs as $lang)
                html += '        <div class="reletive"><input type="text" name="setting[data][fields][' + x + '][title][{{ $lang->code }}]" data-field="[title][{{ $lang->code }}]" class="form-control" placeholder="Заголовок" /><img src="{{ asset($lang['image']) }}" class="flag" /></div>';
            @endforeach
                html += '    </td>' +
                '            <td>' +
                '                <input type="checkbox" name="setting[data][fields][' + x + '][required]" data-field="[required]" value="1" />' +
                '            </td>' +
                '            <td><a href="#" class="btn btn-danger" onclick="$(\'#menuItem_' + x + '\').remove();return false;">&times;</a></td>' +
                '        </tr>' +
                '    </table>' +
                '</div>' +
                '</li>';

            $('.sortable').append(html);
            sort();
        }

        function resort() {
            $('.sortable li').each(function(i, e){
                $(this).attr('id', 'menuItem_' + i);
                $(this).find('.values').attr('onclick', 'addValues(this, ' + i + ');return false;');
                $(this).find('input, select').each(function() {
                    $(this).attr('name', 'setting[data][fields][' + i + ']' + $(this).attr('data-field'));
                });
            });
        }

        $(document).on('change', '[data-field="[type]"]', function (){
            if ($(this).val() === 'select') {
                $(this).closest('li').find('.placeholder > .reletive').fadeOut();
            } else {
                $(this).closest('li').find('.placeholder > .reletive').fadeIn();
            }
        });

        function sort() {
            $('.sortable').nestedSortable({
                handle: '.menu-item-handle',
                items: 'li',
                toleranceElement: '.menu-item-handle',
                opacity: .6,
                isTree: true,
                maxLevels: 1,
                expandOnHover: 700,
                startCollapsed: false,
                placeholder: false,
                update: function() {
                    resort();
                }
            });
        }
    </script>
@endsection