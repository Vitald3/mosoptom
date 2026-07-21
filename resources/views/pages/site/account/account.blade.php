@extends('layouts.base')
@section('meta')
    <title>{{ $meta_title }}</title>
    @if($meta_description)
        <meta name="description" content="{{ $meta_description }}" />
    @endif
    @if($meta_keywords)
        <meta name="keywords" content="{{ $meta_keywords }}" />
    @endif
@endsection
@section('page-styles')
    <link rel="stylesheet" href="{{ asset('assets/site/css/account/account.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/site/css/account/account_str.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/site/css/media/account.css') }}" />
@endsection
@section('content')
    <section id="success" class="container">
        <div class="account_container flex-2">
            <div class="account_left">
                @include('pages.site.account.menu')
            </div>
            <form action="{{ route('account_save') }}" method="post" enctype="multipart/form-data" class="validate_js account_right" novalidate>
                <h1>{{ $title }}</h1>
                <div class="h2 mt30">{{ __('locale.text_account_2') }}</div>
                <div class="over">
                    <ul class="tabs">
                        <li>
                            <a href="#fisical" class="link-tab relative{{ !$customer['type'] ? ' active' : '' }}">
                                {{ __('locale.text_account_3') }}
                                <input type="radio" name="type" value="0" class="hid_radio"{{ !$customer['type'] ? ' checked' : '' }} />
                            </a>
                        </li>
                        <li>
                            <a href="#uri" class="link-tab relative{{ $customer['type'] == 1 ? ' active' : '' }}">
                                {{ __('locale.text_account_4') }}
                                <input type="radio" name="type" value="1" class="hid_radio"{{ $customer['type'] == 1 ? ' checked' : '' }} />
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane{{ !$customer['type'] ? ' active' : '' }}" id="fisical">
                        <p>{{ __('locale.text_account_1') }}</p>
                        <div class="h2 mt50">{{ __('locale.text_account_h2') }}</div>
                        <div class="col-12 flex-2 col-input-6">
                            <div class="form_group">
                                <input type="text" name="firstname" value="{{ $customer['firstname'] }}" id="account_firstname" class="input" required />
                                <label for="account_firstname" class="required">{{ __('locale.text_write_name') }}</label>
                            </div>
                            <div class="form_group">
                                <input type="text" name="lastname" value="{{ $customer['lastname'] }}" id="account_lastname" class="input" required />
                                <label for="account_lastname" class="required">{{ __('locale.text_write_lastname') }}</label>
                            </div>
                        </div>
                        <div class="col-12 flex-2 col-input-6">
                            <div class="form_group mb0{{ $customer['phone'] ? ' active' : '' }}">
                                <input type="tel" name="phone" value="{{ $customer['phone'] }}" data-mask="{{ $languages[$lang]->mask }}" id="account_phone" class="input tel" required />
                                <label for="account_phone" class="required">{{ __('locale.text_write_phone') }}</label>
                            </div>
                            <div class="form_group mb0">
                                <input type="email" name="email" value="{{ $customer['email'] }}" id="account_email" class="input" required />
                                <label for="account_email" class="required">{{ __('locale.text_write_email') }}</label>
                            </div>
                        </div>
                        <div class="h2 mt50 mb20">{{ __('locale.text_account_6') }}</div>
                        <p class="mb30">{{ __('locale.text_account_7') }}</p>
                        <div class="flex-2 flex-start wrap soc_merge">
                            <label class="facebook{{ isset($socials['facebook']) ? ' active' : '' }}">
                                <input type="hidden" name="social[facebook]" value="{{ !empty($socials['facebook']['text']) ? $socials['facebook']['text'] : '' }}" />
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M15 6.875H11.25V4.375C11.25 3.685 11.81 3.125 12.5 3.125H13.75V0H11.25C9.17875 0 7.5 1.67875 7.5 3.75V6.875H5V10H7.5V20H11.25V10H13.75L15 6.875Z" fill="#1976D2"/>
                                </svg>
                                <span>Facebook</span>
                            </label>
                            <label class="google{{ isset($socials['google']) ? ' active' : '' }}">
                                <input type="hidden" name="social[google]" value="{{ !empty($socials['google']['text']) ? $socials['google']['text'] : '' }}" />
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M9.5 12.4H14.8751C14.0904 14.6116 11.9776 16.2 9.5 16.2C6.3574 16.2 3.8 13.6426 3.8 10.5C3.8 7.3574 6.3574 4.8 9.5 4.8C10.8623 4.8 12.1733 5.2883 13.1917 6.1756L15.6883 3.3104C13.9783 1.8208 11.7819 1 9.5 1C4.2617 1 0 5.2617 0 10.5C0 15.7383 4.2617 20 9.5 20C14.7383 20 19 15.7383 19 10.5V8.6H9.5V12.4Z" fill="#F44336"/>
                                </svg>
                                <span>Google</span>
                            </label>
                            <label class="vk{{ isset($socials['vk']) ? ' active' : '' }}">
                                <input type="hidden" name="social[vk]" value="{{ !empty($socials['vk']['text']) ? $socials['vk']['text'] : '' }}" />
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none">
                                    <g clip-path="url(#clip0_432_74845)">
                                        <path d="M12.2325 19.5715H13.7264C13.7264 19.5715 14.178 19.5215 14.4077 19.273C14.6202 19.0449 14.6124 18.6167 14.6124 18.6167C14.6124 18.6167 14.5827 16.6119 15.514 16.3166C16.4312 16.0259 17.6095 18.2542 18.858 19.1121C19.8018 19.7606 20.519 19.6184 20.519 19.6184L23.8583 19.5715C23.8583 19.5715 25.6054 19.4637 24.7772 18.0901C24.71 17.9776 24.2943 17.0744 22.2942 15.2181C20.2003 13.2742 20.4815 13.5898 23.0036 10.2286C24.5396 8.18159 25.1538 6.9315 24.9616 6.39708C24.7787 5.88767 23.649 6.02206 23.649 6.02206L19.8924 6.04393C19.8924 6.04393 19.6143 6.00643 19.4065 6.12988C19.2049 6.25176 19.0752 6.53303 19.0752 6.53303C19.0752 6.53303 18.4798 8.11752 17.686 9.4645C16.0125 12.3069 15.3421 12.4569 15.0686 12.2803C14.4327 11.8694 14.592 10.6271 14.592 9.74577C14.592 6.99088 15.0093 5.84236 13.7779 5.54546C13.3685 5.44701 13.0685 5.38138 12.0231 5.37045C10.6824 5.35638 9.54636 5.37513 8.90413 5.68922C8.47597 5.89861 8.14626 6.36583 8.34784 6.3924C8.59629 6.42521 9.15883 6.54397 9.45729 6.95025C9.84326 7.47373 9.82919 8.65194 9.82919 8.65194C9.82919 8.65194 10.0511 11.8944 9.31197 12.2975C8.80412 12.5741 8.10876 12.01 6.61646 9.43012C5.85234 8.10971 5.27417 6.64867 5.27417 6.64867C5.27417 6.64867 5.16323 6.37677 4.96478 6.23145C4.72413 6.05487 4.38661 5.99862 4.38661 5.99862L0.814471 6.02049C0.814471 6.02049 0.278494 6.03612 0.0816048 6.26895C-0.093408 6.47678 0.0675413 6.90493 0.0675413 6.90493C0.0675413 6.90493 2.86462 13.4476 6.03048 16.7463C8.93538 19.77 12.2325 19.5715 12.2325 19.5715Z" fill="#1E88E5"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_432_74845">
                                            <rect width="25" height="25" fill="#1E88E5"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                                <span>вКонтакте</span>
                            </label>
                            <label class="yandex{{ isset($socials['yandex']) ? ' active' : '' }}">
                                <input type="hidden" name="social[yandex]" value="{{ !empty($socials['yandex']['text']) ? $socials['yandex']['text'] : '' }}" />
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="16" viewBox="0 0 14 16" fill="none">
                                    <path d="M13.6562 16V0H6.75C3.19531 0 0.8125 2.03125 0.8125 5.4375C0.8125 7.65625 1.82812 9.21094 3.53125 10.0312L0.28125 16H5L7.8125 10.7188H9.3125V16H13.6562ZM9.3125 7.34375H7.78125C6.25781 7.34375 5.34375 6.80469 5.34375 5.4375C5.34375 4.0625 6.25781 3.46875 7.78125 3.46875H9.3125V7.34375Z" fill="#EB5757"/>
                                </svg>
                                <span>Яндекс</span>
                            </label>
                        </div>
                    </div>
                    <div class="tab-pane{{ $customer['type'] == 1 ? ' active' : '' }}" id="uri">
                        <div class="h2 mt50 mb20">{{ __('locale.text_account_17') }}</div>
                        <div class="flex mb20">
                            <div class="form_group mb0 col-6 col-xs-12">
                                <input type="text" name="legal[firstname]" value="{{ !empty($customer['legal']['firstname']) ? $customer['legal']['firstname'] : '' }}" id="legal_firstname" class="input" required />
                                <label for="legal_firstname" class="required">{{ __('locale.text_write_name') }}</label>
                            </div>
                            <a href="#" class="btn-default add_emails hid-xs">{{ __('locale.text_account_15') }}</a>
                        </div>
                        <div class="flex">
                            <div class="form_group mb0 col-6 col-xs-12">
                                <input type="text" name="legal[lastname]" value="{{ !empty($customer['legal']['lastname']) ? $customer['legal']['lastname'] : '' }}" id="legal_lastname" class="input" required />
                                <label for="legal_lastname" class="required">{{ __('locale.text_write_lastname') }}</label>
                            </div>
                            <a href="#" class="btn-default add_phones hid-xs">{{ __('locale.text_account_16') }}</a>
                        </div>
                        <div id="phones">
							<?php $phone_row = 0; ?>
                            @if(!empty($customer['phones']))
                                @foreach($customer['phones'] as $phones)
                                    <div class="flex-2 flex-start wrap mt20" id="phone_row{{ $phone_row }}">
                                        <div class="form_group mb0 mr40 col-6">
                                            <input type="tel" data-mask="{{ $languages[$lang]->mask }}" name="legal[phones][{{ $phone_row }}][phone]" value="{{ $phones['phone'] }}" id="phones{{ $phone_row }}" class="tel input" required />
                                            <label for="phones{{ $phone_row }}" class="required">{{ __('locale.text_write_phone') }}</label>
                                        </div>
                                        <div class="flex">
                                            <div class="custom_radio mt0">
                                                <input type="radio" value="{{ $phone_row }}" id="phone_default{{ $phone_row }}" name="legal[phone_default]"{{ $customer['phone'] == $phones['phone'] ? ' checked' : '' }} />
                                                <span></span>
                                            </div>
                                            <label for="phone_default{{ $phone_row }}">{{ __('locale.text_account_13') }}</label>
                                        </div>
                                    </div>
									<?php $phone_row++; ?>
                                @endforeach
                            @endif
                            @if(empty($customer['phones']))
                                <div class="flex-2 flex-start wrap mt20" id="phone_row{{ $phone_row }}">
                                    <div class="form_group mb0 col-6 mr40 col-xs-12">
                                        <input type="tel" data-mask="{{ $languages[$lang]->mask }}" name="legal[phones][{{ $phone_row }}][phone]" value="{{ $customer['phone'] }}" id="phones{{ $phone_row }}" class="tel input" required />
                                        <label for="phones{{ $phone_row }}" class="required">{{ __('locale.text_write_phone') }}</label>
                                    </div>
                                    <div class="flex flex-start col-xs-12">
                                        <div class="custom_radio mt0">
                                            <input type="radio" value="{{ $phone_row }}" id="phone_default{{ $phone_row }}" name="legal[phone_default]"{{ empty($customer['phones']) ? ' checked' : '' }} />
                                            <span></span>
                                        </div>
                                        <label for="phone_default{{ $phone_row }}">{{ __('locale.text_account_13') }}</label>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div id="emails">
							<?php $email_row = 0; ?>
                            @if(!empty($customer['emails']))
                                @foreach($customer['emails'] as $emails)
                                    <div class="flex-2 wrap flex-start mt20" id="email_row{{ $email_row }}">
                                        <div class="form_group mb0 mr40 col-6 col-xs-12">
                                            <input type="email" name="legal[emails][{{ $email_row }}][email]" value="{{ $emails['email'] }}" id="emails{{ $email_row }}" class="input" />
                                            <label for="emails{{ $email_row }}">{{ __('locale.text_write_email') }}</label>
                                        </div>
                                        <div class="flex flex-start col-xs-12">
                                            <div class="custom_radio mt0">
                                                <input type="radio" value="{{ $email_row }}" id="email_default{{ $email_row }}" name="legal[email_default]"{{ $customer['email'] == $emails['email'] ? ' checked' : '' }} />
                                                <span></span>
                                            </div>
                                            <label for="email_default{{ $email_row }}">{{ __('locale.text_account_14') }}</label>
                                        </div>
                                    </div>
									<?php $phone_row++; ?>
                                @endforeach
                            @endif
                            @if(empty($customer['emails']))
                                <div class="flex-2 wrap flex-start mt20" id="email_row{{ $email_row }}">
                                    <div class="form_group mb0 mr40 col-6 col-xs-12">
                                        <input type="email" name="legal[emails][{{ $email_row }}][email]" value="{{ $customer['email'] }}" id="emails{{ $email_row }}" class="input" />
                                        <label for="emails{{ $email_row }}">{{ __('locale.text_write_email') }}</label>
                                    </div>
                                    <div class="flex flex-start col-xs-12">
                                        <div class="custom_radio mt0">
                                            <input type="radio" value="{{ $email_row }}" id="email_default{{ $email_row }}" name="legal[email_default]"{{ empty($customer['emails']) ? ' checked' : '' }} />
                                            <span></span>
                                        </div>
                                        <label for="email_default{{ $email_row }}">{{ __('locale.text_account_14') }}</label>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <a href="#" class="btn-default add_phones hid-sm hid-lg hid-ms" style="margin: 15px 0">{{ __('locale.text_account_16') }}</a>
                        <a href="#" class="btn-default add_emails hid-sm hid-lg hid-ms">{{ __('locale.text_account_15') }}</a>
                        <div class="h2 mt50 mb20">{{ __('locale.text_account_18') }}</div>
                        <div class="col-12 flex-2 col-input-6">
                            <div class="form_group">
                                <input type="text" name="legal[inn]" value="{{ !empty($customer['legal']['inn']) ? $customer['legal']['inn'] : '' }}" id="legal_inn" class="input" required />
                                <label for="legal_inn" class="required">{{ __('locale.text_register_v2') }}</label>
                            </div>
                            <div class="form_group">
                                <input type="text" name="legal[ogrn]" value="{{ !empty($customer['legal']['ogrn']) ? $customer['legal']['ogrn'] : '' }}" id="legal_ogrn" class="input" required />
                                <label for="legal_ogrn" class="required">{{ __('locale.text_register_v12') }}</label>
                            </div>
                        </div>
                        <div class="col-12 flex-2 col-input-6">
                            <div class="form_group">
                                <input type="text" name="legal[kpp]" value="{{ !empty($customer['legal']['kpp']) ? $customer['legal']['kpp'] : '' }}" id="legal_kpp" class="input" required />
                                <label for="legal_kpp" class="required">{{ __('locale.text_register_v5') }}</label>
                            </div>
                            <div class="form_group">
                                <input type="text" name="legal[company]" value="{{ !empty($customer['legal']['company']) ? $customer['legal']['company'] : '' }}" id="legal_company" class="input" required />
                                <label for="legal_company" class="required">{{ __('locale.text_account_19') }}</label>
                            </div>
                        </div>
                        <div class="col-12 flex-2 col-input-6">
                            <div class="form_group">
                                <select name="legal[kontragent]" class="selectize" id="legal_kontragent" required>
                                    <option value="1"{{ !empty($customer['legal']['kontragent']) && $customer['legal']['kontragent'] == 1 ? ' selected' : '' }}>{{ __('locale.text_register_v6') }}</option>
                                    <option value="2"{{ !empty($customer['legal']['kontragent']) && $customer['legal']['kontragent'] == 2 ? ' selected' : '' }}>{{ __('locale.text_register_v7') }}</option>
                                </select>
                                <label class="required" for="legal_kontragent">{{ __('locale.text_register_v8') }}</label>
                            </div>
                            <div class="form_group">
                                <select name="legal[forma_sobstvennosti]" class="selectize" id="legal_forma_sobstvennosti" required>
                                    <option value="1"{{ !empty($customer['legal']['forma_sobstvennosti']) && $customer['legal']['forma_sobstvennosti'] == 1 ? ' selected' : '' }}>ООО</option>
                                    <option value="2"{{ !empty($customer['legal']['forma_sobstvennosti']) && $customer['legal']['forma_sobstvennosti'] == 2 ? ' selected' : '' }}>ОАО</option>
                                    <option value="3"{{ !empty($customer['legal']['forma_sobstvennosti']) && $customer['legal']['forma_sobstvennosti'] == 3 ? ' selected' : '' }}>ПАО</option>
                                    <option value="4"{{ !empty($customer['legal']['forma_sobstvennosti']) && $customer['legal']['forma_sobstvennosti'] == 4 ? ' selected' : '' }}>ЗАО</option>
                                    <option value="5"{{ !empty($customer['legal']['forma_sobstvennosti']) && $customer['legal']['forma_sobstvennosti'] == 5 ? ' selected' : '' }}>АО</option>
                                    <option value="6"{{ !empty($customer['legal']['forma_sobstvennosti']) && $customer['legal']['forma_sobstvennosti'] == 6 ? ' selected' : '' }}>ИП</option>
                                </select>
                                <label class="required" for="legal_forma_sobstvennosti">{{ __('locale.text_register_v9') }}</label>
                            </div>
                        </div>
                        <div class="form_group">
                            <input type="text" name="legal[address]" value="{{ !empty($customer['legal']['address']) ? $customer['legal']['address'] : '' }}" id="legal_address" class="input" required />
                            <label for="legal_address" class="required">{{ __('locale.text_register_v10') }}</label>
                        </div>
                        <div class="form_group">
                            <input type="text" name="legal[address2]" value="{{ !empty($customer['legal']['address2']) ? $customer['legal']['address2'] : '' }}" id="legal_address2" class="input" required />
                            <label for="legal_address2" class="required">{{ __('locale.text_register_v11') }}</label>
                        </div>
                    </div>
                    <div class="h2 mt50">{{ __('locale.text_account_addresses') }}</div>
                    <div id="addresses">
						<?php $row = 0; ?>
                        @if(!empty($customer['address']))
                            @foreach($customer['address'] as $address)
                                <div id="address_row{{ $row }}">
                                    <div class="form_group">
                                        <input type="hidden" name="address[{{ $row }}][id]" value="{{ $address['id'] }}" />
                                        <input type="text" name="address[{{ $row }}][address]" value="{{ $address['address'] }}" id="account_address{{ $row }}" class="input" required />
                                        <label for="account_address{{ $row }}" class="required">{{ __('locale.text_account_address') }}</label>
                                    </div>
                                    <div class="flex-2 flex-start">
                                        <div class="form_group mb0 kvw" style="margin-right: 30px">
                                            <input type="text" name="address[{{ $row }}][address2]" value="{{ $address['address2'] }}" id="account_address2{{ $row }}" class="input" />
                                            <label for="account_address2{{ $row }}">{{ __('locale.text_account_address2') }}</label>
                                        </div>
                                        <div class="flex">
                                            <div class="custom_radio mt0">
                                                <input type="radio" value="{{ $row }}" id="account_default_address{{ $row }}" name="default"{{ $address_id == $address['id'] ? ' checked' : '' }} />
                                                <span></span>
                                            </div>
                                            <label for="account_default_address{{ $row }}">{{ __('locale.text_account_default_address') }}</label>
                                        </div>
                                    </div>
                                </div>
								<?php $row++; ?>
                            @endforeach
                        @endif
                    </div>
                    <a href="#" class="btn-default float-right add_address">{{ __('locale.text_account_5') }}</a>
                    <div class="h2 mt50 mb20">{{ __('locale.text_account_8') }}</div>
                    <div class="col-12 flex-2 col-input-6">
                        <div class="form_group mb0">
                            <input type="password" name="password" id="account_password" class="input" />
                            <label for="account_password">{{ __('locale.text_account_9') }}</label>
                        </div>
                        <div class="form_group mb0">
                            <input type="text" name="confirm" id="account_confirm" class="input" />
                            <label for="account_confirm">{{ __('locale.text_account_10') }}</label>
                        </div>
                    </div>
                    <input type="submit" value="{{ __('locale.text_account_11') }}" class="btn-default mt50" />
                </div>
            </form>
        </div>
    </section>
