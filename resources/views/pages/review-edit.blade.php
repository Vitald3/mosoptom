@extends('layouts.contentLayoutMaster')
@if($id)
    @section('title','Редактирование отзыва')
@else
    @section('title','Создание отзыва')
@endif
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
                                        <label>Автор</label>
                                        <input type="text" name="author" class="form-control @error('author') is-invalid @enderror" placeholder="Автор"
                                               value="{{ old('author', $author) }}" required>
                                        @error('author')
                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Товар</label>
                                        <input type="text" name="product" class="form-control @error('product_id') is-invalid @enderror" placeholder="Товар"
                                               value="{{ old('product', $product) }}" required>
                                        <input type="hidden" name="product_id" value="{{ $product_id }}" />
                                        @error('product_id')
                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label class="required">Рейтинг</label>
                                        <div style="display: flex;align-items: center">
                                            <div class="radio">
                                                <input id="rating1" type="radio" name="rating" class="form-control @error('rating') is-invalid @enderror" placeholder="Рейтинг" value="1"{{ old('rating', $rating) == 1 ? ' checked' : '' }}>
                                                <label for="rating1">1</label>
                                            </div>
                                            <div class="radio" style="margin-left: 6px">
                                                <input id="rating2" type="radio" name="rating" class="form-control @error('rating') is-invalid @enderror" placeholder="Рейтинг" value="2"{{ old('rating', $rating) == 2 ? ' checked' : '' }}>
                                                <label for="rating2">2</label>
                                            </div>
                                            <div class="radio" style="margin-left: 6px">
                                                <input id="rating3" type="radio" name="rating" class="form-control @error('rating') is-invalid @enderror" placeholder="Рейтинг" value="3"{{ old('rating', $rating) == 3 ? ' checked' : '' }}>
                                                <label for="rating3">3</label>
                                            </div>
                                            <div class="radio" style="margin-left: 6px">
                                                <input id="rating4" type="radio" name="rating" class="form-control @error('rating') is-invalid @enderror" placeholder="Рейтинг" value="4"{{ old('rating', $rating) == 4 ? ' checked' : '' }}>
                                                <label for="rating4">4</label>
                                            </div>
                                            <div class="radio" style="margin-left: 6px">
                                                <input id="rating5" type="radio" name="rating" class="form-control @error('rating') is-invalid @enderror" placeholder="Рейтинг" value="5"{{ old('rating', $rating) == 5 ? ' checked' : '' }}>
                                                <label for="rating5">5</label>
                                            </div>
                                        </div>
                                        @error('rating')
                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Текст отзыва</label>
                                        <textarea name="text" class="form-control @error('text') is-invalid @enderror" placeholder="Текст отзыва" required>{{ old('text', $text) }}</textarea>
                                        @error('text')
                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                        @enderror
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
                    $('[name="product"]').val(ui.item.value);
                    $('[name="product_id"]').val(ui.item.id);
                }
            });
        });
    </script>
@endsection