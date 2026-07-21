@extends('layouts.contentLayoutMaster')
@if($id)
    @section('title','Редактирование языка')
@else
    @section('title','Создание языка')
@endif

@section('content')
    <section class="users-edit">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger" role="alert" style="margin-bottom:20px"><strong>{{ session('error') }}</strong></div>
                    @endif
                    @if($errors->all())
                        <span class="btn btn-danger invalid-feedback" role="alert" style="margin-bottom:20px;display: block"><strong>Проверьте форму на наличие ошибок</strong></span>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success" role="alert" style="margin-bottom:20px"><strong>{{ session('success') }}</strong></div>
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
                                        <span class="invalid-feedback" role="alert" style="display: block">
                                  <strong>{{ $message }}</strong>
                              </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Код языка</label>
                                        <select name="code" class="form-control @error('cod') is-invalid @enderror" required>
                                            <option value="0">Выберите код</option>
                                            @foreach($codes as $c)
                                                <option value="{{ $c }}"{{ (old('code', $code) == $c) || (old('code') == $c) ? ' selected' : '' }}>{{ $c }}</option>
                                            @endforeach
                                        </select>
                                        @error('code')
                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Код для атрибута hreflang</label>
                                        <input type="text" name="hreflang" class="form-control @error('hreflang') is-invalid @enderror" placeholder="Код для атрибута hreflang"
                                               value="{{ old('hreflang', $hreflang) }}" required>
                                        @error('hreflang')
                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Маска телефона</label>
                                        <input type="text" name="mask" class="form-control @error('mask') is-invalid @enderror" placeholder="Маска телефона" value="{{  old('mask', $mask) }}" required />
                                        @error('mask')
                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Порядок сортировки</label>
                                        <input type="text" name="sort" class="form-control" placeholder="Порядок сортировки" value="{{ old('sort', $sort) }}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Статус</label>
                                    <select name="status" class="form-control">
                                        <option value="1"{{ old('status', $status) == 1 ? ' selected' : '' }}>Включено</option>
                                        <option value="0"{{ old('status', $status) == 0 || !old('status', $status) ? ' selected' : '' }}>Выключено</option>
                                    </select>
                                </div>
                                <section id="dropzone-examples">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <label>Изображение</label>
                                                <div class="preview">
                                                    <a href="#" class="event_file not_remove" data-input="#product-image">
                                                        <img src="{{ asset($image ? $image : (old('image') ? old('image') : 'assets/admin/img/no_image.png')) }}" />
                                                        <input id="product-image" type="hidden" name="image" value="{{ old('image', $image) }}" />
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Сохранить</button>
                                <a href="{{ url()->previous() }}" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Назад</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection