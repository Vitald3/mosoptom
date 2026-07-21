@extends('layouts.contentLayoutMaster')

@if($id)
    @section('title','Редактирование клиента')
@else
    @section('title','Создание клиента')
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
                        <input type="hidden" name="ip" value="{{ !empty($ip) ? $ip : old('ip') }}" />
                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <ul class="nav nav-tabs nav-fill" id="myTab2" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="content" data-toggle="tab" href="#content_tab" role="tab" aria-controls="content_tab" aria-selected="true">
                                            Общие
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="address" data-toggle="tab" href="#address_tab" role="tab" aria-controls="address_tab" aria-selected="true">
                                            Адрес
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="order" data-toggle="tab" href="#order_tab" role="tab" aria-controls="order_tab" aria-selected="true">
                                            Заказы
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="reward" data-toggle="tab" href="#reward_tab" role="tab" aria-controls="reward_tab" aria-selected="true">
                                            Бонусы
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="ip" data-toggle="tab" href="#ip_tab" role="tab" aria-controls="ip_tab" aria-selected="true">
                                            Ip
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content pt-1">
                                    <div class="tab-pane active" id="content_tab" role="tabpanel" aria-labelledby="content">
                                        <div class="form-group">
                                            <label>Группа клиентов</label>
                                            <select name="customer_group_id" class="form-control" required>
                                                <option value="">Выберите группы клиентов</option>
                                                @foreach($customer_groups as $customer_group)
                                                    <option value="{{ $customer_group->id }}"{{ $customer_group->id == old('customer_group_id', $customer_group_id) ? ' selected' : '' }}>{{ $customer_group->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('customer_group_id')
                                            <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Имя</label>
                                                <input type="text" name="firstname" class="form-control" placeholder="Имя"
                                                       value="{{ old('firstname', $firstname) }}" required />
                                                @error('firstname')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Фамилия</label>
                                                <input type="text" name="lastname" class="form-control" placeholder="Фамилия"
                                                       value="{{ old('lastname', $lastname) }}" required />
                                                @error('lastname')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Email</label>
                                                <input type="text" name="email" class="form-control" placeholder="Email"
                                                       value="{{ old('email', $email) }}" required />
                                                @error('email')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Телефон</label>
                                                <input type="text" name="phone" class="form-control" placeholder="Телефон"
                                                       value="{{ old('phone', $phone) }}" required />
                                                @error('phone')
                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Пароль</label>
                                                <input type="password" name="password" class="form-control" placeholder="Пароль"
                                                       value="{{ old('password') }}" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Подтвержден</label>
                                            <select name="approval" class="select2 form-control">
                                                <option value="1"{{ old('approval', $approval) == 1 ? ' selected' : '' }}>Да</option>
                                                <option value="0"{{ old('approval', $approval) == 0 || !old('approval', $approval) ? ' selected' : '' }}>Нет</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Статус</label>
                                            <select name="status" class="form-control">
                                                <option value="1"{{ old('status', $status) == 1 ? ' selected' : '' }}>Включено</option>
                                                <option value="0"{{ old('status', $status) == 0 || !old('status', $status) ? ' selected' : '' }}>Выключено</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="address_tab" role="tabpanel" aria-labelledby="address">
                                        <div class="addresses">
                                            <div style="margin-bottom: 10px">
                                                <a href="#" onclick="addAddress();return false;" class="btn btn-primary">Добавить адрес</a>
                                            </div>
                                            <ul class="nav nav-tabs nav-fill lists" role="tablist">
                                                @foreach($address as $key => $a)
                                                    <?php $key++; ?>
                                                    <li class="nav-item">
                                                        <a class="nav-link{{ $key == 1 ? ' active' : '' }}" id="address-{{ $key }}" data-toggle="tab" href="#address-{{ $key }}_tab" role="tab" aria-controls="address-{{ $key }}_tab" aria-selected="true">
                                                            Адрес #{{ $key }}
                                                            <span style="float: right;margin-top: -8px;margin-right: -18px" class="btn btn-danger" onclick="$('#address-{{ $key }}_tab').remove();$(this).parent().parent().remove()">&times;</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>

                                            <div class="tab-content pt-1 lists_address">
                                                <?php $address_row = 0; ?>
                                                @foreach($address as $key => $a)
                                                    <?php $key++; ?>
                                                    <div class="tab-pane{{ $key == 1 ? ' active' : '' }}" id="address-{{ $key }}_tab" role="tabpanel" aria-labelledby="address-{{ $key }}">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Имя</label>
                                                                <input type="text" name="address[{{ $address_row }}][firstname]" class="form-control" placeholder="Имя"
                                                                       value="{{ !empty($a['firstname']) ? $a['firstname'] : old('address.' . $address_row . 'firstname') }}" required />
                                                                @error('address.' . $address_row . 'firstname')
                                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Фамилия</label>
                                                                <input type="text" name="address[{{ $address_row }}][lastname]" class="form-control" placeholder="Фамилия"
                                                                       value="{{ !empty($a['lastname']) ? $a['lastname'] : old('address.' . $address_row . 'lastname') }}" required />
                                                                @error('address.' . $address_row . 'lastname')
                                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Компания</label>
                                                                <input type="text" name="address[{{ $address_row }}][company]" class="form-control" placeholder="Компания"
                                                                       value="{{ !empty($a['company']) ? $a['company'] : old('address.' . $address_row . 'company') }}" />
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Адрес</label>
                                                                <input type="text" name="address[{{ $address_row }}][address]" class="form-control" placeholder="Адрес"
                                                                       value="{{ !empty($a['address']) ? $a['address'] : old('address.' . $address_row . 'address') }}" required />
                                                                @error('address.' . $address_row . 'address')
                                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Квартира или офис</label>
                                                                <input type="text" name="address[{{ $address_row }}][address2]" class="form-control" placeholder="Квартира или офис"
                                                                       value="{{ !empty($a['address2']) ? $a['address2'] : old('address.' . $address_row . 'address2') }}" />
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Город</label>
                                                                <input type="text" name="address[{{ $address_row }}][city]" class="form-control" placeholder="Город"
                                                                       value="{{ !empty($a['city']) ? $a['city'] : old('address.' . $address_row . 'city') }}" required />
                                                                @error('address.' . $address_row . 'city')
                                                                <span class="invalid-feedback" role="alert" style="display: block"><strong>{{ $message }}</strong></span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label>Индекс</label>
                                                                <input type="text" name="address[{{ $address_row }}][postcode]" class="form-control" placeholder="Индекс"
                                                                       value="{{ !empty($a['postcode']) ? $a['postcode'] : old('address.' . $address_row . 'postcode') }}" />
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Адрес по умолчанию</label>
                                                            <select name="address[{{ $address_row }}][default]" class="form-control">
                                                                <option value="">Выбрать</option>
                                                                <option value="1"{{ (!empty($a['id']) && $a['id'] == $address_id) || old('address.' . $address_row . 'default') == 1 ? ' selected' : '' }}>Да</option>
                                                                <option value="0"{{ old('address.' . $address_row . 'default') == 0 || !old('address.' . $address_row . 'default') ? ' selected' : '' }}>Нет</option>
                                                            </select>
                                                        </div>
                                                        <?php $address_row++; ?>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="order_tab" role="tabpanel" aria-labelledby="order">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Номер заказа</th>
                                                <th>Итого</th>
                                                <th>Дата заказа</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(!empty($get_orders))
                                                @foreach($get_orders as $order)
                                                    <tr>
                                                        <td><a href="{{ url('admin/order/' . $order['id']) }}">{{ $order['id'] }}</a></td>
                                                        <td>{{ format_price($order['total'], $currency) }}</td>
                                                        <td>{{ date('Y-m-d', \strtotime($order['created_at'])) }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="3">Нет данных</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane" id="reward_tab" role="tabpanel" aria-labelledby="reward">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Дата добавления</th>
                                                <th>Описание</th>
                                                <th>Бонусы</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(!empty($get_rewards))
                                                @foreach($get_rewards as $reward)
                                                    <tr>
                                                        <td>{{ date('Y-m-d', \strtotime($reward['created_at'])) }}</td>
                                                        <td>{{ $reward['description'] }}</td>
                                                        <td>{{ $reward['points'] }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="3">Нет данных</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane" id="ip_tab" role="tabpanel" aria-labelledby="ip">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Ip</th>
                                                <th>Дата добавления</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(!empty($get_ip))
                                                @foreach($get_ip as $ip)
                                                    <tr>
                                                        <td><a href="http://ipgeobase.ru/?address={{ $ip['ip'] }}">{{ $ip['ip'] }}</a></td>
                                                        <td>{{ date('Y-m-d', \strtotime($order['created_at'])) }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="2">Нет данных</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
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
        var address_row = {{ $address_row }};

        function addAddress() {
            var row = (address_row === 0 ? 1 : address_row);
            
            var html = '<div class="tab-pane' + (row === 1 ? ' active' : '') + '" id="address-' + row + '_tab" role="tabpanel" aria-labelledby="address-' + row + '">' +
                '<div class="form-group">' +
                '  <div class="controls">' +
                '    <label>Имя</label>' +
                '    <input type="text" name="address[' + row + '][firstname]" class="form-control" placeholder="Имя" required />' +
                '  </div>' +
                '</div>' +
                '<div class="form-group">' +
                '  <div class="controls">' +
                '    <label>Фамилия</label>' +
                '    <input type="text" name="address[' + row + '][lastname]" class="form-control" placeholder="Фамилия" required />' +
                '  </div>' +
                '</div>' +
                '<div class="form-group">' +
                '  <div class="controls">' +
                '    <label>Компания</label>' +
                '    <input type="text" name="address[' + row + '][company]" class="form-control" placeholder="Компания" />' +
                '  </div>' +
                '</div>' +
                '<div class="form-group">' +
                '  <div class="controls">' +
                '    <label>Адрес</label>' +
                '    <input type="text" name="address[' + row + '][address]" class="form-control" placeholder="Адрес" />' +
                '  </div>' +
                '</div>' +
                '<div class="form-group">' +
                '  <div class="controls">' +
                '    <label>Город</label>' +
                '    <input type="text" name="address[' + row + '][city]" class="form-control" placeholder="Город" required />' +
                '  </div>' +
                '</div>' +
                '<div class="form-group">' +
                '  <div class="controls">' +
                '    <label>Индекс</label>' +
                '    <input type="text" name="address[' + row + '][postcode]" class="form-control" placeholder="Индекс" />' +
                '  </div>' +
                '</div>' +
                '<div class="form-group">' +
                '  <label>Адрес по умолчанию</label>' +
                '  <select name="address[' + row + '][default]" class="form-control">' +
                '    <option value="">Выбрать</option>' +
                '    <option value="1">Да</option>' +
                '    <option value="0">Нет</option>' +
                '  </select>' +
                '</div>' +
                '</div>';

            $('.lists').append('<li class="nav-item"><a class="nav-link' + (row === 1 ? ' active' : '') + '" id="address-' + row + '" data-toggle="tab" href="#address-' + row + '_tab" role="tab" aria-controls="address-' + row + '_tab" aria-selected="true"> Адрес #' + row + '</a></li>');

            $('.lists_address').append(html);

            address_row++;
        }
    </script>
@endsection