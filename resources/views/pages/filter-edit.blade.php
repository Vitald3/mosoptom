@extends('layouts.contentLayoutMaster')
@if($id)
    @section('title','Редактирование фильтра')
@else
    @section('title','Создание фильтра')
@endif
@section('vendor-styles')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/admin/css/select2.min.css')}}">
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
                    <form action="{{ $action }}" method="post" novalidate>
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
                                        <a class="nav-link" id="values" data-toggle="tab" href="#filter_tab" role="tab" aria-controls="filter_tab" aria-selected="true">
                                            Значения
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
                                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>Заполните Название</strong></span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Описание</label>
                                                                <textarea rows="5" name="meta[{{ $l['code'] }}][description]" class="form-control" placeholder="Описание">{{ old('meta.' . $l['code'] . '.description', !empty($meta[$l['code']]['description']) ? $meta[$l['code']]['description'] : '') }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="tab-pane" id="data_tab" role="tabpanel" aria-labelledby="data">
                                        <div class="form-group multiple-select2">
                                            <label>Отображать в категориях</label>
                                            <select name="filter_category[]" class="form-control select2 @error('filter_category') is-invalid @enderror" required  multiple>
                                                @foreach($categories as $category)
                                                    @if(!is_null($category->metaLang))
                                                        <option value="{{ $category->id }}"{{ in_array($category->id, (array)old('filter_category', $filter_category)) ? ' selected' : '' }}>{{ $category->metaLang['name'] }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @error('filter_category')
                                            <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Тип</label>
                                                <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                                    <option value="">Выберите тип</option>
                                                    <option value="checkbox"{{ old('type', $type) == 'checkbox' ? ' selected' : '' }}>Флажек</option>
                                                    <option value="radio"{{ old('type', $type) == 'radio' ? ' selected' : '' }}>Кнопка</option>
                                                    <option value="select"{{ old('type', $type) == 'select' ? ' selected' : '' }}>Список</option>
                                                    <option value="slider"{{ old('type', $type) == 'slider' ? ' selected' : '' }}>Слайдер</option>
                                                </select>
                                                @error('type')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Порядок сортировки</label>
                                                <input type="text" name="sort" class="form-control" placeholder="Порядок сортировки" value="{{ old('sort', $sort) }}">
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
                                    <div class="tab-pane" id="filter_tab" role="tabpanel" aria-labelledby="values">
                                        <?php $value_row = 0; ?>
                                        <div class="form-group">
                                            <div id="filters">
                                                @if(!$langs->isEmpty() && $filter_values)
                                                    @foreach($filter_values as $value)
                                                        <div style="padding: 15px;border: 1px solid #eee" id="value-{{ $value_row }}">
                                                            <div class="row">
                                                                <div class="col-12 col-sm-5 col-lg-6">
                                                                    @foreach($langs as $lang)
                                                                        <fieldset style="margin-bottom: 10px">
                                                                            <div class="input-group">
                                                                                <div class="input-group-prepend">
                                                                                    <span class="input-group-text"><img style="width: 20px" src="{{ asset($lang->image) }}" title="{{ $lang->name }}" /></span>
                                                                                </div>
                                                                                <textarea name="filter_values[{{ $value_row }}][description][{{ $lang->code }}][name]" placeholder="Название" class="form-control">{{ old('filter_values.' . $value_row . '.description.' . $lang->code . '.name', !empty($value['description'][$lang->code]) ? $value['description'][$lang->code]['name'] : '') }}</textarea>
                                                                            </div>
                                                                        </fieldset>
                                                                    @endforeach
                                                                </div>
                                                                <div class="col-12 col-sm-3 col-lg-2">
                                                                    <fieldset>
                                                                        <div class="checkbox">
                                                                            <input type="checkbox" name="filter_values[{{ $value_row }}][top]" class="checkbox-input" value="1" id="checkbox{{ $value_row }}"{{ old('filter_values.' . $value_row . '.top', !empty($value['top']) ? $value['top'] : 0) ? ' checked' : '' }} />
                                                                            <label for="checkbox{{ $value_row }}">Быстрый доступ</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-12 col-sm-3 col-lg-3">
                                                                    <input type="text" name="filter_values[{{ $value_row }}][sort]" class="form-control" placeholder="Порядок сортировки"
                                                                           value="{{ old('filter_values.' . $value_row . '.sort', !empty($value['sort']) ? $value['sort'] : '') }}">
                                                                    <input type="text" name="filter_values[{{ $value_row }}][slug]" class="form-control" placeholder="Seo url"
                                                                           value="{{ old('filter_values.' . $value_row . '.slug', !empty($value['slug']) ? $value['slug'] : '') }}">
                                                                    <input type="hidden" name="filter_values[{{ $value_row }}][id]" value="{{ $value['id'] }}" />
                                                                </div>
                                                                <div class="col-12 col-sm-2 col-lg-1">
                                                                    <a href="#" onclick="$('#value-{{ $value_row }}').remove();return false;" class="btn btn-danger">&times;</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php $value_row++; ?>
                                                    @endforeach
                                                @elseif($langs->isEmpty())
                                                    <div class="alert alert-danger">Создайте языки</div>
                                                @endif
                                            </div>
                                            @error('filter_values')
                                            <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <a href="#" onclick="addFilterValue();return false;" class="btn btn-primary">+</a>
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
                </div>
            </div>
        </div>
    </section>
@endsection

@section('vendor-scripts')
    <script src="{{asset('assets/admin/js/select2.full.min.js')}}"></script>
@endsection

@section('page-scripts')
    <script>
        var $selectMulti = $(".select2").select2();

        $selectMulti.select2({
            dropdownAutoWidth: true,
            width: '100%',
            minimumResultsForSearch: -1,
            placeholder: "Выберите категорию"
        });
    </script>
    @if (!$langs->isEmpty())
        <script>
            var value_row = {{ $value_row }};

            function addFilterValue() {
                var html = '<div style="padding: 15px;border: 1px solid #eee" id="value-' + value_row + '">\n' +
                    '           <div class="row">\n' +
                    '               <div class="col-12 col-sm-5 col-lg-6">\n';
                @foreach($langs as $lang)
                    html += '           <fieldset style="margin-bottom: 10px">\n' +
                    '                       <div class="input-group">\n' +
                    '                           <div class="input-group-prepend">\n' +
                    '                               <span class="input-group-text"><img style="width: 20px" src="{{ asset($lang->image) }}" title="{{ $lang->name }}" /></span>\n' +
                    '                           </div>\n' +
                    '                           <textarea name="filter_values[' + value_row + '][description][{{ $lang->code }}][name]" placeholder="Название" class="form-control"></textarea>\n' +
                    '                       </div>\n' +
                    '                   </fieldset>\n';
                @endforeach
                    html += '       </div>' +
                    '               <div class="col-12 col-sm-3 col-lg-2">' +
                    '                 <fieldset>' +
                    '                   <div class="checkbox">' +
                    '                     <input type="checkbox" name="filter_values[' + value_row + '][top]" class="checkbox-input" value="1" id="checkbox' + value_row + '" />' +
                    '                     <label for="checkbox' + value_row + '">Быстрый доступ</label>' +
                    '                   </div>' +
                    '                 </fieldset>' +
                    '               </div>\n' +
                    '               <div class="col-12 col-sm-3 col-lg-3">\n' +
                    '                   <input type="text" name="filter_values[' + value_row + '][sort]" class="form-control" placeholder="Порядок сортировки">\n' +
                    '                   <input type="text" name="filter_values[' + value_row + '][slug]" class="form-control" placeholder="Seo url" />\n' +
                    '                   <input type="hidden" name="filter_values[' + value_row + '][id]" />\n' +
                    '               </div>' +
                    '               <div class="col-12 col-sm-2 col-lg-1">\n' +
                    '                   <a href="#" onclick="$(\'#value-' + value_row + '\').remove();return false;" class="btn btn-danger">&times;</a>\n' +
                    '               </div>' +
                    '            </div>\n' +
                    '        </div>';

                $('#filters').append(html);
                value_row++;
            }
        </script>
    @endif
@endsection