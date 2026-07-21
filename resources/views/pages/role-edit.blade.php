@extends('layouts.contentLayoutMaster')
@if($id)
    @section('title','Редактирование роли')
@else
    @section('title','Создание роли')
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
                                <div class="form-group multiple-select2">
                                    <label>Разрешения</label>
                                    <select name="permissions[]" multiple class="select2 form-control @error('permissions') is-invalid @enderror" required data-validation-required-message="Выберите Разрешения">
                                        <option value="0">Выберите разрешение</option>
                                        @foreach($permissions2 as $permission)
                                            <option value="{{ $permission['id'] }}"{{ in_array($permission['id'], (array)old('permissions', $permissions)) ? ' selected' : '' }}>{{ $permission['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('permissions')
                                    <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Описание</label>
                                        <textarea name="description" class="form-control" placeholder="Символьное обозначение">{{ old('description', $description) }}</textarea>
                                    </div>
                                </div>
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

@section('page-scripts')
    <script src="{{asset('assets/admin/js/select2.full.min.js')}}"></script>

    <script>
        var $selectMulti = $(".select2").select2();

        $selectMulti.select2({
            dropdownAutoWidth: true,
            width: '100%',
            minimumResultsForSearch: -1,
            placeholder: "Выберите разрешение"
        });
    </script>
@endsection