@endsection

@section('page-scripts')
    <script src="https://vk.com/js/api/openapi.js" async></script>
    <script>
        $(document).ready(function(){
            var row = {{ $row }};
            var email_row = {{ $email_row }} + 1;
            var phone_row = {{ $phone_row }} + 1;

            $('.add_address').on('click', function(){
                var html =  '<div id="address_row' + row + '">';
                html +=     '    <div class="form_group">';
                html +=     '        <input type="hidden" name="address[' + row + '][id]" />';
                html +=     '        <input type="text" name="address[' + row + '][address]" id="account_address' + row + '" class="input" required />';
                html +=     '        <label for="account_address' + row + '" class="required">{{ __('locale.text_account_address') }}</label>';
                html +=     '    </div>';
                html +=     '    <div class="flex-2 flex-start">';
                html +=     '        <div class="form_group mb0 kvw" style="margin-right: 30px">';
                html +=     '            <input type="text" name="address[' + row + '][address2]" id="account_address2' + row + '" class="input" />';
                html +=     '            <label for="account_address2' + row + '">{{ __('locale.text_account_address2') }}</label>';
                html +=     '        </div>';
                html +=     '        <div class="flex">';
                html +=     '        <div class="custom_radio mt0">';
                html +=     '            <input type="radio" value="' + row + '" id="account_default_address' + row + '" name="default" />';
                html +=     '            <span></span>';
                html +=     '        </div>';
                html +=     '        <label for="account_default_address' + row + '">{{ __('locale.text_account_default_address') }}</label>';
                html +=     '    </div>';
                html +=     '</div>';

                $('#addresses').append(html);
                row++;

                return false;
            });

            $('.add_emails').on('click', function(){
                var html = '<div class="flex-2 wrap flex-start mt20" id="email_row' + email_row + '">\n' +
                    '                                    <div class="form_group mb0 mr40 col-6 col-xs-12">\n' +
                    '                                        <input type="email" name="legal[emails][' + email_row + '][email]" id="emails' + email_row + '" class="input" />\n' +
                    '                                        <label for="emails' + email_row + '">{{ __('locale.text_write_email') }}</label>\n' +
                    '                                    </div>\n' +
                    '                                    <div class="flex flex-start col-xs-12">\n' +
                    '                                        <div class="custom_radio mt0">\n' +
                    '                                            <input type="radio" value="' + email_row + '" id="email_default' + email_row + '" name="legal[email_default]" />\n' +
                    '                                            <span></span>\n' +
                    '                                        </div>\n' +
                    '                                        <label for="email_default' + email_row + '">{{ __('locale.text_account_14') }}</label>\n' +
                    '                                    </div>\n' +
                    '                                </div>';

                $('#emails').append(html);
                email_row++;

                return false;
            });

            $('.add_phones').on('click', function(){
                var html = '<div class="flex-2 wrap flex-start mt20" id="phone_row' + phone_row + '">\n' +
                    '                                <div class="form_group mb0 col-6 mr40 col-xs-12">\n' +
                    '                                    <input type="tel" data-mask="{{ $languages[$lang]->mask }}" name="legal[phones][' + phone_row + '][phone]" id="phones' + phone_row + '" class="tel input" required />\n' +
                    '                                    <label for="phones' + phone_row + '" class="required">{{ __('locale.text_write_phone') }}</label>\n' +
                    '                                </div>\n' +
                    '                                <div class="flex flex-start col-xs-12">\n' +
                    '                                    <div class="custom_radio mt0">\n' +
                    '                                        <input type="radio" value="' + phone_row + '" id="phone_default' + phone_row + '" name="legal[phone_default]" />\n' +
                    '                                        <span></span>\n' +
                    '                                    </div>\n' +
                    '                                    <label for="phone_default' + phone_row + '">{{ __('locale.text_account_13') }}</label>\n' +
                    '                                </div>\n' +
                    '                            </div>';

                $('#phones').append(html);
                phone_row++;

                $('[type="tel"]').each(function () {
                    $(this).mask($(this).attr('data-mask'));
                });

                return false;
            });

            $('.vk').on('click', function(){
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                    $(this).find('input').val('');
                } else {
                    vk.init();
                }
            });

            $('.facebook').on('click', function(){
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                    $(this).find('input').val('');
                } else {
                    FB.getLoginStatus(function (response) {
                        if (response.authResponse) {
                            handle_fb_data(response);
                        } else {
                            FB.login(function (response) {
                                if (response.status === 'connected') {
                                    handle_fb_data(response);
                                }
                            }, {scope: 'email'});
                        }
                    });
                }
            });

            $('.yandex').on('click', function(){
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                    $(this).find('input').val('');
                } else {
                    window.open('{!! $yandex_link !!}', '', 'popup');
                }
            });

            $('.google').on('click', function(){
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                    $(this).find('input').val('');
                } else {
                    auth2 = gapi.auth2.init({
                        client_id: '303981676378-9ruds11bmeuq60mffbhueusf30muup73.apps.googleusercontent.com',
                        scope: 'https://www.googleapis.com/auth/userinfo.email',
                        cookiepolicy: 'single_host_origin'
                    });

                    auth2.attachClickHandler(this, {}, function (googleUser) {
                        onSignIn(googleUser);
                        window.onSignIn = onSignIn
                    });
                }
            });
        });

        function handle_fb_data(response){
            FB.api('/me?fields=id,email,name', function(response) {
                $('.facebook input[type="hidden"]').val(JSON.stringify(response));
                $('.facebook').addClass('active');
            });
        }

        window.fbAsyncInit = function() {
            FB.init({
                appId      : '977168536565227',
                cookie     : true,
                xfbml      : true,
                version    : 'v2.8'
            });
        };

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        var auth2;

        function init_google() {
            gapi.load('auth2', function() {
                auth2 = gapi.auth2.init({
                    client_id: '303981676378-9ruds11bmeuq60mffbhueusf30muup73.apps.googleusercontent.com',
                    scope: 'https://www.googleapis.com/auth/userinfo.email',
                    cookiepolicy: 'single_host_origin'
                });

                //$('.google').trigger('click');
            });
        }

        function onSignIn(googleUser) {
            var profile = googleUser.getBasicProfile();

            $('.google input[type="hidden"]').val(JSON.stringify(profile));
            $('.google').addClass('active');
        }

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "https://apis.google.com/js/platform.js?onload=init_google";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'google-jssdk'));

        function yandex_logged(text) {
            $('.yandex input[type="hidden"]').val(text);
            $('.yandex').addClass('active');
        }

        var vk = {
            data: {},
            api: "//vk.com/js/api/openapi.js",
            appID: 8019998,
            appPermissions: 'RxKhohVKEvOoCyw3kyYG',
            init: function(){
                VK.init({apiId: vk.appID});
                load();

                function load(){
                    VK.Auth.login(authInfo, vk.appPermissions);

                    function authInfo(response){
                        if (response.session){
                            vk.data.user = response.session.user;
                            $('.vk input[type="hidden"]').val(JSON.stringify(vk.data.user));
                            $('.vk').addClass('active');
                        }
                    }
                }
            }
        }
    </script>
@endsection