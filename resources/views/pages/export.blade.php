@extends('layouts.contentLayoutMaster')
@section('title','Экспорт/Импорт')

@section('content')
    <section class="users-edit">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger" role="alert" style="margin-bottom:20px"><strong>{{ session('error') }}</strong></div>
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
                    @if(session('success'))
                        <div class="alert alert-success" role="alert" style="margin-bottom:20px"><strong>{{ session('success') }}</strong></div>
                    @endif
                    <div class="row">
                        <div class="col-12 col-sm-12">
                            <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="categories" data-toggle="tab" href="#category" role="tab" aria-controls="category" aria-selected="true">
                                        Категории
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="products" data-toggle="tab" href="#product" role="tab" aria-controls="product" aria-selected="true">
                                        Товары
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="categories_page" data-toggle="tab" href="#category_page" role="tab" aria-controls="category_page" aria-selected="true">
                                        Категории статей
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pages" data-toggle="tab" href="#page" role="tab" aria-controls="page" aria-selected="true">
                                        Статьи
                                    </a>
                                </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content pt-1">
                                <div class="tab-pane active" id="category" role="tabpanel" aria-labelledby="categories">
                                    <ul class="nav nav-tabs nav-fill" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="category-link-export" data-toggle="tab" href="#category-export" role="tab" aria-controls="category-export" aria-selected="true">
                                                Экспорт
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="category-link-import" data-toggle="tab" href="#category-import" role="tab" aria-controls="category-import" aria-selected="true">
                                                Импорт
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content pt-1">
                                        <div class="tab-pane active" id="category-export" role="tabpanel" aria-labelledby="category-link-export">
                                            <form action="{{ url('admin/export') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="field" value="category" />
                                                <div class="row">
                                                    <div class="col-12 col-sm-6 col-xs-12">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Лимит экспортируемых категорий</label>
                                                                <div class="row">
                                                                    <div class="col-12 col-sm-6 col-xs-6">
                                                                        <input type="text" name="csv_export[limit_from]" placeholder="От" class="form-control" autocomplete="off">
                                                                    </div>
                                                                    <div class="col-12 col-sm-6 col-xs-6">
                                                                        <input type="text" name="csv_export[limit_to]" placeholder="До" class="form-control" autocomplete="off">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Кодировка файла</label>
                                                                <select name="csv_export[file_encoding]" class="form-control">
                                                                    <option value="ISO-8859-1">ISO-8859-1 (Western Europe)</option>
                                                                    <option value="ISO-8859-5">ISO-8859-5 (Cyrillc, DOS)</option>
                                                                    <option value="KOI8-R">KOI8-R (Cyrillic, Unix)</option>
                                                                    <option value="UTF-16LE">UNICODE (MS Excel text format)</option>
                                                                    <option value="UTF-8">UTF-8</option>
                                                                    <option value="windows-1250">windows-1250 (Central European languages)</option>
                                                                    <option value="windows-1251" selected>windows-1251 (Cyrillc)</option>
                                                                    <option value="windows-1252">windows-1252 (Western languages)</option>
                                                                    <option value="windows-1253">windows-1253 (Greek)</option>
                                                                    <option value="windows-1254">windows-1254 (Turkish)</option>
                                                                    <option value="windows-1255">windows-1255 (Hebrew)</option>
                                                                    <option value="windows-1256">windows-1256 (Arabic)</option>
                                                                    <option value="windows-1257">windows-1257 (Baltic languages)</option>
                                                                    <option value="windows-1258">windows-1258 (Vietnamese)</option>
                                                                    <option value="CP932">CP932 (Japanese)</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Разделитель полей</label>
                                                                <select name="csv_export[csv_delimiter]" class="form-control">
                                                                    <option value=";" selected> ; </option>
                                                                    <option value=","> , </option>
                                                                    <option value="Tab"> Tab </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Язык</label>
                                                                <select name="csv_export[lang]" class="form-control">
                                                                    @foreach($langs as $lang)
                                                                        <option value="{{ $lang->code }}" selected>{{ $lang->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-xs-12">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Выберите поля для экспорта</label>
                                                                <div class="well">
                                                                    @foreach($category_fields as $field)
                                                                        <fieldset style="margin-bottom: 15px">
                                                                            <div class="checkbox">
                                                                                <input type="checkbox" name="csv_export[category_fields][]" value="{{ $field['field'] }}" class="checkbox-input" id="category_checkbox-{{ $field['field'] }}">
                                                                                <label for="category_checkbox-{{ $field['field'] }}">{{ $field['name'] }}</label>
                                                                            </div>
                                                                        </fieldset>
                                                                    @endforeach
                                                                </div>
                                                                <div class="flex">
                                                                    <a href="#" onclick="$(this).parent().prev().find('input').prop('checked', true);return false;">Выделить все</a>
                                                                    <span>&nbsp;&nbsp;&nbsp;</span>
                                                                    <a href="#" onclick="$(this).parent().prev().find('input').prop('checked', false);return false;">Снять выделение</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls text-right">
                                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Экспорт</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="tab-pane" id="category-import" role="tabpanel" aria-labelledby="category-link-import">
                                            <form action="{{ url('admin/import') }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="field" value="category" />
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Кодировка файла</label>
                                                        <select name="csv_import[file_encoding]" class="form-control">
                                                            <option value="ISO-8859-1">ISO-8859-1 (Western Europe)</option>
                                                            <option value="ISO-8859-5">ISO-8859-5 (Cyrillc, DOS)</option>
                                                            <option value="KOI8-R">KOI8-R (Cyrillic, Unix)</option>
                                                            <option value="UTF-16LE">UNICODE (MS Excel text format)</option>
                                                            <option value="UTF-8">UTF-8</option>
                                                            <option value="windows-1250">windows-1250 (Central European languages)</option>
                                                            <option value="windows-1251" selected>windows-1251 (Cyrillc)</option>
                                                            <option value="windows-1252">windows-1252 (Western languages)</option>
                                                            <option value="windows-1253">windows-1253 (Greek)</option>
                                                            <option value="windows-1254">windows-1254 (Turkish)</option>
                                                            <option value="windows-1255">windows-1255 (Hebrew)</option>
                                                            <option value="windows-1256">windows-1256 (Arabic)</option>
                                                            <option value="windows-1257">windows-1257 (Baltic languages)</option>
                                                            <option value="windows-1258">windows-1258 (Vietnamese)</option>
                                                            <option value="CP932">CP932 (Japanese)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Разделитель полей</label>
                                                        <select name="csv_import[csv_delimiter]" class="form-control">
                                                            <option value=";" selected> ; </option>
                                                            <option value=","> , </option>
                                                            <option value="Tab"> Tab </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Режим импорта</label>
                                                        <select name="csv_import[mode]" class="form-control">
                                                            <option value="1" selected>Обновить</option>
                                                            <option value="2">Добавить</option>
                                                            <option value="3">Обновить и добавить</option>
                                                            <option value="4">Удалить</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Идентификатор</label>
                                                        <select name="csv_import[key_field]" class="form-control">
                                                            <option value="id" selected>ID категории</option>
                                                            <option value="name">Наименование категории</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <div class="max">Максимальный ID категории - <b>{{ $max_id }}</b></div>
                                                        <label>Использовать ID из файла (Для режима создать или создать и обновить ID должен превышать максимальный ID в магазине)</label>
                                                        <select name="csv_import[import_id]" class="form-control">
                                                            <option value="1">Да</option>
                                                            <option value="0" selected>Нет</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Статус</label>
                                                        <select name="csv_import[status]" class="form-control">
                                                            <option value="">Выбрать статус</option>
                                                            <option value="1" selected>Включено</option>
                                                            <option value="0">Выключено</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Скачать изображения</label>
                                                        <select name="csv_import[download]" class="form-control">
                                                            <option value="1">Да</option>
                                                            <option value="0" selected>Нет</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Язык</label>
                                                        <select name="csv_import[lang]" class="form-control">
                                                            @foreach($langs as $lang)
                                                                <option value="{{ $lang->code }}" selected>{{ $lang->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Макет</label>
                                                        <select name="csv_import[layout_id]" class="form-control">
                                                            <option value="">Выбрать макет</option>
                                                            @foreach($layouts as $layout)
                                                                <option value="{{ $layout->id }}"{{ $layout->route == 'categories' ? ' selected' : '' }}>{{ $layout->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Выберите файл csv</label>
                                                        <input type="file" name="file" accept=".csv" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls text-right">
                                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Импорт</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="product" role="tabpanel" aria-labelledby="products">
                                    <ul class="nav nav-tabs nav-fill" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="product-link-export" data-toggle="tab" href="#product-export" role="tab" aria-controls="product-export" aria-selected="true">
                                                Экспорт
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="product-link-import" data-toggle="tab" href="#product-import" role="tab" aria-controls="product-import" aria-selected="true">
                                                Импорт
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content pt-1">
                                        <div class="tab-pane active" id="product-export" role="tabpanel" aria-labelledby="product-link-export">
                                            <form action="{{ url('admin/export') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="field" value="product" />
                                                <div class="row">
                                                    <div class="col-12 col-sm-6 col-xs-12">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Лимит экспортируемых товаров</label>
                                                                <div class="row">
                                                                    <div class="col-12 col-sm-6 col-xs-6">
                                                                        <input type="text" name="csv_export[limit_from]" placeholder="От" class="form-control" autocomplete="off">
                                                                    </div>
                                                                    <div class="col-12 col-sm-6 col-xs-6">
                                                                        <input type="text" name="csv_export[limit_to]" placeholder="До" class="form-control" autocomplete="off">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Кодировка файла</label>
                                                                <select name="csv_export[file_encoding]" class="form-control">
                                                                    <option value="ISO-8859-1">ISO-8859-1 (Western Europe)</option>
                                                                    <option value="ISO-8859-5">ISO-8859-5 (Cyrillc, DOS)</option>
                                                                    <option value="KOI8-R">KOI8-R (Cyrillic, Unix)</option>
                                                                    <option value="UTF-16LE">UNICODE (MS Excel text format)</option>
                                                                    <option value="UTF-8">UTF-8</option>
                                                                    <option value="windows-1250">windows-1250 (Central European languages)</option>
                                                                    <option value="windows-1251" selected>windows-1251 (Cyrillc)</option>
                                                                    <option value="windows-1252">windows-1252 (Western languages)</option>
                                                                    <option value="windows-1253">windows-1253 (Greek)</option>
                                                                    <option value="windows-1254">windows-1254 (Turkish)</option>
                                                                    <option value="windows-1255">windows-1255 (Hebrew)</option>
                                                                    <option value="windows-1256">windows-1256 (Arabic)</option>
                                                                    <option value="windows-1257">windows-1257 (Baltic languages)</option>
                                                                    <option value="windows-1258">windows-1258 (Vietnamese)</option>
                                                                    <option value="CP932">CP932 (Japanese)</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Разделитель полей</label>
                                                                <select name="csv_export[csv_delimiter]" class="form-control">
                                                                    <option value=";" selected> ; </option>
                                                                    <option value=","> , </option>
                                                                    <option value="Tab"> Tab </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Экспорт категорий</label>
                                                                <select name="csv_export[export_category]" class="form-control input-sm">
                                                                    <option value="0">Отключено</option>
                                                                    <option value="1" selected>В виде __CATEGORY_ID__</option>
                                                                    <option value="2">В виде __CATEGORY_NAME__</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Разделитель внутри ячейки</label>
                                                                <select name="csv_export[delimiter_category]" class="form-control input-sm">
                                                                    <option value="|" selected> | </option>
                                                                    <option value="/"> / </option>
                                                                    <option value=">"> &gt; </option>
                                                                    <option value=","> , </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Категории</label>
                                                                <div class="categories well">
                                                                    @foreach($categories as $key => $category)
                                                                        <fieldset style="margin-bottom: 15px">
                                                                            <div class="checkbox">
                                                                                <input type="checkbox" name="csv_export[categories][]" value="{{ $key }}" class="checkbox-input" id="checkbox{{ $key }}">
                                                                                <label for="checkbox{{ $key }}">{{ $category }}</label>
                                                                            </div>
                                                                        </fieldset>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Язык</label>
                                                                <select name="csv_export[lang]" class="form-control">
                                                                    @foreach($langs as $lang)
                                                                        <option value="{{ $lang->code }}" selected>{{ $lang->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-xs-12">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Выберите поля для экспорта</label>
                                                                <div class="well">
                                                                    @foreach($product_fields as $field)
                                                                        <fieldset style="margin-bottom: 15px">
                                                                            <div class="checkbox">
                                                                                <input type="checkbox" name="csv_export[product_fields][]" value="{{ $field['field'] }}" class="checkbox-input" id="product_checkbox-{{ $field['field'] }}">
                                                                                <label for="product_checkbox-{{ $field['field'] }}">{{ $field['name'] }}</label>
                                                                            </div>
                                                                        </fieldset>
                                                                    @endforeach
                                                                </div>
                                                                <div class="flex">
                                                                    <a href="#" onclick="$(this).parent().prev().find('input').prop('checked', true);return false;">Выделить все</a>
                                                                    <span>&nbsp;&nbsp;&nbsp;</span>
                                                                    <a href="#" onclick="$(this).parent().prev().find('input').prop('checked', false);return false;">Снять выделение</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls text-right">
                                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Экспорт</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="tab-pane" id="product-import" role="tabpanel" aria-labelledby="product-link-import">
                                            <form action="{{ url('admin/import') }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="field" value="product" />
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Кодировка файла</label>
                                                        <select name="csv_import[file_encoding]" class="form-control">
                                                            <option value="ISO-8859-1">ISO-8859-1 (Western Europe)</option>
                                                            <option value="ISO-8859-5">ISO-8859-5 (Cyrillc, DOS)</option>
                                                            <option value="KOI8-R">KOI8-R (Cyrillic, Unix)</option>
                                                            <option value="UTF-16LE">UNICODE (MS Excel text format)</option>
                                                            <option value="UTF-8">UTF-8</option>
                                                            <option value="windows-1250">windows-1250 (Central European languages)</option>
                                                            <option value="windows-1251" selected>windows-1251 (Cyrillc)</option>
                                                            <option value="windows-1252">windows-1252 (Western languages)</option>
                                                            <option value="windows-1253">windows-1253 (Greek)</option>
                                                            <option value="windows-1254">windows-1254 (Turkish)</option>
                                                            <option value="windows-1255">windows-1255 (Hebrew)</option>
                                                            <option value="windows-1256">windows-1256 (Arabic)</option>
                                                            <option value="windows-1257">windows-1257 (Baltic languages)</option>
                                                            <option value="windows-1258">windows-1258 (Vietnamese)</option>
                                                            <option value="CP932">CP932 (Japanese)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Разделитель полей</label>
                                                        <select name="csv_import[csv_delimiter]" class="form-control">
                                                            <option value=";" selected> ; </option>
                                                            <option value=","> , </option>
                                                            <option value="Tab"> Tab </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Режим импорта</label>
                                                        <select name="csv_import[mode]" class="form-control">
                                                            <option value="1" selected>Обновить</option>
                                                            <option value="2">Добавить</option>
                                                            <option value="3">Обновить и добавить</option>
                                                            <option value="4">Удалить</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Идентификатор</label>
                                                        <select name="csv_import[key_field]" class="form-control">
                                                            <option value="id" selected>ID товара</option>
                                                            <option value="name">Наименование товара</option>
                                                            <option value="model">Модель товара</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Статус</label>
                                                        <select name="csv_import[status]" class="form-control">
                                                            <option value="">Выбрать статус</option>
                                                            <option value="1" selected>Включено</option>
                                                            <option value="0">Выключено</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Скачать изображения</label>
                                                        <select name="csv_import[download]" class="form-control">
                                                            <option value="1">Да</option>
                                                            <option value="0" selected>Нет</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Создать категории (режим - создать, создать и обновить)</label>
                                                        <select name="csv_import[categories_add]" class="form-control">
                                                            <option value="1">Да</option>
                                                            <option value="0" selected>Нет</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Макет для создаваемых категорий</label>
                                                        <select name="csv_import[category_layout_id]" class="form-control">
                                                            <option value="">Выбрать макет</option>
                                                            @foreach($layouts as $layout)
                                                                <option value="{{ $layout->id }}"{{ $layout->route == 'categories' ? ' selected' : '' }}>{{ $layout->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Создать характеристики (режим - создать, создать и обновить)</label>
                                                        <select name="csv_import[attributes_add]" class="form-control">
                                                            <option value="1">Да</option>
                                                            <option value="0" selected>Нет</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Создать фильтры (режим - создать, создать и обновить)</label>
                                                        <select name="csv_import[filters_add]" class="form-control">
                                                            <option value="1">Да</option>
                                                            <option value="0" selected>Нет</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Тип фильтров (для создаваемых фильтров)</label>
                                                        <select name="csv_import[filters_type]" class="form-control">
                                                            <option value="checkbox">Флажек</option>
                                                            <option value="radio">Кнопка</option>
                                                            <option value="select">Список</option>
                                                            <option value="slider">Слайдер</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <div class="max">Максимальный ID товара - <b>{{ $max_id }}</b></div>
                                                        <label>Использовать ID из файла (Для режима создать или создать и обновить ID должен превышать максимальный ID в магазине)</label>
                                                        <select name="csv_import[import_id]" class="form-control">
                                                            <option value="1">Да</option>
                                                            <option value="0" selected>Нет</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Разделитель внутри ячейки</label>
                                                        <select name="csv_import[delimiter_category]" class="form-control">
                                                            <option value="|" selected> | </option>
                                                            <option value="/"> / </option>
                                                            <option value=","> , </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Калькуляция цены</label>
                                                        <div class="row">
                                                            <div class="col-12 col-sm-6 col-xs-6">
                                                                <select name="csv_import[calc_mode_1]" class="form-control input-sm">
                                                                    <option value="" selected="">Не использовать</option>
                                                                    <option value="*">Умножить</option>
                                                                    <option value="/">Разделить</option>
                                                                    <option value="+">Суммировать</option>
                                                                    <option value="-">Вычесть</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-12 col-sm-6 col-xs-6">
                                                                <input type="text" name="csv_import[calc_mode_1_text]" placeholder="Разделитель точка" class="form-control input-sm" autocomplete="off">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Калькуляция акционной цены</label>
                                                        <div class="row">
                                                            <div class="col-12 col-sm-6 col-xs-6">
                                                                <select name="csv_import[calc_mode_2]" class="form-control input-sm">
                                                                    <option value="" selected="">Не использовать</option>
                                                                    <option value="*">Умножить</option>
                                                                    <option value="/">Разделить</option>
                                                                    <option value="+">Суммировать</option>
                                                                    <option value="-">Вычесть</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-12 col-sm-6 col-xs-6">
                                                                <input type="text" name="csv_import[calc_mode_2_text]" placeholder="Разделитель точка" class="form-control input-sm" autocomplete="off">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Язык</label>
                                                        <select name="csv_import[lang]" class="form-control">
                                                            @foreach($langs as $lang)
                                                                <option value="{{ $lang->code }}" selected>{{ $lang->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Макет</label>
                                                        <select name="csv_import[layout_id]" class="form-control">
                                                            <option value="">Выбрать макет</option>
                                                            @foreach($layouts as $layout)
                                                                <option value="{{ $layout->id }}"{{ $layout->route == 'products' ? ' selected' : '' }}>{{ $layout->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Выберите файл csv</label>
                                                        <input type="file" name="file" accept=".csv" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls text-right">
                                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Импорт</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="category_page" role="tabpanel" aria-labelledby="categories_page">
                                    <ul class="nav nav-tabs nav-fill" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="category-page-link-export" data-toggle="tab" href="#category-page-export" role="tab" aria-controls="category-page-export" aria-selected="true">
                                                Экспорт
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="category-page-link-import" data-toggle="tab" href="#category-page-import" role="tab" aria-controls="category-page-import" aria-selected="true">
                                                Импорт
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content pt-1">
                                        <div class="tab-pane active" id="category-page-export" role="tabpanel" aria-labelledby="category-page-link-export">
                                            <form action="{{ url('admin/export') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="field" value="category_page" />
                                                <div class="row">
                                                    <div class="col-12 col-sm-6 col-xs-12">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Лимит экспортируемых категорий</label>
                                                                <div class="row">
                                                                    <div class="col-12 col-sm-6 col-xs-6">
                                                                        <input type="text" name="csv_export[limit_from]" placeholder="От" class="form-control" autocomplete="off">
                                                                    </div>
                                                                    <div class="col-12 col-sm-6 col-xs-6">
                                                                        <input type="text" name="csv_export[limit_to]" placeholder="До" class="form-control" autocomplete="off">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Кодировка файла</label>
                                                                <select name="csv_export[file_encoding]" class="form-control">
                                                                    <option value="ISO-8859-1">ISO-8859-1 (Western Europe)</option>
                                                                    <option value="ISO-8859-5">ISO-8859-5 (Cyrillc, DOS)</option>
                                                                    <option value="KOI8-R">KOI8-R (Cyrillic, Unix)</option>
                                                                    <option value="UTF-16LE">UNICODE (MS Excel text format)</option>
                                                                    <option value="UTF-8">UTF-8</option>
                                                                    <option value="windows-1250">windows-1250 (Central European languages)</option>
                                                                    <option value="windows-1251" selected>windows-1251 (Cyrillc)</option>
                                                                    <option value="windows-1252">windows-1252 (Western languages)</option>
                                                                    <option value="windows-1253">windows-1253 (Greek)</option>
                                                                    <option value="windows-1254">windows-1254 (Turkish)</option>
                                                                    <option value="windows-1255">windows-1255 (Hebrew)</option>
                                                                    <option value="windows-1256">windows-1256 (Arabic)</option>
                                                                    <option value="windows-1257">windows-1257 (Baltic languages)</option>
                                                                    <option value="windows-1258">windows-1258 (Vietnamese)</option>
                                                                    <option value="CP932">CP932 (Japanese)</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Разделитель полей</label>
                                                                <select name="csv_export[csv_delimiter]" class="form-control">
                                                                    <option value=";" selected> ; </option>
                                                                    <option value=","> , </option>
                                                                    <option value="Tab"> Tab </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Язык</label>
                                                                <select name="csv_export[lang]" class="form-control">
                                                                    @foreach($langs as $lang)
                                                                        <option value="{{ $lang->code }}" selected>{{ $lang->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-xs-12">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Выберите поля для экспорта</label>
                                                                <div class="well">
                                                                    @foreach($category_page_fields as $field)
                                                                        <fieldset style="margin-bottom: 15px">
                                                                            <div class="checkbox">
                                                                                <input type="checkbox" name="csv_export[category_page_fields][]" value="{{ $field['field'] }}" class="checkbox-input" id="category_page_checkbox-{{ $field['field'] }}">
                                                                                <label for="category_page_checkbox-{{ $field['field'] }}">{{ $field['name'] }}</label>
                                                                            </div>
                                                                        </fieldset>
                                                                    @endforeach
                                                                </div>
                                                                <div class="flex">
                                                                    <a href="#" onclick="$(this).parent().prev().find('input').prop('checked', true);return false;">Выделить все</a>
                                                                    <span>&nbsp;&nbsp;&nbsp;</span>
                                                                    <a href="#" onclick="$(this).parent().prev().find('input').prop('checked', false);return false;">Снять выделение</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls text-right">
                                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Экспорт</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="tab-pane" id="category-page-import" role="tabpanel" aria-labelledby="category-page-link-import">
                                            <form action="{{ url('admin/import') }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="field" value="category_page" />
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Кодировка файла</label>
                                                        <select name="csv_import[file_encoding]" class="form-control">
                                                            <option value="ISO-8859-1">ISO-8859-1 (Western Europe)</option>
                                                            <option value="ISO-8859-5">ISO-8859-5 (Cyrillc, DOS)</option>
                                                            <option value="KOI8-R">KOI8-R (Cyrillic, Unix)</option>
                                                            <option value="UTF-16LE">UNICODE (MS Excel text format)</option>
                                                            <option value="UTF-8">UTF-8</option>
                                                            <option value="windows-1250">windows-1250 (Central European languages)</option>
                                                            <option value="windows-1251" selected>windows-1251 (Cyrillc)</option>
                                                            <option value="windows-1252">windows-1252 (Western languages)</option>
                                                            <option value="windows-1253">windows-1253 (Greek)</option>
                                                            <option value="windows-1254">windows-1254 (Turkish)</option>
                                                            <option value="windows-1255">windows-1255 (Hebrew)</option>
                                                            <option value="windows-1256">windows-1256 (Arabic)</option>
                                                            <option value="windows-1257">windows-1257 (Baltic languages)</option>
                                                            <option value="windows-1258">windows-1258 (Vietnamese)</option>
                                                            <option value="CP932">CP932 (Japanese)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Разделитель полей</label>
                                                        <select name="csv_import[csv_delimiter]" class="form-control">
                                                            <option value=";" selected> ; </option>
                                                            <option value=","> , </option>
                                                            <option value="Tab"> Tab </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Режим импорта</label>
                                                        <select name="csv_import[mode]" class="form-control">
                                                            <option value="1" selected>Обновить</option>
                                                            <option value="2">Добавить</option>
                                                            <option value="3">Обновить и добавить</option>
                                                            <option value="4">Удалить</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Идентификатор</label>
                                                        <select name="csv_import[key_field]" class="form-control">
                                                            <option value="id" selected>ID категории</option>
                                                            <option value="name">Наименование категории</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <div class="max">Максимальный ID категории - <b>{{ $max_page_id }}</b></div>
                                                        <label>Использовать ID из файла (Для режима создать или создать и обновить ID должен превышать максимальный ID в магазине)</label>
                                                        <select name="csv_import[import_id]" class="form-control">
                                                            <option value="1">Да</option>
                                                            <option value="0" selected>Нет</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Статус</label>
                                                        <select name="csv_import[status]" class="form-control">
                                                            <option value="">Выбрать статус</option>
                                                            <option value="1" selected>Включено</option>
                                                            <option value="0">Выключено</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Скачать изображения</label>
                                                        <select name="csv_import[download]" class="form-control">
                                                            <option value="1">Да</option>
                                                            <option value="0" selected>Нет</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Язык</label>
                                                        <select name="csv_import[lang]" class="form-control">
                                                            @foreach($langs as $lang)
                                                                <option value="{{ $lang->code }}" selected>{{ $lang->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Макет</label>
                                                        <select name="csv_import[layout_id]" class="form-control">
                                                            <option value="">Выбрать макет</option>
                                                            @foreach($layouts as $layout)
                                                                <option value="{{ $layout->id }}"{{ $layout->route == 'page_category' ? ' selected' : '' }}>{{ $layout->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Выберите файл csv</label>
                                                        <input type="file" name="file" accept=".csv" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls text-right">
                                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Импорт</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="page" role="tabpanel" aria-labelledby="pages">
                                    <ul class="nav nav-tabs nav-fill" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="page-link-export" data-toggle="tab" href="#page-export" role="tab" aria-controls="page-export" aria-selected="true">
                                                Экспорт
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="page-link-import" data-toggle="tab" href="#page-import" role="tab" aria-controls="page-import" aria-selected="true">
                                                Импорт
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content pt-1">
                                        <div class="tab-pane active" id="page-export" role="tabpanel" aria-labelledby="page-link-export">
                                            <form action="{{ url('admin/export') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="field" value="page" />
                                                <div class="row">
                                                    <div class="col-12 col-sm-6 col-xs-12">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Лимит экспортируемых статей</label>
                                                                <div class="row">
                                                                    <div class="col-12 col-sm-6 col-xs-6">
                                                                        <input type="text" name="csv_export[limit_from]" placeholder="От" class="form-control" autocomplete="off">
                                                                    </div>
                                                                    <div class="col-12 col-sm-6 col-xs-6">
                                                                        <input type="text" name="csv_export[limit_to]" placeholder="До" class="form-control" autocomplete="off">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Кодировка файла</label>
                                                                <select name="csv_export[file_encoding]" class="form-control">
                                                                    <option value="ISO-8859-1">ISO-8859-1 (Western Europe)</option>
                                                                    <option value="ISO-8859-5">ISO-8859-5 (Cyrillc, DOS)</option>
                                                                    <option value="KOI8-R">KOI8-R (Cyrillic, Unix)</option>
                                                                    <option value="UTF-16LE">UNICODE (MS Excel text format)</option>
                                                                    <option value="UTF-8">UTF-8</option>
                                                                    <option value="windows-1250">windows-1250 (Central European languages)</option>
                                                                    <option value="windows-1251" selected>windows-1251 (Cyrillc)</option>
                                                                    <option value="windows-1252">windows-1252 (Western languages)</option>
                                                                    <option value="windows-1253">windows-1253 (Greek)</option>
                                                                    <option value="windows-1254">windows-1254 (Turkish)</option>
                                                                    <option value="windows-1255">windows-1255 (Hebrew)</option>
                                                                    <option value="windows-1256">windows-1256 (Arabic)</option>
                                                                    <option value="windows-1257">windows-1257 (Baltic languages)</option>
                                                                    <option value="windows-1258">windows-1258 (Vietnamese)</option>
                                                                    <option value="CP932">CP932 (Japanese)</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Разделитель полей</label>
                                                                <select name="csv_export[csv_delimiter]" class="form-control">
                                                                    <option value=";" selected> ; </option>
                                                                    <option value=","> , </option>
                                                                    <option value="Tab"> Tab </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Экспорт категорий</label>
                                                                <select name="csv_export[export_category]" class="form-control input-sm">
                                                                    <option value="0">Отключено</option>
                                                                    <option value="1" selected>В виде __CATEGORY_ID__</option>
                                                                    <option value="2">В виде __CATEGORY_NAME__</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Разделитель внутри ячейки</label>
                                                                <select name="csv_export[delimiter_category]" class="form-control input-sm">
                                                                    <option value="|" selected> | </option>
                                                                    <option value="/"> / </option>
                                                                    <option value=">"> &gt; </option>
                                                                    <option value=","> , </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Категории</label>
                                                                <div class="categories well">
                                                                    @foreach($page_categories as $key => $category)
                                                                        <fieldset style="margin-bottom: 15px">
                                                                            <div class="checkbox">
                                                                                <input type="checkbox" name="csv_export[categories][]" value="{{ $key }}" class="checkbox-input" id="checkbox{{ $key }}">
                                                                                <label for="checkbox{{ $key }}">{{ $category }}</label>
                                                                            </div>
                                                                        </fieldset>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Язык</label>
                                                                <select name="csv_export[lang]" class="form-control">
                                                                    @foreach($langs as $lang)
                                                                        <option value="{{ $lang->code }}" selected>{{ $lang->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-xs-12">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Выберите поля для экспорта</label>
                                                                <div class="well">
                                                                    @foreach($page_fields as $field)
                                                                        <fieldset style="margin-bottom: 15px">
                                                                            <div class="checkbox">
                                                                                <input type="checkbox" name="csv_export[page_fields][]" value="{{ $field['field'] }}" class="checkbox-input" id="page_checkbox-{{ $field['field'] }}">
                                                                                <label for="page_checkbox-{{ $field['field'] }}">{{ $field['name'] }}</label>
                                                                            </div>
                                                                        </fieldset>
                                                                    @endforeach
                                                                </div>
                                                                <div class="flex">
                                                                    <a href="#" onclick="$(this).parent().prev().find('input').prop('checked', true);return false;">Выделить все</a>
                                                                    <span>&nbsp;&nbsp;&nbsp;</span>
                                                                    <a href="#" onclick="$(this).parent().prev().find('input').prop('checked', false);return false;">Снять выделение</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls text-right">
                                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Экспорт</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="tab-pane" id="page-import" role="tabpanel" aria-labelledby="page-link-import">
                                            <form action="{{ url('admin/import') }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="field" value="page" />
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Кодировка файла</label>
                                                        <select name="csv_import[file_encoding]" class="form-control">
                                                            <option value="ISO-8859-1">ISO-8859-1 (Western Europe)</option>
                                                            <option value="ISO-8859-5">ISO-8859-5 (Cyrillc, DOS)</option>
                                                            <option value="KOI8-R">KOI8-R (Cyrillic, Unix)</option>
                                                            <option value="UTF-16LE">UNICODE (MS Excel text format)</option>
                                                            <option value="UTF-8">UTF-8</option>
                                                            <option value="windows-1250">windows-1250 (Central European languages)</option>
                                                            <option value="windows-1251" selected>windows-1251 (Cyrillc)</option>
                                                            <option value="windows-1252">windows-1252 (Western languages)</option>
                                                            <option value="windows-1253">windows-1253 (Greek)</option>
                                                            <option value="windows-1254">windows-1254 (Turkish)</option>
                                                            <option value="windows-1255">windows-1255 (Hebrew)</option>
                                                            <option value="windows-1256">windows-1256 (Arabic)</option>
                                                            <option value="windows-1257">windows-1257 (Baltic languages)</option>
                                                            <option value="windows-1258">windows-1258 (Vietnamese)</option>
                                                            <option value="CP932">CP932 (Japanese)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Разделитель полей</label>
                                                        <select name="csv_import[csv_delimiter]" class="form-control">
                                                            <option value=";" selected> ; </option>
                                                            <option value=","> , </option>
                                                            <option value="Tab"> Tab </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Режим импорта</label>
                                                        <select name="csv_import[mode]" class="form-control">
                                                            <option value="1" selected>Обновить</option>
                                                            <option value="2">Добавить</option>
                                                            <option value="3">Обновить и добавить</option>
                                                            <option value="4">Удалить</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Идентификатор</label>
                                                        <select name="csv_import[key_field]" class="form-control">
                                                            <option value="id" selected>ID статьи</option>
                                                            <option value="name">Наименование</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Статус</label>
                                                        <select name="csv_import[status]" class="form-control">
                                                            <option value="">Выбрать статус</option>
                                                            <option value="1" selected>Включено</option>
                                                            <option value="0">Выключено</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Скачать изображения</label>
                                                        <select name="csv_import[download]" class="form-control">
                                                            <option value="1">Да</option>
                                                            <option value="0" selected>Нет</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Создать категории (режим - создать, создать и обновить)</label>
                                                        <select name="csv_import[categories_add]" class="form-control">
                                                            <option value="1">Да</option>
                                                            <option value="0" selected>Нет</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Макет для создаваемых категорий</label>
                                                        <select name="csv_import[category_layout_id]" class="form-control">
                                                            <option value="">Выбрать макет</option>
                                                            @foreach($layouts as $layout)
                                                                <option value="{{ $layout->id }}"{{ $layout->route == 'page_category' ? ' selected' : '' }}>{{ $layout->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Создать характеристики (режим - создать, создать и обновить)</label>
                                                        <select name="csv_import[attributes_add]" class="form-control">
                                                            <option value="1">Да</option>
                                                            <option value="0" selected>Нет</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <div class="max">Максимальный ID статьи - <b>{{ $max_id }}</b></div>
                                                        <label>Использовать ID из файла (Для режима создать или создать и обновить ID должен превышать максимальный ID в магазине)</label>
                                                        <select name="csv_import[import_id]" class="form-control">
                                                            <option value="1">Да</option>
                                                            <option value="0" selected>Нет</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Разделитель внутри ячейки</label>
                                                        <select name="csv_import[delimiter_category]" class="form-control">
                                                            <option value="|" selected> | </option>
                                                            <option value="/"> / </option>
                                                            <option value=","> , </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Язык</label>
                                                        <select name="csv_import[lang]" class="form-control">
                                                            @foreach($langs as $lang)
                                                                <option value="{{ $lang->code }}" selected>{{ $lang->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Макет</label>
                                                        <select name="csv_import[layout_id]" class="form-control">
                                                            <option value="">Выбрать макет</option>
                                                            @foreach($layouts as $layout)
                                                                <option value="{{ $layout->id }}"{{ $layout->route == 'pages' ? ' selected' : '' }}>{{ $layout->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Выберите файл csv</label>
                                                        <input type="file" name="file" accept=".csv" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls text-right">
                                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Импорт</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Tab panes -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection