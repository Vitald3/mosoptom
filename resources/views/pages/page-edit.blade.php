@extends('layouts.contentLayoutMaster')
@if($id)
    @section('title','Редактирование статьи')
@else
    @section('title','Создание статьи')
@endif
@section('vendor-styles')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/admin/css/select2.min.css')}}">
@endsection
@section('page-styles')
    <style>
        #preview {
            border: 0;
            display: block;
            margin: auto;
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
            min-height: 200px;
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
                    @if($errors->all())
                        <span class="btn btn-danger invalid-feedback" role="alert" style="margin-bottom:20px;display: block"><strong>Проверьте форму на наличие ошибок</strong></span>
                    @endif
                    <form action="{{ $action }}" id="preview2" method="post" novalidate>
                        @csrf
                        @if($id)
                            <input type="hidden" name="id" value="{{ $id }}" />
                        @endif
                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <ul class="nav nav-tabs nav-fill" id="myTab2" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="content" data-toggle="tab" href="#content_tab" role="tab" aria-controls="content_tab" aria-selected="true">
                                            Общие
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="data" data-toggle="tab" href="#data_tab" role="tab" aria-controls="data_tab" aria-selected="true">
                                            Данные
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="attributes" data-toggle="tab" href="#attribute_tab" role="tab" aria-controls="attribute_tab" aria-selected="true">
                                            Характеристики
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="images" data-toggle="tab" href="#images_tab" role="tab" aria-controls="images_tab" aria-selected="true">
                                            Изображение
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="layout" data-toggle="tab" href="#layout_tab" role="tab" aria-controls="layout_tab" aria-selected="true">
                                            Макет
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content pt-1">
                                    <div class="tab-pane active" id="content_tab" role="tabpanel" aria-labelledby="content">
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
                                                                <label>Название</label>
                                                                <input type="text" name="meta[{{ $l['code'] }}][name]" class="form-control @error('meta.' . $l['code'] . '.name') is-invalid @enderror" placeholder="Название"
                                                                       value="{{ old('meta.' . $l['code'] . '.name', !empty($meta[$l['code']]['name']) ? $meta[$l['code']]['name'] : '') }}" required>
                                                                @error('meta.' . $l['code'] . '.name')
                                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Meta Title</label>
                                                                <input type="text" name="meta[{{ $l['code'] }}][meta_title]" class="form-control @error('meta.' . $l['code'] . '.meta_title') is-invalid @enderror" placeholder="Meta Title"
                                                                       value="{{ old('meta.' . $l['code'] . '.meta_title', !empty($meta[$l['code']]['meta_title']) ? $meta[$l['code']]['meta_title'] : '') }}" required>
                                                                @error('meta.' . $l['code'] . '.meta_title')
                                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Meta Description</label>
                                                                <textarea name="meta[{{ $l['code'] }}][meta_description]" class="form-control" placeholder="Meta Description">{{ old('meta.' . $l['code'] . '.meta_description', !empty($meta[$l['code']]['meta_description']) ? $meta[$l['code']]['meta_description'] : '') }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Meta Keywords</label>
                                                                <input type="text" name="meta[{{ $l['code'] }}][meta_keywords]" class="form-control" placeholder="Meta Keywords"
                                                                       value="{{ old('meta.' . $l['code'] . '.meta_keywords', !empty($meta[$l['code']]['meta_keywords']) ? $meta[$l['code']]['meta_keywords'] : '') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Описание</label>
                                                                <textarea name="meta[{{ $l['code'] }}][description]" class="tinymce">{!! old('meta.' . $l['code'] . '.description', !empty($meta[$l['code']]['description']) ? $meta[$l['code']]['description'] : '') !!}</textarea>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <a href="#" class="mode_html">Режим HTML</a>
                                                        </div>
                                                        <div class="form-group" style="display: none">
                                                            <div class="controls">
                                                                <label>HTML код</label>
                                                                <hr>
                                                                <textarea rows="9" name="meta[{{ $l['code'] }}][html]" data-code="{{ $l['code'] }}" class="html form-control">{!! old('meta.' . $l['code'] . '.html', !empty($meta[$l['code']]['html']) ? $meta[$l['code']]['html'] : '') !!}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                            <div id="html" style="display: none;">
                                                <ul class="nav nav-tabs nav-fill" style="margin-top: 22px">
                                                    @foreach([0 => '375', 1 => '768', 2 => '992', 3 => '1200'] as $key => $l)
                                                        <li class="nav-item">
                                                            <a class="frame nav-link" href="#" data-width="{{ $l }}">
                                                                {{ $l }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                <span class="result-wrapper" id="custom-result-wrapper"></span>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Стили css</label>
                                                        <textarea name="css" rows="5" class="css form-control" placeholder="Стили css">{{ old('css', $css) }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="tab-pane" id="data_tab" role="tabpanel" aria-labelledby="data">
                                        <div class="form-group select2">
                                            <div class="controls">
                                                <label>Категория</label>
                                                <select name="parent_id" class="form-control select2">
                                                    <option value="">Выберите категорию</option>
                                                    @foreach($categories as $key => $category)
                                                        <option value="{{ $key }}"{{ old('parent_id', $parent_id) == $key ? ' selected' : '' }}>{{ $category }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <fieldset>
                                                <div class="checkbox">
                                                    <input type="checkbox" name="top" class="checkbox-input" value="1" id="checkbox1"{{ old('top', $top) ? ' checked' : '' }} />
                                                    <label for="checkbox1">Выводить в шапке</label>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="form-group">
                                            <fieldset>
                                                <div class="checkbox">
                                                    <input type="checkbox" name="bottom" class="checkbox-input" value="1" id="checkbox2"{{ old('bottom', $bottom) ? ' checked' : '' }} />
                                                    <label for="checkbox2">Выводить в подвале</label>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Порядок сортировки</label>
                                                <input type="text" name="sort" class="form-control" placeholder="Порядок сортировки"
                                                       value="{{ old('sort', $sort) }}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>SEO URL</label>
                                                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" placeholder="SEO URL"
                                                       value="{{ old('slug', $slug) }}" required>
                                                @error('slug')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
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
                                    <div class="tab-pane" id="attribute_tab" role="tabpanel" aria-labelledby="attributes">
										<?php $attribute_row = 0; ?>
                                        @if(!$attributes2->isEmpty())
                                            <div class="form-group multiple-select2">
                                                <label>Выберите характеристику</label>
                                                <select class="form-control select2" multiple>
                                                    @foreach($attributes2 as $attribute)
                                                        <option value="{{ $attribute->id }}"{{ in_array($attribute->id, $attribute_im) ? ' selected' : '' }}>{{ $attribute->metaLang[0]['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @if(!$langs->isEmpty())
                                                <div id="attr">
                                                    @foreach($attributes2 as $attribute)
                                                        @if(in_array($attribute->id, $attribute_im))
                                                            <div style="padding: 15px;border: 1px solid #eee" id="attribute-{{ $attribute->id }}">
                                                                <div class="row">
                                                                    <div class="col-12 col-sm-4 col-lg-3">
                                                                        {{ $attribute->meta[0]['name'] }}
                                                                        <input type="hidden" name="page_attribute[{{ $attribute_row }}][attribute_id]" value="{{ $attribute->id }}" />
                                                                    </div>
                                                                    <div class="col-12 col-sm-6 col-lg-8">
                                                                        @foreach($langs as $lang)
                                                                            <fieldset style="margin-bottom: 10px">
                                                                                <div class="input-group">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text"><img style="width: 20px" src="{{ asset($lang->image) }}" title="{{ $lang->name }}" /></span>
                                                                                    </div>
                                                                                    <textarea name="page_attribute[{{ $attribute_row }}][page_attribute_description][{{ $lang->code }}][text]" placeholder="Описание" class="form-control">{{ !empty($attribute_descriptions[$attribute->id]['descriptions'][$lang->code]) ? $attribute_descriptions[$attribute->id]['descriptions'][$lang->code]['text'] : '' }}</textarea>
                                                                                </div>
                                                                            </fieldset>
                                                                        @endforeach
                                                                    </div>
                                                                    <div class="col-12 col-sm-2 col-lg-1">
                                                                        <a href="#" onclick="deleteAttribute({{ $attribute->id }});return false;" class="btn btn-danger">&times;</a>
                                                                    </div>
                                                                </div>
                                                                <div class="row" style="margin-top: 15px">
                                                                    <div class="col-12 col-sm-12 col-lg-12">
                                                                        <a href="#" class="btn btn-primary add_attribute_image" data-row="{{ $attribute_row }}">Добавить изображения</a>
                                                                        <div class="preview">
                                                                            @if(isset($attribute_images[$attribute->id]))
                                                                                @foreach((array)$attribute_images[$attribute->id] as $x => $attribute_image)
                                                                                    <a href="#" class="event_file" data-input="#attribute_image-{{ $attribute_row }}-{{ $x }}"><img src="{{ asset($attribute_image['image']) }}" /><input id="attribute_image-{{ $key }}-{{ $x }}" type="hidden" name="page_attribute[{{ $key }}][image][{{ $x }}]" value="{{ old('page_attribute.' . $key . '.image.' . $x) ? old('page_attribute.' . $key . '.image.' . $x) : $attribute_image['image'] }}" /></a>
                                                                                @endforeach
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
															<?php $attribute_row++; ?>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="alert alert-danger">Создайте языки</div>
                                            @endif
                                        @else
                                            <div class="alert alert-danger">Создайте характеристики</div>
                                        @endif
                                    </div>
                                    <div class="tab-pane" id="images_tab" role="tabpanel" aria-labelledby="images">
                                        <div class="form-group">
                                            <label>Изображение</label>
                                            <div class="preview">
                                                <a href="#" class="event_file not_remove" data-input="#page-image">
                                                    <img src="{{ asset($image ? $image : old('image', 'assets/admin/img/no_image.png')) }}" />
                                                    <input id="page-image" type="hidden" name="image" value="{{ old('image', $image) }}" />
                                                </a>
                                            </div>
                                        </div>
                                        <div class="flex-custom">
                                            <label>Дополнительные изображения</label>
                                            <a href="#" class="btn btn-primary add_page_image">Добавить изображения</a>
                                        </div>
                                        <div class="preview">
											<?php $image_row = 0; ?>
                                            @foreach($images as $image)
                                                <a href="#" class="event_file" data-input="#product-images-{{ $image_row }}">
                                                    <img src="{{ asset($image ? $image : old('image', 'assets/admin/img/no_image.png')) }}" />
                                                    <input id="product-images-{{ $image_row }}" type="hidden" name="images[]" value="{{ old('image', $image) }}" />
                                                </a>
												<?php $image_row++; ?>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="layout_tab" role="tabpanel" aria-labelledby="layout">
                                        <div class="form-group">
                                            <select name="layout_id" class="form-control">
                                                <option value="">Выберите макет</option>
                                                @foreach($layouts as $layout)
                                                    <option value="{{ $layout->id }}"{{ $layout->id == old('layout_id', $layout_id) || (!old('layout_id', $layout_id) && $layout->route == 'pages') ? ' selected' : '' }}>{{ $layout->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
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
                    <form target="preview" action="{{ url('admin/extension/htmlblock/ajax') }}" id="fake" method="post" style="display: none">
                        @csrf
                        <input type="hidden" name="css" class="css" />
                        @foreach($langs as $l)
                            <input type="hidden" name="html[{{ $l['code'] }}]" data-code="{{ $l['code'] }}" class="html2" />
                        @endforeach
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('vendor-scripts')
    <script src="{{asset('assets/admin/js/select2.full.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/tinymce/jquery.tinymce.min.js')}}"></script>
@endsection

@section('page-scripts')
    <link rel="stylesheet" href="{{ asset('assets/admin/js/codemirror-5.31.0/lib/codemirror.css') }}"/>
    <script src="{{ asset('assets/admin/js/codemirror-5.31.0/lib/codemirror.js') }}"></script>
    <script src="{{ asset('assets/admin/js/codemirror-5.31.0/clike.js') }}"></script>
    <script src="{{ asset('assets/admin/js/codemirror-5.31.0/mode/xml/xml.js') }}"></script>
    <script src="{{ asset('assets/admin/js/codemirror-5.31.0/mode/htmlmixed/htmlmixed.js') }}"></script>
    <script src="{{ asset('assets/admin/js/codemirror-5.31.0/mode/css/css.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/admin/js/codemirror-5.31.0/theme/dracula.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/admin/js/codemirror-5.31.0/addon/fold/foldgutter.css') }}"/>
    <script src="{{ asset('assets/admin/js/codemirror-5.31.0/addon/fold/foldcode.js') }}"></script>
    <script src="{{ asset('assets/admin/js/codemirror-5.31.0/addon/fold/foldgutter.js') }}"></script>
    <script src="{{ asset('assets/admin/js/codemirror-5.31.0/addon/fold/brace-fold.js') }}"></script>
    <script src="{{ asset('assets/admin/js/codemirror-5.31.0/addon/fold/comment-fold.js') }}"></script>
    <script src="{{ asset('assets/admin/js/codemirror-5.31.0/addon/edit/matchbrackets.js') }}"></script>
    <script>
        $(document).on('click', '.mode_html', function() {
            $(this).parent().next().slideToggle();
            $('#html').slideToggle();

            $('.html').each(function (i, e) {
                window['editor_html' + i] = CodeMirror.fromTextArea(this, {
                    mode: "text/xml",
                    theme: "dracula",
                    indentUnit: 4,
                    smartIndent: true,
                    foldGutter: true,
                    tabSize: 4,
                    indentWithTabs: true,
                    fixedGutter: true,
                    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                    matchBrackets: true,
                    styleActiveLine: true,
                    lineNumbers: true,
                    lineWrapping: false
                });

                window['editor_html' + i].on("change", function (cm) {
                    $(e).text(cm.getValue());
                    $('.html2[data-code="' + $(e).attr('data-code') + '"]').val(cm.getValue());
                });
            });

            window.css = CodeMirror.fromTextArea($('.css')[0], {
                mode: "text/css",
                lineNumbers: true,
                theme: "dracula",
                lineWrapping: true,
                foldGutter: true,
                indentUnit: 4,
                smartIndent: true,
                tabSize: 4,
                indentWithTabs: true,
                fixedGutter: true,
                gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                matchBrackets: true,
            });

            window.css.on("change", function (cm) {
                $('#fake .css').val(cm.getValue());
                $('.css').val(cm.getValue());
            });

            return false;
        });
    </script>
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

            $('#fake .css').val($('#preview2 .css').val());

            $('#preview2 .html').each(function() {
                $('#fake .html2[data-code="' + $(this).attr('data-code') + '"]').attr('value', $(this).val());
            });

            $('#fake').submit();
        }

        function addIframe(w, h) {
            var wr = document.getElementById('custom-result-wrapper');
            wr.style.maxWidth = w + 'px';
            
            if (!$('#preview').length) {
                wr.innerHTML = `<span class="iframe-wrapper" style="padding-top: ${h * 100 / w}%;"><span class="iframe-inner"><iframe name="preview" id="preview" width="${w}" height="${h}"></iframe></span></span>`;
            } else {
                $('#preview').attr('width', w);
                $('#preview').attr('height', h);
            }

            iframeScale();
        }


        $(document).on('submit', '#fake', function(e){
            $(e).submit();
        });

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

            addIframe(width, height);

            return false;
        });

        @if (!empty($setting['html']))
        var width = $('.frame.active').attr('data-width');
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

        addIframe(width, height);
        @endif
    </script>
    <script>
        var attribute_row = {{ $attribute_row }};
        var image_row = {{ $image_row }};

        $(document).on('click', '.add_page_image', function () {
            $(this).parent().next().append('<a href="#" class="event_file" data-input="#page_images-' + image_row + '"><img src="{{ asset('/images/no_image.png') }}" /><input id="page_images-' + image_row + '" type="hidden" name="images[]" /></a>');
            image_row++;
            return false;
        });

        $(document).on('click', '.add_attribute_image', function () {
            var row = $(this).attr('data-row');
            var x = $(this).next().find('a').length;
            $(this).next().append('<a href="#" class="event_file" data-input="#attribute_image-' + row + '-' + x + '"><img src="{{ asset('/images/no_image.png') }}" /><input id="attribute_image-' + row + '-' + x + '" type="hidden" name="page_attribute[' + row + '][image][' + x + ']" /></a>');
            x++;
            return false;
        });
    </script>

    @if (!$langs->isEmpty())
        <script>
            var $selectMulti = $("#attribute_tab .select2");

            $selectMulti.select2({
                dropdownAutoWidth: true,
                width: '100%',
                minimumResultsForSearch: 1,
                placeholder: "Выберите характеристику"
            });

            $('[name="parent_id"]').select2({
                dropdownAutoWidth: true,
                width: '100%',
                minimumResultsForSearch: 1,
                placeholder: "Выберите категорию"
            });

            function deleteAttribute(id) {
                var val = $selectMulti.val();
                var arr = [];

                for (var i in val) {
                    if (val[i] != id) {
                        arr.push(val[i]);
                    }
                }

                $selectMulti.val(arr).trigger('change');
                $('#attribute-' + id).remove();
            }

            $selectMulti.on('select2:select', function (e) {
                var data = e.params.data;
                var id = data.id;
                var text = data.text;

                var html = '<div style="padding: 15px;border: 1px solid #eee" id="attribute-' + attribute_row + '">\n' +
                    '           <div class="row">\n' +
                    '               <div class="col-12 col-sm-6 col-lg-3">\n' +
                    text +
                    '                   <input type="hidden" name="page_attribute[' + attribute_row + '][attribute_id]" value="' + id + '" />\n' +
                    '               </div>\n' +
                    '               <div class="col-12 col-sm-6 col-lg-8">\n';
                @foreach($langs as $lang)
                    html += '           <fieldset style="margin-bottom: 10px">\n' +
                    '                       <div class="input-group">\n' +
                    '                           <div class="input-group-prepend">\n' +
                    '                               <span class="input-group-text"><img style="width: 20px" src="{{ asset($lang->image) }}" title="{{ $lang->name }}" /></span>\n' +
                    '                           </div>\n' +
                    '                           <textarea name="page_attribute[' + attribute_row + '][page_attribute_description][{{ $lang->code }}][text]" placeholder="Описание" class="form-control"></textarea>\n' +
                    '                       </div>\n' +
                    '                   </fieldset>\n';
                @endforeach
                    html += '       </div>\n' +
                    '               <div class="col-12 col-sm-2 col-lg-1">\n' +
                    '                   <a href="#" onclick="deleteAttribute(' + id + ');return false;" class="btn btn-danger">&times;</a>\n' +
                    '               </div>' +
                    '            </div>' +
                    '            <div class="row">' +
                    '               <div class="col-12 col-sm-12 col-lg-12">' +
                    '                  <a href="#" class="btn btn-primary add_attribute_image" data-row="'+ attribute_row + '">Добавить изображения</a>' +
                    '                  <div class="preview"></div>' +
                    '               </div>' +
                    '           </div>' +
                    '        </div>';

                $('#attr').append(html);
                attribute_row++;
            });
        </script>
    @endif
@endsection