@extends('layouts.contentLayoutMaster')
@section('title','Обратите внимание')
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
                                <div class="form-group">
                                    <label>Товары</label>
                                    <div class="controls">
                                        <input type="text" name="product" placeholder="Товары" class="form-control @error('setting.products') is-invalid @enderror">
                                        <div id="product" class="well well-sm" style="height: 150px; overflow: auto;">
                                            @foreach((array)old('setting.products', !empty($setting['products']) ? $setting['products'] : []) as $p)
                                                <div id="product{{ $p }}">
                                                    <i class="minus">-</i> {{ $products[$p]['name'] }}
                                                    <input type="hidden" name="setting[products][]" value="{{ $p }}" />
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('setting.products')
                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                        @enderror
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
            $('[name="product"]').autocomplete({
                source: function(request, response) {
                    $.ajax( {
                        url: '{{ asset('admin/product_autocomplete') }}',
                        dataType: "json",
                        data: {
                            term: request.term
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
                select: function (event, ui) {
                    $('[name="product"]').val('');

                    $('#product' + ui.item.id).remove();

                    $('#product').append('<div id="product' + ui.item.id + '"><i class="minus">-</i> ' + ui.item.value + '<input type="hidden" name="setting[products][]" value="' + ui.item.id + '" /></div>');
                }
            });

            $('#product').delegate('.minus', 'click', function () {
                $(this).parent().remove();
            });
        })
    </script>
@endsection