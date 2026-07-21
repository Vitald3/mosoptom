@extends('layouts.contentLayoutMaster')
@if($id)
    @section('title','Редактирование товара')
@else
    @section('title','Создание товара')
@endif
@section('vendor-styles')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/admin/css/select2.min.css')}}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection

@section('page-styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/pickadate.css') }}">
    <style>
        #add_specials .picker__holder, #add_discounts .picker__holder {
            width: 320px !important;
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
                                        <a class="nav-link" id="attributes" data-toggle="tab" href="#attribute_tab" role="tab" aria-controls="attribute_tab" aria-selected="true">
                                            Характеристики
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="options" data-toggle="tab" href="#options_tab" role="tab" aria-controls="options_tab" aria-selected="true">
                                            Опции
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="filters" data-toggle="tab" href="#filter_tab" role="tab" aria-controls="filter_tab" aria-selected="true">
                                            Фильтры
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="specials" data-toggle="tab" href="#specials_tab" role="tab" aria-controls="specials_tab" aria-selected="true">
                                            Акции
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="discounts" data-toggle="tab" href="#discounts_tab" role="tab" aria-controls="discounts_tab" aria-selected="true">
                                            Скидки
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="images" data-toggle="tab" href="#images_tab" role="tab" aria-controls="images_tab" aria-selected="true">
                                            Изображение
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="rewards" data-toggle="tab" href="#rewards_tab" role="tab" aria-controls="rewards_tab" aria-selected="true">
                                            Бонусные баллы
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
                                                        <textarea name="meta[{{ $l['code'] }}][description]" class="tinymce">{!! old('meta.' . $l['code'] . '.description', !empty($meta[$l['code']]['description']) ? $meta[$l['code']]['description'] : '') !!}</textarea>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="tab-pane" id="data_tab" role="tabpanel" aria-labelledby="data">
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Отображать в категориях</label>
                                                @if(!empty($categories))
                                                    <div class="categories well">
                                                        @foreach($categories as $key => $category)
                                                            <fieldset style="margin-bottom: 15px">
                                                                <div class="checkbox">
                                                                    <input onchange="parent_category_add();" type="checkbox" name="product_category[]" value="{{ $key }}" class="checkbox-input" id="checkbox{{ $key }}"{{ in_array($key, $product_category) ? ' checked' : '' }}>
                                                                    <label for="checkbox{{ $key }}">{{ $category }}</label>
                                                                </div>
                                                            </fieldset>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="alert alert-danger">Создайте категории</div>
                                                @endif
                                            </div>
                                        </div>
                                        @if(!empty($categories))
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label>Главная категория</label>
                                                    <select name="parent_id" class="form-control">
                                                        <option value="">Выберите категорию</option>
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="form-group">
                                            <label>Сопутствующие товары</label>
                                            <div class="controls">
                                                <input type="text" name="related" placeholder="Сопутствующие товары" class="form-control">
                                                <div id="related-product" class="well well-sm" style="height: 150px; overflow: auto;">
                                                    @foreach($product_related as $product)
                                                        <div id="related-product{{ $product['related_id'] }}">
                                                            <i class="minus">-</i> {{ $product['name'] }}
                                                            <input type="hidden" name="product_related[]" value="{{ $product['related_id'] }}" />
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Производитель</label>
                                                <input type="text" name="manufacturer" class="form-control" placeholder="Производитель" value="{{ old('manufacturer', $manufacturer) }}">
                                                <input type="hidden" name="manufacturer_id" value="{{ old('manufacturer_id', $manufacturer_id) }}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Артикуль</label>
                                                <input type="text" name="model" class="form-control" placeholder="Модель" value="{{ old('model', $model) }}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Количество</label>
                                                <input type="text" name="quantity" class="form-control" placeholder="Модель" value="{{ old('quantity', $quantity) }}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Цена</label>
                                                <input type="text" name="price" class="form-control" placeholder="Цена"
                                                       value="{{ old('price', $price) }}">
                                            </div>
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
                                                <label>Состояние на складе</label>
                                                <select name="stock_status_id" class="form-control @error('stock_status_id') is-invalid @enderror" required>
                                                    @foreach($statuses as $s)
                                                        <option value="{{ $s['id'] }}"{{ $s['id'] == old('stock_status_id', $stock_status_id) ? ' selected' : '' }}>{{ $s['name'] }}</option>
                                                    @endforeach
                                                </select>
                                                @error('stock_status_id')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Вес в граммах</label>
                                                <input type="text" name="weight" class="form-control" placeholder="Производитель" value="{{ old('weight', $weight) }}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>SEO URL</label>
                                                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" placeholder="SEO URL"
                                                       value="{{ old('slug', $slug) }}" required />
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
                                                <select name="attribute_im[]" class="form-control select2" multiple>
                                                    @foreach($attributes2 as $attribute)
                                                        <option value="{{ $attribute->id }}"{{ in_array($attribute->id, (array)old('attribute_im', $attribute_im)) ? ' selected' : '' }}>{{ $attribute->metaLang[0]['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @if(!$langs->isEmpty())
                                                <div id="attr">
                                                    @foreach($attributes2 as $attribute)
                                                        @if(in_array($attribute->id, (array)old('attribute_im', $attribute_im)))
                                                            <div style="padding: 15px;border: 1px solid #eee" id="attribute-{{ $attribute->id }}">
                                                                <div class="row">
                                                                    <div class="col-12 col-sm-4 col-lg-3">
                                                                        {{ $attribute->meta[0]['name'] }}
                                                                        <input type="hidden" name="product_attribute[{{ $attribute_row }}][attribute_id]" value="{{ $attribute->id }}" />
                                                                    </div>
                                                                    <div class="col-12 col-sm-6 col-lg-8">
                                                                        @foreach($langs as $lang)
                                                                            <fieldset style="margin-bottom: 10px">
                                                                                <div class="input-group">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text"><img style="width: 20px" src="{{ asset($lang->image) }}" title="{{ $lang->name }}" /></span>
                                                                                    </div>
                                                                                    <textarea name="product_attribute[{{ $attribute_row }}][product_attribute_description][{{ $lang->code }}][text]" placeholder="Описание" class="form-control">{{ !empty($attribute_descriptions[$attribute->id]['descriptions'][$lang->code]) ? $attribute_descriptions[$attribute->id]['descriptions'][$lang->code]['text'] : old('product_attribute.' . $attribute_row . '.product_attribute_description.' . $lang->code . '.text') }}</textarea>
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
                                                                            @if(old('product_attribute.' . $attribute_row . '.image', (isset($attribute_images[$attribute->id]) ? $attribute_images[$attribute->id] : [])))
                                                                                @foreach((array)old('product_attribute.' . $attribute_row . '.image', $attribute_images[$attribute->id]) as $x => $attribute_image)
                                                                                    <a href="#" class="event_file" data-input="#attribute_image-{{ $attribute_row }}-{{ $x }}">
                                                                                        <img src="{{ asset($attribute_image['image']) }}" />
                                                                                        <input id="attribute_image-{{ $attribute_row }}-{{ $x }}" type="hidden" name="product_attribute[{{ $attribute_row }}][image][{{ $x }}]" value="{{ old('product_attribute.' . $attribute_row . '.image.' . $x, $attribute_image['image']) }}" />
                                                                                    </a>
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
                                    <div class="tab-pane" id="options_tab" role="tabpanel" aria-labelledby="options">
                                        <?php $option_row = 0; ?>
                                        <?php $option_value_row = 0; ?>
                                        @if(!$options->isEmpty())
                                            <div class="form-group multiple-select2">
                                                <select class="add_option form-control select2" multiple>
                                                    @foreach($options->toArray() as $option)
                                                        <option value="{{ $option['id'] }}"{{ in_array($option['id'], $product_option_ids) ? ' selected' : '' }} data-type="{{ $option['type'] }}">{{ $option['meta_lang']['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group" id="option-values">
                                                <ul class="nav nav-tabs nav-fill ul_options" role="tablist">
                                                    @foreach($options->toArray() as $key => $option)
                                                        @if((int)old('product_option.' . $key . '.option_id') === $option['id'] || !empty($option['product_option']))
                                                            <li class="nav-item">
                                                                <a class="nav-link{{ $key == 0 ? ' active' : '' }}" id="option_id-{{ $option['id'] }}" data-toggle="tab" href="#option_id-{{ $option['id'] }}_tab" role="tab" aria-controls="option_id-{{ $option['id'] }}_tab" aria-selected="true">
                                                                    {{ $option['meta_lang']['name'] }}
                                                                </a>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                                <div class="tab-content">
                                                    @foreach($options->toArray() as $key => $option)
                                                        @if((int)old('product_option.' . $key . '.option_id') === $option['id'] || !empty($option['product_option']))
                                                            @foreach((array)old('product_option', $option['product_option']) as $product_option)
                                                                <div class="tab-pane{{ $key == 0 ? ' active' : '' }}" id="option_id-{{ $option['id'] }}_tab">
                                                                    <input type="hidden" name="product_option[{{ $option_row }}][option_id]" value="{{ $product_option['option_id'] }}"/>
                                                                    <input type="hidden" name="product_option[{{ $option_row }}][type]" value="{{ $option['type'] }}"/>

                                                                    <div class="form-group">
                                                                        <label for="input-required{{ $option_row }}">Обязательно</label>
                                                                        <div class="controls">
                                                                            <select name="product_option[{{ $option_row }}][required]" id="input-required{{ $option_row }}" class="form-control">
                                                                                @if($product_option['required'])
                                                                                    <option value="1" selected="selected">Да</option>
                                                                                    <option value="0">Нет</option>
                                                                                @else
                                                                                    <option value="1">Да</option>
                                                                                    <option value="0" selected="selected">Нет</option>
                                                                                @endif
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    @if($option['type'] === 'text')
                                                                        <div class="form-group">
                                                                            <label for="input-value{{ $option_row }}">Значение</label>
                                                                            <div class="controls">
                                                                                <input type="text" name="product_option[{{ $option_row }}][value]" value="{{ $product_option['value'] }}" placeholder="Значение" id="input-value{{ $option_row }}" class="form-control"/>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                    @if($option['type'] === 'textarea')
                                                                        <div class="form-group">
                                                                            <label for="input-value{{ $option_row }}">Значение</label>
                                                                            <div class="controls">
                                                                                <textarea name="product_option[{{ $option_row }}][value]" rows="5" placeholder="Значение" id="input-value{{ $option_row }}" class="form-control">{{ $product_option['value'] }}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                    @if($option['type'] === 'date')
                                                                        <div class="form-group">
                                                                            <label for="input-value{{ $option_row }}">Значение</label>
                                                                            <fieldset class="position-relative has-icon-left">
                                                                                <input type="text" name="product_option[{{ $option_row }}][value]" class="form-control pickadate" placeholder="Значение" value="{{ $product_option['value'] }}" id="input-value{{ $option_row }}" >
                                                                                <div class="form-control-position">
                                                                                    <i class='bx bx-calendar'></i>
                                                                                </div>
                                                                            </fieldset>
                                                                        </div>
                                                                    @endif
                                                                    @if($option['type'] === 'time')
                                                                        <div class="form-group">
                                                                            <label for="input-value{{ $option_row }}">Значение</label>
                                                                            <fieldset class="position-relative has-icon-left">
                                                                                <input type="text" name="product_option[{{ $option_row }}][value]" class="form-control pickatime" placeholder="Значение" value="{{ $product_option['value'] }}" id="input-value{{ $option_row }}" >
                                                                                <div class="form-control-position">
                                                                                    <i class='bx bx-calendar'></i>
                                                                                </div>
                                                                            </fieldset>
                                                                        </div>
                                                                    @endif
                                                                    @if($option['type'] === 'datetime')
                                                                        <div class="form-group">
                                                                            <label for="input-value{{ $option_row }}">Значение</label>
                                                                            <fieldset class="position-relative has-icon-left">
                                                                                <input type="text" name="product_option[{{ $option_row }}][value]" class="form-control pickadatetime" placeholder="Значение" value="{{ $product_option['value'] }}" id="input-value{{ $option_row }}" >
                                                                                <div class="form-control-position">
                                                                                    <i class='bx bx-calendar'></i>
                                                                                </div>
                                                                            </fieldset>
                                                                        </div>
                                                                    @endif
                                                                    @if(!empty($product_option['product_option_values']) && ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'color'))
                                                                        <div class="table-responsive">
                                                                            <table id="option-value{{ $option_row }}" class="table">
                                                                                <thead>
                                                                                <tr>
                                                                                    <th class="text-left">Значение</th>
                                                                                    @if($option['type'] !== 'select')
                                                                                        <th class="text-left">Изображение</th>
                                                                                    @endif
                                                                                    <th class="text-left">Кол-во</th>
                                                                                    <th class="text-left">Цена</th>
                                                                                    <th class="text-left">Вес</th>
                                                                                    <th class="text-left">Баллы</th>
                                                                                    <th></th>
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                @foreach($product_option['product_option_values'] as $product_option_value)
                                                                                    <tr id="option-value-row{{ $option_value_row }}">
                                                                                        <td class="text-left">
                                                                                            <select name="product_option[{{ $option_row }}][product_option_values][{{ $option_value_row }}][option_value_id]" class="form-control">
                                                                                                @foreach($option['option_values'] as $option_value)
                                                                                                    <option value="{{ $option_value['id'] }}"{{ $option_value['id'] == $product_option_value['option_value_id'] ? ' selected="selected"' : '' }}>{{ $option_value['option_value_description']['name'] }}</option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </td>
                                                                                        @if($option['type'] !== 'select')
                                                                                            <td class="text-left">
                                                                                                <div class="preview">
                                                                                                    <a href="#" class="event_file" data-input="#option_image-{{ $option_row }}-{{ $option_value_row }}">
                                                                                                        <img src="{{ asset($product_option_value['image'] ? $product_option_value['image'] : 'assets/admin/img/no_image.png') }}" />
                                                                                                        <input id="option_image-{{ $option_row }}-{{ $option_value_row }}" type="hidden" name="product_option[{{ $option_row }}][product_option_values][{{ $option_value_row }}][image]" value="{{ $product_option_value['image'] }}" />
                                                                                                    </a>
                                                                                                </div>
                                                                                            </td>
                                                                                        @endif
                                                                                        <td class="text-left">
                                                                                            <input type="text" name="product_option[{{ $option_row }}][product_option_values][{{ $option_value_row }}][quantity]" value="{{ $product_option_value['quantity'] }}" placeholder="Количество" class="form-control"/>
                                                                                        </td>
                                                                                        <td class="text-left">
                                                                                            <input type="text" name="product_option[{{ $option_row }}][product_option_values][{{ $option_value_row }}][price]" value="{{ $product_option_value['price'] }}" placeholder="Цена" class="form-control"/>
                                                                                        </td>
                                                                                        <td class="text-left">
                                                                                            <input type="text" name="product_option[{{ $option_row }}][product_option_values][{{ $option_value_row }}][weight]" value="{{ $product_option_value['weight'] }}" placeholder="Вес" class="form-control"/>
                                                                                        </td>
                                                                                        <td class="text-left">
                                                                                            <input type="text" name="product_option[{{ $option_row }}][product_option_values][{{ $option_value_row }}][reward]" value="{{ $product_option_value['reward'] }}" placeholder="Баллы" class="form-control"/>
                                                                                        </td>
                                                                                        <td class="text-right">
                                                                                            <button type="button" onclick="$('#option-value-row{{ $option_value_row }}').remove();" class="btn btn-danger">&times;</button>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <?php $option_value_row++ ?>
                                                                                @endforeach
                                                                                </tbody>
                                                                                <tfoot>
                                                                                <tr>
                                                                                    <td colspan="{!! $option['type'] === 'select' ? '5' : '6' !!}"></td>
                                                                                    <td class="text-right">
                                                                                        <button type="button" onclick="addOptionValue('{{ $option_row }}', '{{ $option['type'] }}');" class="btn btn-primary">Добавить</button>
                                                                                    </td>
                                                                                </tr>
                                                                                </tfoot>
                                                                            </table>
                                                                        </div>
                                                                        <select id="option-values{{ $option_row }}" style="display: none;">
                                                                            @foreach($option['option_values'] as $option_value)
                                                                                <?php $option_value = $option_value['option_value_description']; ?>
                                                                                <option value="{{ $option_value['option_value_id'] }}">{{ $option_value['name'] }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    @endif
                                                                </div>
                                                                <?php $option_row++ ?>
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-danger">Создайте опции</div>
                                        @endif
                                    </div>
                                    <div class="tab-pane" id="filter_tab" role="tabpanel" aria-labelledby="filters">
                                        @if(!$filters->isEmpty())
                                            <div class="form-group multiple-select2">
                                                <select name="product_filter[]" class="add_filter form-control select2" multiple>
                                                    @foreach($filters as $filter)
                                                        @if(!is_null($filter->metaLang))
                                                            <option value="{{ $filter->id }}"{{ in_array($filter->id, (array)old('product_filter', $product_filter)) ? ' selected' : '' }}>{{ $filter->metaLang['name'] }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group" id="filter-values">
                                                @foreach($filters as $filter)
                                                    @if(!$filter->filter_values->isEmpty() && in_array($filter->id, (array)old('product_filter', $product_filter)))
                                                        <div style="margin-bottom: 15px" id="filter-{{ $filter->id }}">
                                                            <label>{{ $filter->metaLang['name'] }}</label>
                                                            <div class="well">
                                                                @foreach($filter->filter_values as $filter_values)
                                                                    @if(!empty($filter_values->filter_value_description) && !is_null($filter_values->filter_value_description->name))
                                                                        <div>
                                                                            <input style="vertical-align: middle" id="value-{{ $filter->id }}-{{ $filter_values->id }}" name="product_filter_values[{{ $filter->id }}][]" type="checkbox" value="{{ $filter_values->id }}"{{ in_array($filter_values->id, (array)old('product_filter_values.' . $filter->id, $product_filter_value)) ? ' checked' : '' }} />
                                                                            <label for="value-{{ $filter->id }}-{{ $filter_values->id }}">&nbsp;&nbsp;{{ $filter_values->filter_value_description->name }}</label>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="alert alert-danger">Создайте фильтры</div>
                                        @endif
                                    </div>
                                    <div class="tab-pane" id="specials_tab" role="tabpanel" aria-labelledby="specials">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Группа клиентов</th>
                                                <th>Цена</th>
                                                <th>Дата начала</th>
                                                <th>Дата окончания</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody id="add_specials">
											<?php $special_row = 0; ?>
                                            @if(!empty($product_special))
                                                @foreach($product_special as $special)
                                                    <tr id="special_row{{ $special_row }}">
                                                        <td>
                                                            <select name="product_special[{{ $special_row }}][customer_group_id]" class="form-control">
                                                                @foreach($customer_groups as $cg)
                                                                    <option value="{{ $cg->id }}"{{ $cg->id == $special['customer_group_id'] ? ' selected' : '' }}>{{ $cg->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="product_special[{{ $special_row }}][price]" value="{{ $special['price'] }}" placeholder="Цена" class="form-control" /></td>
                                                        <td>
                                                            <fieldset class="form-group position-relative has-icon-left">
                                                                <input type="text" name="product_special[{{ $special_row }}][date_start]" value="{{ $special['date_start'] }}" class="form-control pickadate" placeholder="Дата начала" />
                                                                <div class="form-control-position">
                                                                    <i class='bx bx-calendar'></i>
                                                                </div>
                                                            </fieldset>
                                                        </td>
                                                        <td>
                                                            <fieldset class="form-group position-relative has-icon-left">
                                                                <input type="text" name="product_special[{{ $special_row }}][date_end]" value="{{ $special['date_end'] }}" class="form-control pickadate" placeholder="Дата окончания" />
                                                                <div class="form-control-position">
                                                                    <i class='bx bx-calendar'></i>
                                                                </div>
                                                            </fieldset>
                                                        </td>
                                                        <td><a href="#" class="btn btn-danger" onclick="$('#special_row{{ $special_row }}').remove();return false;">&times;</a></td>
                                                    </tr>
													<?php $special_row++; ?>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5">Нет данных</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td colspan="5">
                                                    <a href="#" class="btn btn-primary" onclick="addSpecial();return false;">Добавить акцию</a>
                                                </td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="tab-pane" id="discounts_tab" role="tabpanel" aria-labelledby="discounts">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Группа клиентов</th>
                                                <th>Количество</th>
                                                <th>Цена</th>
                                                <th>Дата начала</th>
                                                <th>Дата окончания</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody id="add_discounts">
											<?php $discount_row = 0; ?>
                                            @if(!empty($product_discount))
                                                @foreach($product_discount as $discount)
                                                    <tr id="discount_row{{ $discount_row }}">
                                                        <td>
                                                            <select name="product_discount[{{ $discount_row }}][customer_group_id]" class="form-control">
                                                                @foreach($customer_groups as $cg)
                                                                    <option value="{{ $cg->id }}"{{ $cg->id == $discount['customer_group_id'] ? ' selected' : '' }}>{{ $cg->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="product_discount[{{ $discount_row }}][quantity]" value="{{ $discount['quantity'] }}" placeholder="Количество" class="form-control" /></td>
                                                        <td><input type="text" name="product_discount[{{ $discount_row }}][price]" value="{{ $discount['price'] }}" placeholder="Цена" class="form-control" /></td>
                                                        <td>
                                                            <fieldset class="form-group position-relative has-icon-left">
                                                                <input type="text" name="product_discount[{{ $discount_row }}][date_start]" value="{{ $discount['date_start'] }}" class="form-control pickadate" placeholder="Дата начала" />
                                                                <div class="form-control-position">
                                                                    <i class='bx bx-calendar'></i>
                                                                </div>
                                                            </fieldset>
                                                        </td>
                                                        <td>
                                                            <fieldset class="form-group position-relative has-icon-left">
                                                                <input type="text" name="product_discount[{{ $discount_row }}][date_end]" value="{{ $discount['date_end'] }}" class="form-control pickadate" placeholder="Дата окончания" />
                                                                <div class="form-control-position">
                                                                    <i class='bx bx-calendar'></i>
                                                                </div>
                                                            </fieldset>
                                                        </td>
                                                        <td><a href="#" class="btn btn-danger" onclick="$('#discount_row{{ $discount_row }}').remove();return false;">&times;</a></td>
                                                    </tr>
													<?php $discount_row++; ?>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6">Нет данных</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td colspan="6">
                                                    <a href="#" class="btn btn-primary" onclick="addDiscount();return false;">Добавить скидку</a>
                                                </td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="tab-pane" id="images_tab" role="tabpanel" aria-labelledby="images">
                                        <label>Главное изображение</label>
                                        <div class="preview">
                                            <a href="#" class="event_file not_remove" data-input="#product-image">
                                                <img src="{{ asset($image ? $image : old('image', 'assets/admin/img/no_image.png')) }}" />
                                                <input id="product-image" type="hidden" name="image" value="{{ old('image', $image) }}" />
                                            </a>
                                        </div>
                                        <hr>
                                        <div class="flex-custom">
                                            <label>Дополнительные изображения</label>
                                            <a href="#" class="btn btn-primary add_product_image">Добавить изображения</a>
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
                                    <div class="tab-pane" id="rewards_tab" role="tabpanel" aria-labelledby="rewards">
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Цена в баллах</label>
                                                <input type="text" name="reward" class="form-control" placeholder="Цена в баллах" value="{{ old('reward', $reward) }}" />
                                            </div>
                                        </div>
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Группа клиентов</th>
                                                <th>Баллы</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($customer_groups as $cg)
                                                <tr>
                                                    <td>{{ $cg->name }}</td>
                                                    <td><input type="text" name="product_reward[{{ $cg->id }}][reward]" value="{{ !empty($product_rewards[$cg->id]) ? $product_rewards[$cg->id]['reward'] : '' }}" placeholder="Цена в баллах" class="form-control" /></td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane" id="layout_tab" role="tabpanel" aria-labelledby="layout">
                                        <div class="form-group">
                                            <select name="layout_id" class="form-control">
                                                <option value="">Выберите макет</option>
                                                @foreach($layouts as $layout)
                                                    <option value="{{ $layout->id }}"{{ $layout->id == old('layout_id', $layout_id) || (!old('layout_id', $layout_id) && $layout->route == 'products') ? ' selected' : '' }}>{{ $layout->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                @role('edit|create|edit_content')
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
    <script src="{{asset('assets/admin/js/tinymce/jquery.tinymce.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/picker.js')}}"></script>
    <script src="{{asset('assets/admin/js/picker.date.js')}}"></script>
    <script src="{{asset('assets/admin/js/picker.time.js')}}"></script>
@endsection

@section('page-scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(document).ready(function(){
            $('[name="related"]').autocomplete({
                source: function(request, response) {
                    $.ajax( {
                        url: '{{ asset('admin/product_autocomplete') }}',
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response($.map(data, function(item) {
                                return {
                                    value: item['name'],
                                    id: item['id']
                                }
                            }));
                        }
                    });
                },
                select: function(event, ui) {
                    $('[name="related"]').val('');

                    $('#related-product' + ui.item.id).remove();

                    $('#related-product').append('<div id="related-product' + ui.item.id + '"><i class="minus">-</i> ' + ui.item.value + '<input type="hidden" name="product_related[]" value="' + ui.item.id + '" /></div>');
                }
            });

            $('#related-product').delegate('.minus', 'click', function() {
                $(this).parent().remove();
            });
            
            $('.pickadate').pickadate({
                format: 'yyyy-mm-dd'
            });

            $('.pickatime').pickatime({
                format: 'T!ime selected: h:i a',
                formatLabel: 'HH:i a',
                formatSubmit: 'HH:i',
                hiddenPrefix: 'prefix__',
                hiddenSuffix: '__suffix'
            });

            $('.pickadatetime').pickadate({
                format: 'yyyy-mm-dd HH:i'
            });

            $('[name="manufacturer"]').autocomplete({
                source: function(request, response) {
                    $.ajax( {
                        url: '{{ asset('admin/manufacturer_autocomplete') }}',
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response($.map(data, function(item) {
                                return {
                                    value: item['name'],
                                    id: item['id']
                                }
                            }));
                        }
                    });
                },
                select: function(event, ui) {
                    $('[name="manufacturer"]').val(ui.item.value);
                    $('[name="manufacturer_id"]').val(ui.item.id);
                }
            });
        });

        var attribute_row = {{ $attribute_row }};
        var image_row = {{ $image_row }};
        var special_row = {{ $special_row }};
        var discount_row = {{ $discount_row }};

        function addDiscount() {
            var html = '<tr id="discount_row' + discount_row + '">' +
                '  <td>' +
                '    <select name="product_discount[' + discount_row + '][customer_group_id]" class="form-control">';
            @foreach($customer_groups as $cg)
                html += '<option value="{{ $cg->id }}">{{ $cg->name }}</option>';
            @endforeach
                html +=    '    </select>' +
                '  </td>' +
                '  <td><input type="text" name="product_discount[' + discount_row + '][quantity]" placeholder="Количество" class="form-control" /></td>' +
                '  <td><input type="text" name="product_discount[' + discount_row + '][price]" placeholder="Цена" class="form-control" /></td>' +
                '  <td>' +
                '    <fieldset class="position-relative has-icon-left">' +
                '      <input type="text" name="product_discount[' + discount_row + '][date_start]" class="form-control pickadate" placeholder="Дата начала" />' +
                '      <div class="form-control-position"><i class="bx bx-calendar"></i></div>' +
                '    </fieldset>' +
                '  </td>' +
                '  <td>' +
                '    <fieldset class="position-relative has-icon-left">' +
                '      <input type="text" name="product_discount[' + discount_row + '][date_end]" class="form-control pickadate" placeholder="Дата окончания" />' +
                '      <div class="form-control-position"><i class="bx bx-calendar"></i></div>' +
                '    </fieldset>' +
                '  </td>' +
                '  <td><a href="#" class="btn btn-danger" onclick="$(\'#discount_row' + discount_row + '\').remove();return false;">&times;</a></td>' +
                '</tr>';

            $('#add_discounts').append(html);
            discount_row++;

            $('.pickadate').pickadate({
                format: 'yyyy-mm-dd'
            });
        }

        function addSpecial() {
            var html = '<tr id="special_row' + special_row + '">' +
                '  <td>' +
                '    <select name="product_special[' + special_row + '][customer_group_id]" class="form-control">';
            @foreach($customer_groups as $cg)
                html += '<option value="{{ $cg->id }}">{{ $cg->name }}</option>';
            @endforeach
                html +=    '    </select>' +
                '  </td>' +
                '  <td><input type="text" name="product_special[' + special_row + '][price]" placeholder="Цена" class="form-control" /></td>' +
                '  <td>' +
                '    <fieldset class="position-relative has-icon-left">' +
                '      <input type="text" name="product_special[' + special_row + '][date_start]" class="form-control pickadate" placeholder="Дата начала" />' +
                '      <div class="form-control-position"><i class="bx bx-calendar"></i></div>' +
                '    </fieldset>' +
                '  </td>' +
                '  <td>' +
                '    <fieldset class="position-relative has-icon-left">' +
                '      <input type="text" name="product_special[' + special_row + '][date_end]" class="form-control pickadate" placeholder="Дата окончания" />' +
                '      <div class="form-control-position"><i class="bx bx-calendar"></i></div>' +
                '    </fieldset>' +
                '  </td>' +
                '  <td><a href="#" class="btn btn-danger" onclick="$(\'#special_row' + special_row + '\').remove();return false;">&times;</a></td>' +
                '</tr>';

            $('#add_specials').append(html);
            special_row++;

            $('.pickadate').pickadate({
                format: 'yyyy-mm-dd'
            });
        }

        $(document).on('click', '.add_product_image', function () {
            $(this).parent().next().append('<a href="#" class="event_file" data-input="#product_images-' + image_row + '"><img src="{{ asset('assets/admin/img/no_image.png') }}" /><input id="product_images-' + image_row + '" type="hidden" name="images[]" /></a>');
            image_row++;
            return false;
        });

        $(document).on('click', '.add_attribute_image', function () {
            var row = $(this).attr('data-row');
            var x = $(this).next().find('a').length;
            $(this).next().append('<a href="#" class="event_file" data-input="#attribute_image-' + row + '-' + x + '"><img src="{{ asset('assets/admin/img/no_image.png') }}" /><input id="attribute_image-' + row + '-' + x + '" type="hidden" name="product_attribute[' + row + '][image][' + x + ']" /></a>');
            x++;
            return false;
        });

        var options = $("#options_tab .select2");
        var option_row = {{ $option_row }};
        var json_options = JSON.parse('{!! $options->keyBy('id') !!}');

        options.select2({
            dropdownAutoWidth: true,
            width: '100%',
            minimumResultsForSearch: 1,
            placeholder: "Выберите опцию"
        });

        options.on('select2:select', function (e) {
            var data = e.params.data;
            var id = data.id;
            var type = $('.add_option option[value="' + id + '"]').attr('data-type');
            var text = data.text;

            html =  '<div class="tab-pane" id="option_id-' + id + '_tab">';
            html += '	<input type="hidden" name="product_option[' + option_row + '][product_option_id]" />';
            html += '	<input type="hidden" name="product_option[' + option_row + '][name]" value="' + text + '" />';
            html += '	<input type="hidden" name="product_option[' + option_row + '][option_id]" value="' + id + '" />';
            html += '	<input type="hidden" name="product_option[' + option_row + '][type]" value="' + type + '" />';

            html += '	<div class="form-group">';
            html += '	  <label for="input-required' + option_row + '">Обязательно</label>';
            html += '	  <div class="controls"><select name="product_option[' + option_row + '][required]" id="input-required' + option_row + '" class="form-control">';
            html += '	      <option value="1">Да</option>';
            html += '	      <option value="0">Нет</option>';
            html += '	  </select></div>';
            html += '	</div>';

            if (type === 'text') {
                html += '	<div class="form-group">';
                html += '	  <label for="input-value' + option_row + '">Значение</label>';
                html += '	  <div class="controls"><input type="text" name="product_option[' + option_row + '][value]" value="" placeholder="Значение" id="input-value' + option_row + '" class="form-control" /></div>';
                html += '	</div>';
            }

            if (type === 'textarea') {
                html += '	<div class="form-group">';
                html += '	  <label for="input-value' + option_row + '">Значение</label>';
                html += '	  <div class="controls"><textarea name="product_option[' + option_row + '][value]" rows="5" placeholder="Значение" id="input-value' + option_row + '" class="form-control"></textarea></div>';
                html += '	</div>';
            }

            if (type === 'date') {
                html += '	<div class="form-group">';
                html += '	  <label for="input-value' + option_row + '">Значение</label>';
                html += '     <fieldset class="position-relative has-icon-left">';
                html += '       <input type="text" name="product_option[' + option_row + '][value]" class="form-control pickadate" placeholder="Значение" id="input-value' + option_row + '" />';
                html += '       <div class="form-control-position">';
                html += '         <i class="bx bx-calendar"></i>';
                html += '       </div>';
                html += '     </fieldset>';
                html += '	</div>';
            }

            if (type === 'time') {
                html += '	<div class="form-group">';
                html += '	  <label for="input-value' + option_row + '">Значение</label>';
                html += '     <fieldset class="position-relative has-icon-left">';
                html += '       <input type="text" name="product_option[' + option_row + '][value]" class="form-control pickatime" placeholder="Значение" id="input-value' + option_row + '" />';
                html += '       <div class="form-control-position">';
                html += '         <i class="bx bx-calendar"></i>';
                html += '       </div>';
                html += '     </fieldset>';
                html += '	</div>';
            }

            if (type === 'datetime') {
                html += '	<div class="form-group">';
                html += '	  <label for="input-value' + option_row + '">Значение</label>';
                html += '     <fieldset class="position-relative has-icon-left">';
                html += '       <input type="text" name="product_option[' + option_row + '][value]" class="form-control pickadatetime" placeholder="Значение" id="input-value' + option_row + '" />';
                html += '       <div class="form-control-position">';
                html += '         <i class="bx bx-calendar"></i>';
                html += '       </div>';
                html += '     </fieldset>';
                html += '	</div>';
            }

            if (type === 'select' || type === 'radio' || type === 'checkbox' || type === 'color') {
                html += '<div class="table-responsive">';
                html += '  <table id="option-value' + option_row + '" class="table">';
                html += '  	 <thead>';
                html += '      <tr>';
                html += '        <th class="text-left">Значение</th>';

                if (type !== 'select') {
                    html += '        <th class="text-left">Изображение</th>';
                }

                html += '        <th class="text-left">Кол-во</th>';
                html += '        <th class="text-left">Цена</th>';
                html += '        <th class="text-left">Вес</th>';
                html += '        <th></th>';
                html += '      </tr>';
                html += '  	 </thead>';
                html += '  	 <tbody>';
                html += '    </tbody>';
                html += '    <tfoot>';
                html += '      <tr>';
                html += '        <td colspan="' + (type === 'select' ? '4' : '5') + '"></td>';
                html += '        <td class="text-right"><button type="button" onclick="addOptionValue(' + option_row + ', \'' + type + '\');" class="btn btn-primary">Добавить</button></td>';
                html += '      </tr>';
                html += '    </tfoot>';
                html += '  </table>';
                html += '</div>';

                html += '  <select id="option-values' + option_row + '" style="display: none;">';

                var option_value = json_options[id]['option_values'];

                for (i = 0; i < option_value.length; i++) {
                    html += '  <option value="' + option_value[i]['id'] + '">' + option_value[i]['option_value_description']['name'] + '</option>';
                }

                html += '  </select>';
                html += '</div>';
            }

            $('#option-values .tab-content').append(html);

            $('.ul_options').append('<li class="nav-item"><a class="nav-link" id="option_id-' + id + '" data-toggle="tab" href="#option_id-' + id + '_tab" role="tab" aria-controls="option_id-' + id + '_tab" aria-selected="true">' + text + '</a></li>');

            $('.ul_options a[href="#option_id-' + id + '_tab"]').tab('show');

            $('.pickadate').pickadate({
                format: 'yyyy-mm-dd'
            });

            $('.pickatime').pickatime({
                format: 'T!ime selected: h:i a',
                formatLabel: 'HH:i a',
                formatSubmit: 'HH:i',
                hiddenPrefix: 'prefix__',
                hiddenSuffix: '__suffix'
            });

            $('.pickadatetime').pickadate({
                format: 'yyyy-mm-dd HH:i'
            });

            option_row++;
        });

        var option_value_row = {{ $option_value_row }};

        function addOptionValue(option_row, type) {
            html = '<tr id="option-value-row' + option_value_row + '">';
            html += '  <td class="text-left"><select name="product_option[' + option_row + '][product_option_values][' + option_value_row + '][option_value_id]" class="form-control">';
            html += $('#option-values' + option_row).html();
            html += '  </select></td>';

            if (type !== 'select') {
                html += '<td class="text-left">';
                html += '    <div class="preview">';
                html += '    <a href="#" class="event_file" data-input="#option_image-' + option_row + '-' + option_value_row + '">';
                html += '    <img src="{{ asset('assets/admin/img/no_image.png') }}" />';
                html += '    <input id="option_image-' + option_row + '-' + option_value_row + '" type="hidden" name="product_option[' + option_row + '][product_option_values][' + option_value_row + '][image]" />';
                html += '    </a>';
                html += '</div>';
                html += '</td>';
            }

            html += '  <td class="text-right"><input type="text" name="product_option[' + option_row + '][product_option_values][' + option_value_row + '][quantity]" placeholder="Количество" class="form-control" /></td>';
            html += '  <td class="text-right">';
            html += '  <input type="text" name="product_option[' + option_row + '][product_option_values][' + option_value_row + '][price]" placeholder="Цена" class="form-control" /></td>';
            html += '  <td class="text-right">';
            html += '  <input type="text" name="product_option[' + option_row + '][product_option_values][' + option_value_row + '][weight]" placeholder="Вес" class="form-control" /></td>';
            html += '  <td class="text-right">';
            html += '  <input type="text" name="product_option[' + option_row + '][product_option_values][' + option_value_row + '][reward]" placeholder="Баллы" class="form-control" /></td>';
            html += '  <td class="text-left"><button type="button" onclick="$(\'#option-value-row' + option_value_row + '\').remove();" class="btn btn-danger">&times;</button></td>';
            html += '</tr>';

            $('#option-value' + option_row + ' tbody').append(html);

            option_value_row++;
        }

        options.on('select2:unselect', function (e) {
            var data = e.params.data;
            var id = data.id;

            $('#option-' + id).remove();
        });

        var $selectMultiFilter = $("#filter_tab .select2");

        $selectMultiFilter.select2({
            dropdownAutoWidth: true,
            width: '100%',
            minimumResultsForSearch: 1,
            placeholder: "Выберите фильтр"
        });

        var filters = JSON.parse('{!! $filters->keyBy('id') !!}');

        $selectMultiFilter.on('select2:select', function (e) {
            var data = e.params.data;
            var id = data.id;
            var text = data.text;
            $('#filter-' + id).remove();

            var html = '<div style="margin-bottom: 15px" id="filter-' + id + '">' +
                '           <label>' + text + '</label>' +
                '           <div class="well">';

            for (var i in filters[id]['filter_values']) {
                html += '<div><input style="vertical-align: middle" id="value-' + id + '-' + filters[id]['filter_values'][i]['id'] + '" name="product_filter_values[' + id + '][]" type="checkbox" value="' + filters[id]['filter_values'][i]['id'] + '" /> <label for="value-' + id + '-' + filters[id]['filter_values'][i]['id'] + '">&nbsp;&nbsp;' + filters[id]['filter_values'][i]['filter_value_description']['name'] + '</label></div>';
            }

            html += '</div></div>';

            $('#filter-values').prepend(html);
        });

        $selectMultiFilter.on('select2:unselect', function (e) {
            var data = e.params.data;
            var id = data.id;

            $('#filter-' + id).remove();
        });

        function parent_category_add() {
            var parent_id = $('[name="parent_id"]').val();

            if (parent_id == '') {
                parent_id = {{ $parent_id }};
            }

            var html = '<option value="">Выберите категорию</option>';

            $('.categories input:checked').each(function () {
                html += '<option value="' + $(this).val() + '">' + $(this).next().text() + '</option>';
            });

            $('[name="parent_id"]').html(html);
            $('[name="parent_id"]').val(parent_id);
        }

        parent_category_add();
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
                    '                   <input type="hidden" name="product_attribute[' + attribute_row + '][attribute_id]" value="' + id + '" />\n' +
                    '               </div>\n' +
                    '               <div class="col-12 col-sm-6 col-lg-8">\n';
                @foreach($langs as $lang)
                    html += '           <fieldset style="margin-bottom: 10px">\n' +
                    '                       <div class="input-group">\n' +
                    '                           <div class="input-group-prepend">\n' +
                    '                               <span class="input-group-text"><img style="width: 20px" src="{{ asset($lang->image) }}" title="{{ $lang->name }}" /></span>\n' +
                    '                           </div>\n' +
                    '                           <textarea name="product_attribute[' + attribute_row + '][product_attribute_description][{{ $lang->code }}][text]" placeholder="Описание" class="form-control"></textarea>\n' +
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