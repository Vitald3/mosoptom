@extends('layouts.contentLayoutMaster')

@if($id)
    @section('title','Редактирование характеристики')
@else
    @section('title','Создание характеристики')
@endif

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
                                                                <label>Описание</label>
                                                                <textarea name="meta[{{ $l['code'] }}][description]" class="form-control" placeholder="Описание">{{ old('meta.' . $l['code'] . '.description', !empty($meta[$l['code']]['description']) ? $meta[$l['code']]['description'] : '') }}</textarea>
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
                                                <label>Порядок сортировки</label>
                                                <input type="text" name="sort" class="form-control" placeholder="Порядок сортировки"
                                                       value="{{ old('sort', $sort) }}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Группа характеристик</label>
                                                <select name="attribute_group_id" class="form-control @error('attribute_group_id') is-invalid @enderror">
                                                    <option value="">Выберите группу</option>
                                                    @foreach($attribute_groups as $attribute_group)
                                                        <option value="{{ $attribute_group['id'] }}"{{ old('attribute_group_id', $attribute_group_id) == $attribute_group['id'] ? ' selected' : '' }}>{{ $attribute_group['name'] }}</option>
                                                    @endforeach
                                                </select>
                                                @error('attribute_group_id')
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
                                        <div class="preview">
                                            <a href="#" class="event_file not_remove" data-input="#attribute-image">
                                                <img src="{{ asset($image ? $image : (old('image') ? old('image') : 'assets/admin/img/no_image.png')) }}" />
                                                <input id="attribute-image" type="hidden" name="image" value="{{ old('image', $image) }}" />
                                            </a>
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