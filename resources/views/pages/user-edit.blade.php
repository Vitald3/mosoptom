@extends('layouts.contentLayoutMaster')
@if($id)
    @section('title','Редактирование пользователя')
@else
    @section('title','Создание пользователя')
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
                                        <label>Имя</label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Имя" value="{{ old('name', $name) }}" required/>
                                        @error('name')
                                        <span class="invalid-feedback" role="alert" style="display: block">
                                  <strong>{{ $message }}</strong>
                              </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Email (логин)</label>
                                        <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email', $email) }}" required/>
                                        @error('email')
                                        <span class="invalid-feedback" role="alert" style="display: block">
                                  <strong>{{ $message }}</strong>
                              </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Пароль</label>
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Пароль" value="{{ old('password', $password) }}" />
                                        @error('password')
                                        <span class="invalid-feedback" role="alert" style="display: block">
                                  <strong>{{ $message }}</strong>
                              </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Роль пользователя</label>
                                    <select name="role_id" class="form-control @error('role_id') is-invalid @enderror" required>
                                        <option value="">Выберите роль</option>
                                        @foreach ($roles as $key => $r)
                                            <option value="{{ $key }}"{{ old('role_id', $role_id) == $key ? ' selected' : '' }}>{{ $r }}</option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                    <span class="invalid-feedback" role="alert" style="display: block">
                                  <strong>{{ $message }}</strong>
                              </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Статус</label>
                                    <select name="status" class="form-control">
                                        <option value="1"{{ old('status', $status) == 1 ? ' selected' : '' }}>Включено</option>
                                        <option value="0"{{ old('status', $status) == 0 || !old('status', $status) ? ' selected' : '' }}>Выключено</option>
                                    </select>
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