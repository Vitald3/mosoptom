@extends('layouts.contentLayoutMaster')
@if($id)
    @section('title','Редактирование категории')
@else
    @section('title','Создание категории')
@endif
@section('vendor-styles')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
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
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="tab-pane" id="data_tab" role="tabpanel" aria-labelledby="data">
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Родительская категория</label>
                                                <input type="text" name="parent" class="form-control" placeholder="Родительская категория"
                                                       value="{{ old('parent', $parent) }}">
                                                <input type="hidden" name="parent_id" value="{{ old('parent_id', $parent_id) }}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <fieldset>
                                                <div class="checkbox">
                                                    <input type="checkbox" name="top" class="checkbox-input" value="1" id="checkbox1"{{ old('top', $top) ? ' checked' : '' }} />
                                                    <label for="checkbox1">Выводить в главном меню</label>
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
                                    <div class="tab-pane" id="images_tab" role="tabpanel" aria-labelledby="images">
                                        <label>Главное изображение</label>
                                        <div class="preview">
                                            <a href="#" class="event_file not_remove" data-input="#category-image">
                                                <img src="{{ asset($image ? $image : old('image', 'assets/admin/img/no_image.png')) }}" />
                                                <input id="category-image" type="hidden" name="image" value="{{ old('image', $image) }}" />
                                            </a>
                                        </div>
                                        <label>Иконка</label>
                                        <div class="preview">
                                            <a href="#" class="event_file not_remove" data-input="#category2-image">
                                                <img src="{{ asset($image2 ? $image2 : old('image2', 'assets/admin/img/no_image.png')) }}" />
                                                <input id="category2-image" type="hidden" name="image2" value="{{ old('image2', $image2) }}" />
                                            </a>
                                        </div>
                                        <label>Картинка в меню</label>
                                        <div class="preview">
                                            <a href="#" class="event_file not_remove" data-input="#category3-image">
                                                <img src="{{ asset($image3 ? $image3 : old('image2', 'assets/admin/img/no_image.png')) }}" />
                                                <input id="category3-image" type="hidden" name="image3" value="{{ old('image3', $image3) }}" />
                                            </a>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="layout_tab" role="tabpanel" aria-labelledby="layout">
                                        <div class="form-group">
                                            <select name="layout_id" class="form-control">
                                                <option value="">Выберите макет</option>
                                                @foreach($layouts as $layout)
                                                    <option value="{{ $layout->id }}"{{ $layout->id == old('layout_id', $layout_id) || (!old('layout_id', $layout_id) && $layout->route == 'categories') ? ' selected' : '' }}>{{ $layout->name }}</option>
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
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(document).ready(function () {
            $('[name="parent"]').autocomplete({
                source: function(request, response) {
                    $.ajax( {
                        url: '{{ asset('admin/category_autocomplete') }}?id={{ $id ? $id : 0 }}',
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                select: function(event, ui) {
                    $('[name="parent"]').val(ui.item.value);
                    $('[name="parent_id"]').val(ui.item.id);
                }
            });
        });
    </script>
@endsection