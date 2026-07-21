@extends('layouts.contentLayoutMaster')
@section('title','Копирование фильтров')

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
                    <form action="{{ url('admin/filters_add') }}" method="post" novalidate>
                        @csrf
                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Тип</label>
                                        <select name="type" class="form-control @error('type') is-invalid @enderror" required data-validation-required-message="Выберите тип">
                                            <option value="">Выберите тип</option>
                                            <option value="checkbox"{{ isset($type) && $type == 'checkbox' ? ' selected' : '' }}>Флажек</option>
                                            <option value="radio"{{ isset($type) && $type == 'radio' ? ' selected' : '' }}>Кнопка</option>
                                            <option value="select"{{ isset($type) && $type == 'select' ? ' selected' : '' }}>Список</option>
                                            <option value="slider"{{ isset($type) && $type == 'slider' ? ' selected' : '' }}>Слайдер</option>
                                        </select>
                                        @error('type')
                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>Выберите тип</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Привязать фильтры к категориям</label>
                                    <select name="category" class="form-control">
                                        <option value="1"{{ isset($category) && $category == 1 ? ' selected' : '' }}>Да</option>
                                        <option value="0"{{ !isset($category) ? ' selected' : '' }}>Нет</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Привязать фильтры к товарам</label>
                                    <select name="product" class="form-control">
                                        <option value="1"{{ isset($product) && $product == 1 ? ' selected' : '' }}>Да</option>
                                        <option value="0"{{ !isset($product) ? ' selected' : '' }}>Нет</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Удалить ранее скопированные фильтры</label>
                                    <select name="delete" class="form-control">
                                        <option value="1"{{ isset($delete) && $delete == 1 ? ' selected' : '' }}>Да</option>
                                        <option value="0"{{ !isset($delete) ? ' selected' : '' }}>Нет</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Статус</label>
                                    <select name="status" class="form-control">
                                        <option value="1"{{ isset($status) && $status == 1 ? ' selected' : '' }}>Включено</option>
                                        <option value="0"{{ !isset($status) ? ' selected' : '' }}>Выключено</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Копировать</button>
                                <a href="{{ url()->previous() }}" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Назад</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection