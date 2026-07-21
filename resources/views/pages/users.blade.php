@extends('layouts.contentLayoutMaster')
@section('title','Список пользователей админпанели')
@section('content')
  <section class="users-list-wrapper">
    <div class="users-list-filter px-1">
      <form action="{{ asset('admin/users') }}" method="get">
        <div class="row border rounded py-2 mb-2" style="background: #fff">
          <div class="col-12 col-sm-6 col-lg-3">
            <label for="users-list-verified">Статус</label>
            <fieldset class="form-group">
              <select name="status" class="form-control">
                <option value="">Выберите статус</option>
                <option value="1"{{ $status == 1 ? ' selected' : '' }}>Включено</option>
                <option value="0"{{ $status != '' && $status == 0 ? ' selected' : '' }}>Выключено</option>
              </select>
            </fieldset>
          </div>
          <div class="col-12 col-sm-6 col-lg-3">
            <label for="users-list-role">Имя</label>
            <fieldset class="form-group">
              <input type="text" name="name" placeholder="Имя" value="{{ $name }}" class="form-control" />
            </fieldset>
          </div>
          <div class="col-12 col-sm-6 col-lg-3">
            <label for="users-list-role">Email</label>
            <fieldset class="form-group">
              <input type="text" name="email" placeholder="Email" value="{{ $email }}" class="form-control" />
            </fieldset>
          </div>
          <div class="col-12 col-sm-6 col-lg-6 d-flex align-items-center" style="justify-content: space-between">
            <button type="submit" style="width: 48%" class="btn btn-primary btn-block glow users-list-clear mb-0">Применить</button>
            <a href="{{ asset('admin/users') }}" style="margin-top:0;width: 48%" class="btn btn-primary btn-block glow users-list-clear mb-0">Очистить</a>
          </div>
        </div>
      </form>
    </div>
    <div class="users-list-table">
      <div class="card">
        <div class="card-content">
          <div class="card-body">
            @if(session('error'))
              <div class="alert alert-danger" role="alert" style="margin-bottom:20px"><strong>{{ session('error') }}</strong></div>
            @endif
            @if(session('success'))
              <div class="alert alert-success" role="alert" style="margin-bottom:20px"><strong>{{ session('success') }}</strong></div>
            @endif
            @if($errors->all())
              <span class="btn btn-danger invalid-feedback" role="alert" style="margin-bottom:20px;display: block"><strong>Проверьте форму на наличие ошибок</strong></span>
            @endif
            <form action="{{ asset('admin/user_delete') . $params }}" method="post" class="table-responsive">
              @csrf
              <div class="col-12 col-sm-12 d-flex align-items-center" style="justify-content: space-between;margin-bottom: 15px">
                @role('delete')
                @if(!$users->isEmpty())
                  <button type="submit" class="btn btn-danger btn-block glow users-list-clear mb-0" style="width: 48%">Удалить</button>
                @endif
                @endrole
                @role('create')
                <a href="{{ asset('admin/user_add') . $params }}" class="btn btn-primary btn-block glow users-list-clear mb-0 mt-0" style="width: 48%">Создать</a>
                @endrole
              </div>
              <table id="users-list-datatable" class="table">
                <thead>
                <tr>
                  @php
                    $colspan = 4;
                  @endphp
                  @role('create')
                  <th>
                    <input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" />
                  </th>
                  @php
                    $colspan += 1;
                  @endphp
                  @endrole
                  <th>
                    <a href="{{ $sort_name }}" class="{{ $sort == 'users.name' ? $order : '' }}">Имя</a>
                  </th>
                  <th>
                    <a href="{{ $sort_email }}" class="{{ $sort == 'users.email' ? $order : '' }}">Email</a>
                  </th>
                  <th>
                    <a href="{{ $sort_role }}" class="{{ $sort == 'roles.name' ? $order : '' }}">Роль</a>
                  </th>
                  <th>
                    <a href="{{ $sort_status }}" class="{{ $sort == 'users.status' ? $order : '' }}">Статус</a>
                  </th>
                  @role('create')
                  <th>Действие</th>
                  @php
                    $colspan += 1;
                  @endphp
                  @endrole
                </tr>
                </thead>
                <tbody>
                @if(!$users->isEmpty())
                  @foreach($users as $user)
                    <tr>
                      @role('create')
                      <td><input type="checkbox" name="selected[]" value="{{ $user['id'] }}" /></td>
                      @endrole
                      <td>{{ $user['name'] }}</td>
                      <td>{{ $user['email'] }}</td>
                      <td>{{ $user['role'] }}</td>
                      <td>{{ $user['status'] == 1 ? 'Включено' : 'Выключено' }}</td>
                      @role('create')
                      <td><a href="{{asset('admin/user/' . $user['id']) . $params}}"><i class="bx bx-edit-alt"></i></a></td>
                      @endrole
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td colspan="{{ $colspan }}">Нет данных</td>
                  </tr>
                @endif
                </tbody>
              </table>
              {{ $users->appends($params_array)->links() }}
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection