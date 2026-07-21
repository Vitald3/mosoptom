@extends('layouts.contentLayoutMaster')
@if($id)
    @section('title','Редактирование региона')
@else
    @section('title','Создание региона')
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
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Seo Url</label>
                                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" placeholder="Seo Url"
                                               value="{{ old('slug', $slug) }}" required>
                                        @error('slug')
                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

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
                                                        <label>Словоформа №1, шорткод - {FORMAT1}</label>
                                                        <input type="text" name="meta[{{ $l['code'] }}][format1]" class="form-control @error('meta.' . $l['code'] . '.format1') is-invalid @enderror" placeholder="Словоформа №1, шорткод - {FORMAT1}"
                                                               value="{{ old('meta.' . $l['code'] . '.format1', !empty($meta[$l['code']]['format1']) ? $meta[$l['code']]['format1'] : '') }}" required>
                                                        @error('meta.' . $l['code'] . '.format1')
                                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Словоформа №2, шорткод - {FORMAT2}</label>
                                                        <input type="text" name="meta[{{ $l['code'] }}][format2]" class="form-control @error('meta.' . $l['code'] . '.format2') is-invalid @enderror" placeholder="Словоформа №2, шорткод -  {FORMAT2}"
                                                               value="{{ old('meta.' . $l['code'] . '.format2', !empty($meta[$l['code']]['format2']) ? $meta[$l['code']]['format2'] : '') }}" required>
                                                        @error('meta.' . $l['code'] . '.format2')
                                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Словоформа №3, шорткод - {FORMAT3}</label>
                                                        <input type="text" name="meta[{{ $l['code'] }}][format3]" class="form-control" placeholder="Словоформа №3, шорткод - {FORMAT3}"
                                                               value="{{ old('meta.' . $l['code'] . '.format3', !empty($meta[$l['code']]['format3']) ? $meta[$l['code']]['format3'] : '') }}" >
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
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
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