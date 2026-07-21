@extends('layouts.base')
@section('meta')
    <title>{{ $meta_title }}</title>
@endsection
@section('page-styles')
    <link rel="stylesheet" href="{{ asset('assets/site/css/account/account.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/site/css/account/account_str.css') }}" />
@endsection
@section('content')
    <section id="content" class="container">
        <div class="account_container">
            <h1 class="title">{{ $title }}</h1>
            @if($warning)
                <div class="alert alert-danger" style="margin: 15px 0">{{ $warning }}</div>
            @else
                <div class="relative">
                    @if(!session('customer_id'))
                        <p>{!! __('locale.text_checkout_1') !!}</p>
                    @endif
                    <div class="totals hid-sm hid-lg hid-ms">
                        <div class="h2_green" style="color: #484848">{{ __('locale.text_checkout_4') }}</div>
                        <div id="totals2">
                            @foreach($totals as $total)
                                <div id="{{ $total['code'] }}" class="flex-2">
                                    <span>{{ $total['title'] }}</span>
                                    <span class="fbold">{{ $total['value'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <form action="{{ route('checkout_save') }}" method="post" enctype="multipart/form-data" class="validate_js flex wrap" novalidate>
                        <div class="checkout_left">
                            <div class="h2 mt30">{{ __('locale.text_checkout_2') }}</div>
                            <ul class="tabs">
                                <li>
                                    <a href="#fisical" class="link-tab relative{{ !isset($customer['type']) || (isset($customer['type']) && $customer['type'] == 0) ? ' active' : '' }}">
                                        <span class="hid-xs">{{ __('locale.text_account_3') }}</span>
                                        <span class="hid-sm hid-lg hid-ms">{{ __('locale.text_mob_checkout_1') }}</span>
                                        <input type="radio" name="type" value="0" class="hid_radio"{{ !isset($customer['type']) ? ' checked' : '' }} />
                                    </a>
                                </li>
                                <li>
                                    <a href="#uri" class="link-tab relative{{ isset($customer['type']) && $customer['type'] == 1 ? ' active' : '' }}">
                                        <span class="hid-xs">{{ __('locale.text_account_4') }}</span>
                                        <span class="hid-sm hid-lg hid-ms">{{ __('locale.text_mob_checkout_2') }}</span>
                                        <input type="radio" name="type" value="1" class="hid_radio"{{ isset($customer['type']) && $customer['type'] == 1 ? ' checked' : '' }} />
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane{{ !isset($customer['type']) || (isset($customer['type']) && $customer['type'] == 0) ? ' active' : '' }}" id="fisical">
                                    <div class="col-12 flex-2 col-input-6">
                                        <div class="form_group">
                                            <input type="text" name="firstname" value="{{ old('firstname', !empty($customer['firstname']) ? $customer['firstname'] : '') }}" id="account_firstname" class="input" required />
                                            <label for="account_firstname" class="required">{{ __('locale.text_write_name') }}</label>
                                        </div>
                                        <div class="form_group">
                                            <input type="text" name="lastname" value="{{ old('lastname', !empty($customer['lastname']) ? $customer['lastname'] : '') }}" id="account_lastname" class="input" required />
                                            <label for="account_lastname" class="required">{{ __('locale.text_write_lastname') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-12 flex-2 col-input-6">
                                        <div class="form_group checkmb mb0{{ !empty($customer['phone']) ? ' active' : '' }}">
                                            <input type="tel" name="phone" value="{{ old('phone', !empty($customer['phone']) ? $customer['phone'] : '') }}" data-mask="{{ $languages[$lang]->mask }}" id="account_phone" class="input tel" required />
                                            <label for="account_phone" class="required">{{ __('locale.text_write_phone') }}</label>
                                        </div>
                                        <div class="form_group mb0">
                                            <input type="email" name="email" value="{{ old('email', !empty($customer['email']) ? $customer['email'] : '') }}" id="account_email" class="input" required />
                                            <label for="account_email" class="required">{{ __('locale.text_write_email') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane{{ isset($customer['type']) && $customer['type'] == 1 ? ' active' : '' }}" id="uri">
                                    <div class="flex-2">
                                        <div class="colinput flex-2 column">
                                            <div class="form_group">
                                                <input type="text" name="legal[inn]" value="{{ old('legal.inn', !empty($customer['legal']['inn']) ? $customer['legal']['inn'] : '') }}" id="legal_inn" class="input" required />
                                                <label for="legal_inn" class="required">{{ __('locale.text_register_v2') }}</label>
                                            </div>
                                            <div class="form_group">
                                                <input type="text" name="legal[firstname]" value="{{ old('legal.firstname', !empty($customer['legal']['firstname']) ? $customer['legal']['firstname'] : '') }}" id="legal_firstname" class="input" required />
                                                <label for="legal_firstname" class="required">{{ __('locale.text_write_name') }}</label>
                                            </div>
                                            <div class="form_group">
                                                <input type="text" name="legal[lastname]" value="{{ old('legal.lastname', !empty($customer['legal']['lastname']) ? $customer['legal']['lastname'] : '') }}" id="legal_lastname" class="input" required />
                                                <label for="legal_lastname" class="required">{{ __('locale.text_write_lastname') }}</label>
                                            </div>
                                        </div>
                                        <div class="colinput flex-2 column">
                                            <div class="form_group">
                                                <input type="text" name="legal[company]" value="{{ old('legal.firstname', !empty($customer['legal']['company']) ? $customer['legal']['company'] : '') }}" id="legal_company" class="input" required />
                                                <label for="legal_company" class="required">{{ __('locale.text_account_19') }}</label>
                                            </div>
                                            <div class="form_group{{ !empty($customer['phone']) ? ' active' : '' }}">
                                                <input type="tel" name="legal[phone]" value="{{ old('legal.phone', !empty($customer['legal']['phone']) ? $customer['legal']['phone'] : '') }}" data-mask="{{ $languages[$lang]->mask }}" id="legal_phone" class="input tel" required />
                                                <label for="legal_phone" class="required">{{ __('locale.text_write_phone') }}</label>
                                            </div>
                                            <div class="form_group">
                                                <input type="email" name="legal[email]" value="{{ old('legal.email', !empty($customer['legal']['email']) ? $customer['legal']['email'] : '') }}" id="legal_email" class="input" required />
                                                <label for="legal_email" class="required">{{ __('locale.text_write_email') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="h2 mt30">{{ __('locale.text_checkout_5') }}</div>
                            @if($shipping_methods)
                                <div class="over">
                                    <ul class="tabs">
										<?php $key = 0; ?>
                                        @foreach($shipping_methods as $shipping_method)
                                            <li>
                                                <a href="#{{ $shipping_method['code'] }}" class="link-tab relative{{ ($key == 0 && !$shipping_method_) || $shipping_method_ == $shipping_method['code'] ? ' active' : '' }}">
                                                    {{ $shipping_method['title'] }}
                                                    <input type="radio" onchange="set_shipping();" name="shipping_method" value="{{ $shipping_method['code'] }}" class="hid_radio"{{ ($key == 0 && !$shipping_method_) || $shipping_method_ == $shipping_method['code'] ? ' checked' : '' }} />
                                                </a>
                                            </li>
											<?php $key++; ?>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="tab-content">
									<?php $key = 0; ?>
                                    @foreach($shipping_methods as $shipping_method)
                                        @if($shipping_method['html'])
                                            <div class="tab-pane{{ ($key == 0 && !$shipping_method_) || $shipping_method_ == $shipping_method['code'] ? ' active' : '' }}" id="{{ $shipping_method['code'] }}">
                                                {!! $shipping_method['html'] !!}
                                            </div>
                                        @endif
										<?php $key++; ?>
                                    @endforeach
                                </div>
                            @endif
                            @if($payment_methods)
                                <div class="h2 mt30">{{ __('locale.text_checkout_20') }}</div>
                                <div class="block_oplat">
									<?php $key = 0; ?>
                                    @foreach($payment_methods as $payment_method)
                                        <div class="flex flex-start check">
                                            <div class="custom_radio mt0">
                                                <input id="{{ $payment_method['code'] }}" onchange="set_payment(this.value);" type="radio" name="payment_method" value="{{ $payment_method['code'] }}"{{ ($key == 0 && !$payment_method_) || $payment_method_ == $payment_method['code'] ? ' checked' : '' }}>
                                                <span></span>
                                            </div>
                                            <div class="labe_cart{{ ($key == 0 && !$payment_method_) || $payment_method_ == $payment_method['code'] ? ' label_active' : '' }}">
                                                <label for="{{ $payment_method['code'] }}">{{ $payment_method['title'] }}</label>
                                                @if($payment_method['text'])
                                                    <p>{{ $payment_method['text'] }}</p>
                                                @endif
                                            </div>
                                        </div>
										<?php $key++; ?>
                                    @endforeach
                                </div>
                            @endif
                            {!! $action !!}
                        </div>
                        <div class="checkout_right">
                            <div class="totals">
                                <div class="h2_green">{{ __('locale.text_checkout_4') }}</div>
                                <div id="totals">
                                    @foreach($totals as $total)
                                        <div id="{{ $total['code'] }}" class="flex-2">
                                            <span>{{ $total['title'] }}</span>
                                            <span class="fbold">{{ $total['value'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="submit" class="btn-default mb20 text-center mt30 col-12" value="{{ __('locale.text_checkout_button') }}" />
                                <div class="agree">{!! sprintf(__('locale.text_checkout_3'), $soglashenie_link, $oferta_link, $policy) !!}</div>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </section>
@endsection

@section('page-scripts')
    <script>
        $(document).on('change', '.custom_radio input', function(){
            $('.labe_cart').removeClass('label_active');
            $(this).parent().next().addClass('label_active');
        });

        function set_shipping() {
            var val = $('input[name="shipping_method"]:checked').val();
            var quote = $('select[name="' + val + '[' + val + ']"]');

            $.ajax({
                url: '{{ route('checkout_shipping') }}',
                type: 'POST',
                dataType: 'json',
                data: 'shipping_method=' + val + (quote.length ? '&quote=' + quote.val() : '') + '&_token={{ csrf_token() }}',
                success: function(json) {
                    if (json.error) {
                        alert(json.error);
                    } else if (json.totals) {
                        var html = '';

                        for (var i in json.totals) {
                            html += '<div id="' + json.totals[i]['code'] + '" class="flex-2"> <span>' + json.totals[i]['title'] + '</span> <span class="fbold">' + json.totals[i]['value'] + '</span></div>';
                        }

                        $('#totals').html(html);
                        $('#totals2').html(html);
                    }
                }
            });
        }

        function set_payment(val) {
            $.ajax({
                url: '{{ route('checkout_payment') }}',
                type: 'POST',
                dataType: 'json',
                data: 'payment_method=' + val + '&_token={{ csrf_token() }}',
                success: function(json) {
                    if (json.error) {
                        alert(json.error);
                    } else if (json.totals) {
                        var html = '';

                        for (var i in json.totals) {
                            html += '<div id="' + json.totals[i]['code'] + '" class="flex-2"> <span>' + json.totals[i]['title'] + '</span> <span class="fbold">' + json.totals[i]['value'] + '</span></div>';
                        }

                        $('#totals').html(html);
                        $('#totals2').html(html);
                    }
                }
            });
        }

        @if(!$warning)
        set_shipping();
        set_payment($('input[name="payment_method"]:checked').val());
        @endif
    </script>
@endsection