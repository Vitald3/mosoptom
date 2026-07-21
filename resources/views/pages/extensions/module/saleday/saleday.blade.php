@extends('layouts.contentLayoutMaster')
@section('title','Предложение дня')
@section('vendor-styles')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection
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
                                               value="{{ old('setting.name', !empty($setting['name']) ? $setting['name'] : '') }}" required>
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
                                    <fieldset>
                                        <div class="checkbox">
                                            <input type="checkbox" name="setting[rand]" class="checkbox-input" value="1" id="checkbox2"{{ old('setting.rand', !empty($setting['rand']) ? $setting['rand'] : '') ? ' checked' : '' }} />
                                            <label for="checkbox2" class="@error('setting.rand') is-invalid @enderror">Случайный акционный товар</label>
                                        </div>
                                        @error('setting.rand')
                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </fieldset>
                                </div>
                                <div class="form-group">
                                    <label>Или выберите товар</label>
                                    <input type="text" name="setting[product]" class="product form-control @error('setting.product_id') is-invalid @enderror" placeholder="Выбрать товар"
                                           value="{{ old('setting.product', !empty($product) ? $product : '') }}">
                                    <input type="hidden" name="setting[product_id]" value="{{ old('setting.product_id', !empty($setting['product_id']) ? $setting['product_id'] : '') }}">
                                    @error('setting.product_id')
                                    <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Статус</label>
                                    <select name="setting[status]" class="form-control">
                                        <option value="1"{{ old('setting.status', !empty($setting['status'])) ? ' selected' : '' }}>Включено</option>
                                        <option value="0"{{ !old('setting.status', !empty($setting['status'])) ? ' selected' : '' }}>Выключено</option>
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
            $('#checkbox2').on('change', function(){
                if ($(this).prop('checked')) {
                    $('.product').val('');
                    $('.product').next().val('');
                }
            });

            $('.product').autocomplete({
                source: function(request, response) {
                    $.ajax( {
                        url: '{{ asset('admin/product_autocomplete') }}',
                        dataType: "json",
                        data: {
                            term: request.term,
                            special: 1
                        },
                        success: function(data) {
                            response($.map(data, function(item) {
                                return {
                                    value: item['name'],
                                    id: item['id']
                                }
                            }));
                        }
                    });
                },
                select: function(event, ui) {
                    $('#checkbox2').prop('checked', false);
                    $('.product').val(ui.item.value);
                    $('.product').next().val(ui.item.id);
                }
            });
        })
    </script>
@endsection