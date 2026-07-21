@extends('layouts.contentLayoutMaster')
@section('title','Меню модуль')
@section('page-styles')
    <style>
        code {
            width: 100%;
            display: block;
            padding: 10px;
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
                        @endif
                        <input type="hidden" name="setting[hierarhy]" value="{{ !empty($setting['hierarhy']) ? $setting['hierarhy'] : '' }}" />
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
                                        <label>Css класс меню</label>
                                        <input type="text" name="setting[class]" class="form-control" placeholder="Css класс меню" value="{{ !empty($setting['class']) ? $setting['class'] : old('setting.class') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-12 col-sm-6 col-xs-12">
                                            <label>Категории товаров</label>
                                            <div class="well">
                                                @foreach($categories as $key => $category)
                                                    <div style="margin-bottom: 10px">
                                                        <a href="#" class="btn btn-primary add_element" data-id="{{ $key }}" data-type="1">{{ $category }}</a>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <label>Категории статей</label>
                                            <div class="well">
                                                @foreach($page_categories as $key => $category)
                                                    <div style="margin-bottom: 10px">
                                                        <a href="#" class="btn btn-primary add_element" data-id="{{ $key }}" data-type="2">{{ $category }}</a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3 col-sm-6 col-xs-12">
                                            <label>Статьи</label>
                                            <div class="well">
                                                @foreach($pages as $key => $page)
                                                    <div style="margin-bottom: 10px">
                                                        <a href="#" class="btn btn-primary add_element" data-id="{{ $key }}" data-type="3">{{ $page }}</a>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <label>Кастомная ссылка</label>
                                            <div class="reletive" style="margin-bottom: 10px">
                                                <div class="menu-item-handle" style="padding-left: 10px">
                                                    <div>
                                                        @foreach($langs as $lang)
                                                            <div class="reletive">
                                                                <input type="text" data-lang1="{{ $lang['code'] }}" placeholder="Напишите текст" class="form-control" />
                                                                <input type="text" data-lang="{{ $lang['code'] }}" placeholder="Ссылка" class="form-control" onfocus="if(this.value==''){this.value='{{ url('/' . ($lang['code'] == $default_language ? '' : $lang['code'])) }}/'}" />
                                                                <img src="{{ asset($lang['image']) }}" class="flag" style="top: 26px" />
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <a href="#" class="btn btn-primary add_element" data-type="4">Добавить</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <ol class="sortable">
                                    @php

                                        function t($setting, $children, $e, $langs) {
                                            $html = '';
                                            if ($e) $html .= '<ol>';
                                            foreach($children as $h) {
                                                if(isset($setting[$h['id']])) {
                                                    foreach($setting[$h['id']] as $id => $text) {
                                                        $html .= '<li id="menuItem_' . $h['id'] . '">
                                                            <div class="menu-item-handle"><div>';
                                                                foreach($langs as $key => $l) {
                                                                    $html .= '<div class="reletive" style="display: flex;align-items: center"><textarea name="setting[data][' . $h['id'] . '][' . $id . '][' . $l->code . '][text]" onchange="preview();" placeholder="Введите название" class="form-control">' . (isset($text[$l->code]['text']) ? $text[$l->code]['text'] : '') . '</textarea>
                                                                    <input type="' . (isset($text[$l->code]['url']) && strpos($text[$l->code]['url'],  '://') !== false ? 'text' : 'hidden') . '" class="form-control" name="setting[data][' . $h['id'] . '][' . $id . '][' . $l->code . '][url]" value="' . (isset($text[$l->code]['url']) ? $text[$l->code]['url'] : '') . '" />
                                                                    <img src="' . asset($l['image']) . '" class="flag" /></div>';
                                                                }
                                                                $html .= '</div><a href="#" onclick="$(this).closest(\'li\').remove();preview();return false;" class="btn btn-danger">&times;</a>
                                                            </div>';
                                                            if (isset($h['children'])) $html .= t($setting, $h['children'], 1, $langs);
                                                        $html .= '</li>';
                                                    }
                                                }
                                            }
                                            if ($e) $html .= '</ol>';

                                            return $html;
                                        }

                                        if (!empty($setting['data']) && isset($setting['hierarhy'])) echo t($setting['data'], json_decode($setting['hierarhy'], true), 0, $langs)
                                    @endphp
                                </ol>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Добавить стили для класса {{ !empty($setting['class']) ? $setting['class'] : old('setting.class') }}</label>
                                        <textarea name="setting[style]" rows="5" class="form-control" placeholder="Добавить стили для класса">{{ !empty($setting['style']) ? $setting['style'] : old('setting.style') }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <code>
                                        Для вывода в любом месте добавьте в нужный контроллер<br><br>

                                        use App\Http\Controllers\Extensions\MenuController;<br>

                                        ---------------------------------------------------<br>

                                        $menu = new MenuController;<br>
                                        $menu = $menu->getModule({{ $id ? $id : 'здесь ID модуля (доступен после сохранения настроек)' }});<br>

                                        //Выведет html модуля<br>

                                        ---------------------------------------------------<br>

                                        $menu = new MenuController;<br>
                                        $menu = $menu->getItems({{ $id ? $id : 'здесь ID модуля (доступен после сохранения настроек)' }});<br>

                                        //Вернет массив элементов
                                    </code>
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
    <script src="{{asset('assets/admin/js/sortable.js')}}"></script>

    <script>
        function preview() {
            if ($('.sortable.ui-sortable li').length) {
                var list = $('.sortable').nestedSortable('toHierarchy');
                $('[name="setting[hierarhy]').val(JSON.stringify(list));
            }
        }

        @if (!empty($setting['hierarhy']) && !empty($setting['data']))
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
        @endif

        var x = $('.sortable li').length;

        var langs = JSON.parse('{!! json_encode($langs) !!}');

        $(document).on('click', '.add_element', function (e) {
            var id = $(this).attr('data-id');
            var type = $(this).attr('data-type');
            $(this).addClass('active');
            x++;

            if (type == 4) {
                var html = '<li id="menuItem_' + x + '"><div class="menu-item-handle"><div>';
                $(this).parent().find('input[data-lang1]').each(function () {
                    for (i in langs) {
                        if (langs[i]['code'] == $(this).attr('data-lang1')) {
                            html += '<div class="reletive flex"><textarea name="setting[data][' + x + '][0][' + $(this).attr('data-lang1') + '][text]" onchange="preview();" placeholder="Введите название" class="form-control">' + $(this).val() + '</textarea><input type="text" class="form-control" name="setting[data][' + x + '][0][' + $(this).attr('data-lang1') + '][url]" value="' + $(this).next().val() + '" /><img src="{{ url('/') }}/' + langs[i]['image'] + '" class="flag" /></div>';
                        }
                    }
                });

                html += '</div><a href="#" onclick="$(this).closest(\'li\').remove();preview();return false;" class="btn btn-danger">&times;</a></div></li>';

                $(this).parent().find('input').val('');

                $('.sortable').append(html);
                preview();

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

                return false;
            }

            $.ajax({
                url: '{{ url('admin/extension/menu/ajax') }}?id=' + id + '&type=' + type,
                dataType: 'json',
                success: function(json) {
                    if (json) {
                        var html = '<li id="menuItem_' + x + '"><div class="menu-item-handle"><div>';
                        for (i in json) {
                            html += '<div class="reletive flex"><textarea name="setting[data][' + x + '][' + id + '][' + i + '][text]" onchange="preview();" placeholder="Введите название" class="form-control">' + json[i]['name'] + '</textarea><input type="hidden" name="setting[data][' + x + '][' + id + '][' + i + '][url]" value="' + json[i]['url'] + '" class="form-control" /><img src="' + json[i]['image'] + '" class="flag" /></div>';
                        }

                        html += '</div><a href="#" onclick="$(this).closest(\'li\').remove();preview();return false;" class="btn btn-danger">&times;</a></div></li>';

                        $('.sortable').append(html);
                        preview();

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
                    }
                }
            });

            return false;
        });
    </script>
@endsection