@extends('layouts.contentLayoutMaster')
@section('title','Электронными деньгами')

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
                                        <label>Сумма заказа от</label>
                                        <input type="text" name="setting[total]" class="form-control" placeholder="Сумма заказа от" value="{{ old('setting.total', (!empty($setting['total']) ? $setting['total'] : 0)) }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Статус заказа после оплаты</label>
                                    <select name="setting[order_status_id]" class="form-control">
                                        <option value="">Выберите статус</option>
                                        @foreach($statuses as $s)
                                            <option value="{{ $s['id'] }}"{{ old('setting.order_status_id', (!empty($setting['order_status_id']) ? $setting['order_status_id'] : '')) == $s['id'] ? ' selected' : '' }}>{{ $s['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Порядок сортировки</label>
                                        <input type="text" name="setting[sort_order]" class="form-control" placeholder="Порядок сортировки" value="{{ old('setting.sort_order', (!empty($setting['sort_order']) ? $setting['sort_order'] : 0)) }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Статус</label>
                                    <select name="setting[status]" class="form-control">
                                        <option value="1"{{ old('setting.status', (!empty($setting['status']) ? $setting['status'] : 0)) ? ' selected' : '' }}>Включено</option>
                                        <option value="0"{{ old('setting.status', (!empty($setting['status']) ? $setting['status'] : 0)) == 0 || !old('setting.status', (!empty($setting['status']) ? $setting['status'] : 0)) ? ' selected' : '' }}>Выключено</option>
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