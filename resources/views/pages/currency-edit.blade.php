@extends('layouts.contentLayoutMaster')
@if($id)
    @section('title','Редактирование валюты')
@else
    @section('title','Создание валюты')
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
                                        <label>Название</label>
                                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="Название"
                                               value="{{ old('title', $title) }}" required />
                                        @error('title')
                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>ISO код валюты</label>
                                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" placeholder="ISO код валюты"
                                               value="{{ old('code', $code) }}" required>
                                        @error('code')
                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Значение</label>
                                        <input type="text" name="value" class="form-control @error('value') is-invalid @enderror" placeholder="Значение"
                                               value="{{ old('value', $value) }}" required>
                                        @error('value')
                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Знаков после запятой</label>
                                        <input type="text" name="decimal" class="form-control" placeholder="Значение" value="{{ old('decimal', $decimal) }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Символ валюты</label>
                                        <input type="text" name="symbol" class="form-control @error('symbol') is-invalid @enderror" placeholder="Символ валюты"
                                               value="{{ old('symbol', $symbol) }}" required>
                                        @error('symbol')
                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label class="required">Позиция</label>
                                        <div>
                                            <div class="radio">
                                                <input id="position1" type="radio" name="position" class="form-control" value="1"{{ $position == 1 ? ' checked' : '' }}>
                                                <label for="position1">Слева</label>
                                            </div>
                                            <div class="radio">
                                                <input id="position2" type="radio" name="position" class="form-control" value="2"{{ $position == 2 ? ' checked' : '' }}>
                                                <label for="position2">Справа</label>
                                            </div>
                                        </div>
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

@section('page-scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(document).ready(function () {
            $('[name="product"]').autocomplete({
                source: function(request, response) {
                    $.ajax( {
                        url: '{{ asset('admin/product_autocomplete') }}',
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                select: function(event, ui) {
                    $('[name="product"]').val(ui.item.value);
                    $('[name="product_id"]').val(ui.item.id);
                }
            });
        });
    </script>
@endsection