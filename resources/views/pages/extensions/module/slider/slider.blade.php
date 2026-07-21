@extends('layouts.contentLayoutMaster')
@section('title','Слайдер модуль')

@section('page-styles')
    <style>
        code {
            width: 100%;
            display: block;
            padding: 10px;
        }
        .table {
            table-layout: fixed;
        }
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
        .menu-item-handle textarea {height: auto}
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
                    @if ($errors->any())
                        <div class="alert alert-danger" style="max-height: 300px;overflow: auto">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{!! $error !!}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ $action }}" method="post" novalidate>
                        @csrf
                        @if($id)
                            <input type="hidden" name="id" value="{{ $id }}" />
                        @endif
                        <input type="hidden" name="setting[hierarhy]" value="{{ old('setting.hierarhy', !empty($setting['hierarhy']) ? $setting['hierarhy'] : '') }}" />
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
                                    <fieldset>
                                        <div class="checkbox">
                                            <input type="checkbox" name="setting[loop]" class="checkbox-input" value="true" id="loop"{{ old('setting.loop', !empty($setting['loop']) ? $setting['loop'] : 'false') ? ' checked' : '' }} />
                                            <label for="loop">Цикличная прокрутка</label>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="form-group">
                                    <fieldset>
                                        <div class="checkbox">
                                            <input type="checkbox" name="setting[nav]" class="checkbox-input" value="true" id="nav"{{ old('setting.nav', !empty($setting['nav']) ? $setting['nav'] : false) ? ' checked' : '' }} />
                                            <label for="nav">Отображать стрелки</label>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="form-group">
                                    <fieldset>
                                        <div class="checkbox">
                                            <input type="checkbox" name="setting[dots]" class="checkbox-input" value="true" id="dots"{{ old('setting.dots', !empty($setting['dots']) ? $setting['dots'] : false) ? ' checked' : '' }} />
                                            <label for="dots">Отображать пагинацию</label>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Видимых слайдов</label>
                                        <input type="number" name="setting[items]" class="form-control" placeholder="Видимых слайдов" value="{{ old('setting.items', !empty($setting['items']) ? $setting['items'] : 1) }}">
                                    </div>
                                </div>
                                <div class="flex" style="justify-content: space-between">
                                    <div class="form-group">
                                        <select class="export form-control" onchange="addSlider(this.value);">
                                            <option value="">Экспортировать модуль</option>
                                            @foreach($extensions as $extension)
                                                <option value="{{ $extension->id }}">{{ $extension->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <a href="#" onclick="addSlider('');" class="btn btn-primary" data-toggle title="Добавить"><span style="margin-top: 5px" class="bx bx-plus"></span></a>
                                </div>
                                <div class="head" style="padding: 10px 10px 10px 60px;{!! !isset($setting['hierarhy']) ? 'display: none' : '' !!}">
                                    <table class="table">
                                        <tr>
                                            <td>Заголовок</td>
                                            <td>Текст</td>
                                            <td>Кнопка</td>
                                            <td>Ссылка</td>
                                            <td>Картинка</td>
                                            <td>Действие</td>
                                        </tr>
                                    </table>
                                </div>
                                <ol class="sortable">
									<?php

									function t($setting, $children, $langs, $extensions) {
										$html = '';

										foreach($children as $h) {
											if(isset($setting[$h['id']])) {
												$html .= '<li id="menuItem_' . $h['id'] . '">
                                                                    <div class="menu-item-handle">
                                                                      <table class="table" style="margin: 0">
                                                                        <tr>
                                                                          <td style="padding: 0">';

												foreach($setting[$h['id']] as $id => $text) {
													if ($id == 'html_id') {
														if (isset($extensions[$text]->name)) {
															$html .= '
                                                                <div class="reletive required flex">
                                                                   <span>' . $extensions[$text]->name . '</span>
                                                                   <input type="hidden" name="setting[data][' . $h['id'] . '][html_id]" value="' . $text . '" />
                                                                </div>';
														}
													}
												}

													foreach ($langs as $l) {
														$html .= '<div class="reletive required flex">
                                                                     <textarea rows="5" required name="setting[data][' . $h['id'] . '][' . $l->code . '][title]" onchange="preview();" placeholder="Введите заголовок" class="form-control">' . old('setting.' . $h['id'] . '.' . $l->code . '.title', !empty($setting[$h['id']][$l->code]['title']) ? $setting[$h['id']][$l->code]['title'] : '') . '</textarea>
                                                                     <img src="' . asset($l->image) . '" class="flag" />
                                                                  </div>';
													}

													$html .= '</td><td style="padding: 0">';

													foreach ($langs as $l) {
														$html .= '<div class="reletive required flex">
                                                                    <textarea rows="5" required name="setting[data][' . $h['id'] . '][' . $l->code . '][text]" onchange="preview();" placeholder="Введите текст" class="form-control">' . old('setting.' . $h['id'] . '.' . $l->code . '.text', !empty($setting[$h['id']][$l->code]['text']) ? $setting[$h['id']][$l->code]['text'] : '') . '</textarea>
                                                                    <img src="' . asset($l->image) . '" class="flag" />
                                                                  </div>';
													}

													$html .= '</td><td style="padding: 0">';

													foreach ($langs as $l) {
														$html .= '<div class="reletive required flex">
                                                                    <textarea required name="setting[data][' . $h['id'] . '][' . $l->code . '][button]" onchange="preview();" placeholder="Введите текст кнопки" class="form-control">' . old('setting.' . $h['id'] . '.' . $l->code . '.button', !empty($setting[$h['id']][$l->code]['button']) ? $setting[$h['id']][$l->code]['button'] : '') . '</textarea>
                                                                    <img src="' . asset($l->image) . '" class="flag" />
                                                                  </div>
                                                                  <div class="reletive required flex">
                                                                    <textarea required name="setting[data][' . $h['id'] . '][' . $l->code . '][button_href]" onchange="preview();" placeholder="Ссылка" class="form-control">' . old('setting.' . $h['id'] . '.' . $l->code . '.button_href', !empty($setting[$h['id']][$l->code]['button_href']) ? $setting[$h['id']][$l->code]['button_href'] : '') . '</textarea>
                                                                    <img src="' . asset($l->image) . '" class="flag" />
                                                                  </div>';
													}

													$html .= '</td><td style="padding: 0">';

													foreach ($langs as $l) {
														$html .= '<div class="reletive required flex">
                                                                    <textarea required name="setting[data][' . $h['id'] . '][' . $l->code . '][a]" onchange="preview();" placeholder="Введите текст ссылки" class="form-control">' . old('setting.' . $h['id'] . '.' . $l->code . '.a', !empty($setting[$h['id']][$l->code]['a']) ? $setting[$h['id']][$l->code]['a'] : '') . '</textarea>
                                                                    <img src="' . asset($l->image) . '" class="flag" />
                                                                  </div>
                                                                  <div class="reletive required flex">
                                                                    <textarea required name="setting[data][' . $h['id'] . '][' . $l->code . '][a_href]" onchange="preview();" placeholder="Ссылка" class="form-control">' . old('setting.' . $h['id'] . '.' . $l->code . '.a_href', !empty($setting[$h['id']][$l->code]['a_href']) ? $setting[$h['id']][$l->code]['a_href'] : '') . '</textarea>
                                                                    <img src="' . asset($l->image) . '" class="flag" />
                                                                  </div>';
													}

													$html .= '<td>
                                                                    <div class="preview" style="margin: 0">
                                                                      <a href="#" class="event_file not_remove" style="margin: 0" data-input="#slider-' . $h['id'] . '">
                                                                        <img src="' . asset(!empty($setting[$h['id']]['image']) ? $setting[$h['id']]['image'] : (old('setting.data.' . $h['id'] . '.image') ? old('setting.data.' . $h['id'] . '.image') : 'assets/admin/img/no_image.png')) . '" />
                                                                        <input id="slider-' . $h['id'] . '" type="hidden" class="image" name="setting[data][' . $h['id'] . '][image]" value="' . old('setting.' . $h['id'] . '.image', !empty($setting[$h['id']]['image']) ? $setting[$h['id']]['image'] : '') . '" />
                                                                      </a>
                                                                    </div>
                                                                  </td>';
											}

											$html .= '</td><td style="text-align:right; padding: 0"><a href="#" onclick="$(this).closest(\'li\').remove();preview();x--;return false;" class="btn btn-danger">&times;</a></td></tr></table></div></li>';
										}

										return $html;
									}

									$hierarhy = old('setting.hierarhy', !empty($setting['hierarhy']) ? $setting['hierarhy'] : []);

									if (!empty($setting['data']) && $hierarhy) echo t($setting['data'], json_decode($hierarhy, true), $langs, $extensions)
									?>
                                </ol>
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
    <script src="{{asset('assets/admin/js/sortable.js')}}"></script>

    <script>
        var x = $('.sortable li').length;

        var langs = JSON.parse('{!! json_encode($langs) !!}');

        function addSlider(val) {
            if (val) {
                var text = $('.export option[value="' + val + '"]').text();
                $('.export').val('');

                var html =
                    '<li id="menuItem_' + x + '">' +
                    '    <div class="menu-item-handle">' +
                    '        <table class="table" style="margin: 0">' +
                    '            <tr>' +
                    '                <td style="padding: 0" colspan="2">';

                html +=
                    '<div class="reletive flex">' +
                    '   <span>' + text + '</span>' +
                    '   <input type="hidden" name="setting[data][' + x + '][html_id]" value="' + val + '" />' +
                    '</div>';

                html += '</td><td style="text-align:right;padding: 0"><a href="#" onclick="$(this).closest(\'li\').remove();preview();x--;return false;" class="btn btn-danger">&times;</a></td></tr></table></div></li>';
            } else {
                var html =
                    '<li id="menuItem_' + x + '">' +
                    '    <div class="menu-item-handle">' +
                    '        <table class="table" style="margin: 0">' +
                    '            <tr>' +
                    '                <td style="padding: 0">';

                for (var i in langs) {
                    html +=
                        '<div class="reletive flex">' +
                        '   <textarea rows="5" name="setting[data][' + x + '][' + langs[i]['code'] + '][title]" onchange="preview();" placeholder="Введите заголовок" class="form-control"></textarea>' +
                        '   <img src="{{ url('/') }}/' + langs[i]['image'] + '" class="flag" />' +
                        '</div>';
                }

                html += '</td><td style="padding: 0">';

                for (var i in langs) {
                    html +=
                        '<div class="reletive flex">' +
                        '   <textarea rows="5" name="setting[data][' + x + '][' + langs[i]['code'] + '][text]" onchange="preview();" placeholder="Введите текст" class="form-control"></textarea>' +
                        '   <img src="{{ url('/') }}/' + langs[i]['image'] + '" class="flag" />' +
                        '</div>';
                }

                html += '</td><td style="padding: 0">';

                for (var i in langs) {
                    html +=
                        '<div class="reletive flex">' +
                        '   <textarea name="setting[data][' + x + '][' + langs[i]['code'] + '][button]" onchange="preview();" placeholder="Введите текст кнопки" class="form-control"></textarea>' +
                        '   <img src="{{ url('/') }}/' + langs[i]['image'] + '" class="flag" />' +
                        '</div>' +
                        '<div class="reletive flex">' +
                        '   <textarea name="setting[data][' + x + '][' + langs[i]['code'] + '][button_href]" onchange="preview();" placeholder="Ссылка" class="form-control"></textarea>' +
                        '   <img src="{{ url('/') }}/' + langs[i]['image'] + '" class="flag" />' +
                        '</div>';
                }

                html += '</td><td style="padding: 0">';

                for (var i in langs) {
                    html +=
                        '<div class="reletive flex">' +
                        '   <textarea name="setting[data][' + x + '][' + langs[i]['code'] + '][a]" onchange="preview();" placeholder="Введите текст ссылки" class="form-control"></textarea>' +
                        '   <img src="{{ url('/') }}/' + langs[i]['image'] + '" class="flag" />' +
                        '</div>' +
                        '<div class="reletive flex">' +
                        '   <textarea name="setting[data][' + x + '][' + langs[i]['code'] + '][a_href]" onchange="preview();" placeholder="Ссылка" class="form-control"></textarea>' +
                        '   <img src="{{ url('/') }}/' + langs[i]['image'] + '" class="flag" />' +
                        '</div>';
                }

                html += '<td>\n' +
                    '      <div class="preview" style="margin: 0">' +
                    '        <a href="#" class="event_file not_remove" style="margin: 0" data-input="#slider-' + x + '">' +
                    '          <img src="{{ asset('assets/admin/img/no_image.png') }}" />' +
                    '          <input id="slider-' + x + '" type="hidden" class="image" name="setting[data][' + x + '][image]" />' +
                    '        </a>' +
                    '      </div>' +
                    '    </td>';

                html += '</td><td style="text-align:right;padding: 0"><a href="#" onclick="$(this).closest(\'li\').remove();preview();x--;return false;" class="btn btn-danger">&times;</a></td></tr></table></div></li>';
            }

            $('.head').fadeIn();
            $('.sortable').append(html);
            x++;
            sort();
        }

        function preview() {
            if ($('.sortable.ui-sortable li').length) {
                var list = $('.sortable').nestedSortable('toHierarchy');
                $('[name="setting[hierarhy]').val(JSON.stringify(list));
            }
        }

        function sort() {
            $('.sortable').nestedSortable({
                handle: '.menu-item-handle',
                items: 'li',
                toleranceElement: '.menu-item-handle',
                opacity: .6,
                isTree: true,
                expandOnHover: 700,
                startCollapsed: false,
                placeholder: 'sortable-placeholder',
                update: function () {
                    preview();
                }
            });

            preview();
        }

        @if (!empty($setting['hierarhy']))
        sort();
        @endif
    </script>
@endsection