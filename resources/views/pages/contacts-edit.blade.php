@extends('layouts.contentLayoutMaster')
@section('title','Контакты')

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
                    @if(session('success'))
                        <div class="alert alert-success" role="alert" style="margin-bottom:20px"><strong>{{ session('success') }}</strong></div>
                    @endif
                    <form action="{{ $action }}" method="post" novalidate>
                        @csrf
                        <input type="hidden" name="code" value="contacts" />
                        <div class="row">
                            <div class="col-12 col-sm-12">
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
                                                        <label>Meta Title</label>
                                                        <input type="text" name="contacts[meta_title][{{ $l['code'] }}]" class="form-control @error('contacts.meta_title.' . $l['code']) is-invalid @enderror" placeholder="Meta Title"
                                                               value="{{ old('contacts.meta_title.' . $l['code'], !empty($contacts['meta_title'][$l['code']]) ? $contacts['meta_title'][$l['code']] : '') }}" required>
                                                        @error('contacts.meta_title.' . $l['code'])
                                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Meta Description</label>
                                                        <textarea name="contacts[meta_description][{{ $l['code'] }}]" class="form-control @error('contacts.meta_description.' . $l['code']) is-invalid @enderror" required placeholder="Meta Description">{{ old('contacts.meta_description.' . $l['code'], !empty($contacts['meta_description'][$l['code']]) ? $contacts['meta_description'][$l['code']] : '') }}</textarea>
                                                        @error('contacts.meta_description.' . $l['code'])
                                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Meta Keywords</label>
                                                        <input type="text" name="contacts[meta_keywords][{{ $l['code'] }}]" class="form-control" placeholder="Meta Keywords"
                                                               value="{{ old('contacts.meta_keywords.' . $l['code'], !empty($contacts['meta_keywords'][$l['code']]) ? $contacts['meta_keywords'][$l['code']] : '') }}">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Адрес</label>
                                                        <input type="text" name="contacts[address][{{ $l['code'] }}]" class="form-control @error('contacts.address.' . $l['code']) is-invalid @enderror" placeholder="Адрес"
                                                               value="{{ old('contacts.address.' . $l['code'], !empty($contacts['address'][$l['code']]) ? $contacts['address'][$l['code']] : '') }}" required>
                                                        @error('contacts.address.' . $l['code'])
                                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Режим работы</label>
                                                        <textarea name="contacts[open][{{ $l['code'] }}]" class="form-control @error('contacts.open.' . $l['code']) is-invalid @enderror" placeholder="Режим работы" required>{{ old('contacts.open.' . $l['code'], !empty($contacts['open'][$l['code']]) ? $contacts['open'][$l['code']] : '') }}</textarea>
                                                        @error('contacts.open.' . $l['code'])
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
                                    <div class="controls">
                                        <label>Телефон (отдел продаж)</label>
                                        <input type="text" name="contacts[phone]" class="form-control" placeholder="Телефон (отдел продаж)"
                                               value="{{ old('contacts.phone', !empty($contacts['phone']) ? $contacts['phone'] : '') }}" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>Телефон (отдел логистики)</label>
                                        <input type="text" name="contacts[phone2]" class="form-control" placeholder="Телефон (отдел логистики)"
                                               value="{{ old('contacts.phone2', !empty($contacts['phone2']) ? $contacts['phone2'] : '') }}" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>Email (отдел логистики)</label>
                                        <input type="text" name="contacts[email]" class="form-control" placeholder="Email (отдел логистики)"
                                               value="{{ old('contacts.email', !empty($contacts['email']) ? $contacts['email'] : '') }}" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>Email (отдел логистики)</label>
                                        <input type="text" name="contacts[email2]" class="form-control" placeholder="Email (отдел логистики)"
                                               value="{{ old('contacts.email2', !empty($contacts['email2']) ? $contacts['email2'] : '') }}" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>Наименование компании</label>
                                        <input type="text" name="contacts[company]" class="form-control" placeholder="Наименование компании"
                                               value="{{ old('contacts.company', !empty($contacts['company']) ? $contacts['company'] : '') }}" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>КПП</label>
                                        <input type="text" name="contacts[kpp]" class="form-control" placeholder="КПП"
                                               value="{{ old('contacts.kpp', !empty($contacts['kpp']) ? $contacts['kpp'] : '') }}" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>БИК</label>
                                        <input type="text" name="contacts[bik]" class="form-control" placeholder="БИК"
                                               value="{{ old('contacts.bik', !empty($contacts['bik']) ? $contacts['bik'] : '') }}" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>Юридический адрес</label>
                                        <input type="text" name="contacts[company_address]" class="form-control" placeholder="Юридический адрес"
                                               value="{{ old('contacts.company_address', !empty($contacts['company_address']) ? $contacts['company_address'] : '') }}" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>Корреспонденский счет</label>
                                        <input type="text" name="contacts[kc]" class="form-control" placeholder="Корреспонденский счет"
                                               value="{{ old('contacts.kc', !empty($contacts['kc']) ? $contacts['kc'] : '') }}" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>ИНН</label>
                                        <input type="text" name="contacts[inn]" class="form-control" placeholder="ИНН"
                                               value="{{ old('contacts.inn', !empty($contacts['inn']) ? $contacts['inn'] : '') }}" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>Регистрационный номер</label>
                                        <input type="text" name="contacts[register_number]" class="form-control" placeholder="Регистрационный номер"
                                               value="{{ old('contacts.register_number', !empty($contacts['register_number']) ? $contacts['register_number'] : '') }}" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>Расчетный счет</label>
                                        <input type="text" name="contacts[rs]" class="form-control" placeholder="Расчетный счет"
                                               value="{{ old('contacts.rs', !empty($contacts['rs']) ? $contacts['rs'] : '') }}" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>Iframe карты</label>
                                        <input type="text" name="contacts[geo]" class="form-control" placeholder="Координаты для карты"
                                               value="{{ old('contacts.geo', !empty($contacts['geo']) ? $contacts['geo'] : '') }}" />
                                    </div>
                                </div>
                            </div>
                            @role('edit|create')
                            <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Сохранить</button>
                            </div>
                            @endrole
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection