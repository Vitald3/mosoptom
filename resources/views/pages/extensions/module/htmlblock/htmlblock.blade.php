@extends('layouts.contentLayoutMaster')
@section('title','HTML - Блок')

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
                    <form action="{{ $action }}" id="preview2" method="post" novalidate>
                        @csrf
                        @if($id)
                            <input type="hidden" name="id" value="{{ $id }}" />
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
                                                <div class="controls">
                                                    <label>HTML код</label>
                                                    <hr>
                                                    <textarea rows="9" name="setting[html][{{ $l['code'] }}]" data-code="{{ $l['code'] }}" class="html form-control">{!! old('setting.html.' . $l['code'], !empty($setting['html'][$l['code']]) ? $setting['html'][$l['code']] : '') !!}</textarea>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <hr>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Стили css</label>
                                        <textarea name="setting[css]" rows="5" class="css form-control" placeholder="Стили css">{{ old('setting.css', !empty($setting['css']) ? $setting['css'] : '') }}</textarea>
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
        $('.html').each(function(i, e) {
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

            window['editor_html' + i].on("change", function(cm) {
                $(e).val(cm.getValue());
                $(e).trigger('change');
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

        window.css.on("change", function(cm) {
            $('#fake .css').val(cm.getValue());
            $('.css').trigger('change');
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
            if (!$('#preview').length) {
                var wr = document.getElementById('custom-result-wrapper');
                wr.style.maxWidth = w + 'px';
                wr.innerHTML = `<span class="iframe-wrapper" style="padding-top: ${h * 100 / w}%;"><span class="iframe-inner"><iframe name="preview" id="preview" width="${w}" height="${h}"></iframe></span></span>`;
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

            addIframe($(this).attr('data-width'), height);

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

        $(document).on('change', '.css, .html', function () {
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
        });
    </script>
@endsection