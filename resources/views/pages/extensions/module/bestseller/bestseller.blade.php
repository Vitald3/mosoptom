@extends('layouts.contentLayoutMaster')
@section('title','Популярные товары')

@section('content')
    <section class="users-edit">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    @if(session('error'))
                        <span class="btn btn-danger invalid-feedback" role="alert" style="margin-bottom:20px;display: block"><strong>{{ session('error') }}</strong></span>
                    @endif
                    <form action="{{ $action }}" method="post" novalidate>
                        @csrf
                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Название</label>
                                        <input type="text" name="setting[name]" class="form-control @error('setting.name') is-invalid @enderror" placeholder="Название"
                                               value="{{old('setting.name', !empty($setting['name']) ? $setting['name'] : '') }}" required>
                                        @error('setting.name')
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
                                                        <label>Заголовок</label>
                                                        <input type="text" name="setting[title][{{ $l['code'] }}]" class="form-control @error('setting.title') is-invalid @enderror" placeholder="Заголовок"
                                                               value="{{ old('setting.title.' . $l['code'], !empty($setting['title'][$l['code']]) ? $setting['title'][$l['code']] : '') }}">
                                                        @error('setting.title')
                                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <br>
                                    <hr>
                                    <br>
                                @endif
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Лимит товаров</label>
                                        <input type="text" name="setting[limit]" class="form-control" placeholder="Лимит товаров" value="{{ old('setting.limit', !empty($setting['limit']) ? $setting['limit'] : '') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Ширина изображений</label>
                                        <input type="text" name="setting[width]" class="form-control" placeholder="Ширина изображений"
                                               value="{{old('setting.width', !empty($setting['width']) ? $setting['width'] : '') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Высота изображений</label>
                                        <input type="text" name="setting[height]" class="form-control" placeholder="Высота изображений"
                                               value="{{old('setting.height', !empty($setting['height']) ? $setting['height'] : '') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Статус</label>
                                    <select name="setting[status]" class="form-control">
                                        <option value="1"{{ old('status', !empty($setting['status'])) ? ' selected' : '' }}>Включено</option>
                                        <option value="0"{{ !old('status', !empty($setting['status'])) ? ' selected' : '' }}>Выключено</option>
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