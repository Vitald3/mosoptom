@extends('layouts.contentLayoutMaster')
@if($id)
    @section('title','Редактирование схемы')
@else
    @section('title','Создание схемы')
@endif

@section('page-styles')
    <style>
        ol.sortable {
            margin: 0;
            list-style: none;
        }
        .sortable ol {list-style: none}
        .menu-item-handle {
            width: 100%;
            z-index: 1;
            cursor: move;
            display: flex;
            align-items: center;
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
        .menu-item-handle select {
            height: 40px;
            border: 1px solid #dfe2e4;
            border-radius: 0;
            padding: 0 15px;
            width: 66%;
            outline: 0;
        }
        .menu-item-handle span {
            vertical-align: middle;
            background: #5a8dee;
            color: #fff;
            text-align: center;
            border-radius: 4px;
            width: 30%;
            height: 38px;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .menu-item-handle button {
            height: 38px;
        }
    </style>
@endsection

@section('content')
    <section class="users-edit">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    @if(session('error'))
                        <span class="btn btn-danger invalid-feedback" role="alert" style="margin-bottom:20px;display: block"><strong>{{ $session('error') }}</strong></span>
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
                                    <label>Выберите тип</label>
                                    <select name="route" class="form-control @error('route') is-invalid @enderror" required>
                                        @foreach($routes as $key => $r)
                                            <option value="{{ $key }}"{{ old('route', $route) == $key ? ' selected' : '' }}>{{ $r }}</option>
                                        @endforeach
                                    </select>
                                    @error('route')
                                    <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <hr>
                                <?php $module_row = 0; ?>
                                @foreach($positions as $key => $position)
                                    <div class="form-group">
                                        <div class="controls">
                                            <label>{{ $position }}</label>
                                            <div class="menu-item-handle tfoot-{{ $key }}" style="padding-left: 10px;cursor: default">
                                                <span>Выберите модуль</span>
                                                <select>
                                                    @foreach($extensions as $extension)
                                                        <optgroup label="{{ $extension['name'] }}">
                                                            @if(!$extension['module'])
                                                                <option value="{{ $extension['code'] }}" data-id="">{{ $extension['name'] }}</option>
                                                            @else
                                                                @foreach($extension['module'] as $module)
                                                                    <option value="{{ $module['code'] }}" data-id="{{ $module['id'] }}">{{ $module['name'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                                <button type="button" onclick="addModule('{{ $key }}');" data-toggle="tooltip" title="Добавить" class="btn btn-primary btn-sm"><i class="bx bx-plus-circle"></i></button>
                                            </div>
                                            <ol class="sortable sortable-{{ $key }}" data-type="{{ $key }}">
                                                @if(!empty($layout_extensions))
                                                    @foreach($layout_extensions as $key2 => $layout_extension)
                                                        @if($key == $layout_extension['position'])
                                                            <li id="menuItem_{{ $key }}_{{ $module_row }}">
                                                                <div class="menu-item-handle tfoot">
                                                                    <select name="layout_extension[{{ $module_row }}][code]" class="code_select" style="width: 90%">
                                                                        @foreach($extensions as $extension)
                                                                            <optgroup label="{{ $extension['name'] }}">
                                                                                @if(!$extension['module'])
                                                                                    @if($extension['code'] == $layout_extension['code'])
                                                                                        <option value="{{ $extension['code'] }}" selected="selected" data-id="">{{ $extension['name'] }}</option>
                                                                                    @else
                                                                                        <option value="{{ $extension['code'] }}" data-id="">{{ $extension['name'] }}</option>
                                                                                    @endif
                                                                                @else
                                                                                    @foreach($extension['module'] as $module)
                                                                                        @if($module['code'] == $layout_extension['code'])
                                                                                            <option value="{{ $module['code'] }}" selected="selected" data-id="{{ $module['id'] }}">{{ $module['name'] }}</option>
                                                                                        @else
                                                                                            <option value="{{ $module['code'] }}" data-id="{{ $module['id'] }}">{{ $module['name'] }}</option>
                                                                                        @endif
                                                                                    @endforeach
                                                                                @endif
                                                                            </optgroup>
                                                                        @endforeach
                                                                    </select>
                                                                    <input type="hidden" name="layout_extension[{{ $module_row }}][extension_id]" class="extension_input" value="{{ $layout_extension['extension_id'] }}" />
                                                                    <input type="hidden" name="layout_extension[{{ $module_row }}][position]" class="position_input" value="{{ $layout_extension['position'] }}" />
                                                                    <input type="hidden" name="layout_extension[{{ $module_row }}][sort]" class="sort_input" value="{{ $layout_extension['sort'] }}" />
                                                                    <button type="button" onclick="$('#menuItem_{{ $key }}_{{ $module_row }}').remove();sort('{{ $key }}');" data-toggle="tooltip" title="Удалить" class="btn btn-danger btn-sm"><i class="bx bx-minus-circle"></i></button>
                                                                </div>
                                                            </li>
                                                        @endif
                                                        <?php $module_row++; ?>
                                                    @endforeach
                                                @endif
                                            </ol>
                                        </div>
                                    </div>
                                @endforeach
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
        function sort(e) {
            var type = e.attr('data-type');

            e.nestedSortable({
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
                    $('.sortable-' + type + ' .sort_input').each(function(i, element) {
                        $(element).val(i);
                        $(element).parent().find('.sort_input').attr('name', 'layout_extension[' + i + '][sort]');
                        $(element).parent().find('.extension_input').attr('name', 'layout_extension[' + i + '][extension_id]');
                        $(element).parent().find('.position_input').attr('name', 'layout_extension[' + i + '][position]');
                        $(element).parent().find('.code_select').attr('name', 'layout_extension[' + i + '][code]');
                    });
                }
            });
        }

        $('.sortable').each(function () {
            sort($(this));
        });

        $(document).on('change', '.code_select', function (){
            $(this).next().val($(this).find('option:selected').attr('data-id'));
        });

        var module_row = {{ $module_row }};

        function addModule(type) {
            html  = '<li id="menuItem_' + type + '_' + module_row + '">';
            html += '  <div class="menu-item-handle"><select name="layout_extension[' + module_row + '][code]" class="code_select" style="width: 90%">';
            @foreach($extensions as $extension)
                html += '    <optgroup label="{{ $extension['name'] }}">';
            @if(!$extension['module'])
                html += '      <option value="{{ $extension['code'] }}" data-id="">{{ $extension['name'] }}</option>';
            @else
                    @foreach($extension['module'] as $module)
                html += '      <option value="{{ $module['code'] }}" data-id="{{ $module['id'] }}">{{ $module['name'] }}</option>';
            @endforeach
                    @endif
                html += '    </optgroup>';
            @endforeach
                html += '  </select>';
            html += '  <input type="hidden" name="layout_extension[' + module_row + '][extension_id]" class="extension_input" value="0" /><input type="hidden" name="layout_extension[' + module_row + '][position]" class="position_input" value="' + type + '" />';
            html += '  <input type="hidden" name="layout_extension[' + module_row + '][sort]" class="sort_input" value="" />';
            html += '  <button type="button" onclick="$(\'#menuItem_' + type + '_' + module_row + '\').remove();sort(\'' + type + '\');" data-toggle="tooltip" title="Удалить" class="btn btn-danger btn-sm"><i class="bx bx-minus-circle"></i></button></div>';
            html += '</li>';

            $('.sortable-' + type).append(html);

            $('.sortable-' + type + ' select[name=\'layout_extension[' + module_row + '][code]\']').val($('.tfoot-' + type + ' select').val());
            $('.sortable-' + type + ' [name=\'layout_extension[' + module_row + '][extension_id]\']').val($('.tfoot-' + type + ' select option:selected').attr('data-id'));

            $('.sortable-' + type + ' .sort_input').each(function(i, element) {
                $(element).val(i);
                $(element).parent().find('.sort_input').attr('name', 'layout_extension[' + i + '][sort]');
                $(element).parent().find('.extension_input').attr('name', 'layout_extension[' + i + '][extension_id]');
                $(element).parent().find('.position_input').attr('name', 'layout_extension[' + i + '][position]');
                $(element).parent().find('.code_select').attr('name', 'layout_extension[' + i + '][code]');
            });

            sort($('.sortable-' + type));

            module_row++;
        }
    </script>
@endsection