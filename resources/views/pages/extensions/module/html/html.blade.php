@extends('layouts.contentLayoutMaster')
@section('title','HTML модуль')

@section('page-styles')
    <style>
        .reletive, .link {
            position: relative;
        }
        img.flag {
            position: absolute;
            top: 8px;
            right: 12px;
            width: 24px;
            height: 24px;
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
            border: 0;
            display: block;
            margin: auto;
        }
        .menu-item-handle textarea {
            display: table-cell;
            vertical-align: middle;
            height: 40px;
            border: 1px solid #dfe2e4;
            border-radius: 0;
        }
        .menu-item-handle a {
            display: table-cell;
            vertical-align: middle;
            width: 100px;
        }
        .menu-item-handle span {
            display: table-cell;
            vertical-align: middle;
            background: #5a8dee;
            color: #fff;
            padding-left: 20px;
            border-radius: 4px;
            font-size: 15px;
            cursor: pointer;
        }
        .menu-item-handle span path {stroke:#fff}
        .menu-item-handle span svg {
            width: 12px;
            height: 15px;
            margin-left: 10px;
        }
        .menu-item-handle span.active svg {
            transform: rotate(180deg);
        }
        .checkbox {
            margin: 3px 0;
        }
        .flex {
            display: flex;
            align-items: center;
            justify-content: space-around;
            flex-wrap: wrap;
        }
        .flex > div {
            flex: 1 0 auto;
        }
        .result-wrapper {
            display: block;
            margin: 15px auto;
            border-radius: 4px;
            background: #dfe2e4;
            padding: 10px;
        }
        .iframe-wrapper {
            display: block;
            position: relative;
            overflow: hidden;
            width: 100%;
        }
        .iframe-inner {
            display: block;
            position: absolute;
            overflow: hidden;
            width: 100%;
            top: 0;
            left: 0;
        }
    </style>
    <script>
        function resizeIframe(obj) {
            obj.style.height = obj.contentWindow.document.body.scrollHeight + 33 + 'px';
        }

        function parentcalc() {
            $('#add_element')[0].style.height = $('#add_element')[0].contentWindow.document.body.scrollHeight + 'px';
        }

        function reloadElement() {
            $.get('{{ url()->current() }}', function (g) {
                $('.elems').html($(g).find('.elems').html());
                $('#primary').modal('hide');
                preview($('.sortable').nestedSortable('toHierarchy'));
            });
        }
    </script>
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
                        <input type="hidden" name="setting[hierarchy]" value="{{ !empty($setting->hierarchy) ? $setting->hierarchy : '' }}" />
                        @if (!$langs->isEmpty())
                            @foreach($langs as $key => $l)
                                <textarea name="setting[description][{{ $l['code'] }}]" style="display: none">{!! !empty($setting->description[$l['code']]) ? $setting->description[$l['code']] : old('setting.description.' . $l['code']) !!}</textarea>
                            @endforeach
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
                                    <label>Выберите элемент</label>
                                    <div class="well elems">
                                        @foreach($elements as $element)
                                            <div class="checkbox">
                                                <a href="#" class="btn btn-primary add_element" data-id="{{ $element->id }}" data-code="{{ $element->code }}">{{ $element->name }}</a>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="modal-primary mr-1 mb-1 d-inline-block">
                                        <button type="button" class="add_primary btn btn-outline-primary" style="margin-top: 15px">
                                            Создать элемент
                                        </button>

                                        <div class="modal fade text-left" id="primary" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document" style="max-width: 75%">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title white" id="myModalLabel160">Создание элемента</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <i class="bx bx-x"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <iframe style="width: 100%;border: 0" src="" id="add_element" onload="resizeIframe(this)"></iframe>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                                                            <i class="bx bx-x d-block d-sm-none"></i>
                                                            <span class="d-none d-sm-block">Закрыть</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <ol class="sortable">
                                    <?php
                                    $row = 0;

                                    function t($children, $elements, $e, $langs, &$row, $default_language, $extensions) {
                                        $html = '';

                                        if ($e) $html .= '<ol>';

                                        if (!is_array($children)) {
                                            $children = json_decode($children, true);
                                        }

                                        foreach($children as $list_id => $hierarchy) {
                                            $r = (int)str_replace('menuItem_', '', $list_id);
                                            $row = $row < $r ? $r : $row;

                                            if(!empty($hierarchy['element_id']) && !empty($elements[$hierarchy['element_id']])) {
                                                if ($elements[$hierarchy['element_id']]->code == 'img') {
                                                    $placeholder = 'Заполните alt картинки';
                                                } else {
                                                    $placeholder = 'Напишите текст';
                                                }

                                                $html .= '<li id="' . $list_id . '" data-code="' . $elements[$hierarchy['element_id']]->code . '" data-id="' . $hierarchy['element_id'] . '">
                                                                <div class="menu-item-handle">
                                                                  <span>' . $elements[$hierarchy['element_id']]->name . '
                                                                    <svg width="8" height="5" viewBox="0 0 8 5" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L4 4L7 1" stroke="#2B2A28" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                                                  </span>';

                                                if(!empty($hierarchy['img'])) {
                                                    $html .= '<div>
                                                                    <a href="#" style="width: 15%" data-input="#html-' . $list_id . '" class="add_files_single btn btn-primary">Выбрать</a>
                                                                    <input id="html-' . $list_id . '" type="hidden" class="image" name="setting[data][' . $list_id . '][img]" value="' . $hierarchy['img'] . '" />
                                                                  </div>';
                                                }

                                                $html .= '    <div style="display:none" class="flex">
                                                                    <input type="hidden" name="setting[data][' . $list_id . '][element_id]" value="' . $hierarchy['element_id'] . '" />';
                                                foreach($langs as $key => $l) {
                                                    if(!empty($hierarchy['link'])) {
                                                        $html .= '
                                                                    <div class="link">
                                                                      <textarea onfocus="if(this.value===\'\'){this.value=\'' . url('/' . ($l->code == $default_language ? '' : $l->code)) . '/}" data-lang="' . $l->code . '" name="setting[data][' . $list_id . '][link][' . $l->code . ']" placeholder="Укажите ссылку" class="form-control">' . (isset($hierarchy['link'][$l->code]) ? $hierarchy['link'][$l->code] : '') . '</textarea>
                                                                      <img src="' . asset($l['image']) . '" class="flag" />
                                                                    </div>';
                                                    }

                                                    $html .= '
                                                                    <div class="reletive"' . (($elements[$hierarchy['element_id']]->code == 'ul' || $elements[$hierarchy['element_id']]->code == 'ol') ? ' style="display:none!important"' : '') . '>
                                                                      <textarea name="setting[data][' . $list_id . '][text][' . $l->code . ']" data-lang="' . $l->code . '" placeholder="' . $placeholder . '" class="form-control">' . (isset($hierarchy['text'][$l->code]) ? $hierarchy['text'][$l->code] : '') . '</textarea>
                                                                      <img src="' . asset($l['image']) . '" class="flag" />
                                                                    </div>';
                                                }

                                                $html .= '
                                                                 </div>
                                                                 <a href="#" style="width: 20px" data-id="' . $hierarchy['element_id'] . '" class="btn primary edit_element"><i class="bx bx-edit-alt"></i></a>
                                                                 <a href="#" onclick="$(this).closest(\'li\').remove();x--;sort();return false;" class="btn btn-danger">&times;</a>
                                                                </div>';

                                                if (!empty($hierarchy['children']) && !isset($hierarchy['img'])) $html .= t($hierarchy['children'], $elements, 1, $langs, $row, $default_language, $extensions);
                                                $html .= '</li>';
                                            } elseif(!empty($hierarchy['menu_id']) && !empty($extensions[$hierarchy['menu_id']])) {
                                                $html .= '<li id="' . $list_id . '" data-id="' . $hierarchy['menu_id'] . '" class="menu_id mjs-nestedSortable-no-nesting">
                                                                <div class="menu-item-handle">
                                                                  <span class="not">' . $extensions[$hierarchy['menu_id']]->name . '</span>
                                                                  <input type="hidden" name="setting[data][menuItem_' . $list_id . '][element_id]" value="0" />
                                                                  <input type="hidden" name="setting[data][menuItem_' . $list_id . '][menu_id]" value="' . $hierarchy['menu_id'] . '" />
                                                                  <a href="#" onclick="$(this).closest(\'li\').remove();x--;sort();return false;" class="btn btn-danger">&times;</a>
                                                                </div>
                                                               </li>';
                                            }
                                        }

                                        if ($e) $html .= '</ol>';

                                        return $html;
                                    }

                                    if (isset($setting['hierarchy']) && !$elements->isEmpty()) {
                                        echo t($setting['hierarchy'], $elements, 0, $langs, $row, $default_language, $extensions);
                                        $row++;
                                    }
                                    ?>
                                </ol>
                                <ul class="nav nav-tabs nav-fill" style="margin-top: 22px">
                                    @foreach([0 => '375', 1 => '768', 2 => '992', 3 => '1200'] as $key => $l)
                                        <li class="nav-item">
                                            <a class="frame nav-link{{ $key == 3 ? ' active' : '' }}" href="#" data-width="{{ $l }}">
                                                {{ $l }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <span class="result-wrapper" id="custom-result-wrapper"></span>
                                <div class="form-group">
                                    <label>Экспортировать модуль</label>
                                    <select class="export form-control">
                                        <option value="">Выберите модуль</option>
                                        @foreach($extensions as $extension)
                                            <option value="{{ $extension->id }}">{{ $extension->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Родительский класс блока</label>
                                        <input type="text" name="setting[parent_class]" class="css form-control" placeholder="Родительский класс блока" value="{{ !empty($setting['parent_class']) ? $setting['parent_class'] : (old('setting.parent_class') ? old('setting.parent_class') : 'section_' . ($id ? $id : $extension_last)) }}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Дополнительные стили (в названии класса нельзя использовать символ ".")</label>
                                        <textarea name="setting[css]" rows="5" class="css form-control" placeholder="Используйте родительский класс, чтобы использовать дополнительные стили">{{ !empty($setting['css']) ? $setting['css'] : old('setting.css') }}</textarea>
                                    </div>
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
        function iframeScale() {
            var z = document.getElementById('custom-result-wrapper');
            var i = z.querySelector('.iframe-inner'), f = z.querySelector('iframe');
            if (!i || !f) {
                return
            }
            if (z.offsetWidth < f.getAttribute('width')) {
                var tr = z.offsetWidth / f.getAttribute('width');
                i.style.transform = `scale(${tr})`;
                i.style.width = f.getAttribute('width') + 'px';
                i.style.height = f.getAttribute('height') + 'px';
                i.style.top = Math.round(-0.5 * (1 - tr) * f.getAttribute('height')) + 'px';
                i.style.left = Math.round(-0.5 * (1 - tr) * f.getAttribute('width')) + 'px'
            } else {
                i.style.transform = 'scale(0.95)';
                i.style.width = 'auto';
                i.style.height = 'auto';
                i.style.top = '0';
                i.style.left = '-10px'
            }
        }

        function addIframe(w, h, url) {
            var wr = document.getElementById('custom-result-wrapper');
            wr.style.maxWidth = w + 'px';
            wr.innerHTML = `<span class="iframe-wrapper" style="padding-top: ${h * 100 / w}%;"><span class="iframe-inner"><iframe id="preview" src="${url}" width="${w}" height="${h}"></iframe></span></span>`;
            iframeScale();
        }

        $(document).on('click', '.frame', function () {
            $(this).closest('.nav-tabs').find('.nav-link').removeClass('active');
            $(this).addClass('active');
            var width = $(this).attr('data-width');
            var height = 768;

            if (width == 1200) {
                height = 768;
            } else if (width == 992) {
                height = 600;
            } else if (width == 768) {
                height = 360;
            } else if (width == 375) {
                height = 812;
            }

            addIframe($(this).attr('data-width'), height, $('#preview').attr('src'));

            return false;
        });

        function lists(list) {
            var data = '';

            for (var i in list) {
                var list_id = 'menuItem_' + list[i]['id'];
                var self = $('#' + list_id);
                var id = self.attr('data-id');

                if ($(self).hasClass('menu_id')) {
                    data += '"' + list_id + '": {"menu_id": "' + id + '"}';
                } else {
                    var code = self.attr('data-code');
                    var val = [];
                    var link = [];

                    self.find('> .menu-item-handle > div > .reletive > textarea').each(function () {
                        val.push('"' + $(this).attr('data-lang') + '"' + ': "' + $(this).val().replace(/\r\n/g, "\\n") + '"');
                    });

                    self.find('> .menu-item-handle > div > .link > textarea').each(function () {
                        link.push('"' + $(this).attr('data-lang') + '"' + ': "' + $(this).val().replace(/\r\n/g, "\\n") + '"');
                    });

                    if (val.length > 1) {
                        val = '{' + val.join(', ') + '}';
                    } else {
                        val = '{' + val.join('') + '}';
                    }

                    data += '"' + list_id + '": {';

                    data += '"element_id": "' + id + '", "text": ' + val + ', "code": "' + code + '"';

                    if (link) {
                        if (link.length > 1) {
                            link = '{' + link.join(', ') + '}';
                        } else {
                            link = '{' + link.join('') + '}';
                        }

                        data += ', "link": ' + link + '';
                    }

                    if (self.find('> .menu-item-handle .image').length && self.find('> .menu-item-handle .image').val() !== '') {
                        data += ', "img": "' + self.find('> .menu-item-handle .image').val() + '"';
                    }

                    if (typeof list[i]['children'] != "undefined" && list[i]['children'].length) {
                        data += ', "children": {' + lists(list[i]['children']) + '}';
                    }

                    data += '}';
                }

                if (list.length - 1 > i) {
                    data += ', ';
                }
            }

            return data;
        }

        function preview(list) {
            if (list) {
                var data = '{' + lists(list) + '}';

                data = data.replace(/\n/g, "\\n");

                console.log(JSON.parse(data))

                $('[name="setting[hierarchy]').val(data);

                if ($('[name="setting[css]').val() !== '') {
                    var css = '&css=' + btoa(encodeURIComponent($('[name="setting[css]').val()));
                } else {
                    var css = '';
                }

                if ($('[name="setting[parent_class]').val() !== '') {
                    var parent_class = '&parent_class=' + btoa(encodeURIComponent($('[name="setting[parent_class]').val()));
                } else {
                    var parent_class = '';
                }

                if (!$('#preview').length) {
                    addIframe($('.frame.active').attr('data-width'), 768, '{{ url('admin/extension/html/ajax') }}?preview=' + btoa(encodeURIComponent(data)) + css + parent_class);
                } else {
                    $('#preview').attr('src', '{{ url('admin/extension/html/ajax') }}?preview=' + btoa(encodeURIComponent(data)) + css + parent_class);
                }

                $.get('{{ url('admin/extension/html/ajax') }}?preview=' + btoa(encodeURIComponent(data)) + css + parent_class, function (html) {
                    @foreach($langs as $l)
                    $('[name="setting[description][{{ $l['code'] }}]"]').val($(html).find('#preview #lid-{{ $l['language_id'] }}').html());
                    @endforeach
                });
            }
        }

        function sort() {
            var sortabe = $('.sortable').nestedSortable({
                handle: '> .menu-item-handle',
                items: 'li',
                tabSize: 10,
                toleranceElement: '> .menu-item-handle',
                opacity: .6,
                isTree: true,
                expandOnHover: 700,
                startCollapsed: false,
                placeholder: 'sortable-placeholder',
                update: function () {
                    preview(sortabe.nestedSortable('toHierarchy'));
                }
            });

            preview(sortabe.nestedSortable('toHierarchy'));
        }

        var x = {{ $row }};

        @if (!empty($setting['hierarchy']) && !$elements->isEmpty())
        sort();
        @endif

        $(document).on('change', '.image', function (e) {
            sort();
        });

        $(document).on('click', '.menu-item-handle span:not(.not)', function (e) {
            $(this).toggleClass('active');

            if ($(this).closest('li').attr('data-code') === 'img') {
                $(this).next().next().slideToggle();
            } else {
                $(this).next().slideToggle();
            }
        });

        $(document).on('change', '.reletive textarea, .link textarea, .css', function (e) {
            preview($('.sortable').nestedSortable('toHierarchy'));
        });

        $(document).on('click', '.edit_element', function () {
            var id = $(this).attr('data-id');
            $('#add_element').attr('src', '{{ url('admin/element_edit') }}/' + id + '/1');
            $('#primary').modal('show');
            return false;
        });

        $(document).on('click', '.add_primary', function () {
            $('#add_element').attr('src', '{{ url('admin/element_add/1') }}');
            $('#primary').modal('show');
            return false;
        });

        $(document).on('change', '.export', function (e) {
            var id = $(this).val();
            var text = $(this).find('option:selected').text();
            x++;

            var html  = '' +
                '<li id="menuItem_' + x + '" data-id="' + id + '" class="menu_id mjs-nestedSortable-no-nesting">' +
                '  <div class="menu-item-handle">' +
                '    <span class="not">' + text + '</span>' +
                '    <input type="hidden" name="setting[data][menuItem_' + x + '][element_id]" value="0" />' +
                '    <input type="hidden" name="setting[data][menuItem_' + x + '][menu_id]" value="' + id + '" />' +
                '    <a href="#" onclick="$(this).closest(\'li\').remove();x--;sort();return false;" class="btn btn-danger">&times;</a>' +
                '  </div>' +
                '</li>';

            $('.sortable').append(html);
            sort();

            $(this).val('');
        });

        $(document).on('click', '.add_element', function (e) {
            var id = $(this).attr('data-id');
            var code = $(this).attr('data-code');
            var text = $(this).text();
            $(this).addClass('active');
            x++;

            if (code === 'ul' || code === 'ol') {
                var placeholder = 'Каждый новый пункт списка с новой строки';
            } else if (code === 'img') {
                var placeholder = 'Заполните alt картинки';
            } else {
                var placeholder = 'Напишите текст';
            }

            var html  = '<li id="menuItem_' + x + '" data-code="' + code + '" data-id="' + id + '"' + (code === 'img' ? ' class="mjs-nestedSortable-no-nesting"' : '') + '>' +
                '  <div class="menu-item-handle">' +
                '    <span>' + text +
                '      <svg width="8" height="5" viewBox="0 0 8 5" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L4 4L7 1" stroke="#2B2A28" stroke-linecap="round" stroke-linejoin="round"></path></svg>' +
                '    </span>';

            if (code === 'img') {
                html += '    <div>' +
                    '      <a href="#" style="width: 15%" data-input="#html-' + x + '" class="add_files_single btn btn-primary">Выбрать</a>' +
                    '      <input id="html-' + x + '" type="hidden" class="image" name="setting[data][menuItem_' + x + '][img]" />' +
                    '    </div>';
            }

            html +=     '    <div style="display:none" class="flex">' +
                '      <input type="hidden" name="setting[data][menuItem_' + x + '][element_id]" value="' + id + '" />';
            @foreach($langs as $key => $l)
            if (code === 'a') {
                html += '  <div class="link">' +
                    '        <textarea onfocus="if(this.value===\'\'){this.value=\'{{ url('/' . ($l->code == $default_language ? '' : $l->code)) }}/\'}" data-lang="{{ $l->code }}" name="setting[data][menuItem_' + x + '][link][{{ $l->code }}]" data-lang="{{ $l->code }}" placeholder="Укажите ссылку" class="form-control"></textarea>' +
                    '        <img src="{{ asset($l['image']) }}" class="flag" />' +
                    '      </div>';
            }

            html += '      <div class="reletive"' + ((code === 'ul' || code === 'ol') ? ' style="display: none!important"' : '') + '>' +
                '        <textarea name="setting[data][menuItem_' + x + '][text][{{ $l->code }}]" data-lang="{{ $l->code }}" placeholder="' + placeholder + '" class="form-control"></textarea>' +
                '        <img src="{{ asset($l['image']) }}" class="flag" />' +
                '      </div>';
            @endforeach

                html +=     '    </div>' +
                '    <a href="#" style="width: 20px" data-id="' + id + '" class="btn primary edit_element"><i class="bx bx-edit-alt"></i></a>' +
                '    <a href="#" onclick="$(this).closest(\'li\').remove();x--;sort();return false;" class="btn btn-danger">&times;</a>' +
                '  </div>' +
                '</li>';

            $('.sortable').append(html);
            sort();

            return false;
        });
    </script>
@endsection