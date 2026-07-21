@extends('layouts.contentLayoutMaster')
@if($id)
    @section('title','Редактирование элемента')
@else
    @section('title','Создание элемента')
@endif
@section('vendor-styles')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/admin/css/prism.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/admin/css/dropzone.min.css')}}">
@endsection

@section('page-styles')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/admin/css/dropzone.css')}}">
    @if($x)
        <style>
            .header-navbar.main-header-navbar, footer, .main-menu, .content-overlay {
                display: none;
            }
            html body .content.app-content {
                margin: 0;
            }
            .card {
                margin-bottom: 0;
                box-shadow: none;
            }
            html body.navbar-sticky .app-content .content-wrapper {
                padding: 0;
                margin-top: 0;
            }
        </style>
    @endif
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
                    <form action="{{ $action }}" id="form-element" method="post" novalidate>
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
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Css класс элемента</label>
                                        <input type="text" name="class" class="form-control @error('class') is-invalid @enderror" placeholder="Css класс элемента"
                                               value="{{ old('class', $class) }}" required>
                                        @error('class')
                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                @if(!empty($elements))
                                    <div class="form-group">
                                        <div class="controls">
                                            <label>Выберите элемент</label>
                                            <select onchange="addElement(this.value);" name="code" class="form-control @error('code') is-invalid @enderror" placeholder="элемент" required>
                                                <option value="">Выберите элемент</option>
                                                @foreach($elements as $key => $element)
                                                    <option value="{{ $key }}"{{ old('code', $code) == $key ? ' selected' : '' }}>{{ $element['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('code')
                                            <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <hr>
                                    <div id="elements">
                                        @if(!empty($setting) && isset($elements[$code]['params']))
                                            @include('pages.element-indent', ['setting' => $setting, 'code' => $code, 'params' => isset($elements[$code]['params']) ? $elements[$code]['params'] : [], 'name' => $elements[$code]['name']])
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                @role('edit|create')
                                <button type="submit" onclick="if(typeof parent.reloadElement == 'function'){$.get('{{ $action }}', $('#form-element').serialize(), function(){parent.reloadElement()});return false;}" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Сохранить</button>
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

@section('vendor-scripts')
    <script src="{{asset('assets/admin/js/dropzone.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/prism.min.js')}}"></script>
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/admin/js/color-picker.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/admin/css/color-picker.css') }}" />

    <script>
        function media() {
            var style = '<style id="canvas_style">';

            $('.styles > div').each(function(i, e){
                var style2 = '';

                $(this).find('input, select').each(function(){
                    if ($(this).val() != '') {
                        if (($(this).attr('type') == 'checkbox' && $(this).prop('checked')) || $(this).attr('type') != 'checkbox') style2 += $(this).attr('data-name');

                        if ($(this).attr('type') != 'checkbox') {
                            style2 += ':' + $(this).val();
                        }

                        if (($(this).attr('type') == 'checkbox' && $(this).prop('checked')) || $(this).attr('type') != 'checkbox') style2 += ';';
                        t = true;
                    }
                });

                if (style2) {
                    if (i == 0) {
                        style += '@media (max-width: 767px) {';
                    } else if (i == 1) {
                        style += '@media (min-width: 767px) and (max-width: 991px) {';
                    } else if (i == 2) {
                        style += '@media (min-width: 992px) and (max-width: 1199px) {';
                    } else if (i == 3) {
                        style += '@media (min-width: 1200px) {';
                    }

                    if ($(e).hasClass('children')) {
                        style += '#canvas li {';
                    } else {
                        style += '#canvas' + (i == 5 ? ':hover' : '') + ' {';
                    }

                    style += style2 + '}';

                    if (i == 0 || i == 1 || i == 2 || i == 3) {
                        style += '}';
                    }
                }
            });

            if ($('[name="code"]').val() == 'ul' || $('[name="code"]').val() == 'ol' || $('[name="code"]').val() == 'li') {
                $('#canvas').html('<li>( Просмотр элемента )</li><li>( Просмотр элемента )</li>');
            } else if ($('[name="code"]').val() == 'img') {
                $('#canvas').html('<img src="/images/settings/Logo.svg" id="canvas" />');
            } else if ($('[name="code"]').val() == 'a') {
                $('#canvas').attr('href', '#');
            } else {
                $('#canvas').text('( Просмотр элемента )');
            }

            style += '</style>';

            $('#canvas_style').remove();
            $('#canvas').before(style);
        }

        $(document).on('change', '.styles > div input, .styles > div select', function () {
            media();
        });

        if ($('[name="code"]').val() != '') media();

        function colors() {
            $('.color, [data-name="background"], [data-name="color"], [placeholder="color"]').each(function(i, e){
                e = $(e);

                new CP(this).on("drag", function(r, g, b, a) {
                    this.source.value = this.color(r, g, b, a);
                    e.css('background', this.color(r, g, b, a));
                    e.trigger('change');
                });

                if (e.val()) {
                    CP.HEX(e.val());
                }
            });
        }

        $(document).ready(function(){
            Dropzone.autoDiscover = false;

            dpz();
        });

        function dpz() {
            <?php $x = 0; ?>
            @foreach([0, 1, 2, 3, 4, 5] as $param)
            $("#dpz-single-file{{ $x }}").dropzone({
                paramName: "file", // The name that will be used to transfer the file
                maxFiles: 1,
                addRemoveLinks: true,
                init: function () {
                    var thisDropzone{{ $x }} = this;
                    @if(!empty($setting[$x]['background-image']))
                    <?php $setting[$x]['background-image'] = str_replace([url('') . '/', "url('", "')", ";"], '', $setting[$x]['background-image']); ?>
                    var mockFile{{ $x }} = {
                        name: '{{ basename($setting[$x]['background-image']) }}',
                        size: '{{ filesize($setting[$x]['background-image']) }}'
                    };
                    thisDropzone{{ $x }}.options.addedfile.call(thisDropzone{{ $x }}, mockFile{{ $x }});
                    thisDropzone{{ $x }}.options.thumbnail.call(thisDropzone{{ $x }}, mockFile{{ $x }}, '/{{ $setting[$x]['background-image'] }}');
                    @endif
                        this.on("success", function (file, json) {
                        if (json) {
                            $('[name="setting[{{ $x }}][background-image]"]').remove();

                            for (i in json) {
                                $('#dropzone-examples{{ $x }}').append('<input type="hidden" data-name="' + $('#dropzone-examples{{ $x }}').attr('data-name') + '" name="setting[{{ $x }}][background-image]" value="url(\'{{ url('') }}/' + json[i] + '\');" />')
                            }

                            media();
                        }
                    });
                    this.on("maxfilesexceeded", function (file) {
                        this.removeAllFiles();
                        this.addFile(file);
                    });
                    this.on("removedfile", function (file) {
                        $('[name="setting[{{ $x }}][background-image]"]').remove();
                        media();
                    });
                    $("#dpz-single-file{{ $x }}").addClass('dropzone');
                }
            });
            <?php $x++; ?>
            if ($('[name="setting[{{ $x }}][children][background-image]"]').length) {
                $("#dpz-single-file{{ $x }}").dropzone({
                    paramName: "file", // The name that will be used to transfer the file
                    maxFiles: 1,
                    addRemoveLinks: true,
                    init: function () {
                        var thisDropzone{{ $x }} = this;
                        @if(!empty($setting[$x]['children']['background-image']))
                        <?php $setting[$x]['children']['background-image'] = str_replace([url('') . '/', "url('", "')", ";"], '', $setting[$x]['children']['background-image']); ?>
                        var mockFile{{ $x }} = {
                            name: '{{ basename($setting[$x]['children']['background-image']) }}',
                            size: '{{ filesize($setting[$x]['children']['background-image']) }}'
                        };
                        thisDropzone{{ $x }}.options.addedfile.call(thisDropzone{{ $x }}, mockFile{{ $x }});
                        thisDropzone{{ $x }}.options.thumbnail.call(thisDropzone{{ $x }}, mockFile{{ $x }}, '/{{ $setting[$x]['children']['background-image'] }}');
                        @endif
                            this.on("success", function (file, json) {
                            if (json) {
                                $('[name="setting[{{ $x }}][children][background-image]"]').remove();

                                for (i in json) {
                                    $('#dropzone-examples{{ $x }}').append('<input type="hidden" data-name="' + $('#dropzone-examples{{ $x }}').attr('data-name') + '" name="setting[{{ $x }}][children][background-image]" value="url(\'' + json[i] + '\');" />')
                                }

                                media();
                            }
                        });
                        this.on("maxfilesexceeded", function (file) {
                            this.removeAllFiles();
                            this.addFile(file);
                        });
                        this.on("removedfile", function (file) {
                            $('[name="setting[{{ $x }}][children][background-image]"][value="url(\'images/other/' + file.name + '\')"]').remove();
                            media();
                        });
                        $("#dpz-single-file{{ $x }}").addClass('dropzone');
                    }
                });
            }
            <?php $x++; ?>
            @endforeach
        }

        $(document).on('change', '.auto', function() {
            if ($(this).prop('checked')) $(this).closest('.closest').find('input[type="text"]').val('');
        });

        $(document).ready(function() {
            colors();
        });

        function addElement(code) {
            $.ajax({
                url: '{{ url('admin/get_element') }}/' + code + ('{{ $id ? '/' . $id : '' }}'),
                'type': 'get',
                'dataType': 'html',
                success: function(html) {
                    $('#elem, .look').remove();
                    $('#elements').append(html);

                    colors();

                    if ($('#dpz-single-file0').length) {
                        dpz();
                    }

                    media();

                    if (typeof parent.parentcalc == 'function') {
                        parent.parentcalc();
                    }
                }
            });
        }
    </script>
@endsection