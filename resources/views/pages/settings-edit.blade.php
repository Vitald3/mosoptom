@extends('layouts.contentLayoutMaster')
@section('title','Настройки')
@section('vendor-styles')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/admin/css/select2.min.css')}}">
@endsection
@section('content')
    <section class="users-edit">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger" role="alert" style="margin-bottom:20px"><strong>{{ session('error') }}</strong></div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success" role="alert" style="margin-bottom:20px"><strong>{{ session('success') }}</strong></div>
                    @endif
                    <form action="{{ $action }}" method="post" novalidate>
                        @csrf
                        <input type="hidden" name="code" value="settings" />
                        <div class="row">
                            <div class="col-12 col-sm-12">
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
                                                        <input type="text" name="settings[meta_title][{{ $l['code'] }}]" class="form-control @error('settings.meta_title.' . $l['code']) is-invalid @enderror" placeholder="Meta Title"
                                                               value="{{ old('settings.meta_title.' . $l['code'], !empty($settings['meta_title'][$l['code']]) ? $settings['meta_title'][$l['code']] : '') }}" required>
                                                        @error('settings.meta_title.' . $l['code'])
                                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Meta Description</label>
                                                        <textarea name="settings[meta_description][{{ $l['code'] }}]" class="form-control @error('settings.meta_description.' . $l['code']) is-invalid @enderror" required placeholder="Meta Description">{{ old('settings.meta_description.' . $l['code'], !empty($settings['meta_description'][$l['code']]) ? $settings['meta_description'][$l['code']] : '') }}</textarea>
                                                        @error('settings.meta_description.' . $l['code'])
                                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Meta Keywords</label>
                                                        <input type="text" name="settings[meta_keywords][{{ $l['code'] }}]" class="form-control" placeholder="Meta Keywords"
                                                               value="{{ old('settings.meta_keywords.' . $l['code'], !empty($settings['meta_keywords'][$l['code']]) ? $settings['meta_keywords'][$l['code']] : '') }}">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Словоформа №1, шорткод - {FORMAT1}</label>
                                                        <input type="text" name="settings[format1][{{ $l['code'] }}]" class="form-control @error('settings.format1' . $l['code']) is-invalid @enderror" placeholder="Словоформа №1, шорткод - {FORMAT1}"
                                                               value="{{ old('settings.format1' . $l['code'], !empty($settings['format1'][$l['code']]) ? $settings['format1'][$l['code']] : '') }}" required>
                                                        @error('settings.format1' . $l['code'])
                                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Словоформа №2, шорткод - {FORMAT2}</label>
                                                        <input type="text" name="settings[format2][{{ $l['code'] }}]" class="form-control @error('settings.format2' . $l['code']) is-invalid @enderror" placeholder="Словоформа №2, шорткод - {FORMAT2}"
                                                               value="{{ old('settings.format2' . $l['code'], !empty($settings['format2'][$l['code']]) ? $settings['format2'][$l['code']] : '') }}" required>
                                                        @error('settings.format2' . $l['code'])
                                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Словоформа №3, шорткод - {FORMAT3}</label>
                                                        <input type="text" name="settings[format3][{{ $l['code'] }}]" class="form-control" placeholder="Словоформа №3, шорткод - {FORMAT3}"
                                                               value="{{ old('settings.format1' . $l['code'], !empty($settings['format3'][$l['code']]) ? $settings['format3'][$l['code']] : '') }}">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Название магазина</label>
                                                        <textarea name="settings[name][{{ $l['code'] }}]" class="form-control @error('settings.name.' . $l['code']) is-invalid @enderror" placeholder="Название магазина" required>{{ old('settings.name.' . $l['code'], !empty($settings['name'][$l['code']]) ? $settings['name'][$l['code']] : '') }}</textarea>
                                                        @error('settings.name.' . $l['code'])
                                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Режим работы</label>
                                                        <input type="text" name="settings[open][{{ $l['code'] }}]" class="form-control @error('settings.open.' . $l['code']) is-invalid @enderror" placeholder="Режим работы"
                                                               value="{{ old('settings.open.' . $l['code'], !empty($settings['open'][$l['code']]) ? $settings['open'][$l['code']] : '') }}" required>
                                                        @error('settings.open.' . $l['code'])
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
                                            <label>Телефон</label>
                                            <input type="text" name="settings[phone]" class="form-control @error('settings.phone') is-invalid @enderror" placeholder="Телефон"
                                                   value="{{ old('settings.phone', !empty($settings['phone']) ? $settings['phone'] : '') }}" required/>
                                            @error('settings.phone')
                                            <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="controls">
                                            <label>Доп. Телефон</label>
                                            <input type="text" name="settings[phone2]" class="form-control" placeholder="Доп. Телефон"
                                                   value="{{ old('settings.phone2', !empty($settings['phone2']) ? $settings['phone2'] : '') }}" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="controls">
                                            <label>Телеграм</label>
                                            <input type="text" name="settings[telegram]" class="form-control" placeholder="Телеграм"
                                                   value="{{ old('settings.telegram', !empty($settings['telegram']) ? $settings['telegram'] : '') }}" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="controls">
                                            <label>Whatsapp</label>
                                            <input type="text" name="settings[whatsapp]" class="form-control" placeholder="Whatsapp"
                                                   value="{{ old('settings.whatsapp', !empty($settings['whatsapp']) ? $settings['whatsapp'] : '') }}" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="controls">
                                            <label>Viber</label>
                                            <input type="text" name="settings[viber]" class="form-control" placeholder="Viber"
                                                   value="{{ old('settings.viber', !empty($settings['viber']) ? $settings['viber'] : '') }}" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="controls">
                                            <label>Vk</label>
                                            <input type="text" name="settings[vk]" class="form-control" placeholder="Vk"
                                                   value="{{ old('settings.vk', !empty($settings['vk']) ? $settings['vk'] : '') }}" />
                                        </div>
                                    </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>Email администратора</label>
                                        <input type="text" name="settings[email]" class="form-control @error('settings.email') is-invalid @enderror" placeholder="Email администратора"
                                               value="{{ old('settings.email', !empty($settings['email']) ? $settings['email'] : '') }}" required/>
                                        @error('settings.email')
                                        <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>Количество элементов на странице в админ панели</label>
                                        <input type="number" name="settings[limit]" class="form-control" placeholder="Количество элементов на странице в админ панели"
                                               value="{{ old('settings.limit', !empty($settings['limit']) ? $settings['limit'] : '') }}" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>Количество элементов на сайте</label>
                                        <input type="number" name="settings[limit_sait]" class="form-control" placeholder="Количество элементов на сайте"
                                               value="{{ old('settings.limit_sait', !empty($settings['limit_sait']) ? $settings['limit_sait'] : '') }}" />
                                    </div>
                                </div>

                                    <div class="form-group">
                                        <label>Группа клиентов по умолчанию</label>
                                        <select name="settings[customer_group_id]" class="form-control" required>
                                            @foreach($customer_groups as $customer_group)
                                                <option value="{{ $customer_group->id }}"{{ $customer_group->id == old('setting.customer_group_id', !empty($settings['customer_group_id']) ? $settings['customer_group_id'] : '') ? ' selected' : '' }}>{{ $customer_group->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>Язык по умолчанию</label>
                                        <select name="settings[default_language]" class="form-control">
                                            <option value="0">Выберите язык</option>
                                            @foreach($langs as $l)
                                                <option value="{{ $l['code'] }}"{{ (!empty($settings['default_language']) && $l['code'] == $settings['default_language']) || (old('settings.default_language') && old('settings.default_language') == $l['code']) ? ' selected' : '' }}>{{ $l['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>Валюта по умолчанию</label>
                                        <select name="settings[currency_code]" class="form-control">
                                            <option value="">Выберите валюту</option>
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency['code'] }}"{{ (!empty($settings['currency_code']) && $currency['code'] == $settings['currency_code']) || (old('settings.currency_code') && old('settings.currency_code') == $currency['code']) ? ' selected' : '' }}>{{ $currency['title'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>Статус заказа по умолчанию</label>
                                        <select name="settings[order_status_id]" class="form-control">
                                            <option value="">Выберите статус</option>
                                            @foreach($status as $s)
                                                <option value="{{ $s['id'] }}"{{ (!empty($settings['order_status_id']) && $s['id'] == $settings['order_status_id']) || (old('settings.order_status_id') && old('settings.order_status_id') == $s['id']) ? ' selected' : '' }}>{{ $s['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>Макет по умолчанию</label>
                                        <select name="settings[layout_id]" class="form-control">
                                            <option value="0">Выберите макет</option>
                                            @foreach($layouts as $layout)
                                                <option value="{{ $layout->id }}"{{ old('settings.layout_id', !empty($settings['layout_id']) ? $settings['layout_id'] : 0) == $layout->id ? ' selected' : '' }}>{{ $layout->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                    <div class="form-group">
                                        <div class="controls">
                                            <label>Показывать цены только авторизированным</label>
                                            <select name="settings[price_logged]" class="form-control">
                                                <option value="1"{{ old('settings.price_logged', !empty($settings['price_logged']) ? $settings['price_logged'] : 0) ? ' selected' : '' }}>Да</option>
                                                <option value="0"{{ !old('settings.price_logged', !empty($settings['price_logged']) ? $settings['price_logged'] : 0) ? ' selected' : '' }}>Нет</option>
                                            </select>
                                        </div>
                                    </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>Макет главной страницы</label>
                                        <select name="settings[main_layout_id]" class="form-control">
                                            <option value="0">Выберите макет</option>
                                            @foreach($layouts as $layout)
                                                <option value="{{ $layout->id }}"{{ old('settings.main_layout_id', !empty($settings['main_layout_id']) ? $settings['main_layout_id'] : 0) == $layout->id ? ' selected' : '' }}>{{ $layout->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                    <div class="form-group">
                                        <label>Главная категория (от нее строится Seo url категорий)</label>
                                        <div class="controls">
                                            <input type="text" name="settings[default_category]" value="{{ old('settings.default_category', (!empty($settings['default_category']) ? $settings['default_category'] : '')) }}" placeholder="Главная категория" class="form-control">
                                            <input type="hidden" name="settings[default_category_id]" value="{{ old('settings.default_category_id', (!empty($settings['default_category_id']) ? $settings['default_category_id'] : 0)) }}" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="controls">
                                            <label>Страница обработки персональных данных</label>
                                            <select name="settings[policy]" class="form-control">
                                                <option value="0">Выберите статью</option>
                                                @foreach($pages as $page)
                                                    <option value="{{ $page->id }}"{{ old('settings.policy', !empty($settings['policy']) ? $settings['policy'] : 0) == $page->id ? ' selected' : '' }}>{{ !is_null($page->metaLang) ? $page->metaLang->name : '' }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group multiple-select2">
                                        <label>Статус заказа в обработке</label>
                                        <select name="settings[processing_status][]" multiple class="select2 form-control">
                                            @foreach($status as $s)
                                                <option value="{{ $s['id'] }}"{{ in_array($s['id'], old('settings.processing_status', !empty($settings['processing_status']) ? $settings['processing_status'] : [])) ? ' selected' : '' }}>{{ $s['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group multiple-select2">
                                        <label>Статус выполненного заказа</label>
                                        <select name="settings[complete_status][]" multiple class="select2 form-control">
                                            @foreach($status as $s)
                                                <option value="{{ $s['id'] }}"{{ in_array($s['id'], old('settings.complete_status', !empty($settings['complete_status']) ? $settings['complete_status'] : [])) ? ' selected' : '' }}>{{ $s['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Статус заказа мошенничества</label>
                                        <select name="settings[fraud_status_id]" class="form-control">
                                            @foreach($status as $s)
                                                <option value="{{ $s['id'] }}"{{ $s['id'] == old('settings.fraud_status_id', !empty($settings['fraud_status_id']) ? $settings['fraud_status_id'] : 0) ? ' selected' : '' }}>{{ $s['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                @role('edit|create')
                                    <div class="form-group">
                                        <div class="controls">
                                            <label>Логотип</label>
                                            <div class="preview">
                                                <a href="#" class="event_file not_remove" data-input="#setting-logo">
                                                    <img src="{{ asset(old('settings.logo', (!empty($settings['logo']) ? $settings['logo'] : 'assets/admin/img/no_image.png'))) }}" />
                                                    <input id="setting-logo" type="hidden" name="settings[logo]" value="{{ old('settings.logo', (!empty($settings['logo']) ? $settings['logo'] : '')) }}" />
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="controls">
                                            <label>Логотип для почтовых писем</label>
                                            <div class="preview">
                                                <a href="#" class="event_file not_remove" data-input="#setting-logo-mail">
                                                    <img src="{{ asset(old('settings.logo_mail', (!empty($settings['logo_mail']) ? $settings['logo_mail'] : 'assets/admin/img/no_image.png'))) }}" />
                                                    <input id="setting-logo-mail" type="hidden" name="settings[logo_mail]" value="{{ old('settings.logo_mail', (!empty($settings['logo_mail']) ? $settings['logo_mail'] : '')) }}" />
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                <div class="form-group">
                                    <div class="controls">
                                        <label>Фавикон</label>
                                        <div class="preview">
                                            <a href="#" class="event_file not_remove" data-input="#setting-favicon">
                                                <img src="{{ asset(old('settings.favicon', (!empty($settings['favicon']) ? $settings['favicon'] : 'assets/admin/img/no_image.png'))) }}" />
                                                <input id="setting-favicon" type="hidden" name="settings[favicon]" value="{{ old('settings.favicon', (!empty($settings['favicon']) ? $settings['favicon'] : '')) }}" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endrole
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
@section('vendor-scripts')
    <script src="{{asset('assets/admin/js/select2.full.min.js')}}"></script>
@endsection
@section('page-scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(document).ready(function() {
            $('select[name="settings[processing_status][]"]').select2({
                dropdownAutoWidth: true,
                width: '100%',
                minimumResultsForSearch: 1,
                placeholder: "Выберите статус"
            });

            $('select[name="settings[complete_status][]"]').select2({
                dropdownAutoWidth: true,
                width: '100%',
                minimumResultsForSearch: 1,
                placeholder: "Выберите статус"
            });

            $('[name="settings[default_category]"]').autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: '{{ asset('admin/category_autocomplete') }}',
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function (data) {
                            response(data);
                        }
                    });
                },
                select: function (event, ui) {
                    $('[name="settings[default_category]"]').val(ui.item.name);
                    $('[name="settings[default_category_id]"]').val(ui.item.id);
                }
            });
        });
    </script>
@endsection