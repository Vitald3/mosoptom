@extends('layouts.contentLayoutMaster')
@section('title','Роли пользователей')

@section('content')
    <section class="users-list-wrapper">
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
                        <form action="{{ asset('admin/role_delete') }}" method="post" class="table-responsive">
                            @csrf
                            <div class="col-12 col-sm-12 d-flex align-items-center" style="justify-content: space-between;margin-bottom: 15px">
                                @role('create')
                                @if(!$roles->isEmpty())
                                    <button type="submit" class="btn btn-danger btn-block glow users-list-clear mb-0" style="width: 48%">Удалить</button>
                                @endif
                                @endrole
                                @role('create')
                                <a href="{{ asset('admin/role_add') }}" class="btn btn-primary btn-block glow users-list-clear mb-0 mt-0" style="width: 48%">Создать</a>
                                @endrole
                            </div>
                            <table id="users-list-datatable" class="table">
                                @php
                                    $colspan = 2;
                                @endphp
                                <thead>
                                <tr>
                                    @role('create')
                                    <th>
                                        <input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" />
                                    </th>
                                    @php
                                        $colspan += 1;
                                    @endphp
                                    @endrole
                                    <th>
                                        <a href="{{ $sort_name }}" class="{{ $sort == 'name' ? $order : '' }}">Название</a>
                                    </th>
                                    <th>Описание</th>
                                    @role('create')
                                    <th>Действие</th>
                                    @php
                                        $colspan += 1;
                                    @endphp
                                    @endrole
                                </tr>
                                </thead>
                                <tbody>
                                @if(!$roles->isEmpty())
                                    @foreach($roles as $role)
                                        <tr>
                                            @role('create')
                                            <td><input type="checkbox" name="selected[]" value="{{ $role['id'] }}" /></td>
                                            @endrole
                                            <td>{{ $role['name'] }}</td>
                                            <td>{{ $role['description'] }}</td>
                                            @role('create')
                                            <td><a href="{{asset('admin/role/' . $role['id'])}}"><i class="bx bx-edit-alt"></i></a></td>
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
                            {{ $roles->links() }}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection