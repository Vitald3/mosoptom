@extends('layouts.contentLayoutMaster')
@section('title','Фотогалерея модуль')
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
                                <div class="flex-custom">
                                    <a href="#" class="btn btn-primary add_image">Добавить изображение</a>
                                </div>
                                <div class="preview">
                                    <?php $image_row = 0; ?>
                                    @if(!empty($setting['data']['images']))
                                        @foreach($setting['data']['images'] as $image)
                                            <a href="#" class="event_file" data-input="#photo-images-{{ $image_row }}">
                                                <img src="{{ asset($image ? $image : old('setting.data.images.' . $image_row, 'assets/admin/img/no_image.png')) }}" />
                                                <input id="photo-images-{{ $image_row }}" type="hidden" name="setting[data][images][]" value="{{ old('setting.data.images.' . $image_row, $image) }}" />
                                            </a>
                                            <?php $image_row++; ?>
                                        @endforeach
                                    @endif
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
                                                        <input type="text" name="setting[data][title][{{ $l['code'] }}]" class="form-control" placeholder="Заголовок"
                                                               value="{{ !empty($setting['data']['title'][$l['code']]) ? $setting['data']['title'][$l['code']] : old('setting.data.title.' . $l['code']) }}">
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
                                    <div class="controls">
                                        <label>Лимит картинок</label>
                                        <input type="text" name="setting[limit]" class="form-control" placeholder="Лимит картинок" value="{{ !empty($setting['limit']) ? $setting['limit'] : old('setting.limit') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls checkbox">
                                        <input id="carousel" type="checkbox" name="setting[carousel]" class="form-control" placeholder="Карусель" value="1"{{ !empty($setting['carousel']) ? ' checked' : '' }}>
                                        <label for="carousel">Карусель</label>
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
    <script>
        var image_row = {{ $image_row }};

        $(document).on('click', '.add_image', function () {
            $(this).parent().next().append('<a href="#" class="event_file" data-input="#photo_images-' + image_row + '"><img src="{{ asset('/images/no_image.png') }}" /><input id="photo_images-' + image_row + '" type="hidden" name="setting[data][images][]" /></a>');
            image_row++;
            return false;
        });
    </script>
@endsection