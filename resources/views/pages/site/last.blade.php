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

@section('content')
    <section class="container" id="content">
        <div class="flex wrap flex-start relative" style="margin-bottom: 20px">
            <h1 class="h1">{{ $title }}</h1>
            @if(!empty($products))
                <span class="total_cat">{{ num_decline($products->total(), [__('locale.text_prod1'), __('locale.text_prod2'), __('locale.text_prod3')]) }}</span>
            @endif
            @if(!$filters && !empty($products))
                <div class="sorts green_theme" style="margin-left: auto">
                    <select name="sort" class="selectize" onchange="location=this.value;">
                        @foreach($sorts as $sort)
                            <option value="{{ $sort['url'] }}"{{ $sort['active'] ? ' selected' : '' }}>{{ $sort['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
        <div id="products" class="col-12">
            @if($filters)
                <div class="panels flex-2">
                    <div class="filter_left">
                        <form id="filters" class="filters">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <div class="params flex-2">
                                <div class="flex panel_filter">
                                    <span class="edit_xs hid-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
<g clip-path="url(#clip0_506_45030)">
<path d="M2.57958 5.99997C2.57958 5.7849 2.6617 5.56986 2.82559 5.4059L7.98537 0.24617C8.3136 -0.0820566 8.84576 -0.0820566 9.17385 0.24617C9.50195 0.574263 9.50195 1.10632 9.17385 1.43457L4.60819 5.99997L9.17369 10.5654C9.50179 10.8936 9.50179 11.4256 9.17369 11.7537C8.8456 12.0821 8.31344 12.0821 7.98521 11.7537L2.82543 6.59403C2.66151 6.42999 2.57958 6.21495 2.57958 5.99997Z" fill="#484848"/>
</g>
<defs>
<clipPath id="clip0_506_45030">
<rect width="12" height="12" fill="white" transform="translate(12) rotate(90)"/>
</clipPath>
</defs>
</svg>
                                        {{ __('locale.text_filters') }}
                                    </span>
                                    <span class="edit">{{ __('locale.text_filter_param') }}</span>
                                    <span class="edit cursor f500 select_filter pointer">
                                        {{ __('locale.text_filter_all') }}
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
<path d="M4.99997 7.84871C4.82075 7.84871 4.64155 7.78028 4.50492 7.64371L0.205141 3.3439C-0.0683805 3.07038 -0.0683805 2.62691 0.205141 2.3535C0.478552 2.08008 0.921933 2.08008 1.19548 2.3535L4.99997 3.33171L8.80449 2.35363C9.07801 2.08022 9.52135 2.08022 9.79474 2.35363C10.0684 2.62704 10.0684 3.07051 9.79474 3.34403L5.49503 7.64385C5.35832 7.78044 5.17913 7.84871 4.99997 7.84871Z" fill="#ACBDC0"/>
</svg>
                                    </span>
                                    <span class="active_filter">{{ $active_filter }}</span>
                                </div>
                                <div class="reset_filter flex cursor" style="display:{{ $select_filters || $price_range ? 'block' : 'none' }}">
                                    <span onclick="reset_filter();">{{ __('locale.text_filter_reset') }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none">
                                        <g clip-path="url(#clip0_506_47455)">
                                            <path d="M7.12167 12.9636C9.72946 12.6714 11.8194 10.5868 12.1168 7.97901C12.5072 4.56923 9.85693 1.66666 6.53479 1.62949V0.0998641C6.53479 0.0148851 6.42856 -0.03026 6.35686 0.0228519L3.20733 2.33587C3.15422 2.37571 3.15422 2.45272 3.20733 2.49255L6.35686 4.80557C6.42856 4.85869 6.53479 4.81088 6.53479 4.72856V3.2016C8.86905 3.23877 10.7386 5.22516 10.5793 7.59926C10.4438 9.64141 8.77876 11.2985 6.73661 11.4286C4.5723 11.5667 2.73198 10.0212 2.40268 7.97635C2.34161 7.5966 2.00966 7.32042 1.62725 7.32042C1.1519 7.32042 0.780117 7.74266 0.854474 8.2127C1.31655 11.1259 4.00666 13.3114 7.12167 12.9636Z" fill="#ACBDC0"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_506_47455">
                                                <rect width="13" height="13" fill="white"/>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </div>
                            </div>
                            <div class="hr"{!! $select_filters ? ' style="display: block"' : '' !!}></div>
                            <div id="selected_filters" class="flex flex-start wrap mb0">
                                @if($select_filters)
                                    @foreach($select_filters as $filter_id => $filter)
                                        <div class="flex flex-start wrap mb0">
                                            <div class="edit_h4">{{ $filter['name'] }}:</div>
                                            @foreach($filter['values'] as $value)
                                                <div class="top_filter mb0">
                                                <span onclick="delete_filter({{ $value['id'] }});">
                                                    {{ $value['name'] }}
                                                    <svg style="margin-left: 5px" xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
                                                        <path d="M5.73294 5.00779L8.84799 1.89266C9.05071 1.69003 9.05071 1.36241 8.84799 1.15978C8.64536 0.957156 8.31774 0.957156 8.11511 1.15978L4.99997 4.27492L1.88492 1.15978C1.68219 0.957156 1.35467 0.957156 1.15204 1.15978C0.949319 1.36241 0.949319 1.69003 1.15204 1.89266L4.26709 5.00779L1.15204 8.12293C0.949319 8.32555 0.949319 8.65317 1.15204 8.8558C1.25302 8.95688 1.3858 9.00765 1.51848 9.00765C1.65116 9.00765 1.78384 8.95688 1.88492 8.8558L4.99997 5.74066L8.11511 8.8558C8.21619 8.95688 8.34887 9.00765 8.48155 9.00765C8.61423 9.00765 8.74691 8.95688 8.84799 8.8558C9.05071 8.65317 9.05071 8.32555 8.84799 8.12293L5.73294 5.00779Z" fill="#BED0D6" stroke="#BED0D6"/>
                                                    </svg>
                                                </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div id="panel_filter" class="flex-2 flex-start">
                                @foreach($filters as $filter)
                                    <ul class="second list-un-styled"{!! $filter['type'] == 'slider' || $filter['active'] ? ' style="display: block"' : '' !!}>
                                        @if($filter['type'] == 'select')
                                            <li class="form_group">
                                                <div class="edit mb15">{{ $filter['name'] }}</div>
                                                <div class="msx_640_filter">
                                                    <div class="msx">
                                                        <select name="filter[{{ $filter['id'] }}]" class="target selectize">
                                                            <option value="">{{ __('locale.text_filter_not_list') }}</option>
                                                            @foreach($filter['values'] as $value)
                                                                <option value="{{ $value['id'] }}"{{ $value['active'] ? ' selected' : '' }}>{{ $value['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="hid-sm hid-smd filter_value_change{{ !empty($select_filters[$filter['id']]['values']) ? ' active' : '' }}">
                                                    @if(!empty($select_filters[$filter['id']]['values']))
                                                        @foreach($select_filters[$filter['id']]['values'] as $key => $s_value)
                                                            {{ $s_value['name'] }}{{ $key < (count($select_filters[$filter['id']]['values'])-1) ? ', ' : '' }}
                                                        @endforeach
                                                    @else
                                                        {{ __('locale.text_filter_not_list') }}
                                                    @endif
                                                </div>
                                            </li>
                                        @else
                                            <li{!! $filter['type'] == 'slider' ? ' style="display: flex;flex-direction: column"' : '' !!}>
                                                <div class="edit mb15">{{ $filter['name'] }}</div>
                                                <div class="msx_640_filter">
                                                    <div class="msx">
                                                        <div{!! $filter['type'] != 'slider' ? ' class="overflow-y filter_y"' : '' !!}>
                                                            @foreach($filter['values'] as $value)
                                                                @if($filter['type'] == 'radio')
                                                                    <div class="flex flex-start check">
                                                                        <div class="custom_radio mt0">
                                                                            <input id="value-{{ $value['id'] }}" class="target" type="radio" name="filter[{{ $filter['id'] }}]" value="{{ $value['id'] }}"{{ $value['active'] ? ' checked' : '' }} />
                                                                            <span></span>
                                                                        </div>
                                                                        <label for="value-{{ $value['id'] }}">{{ __('locale.text_newsletter_3') }}</label>
                                                                    </div>
                                                                @elseif($filter['type'] == 'slider')
                                                                    <div class="flex-2">
                                                                        <div class="flex slider_input">
                                                                            <span>{{ __('locale.text_filter_price_from') }}</span>
                                                                            <input type="text" name="filter_range[{{ $filter['id'] }}][]" class="filter_start" value="{{ $value['start'] }}" />
                                                                        </div>
                                                                        <div class="flex slider_input">
                                                                            <span>{{ __('locale.text_filter_price_to') }}</span>
                                                                            <input type="text" name="filter_range[{{ $filter['id'] }}][]" class="filter_end" value="{{ $value['end'] }}" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="slider_box">
                                                                        <div id="slider-{{ $filter['id'] }}" class="filter_slider" data-min="{{ $value['min'] }}" data-max="{{ $value['max'] }}" data-start="{{ $value['start'] }}" data-end="{{ $value['end'] }}"></div>
                                                                    </div>
                                                                @else
                                                                    <div class="flex flex-start check">
                                                                        <div class="custom_checkbox mt0">
                                                                            <input id="value-{{ $value['id'] }}" class="target" type="checkbox" name="filter[{{ $filter['id'] }}][]" value="{{ $value['id'] }}"{{ $value['active'] ? ' checked' : '' }} />
                                                                            <span></span>
                                                                        </div>
                                                                        <label for="value-{{ $value['id'] }}">{{ $value['name'] }}</label>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="hid-sm hid-smd filter_value_change{{ !empty($select_filters[$filter['id']]['values']) ? ' active' : '' }}">
                                                    @if(!empty($select_filters[$filter['id']]['values']))
                                                        @foreach($select_filters[$filter['id']]['values'] as $key => $s_value)
                                                            {{ $s_value['name'] }}{{ $key < (count($select_filters[$filter['id']]['values'])-1) ? ', ' : '' }}
                                                        @endforeach
                                                    @else
                                                        {{ __('locale.text_filter_not_list') }}
                                                    @endif
                                                </div>
                                            </li>
                                        @endif
                                    </ul>
                                @endforeach
                            </div>
                            <div class="filter_click" style="display: none">
                                <a href="#" class="btn-default col-12"></a>
                            </div>
                        </form>
                    </div>
                    @if(!empty($products))
                        <div class="sorts green_theme">
                            <select name="sort" class="selectize" onchange="location=this.value;">
                                @foreach($sorts as $sort)
                                    <option value="{{ $sort['url'] }}"{{ $sort['active'] ? ' selected' : '' }}>{{ $sort['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            @endif

            <div class="products relative flex-2 flex-start wrap">
                @if(!empty($products))
                    @foreach($products as $product)
                        @include('pages.site.product_item', ['product' => $product, 'width' => 288, 'height' => 250])
                    @endforeach
                @else
                    <p class="edit">{{ !$filters ? __('locale.text_product_list_empty') : __('locale.text_product_list_filter_empty') }}</p>
                @endif
            </div>
            <div class="bottom">
                @if($next)
                    <a href="{{ $next }}" class="load_more">
                        {{ sprintf(__('locale.text_load_more_catalog'), num_decline($more, [__('locale.text_prod1'), __('locale.text_prod2'), __('locale.text_prod3')])) }}
                    </a>
                @endif
                @if(!empty($products))
                    {!! $products->links() !!}
                @endif
            </div>
        </div>
    </section>
@endsection

@section('page-scripts')
    @if($filters)
        <script src="{{ asset('assets/site/js/nouislider.min.js') }}"></script>
        <script src="{{ asset('assets/site/js/filter.js') }}"></script>
        <script>
            if ($(window).width() < 641) {
                $(document).on('click', '.edit_xs', function () {
                    fadeToggle('.filter_left');
                });

                $(document).on('click', '.msx_640_filter.active', function (e) {
                    if (!$('.check').is(e.target) && $('.check').has(e.target).length === 0) {
                        e.preventDefault();
                        return false;
                    }
                });

                $(document).on('click', '.msx_640_filter.active .edit', function () {
                    fadeToggle('.msx_640_filter.active', 0);
                });

                $(document).on('click', '#panel_filter > ul', function (e) {
                    if ($(this).find('.msx_640_filter').length) {
                        var msx = $(this).find('.msx_640_filter');
                        var edit = $(this).find('.edit')[0].outerHTML;
                        if (!msx.find('.edit').length) msx.prepend($(edit).prepend('<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none"><g clip-path="url(#clip0_506_45030)"><path d="M2.57958 5.99997C2.57958 5.7849 2.6617 5.56986 2.82559 5.4059L7.98537 0.24617C8.3136 -0.0820566 8.84576 -0.0820566 9.17385 0.24617C9.50195 0.574263 9.50195 1.10632 9.17385 1.43457L4.60819 5.99997L9.17369 10.5654C9.50179 10.8936 9.50179 11.4256 9.17369 11.7537C8.8456 12.0821 8.31344 12.0821 7.98521 11.7537L2.82543 6.59403C2.66151 6.42999 2.57958 6.21495 2.57958 5.99997Z" fill="#484848"></path></g><defs><clipPath id="clip0_506_45030"><rect width="12" height="12" fill="white" transform="translate(12) rotate(90)"></rect></clipPath></defs></svg>'));

                        if (!$('.check').is(e.target) && $('.check').has(e.target).length === 0) {
                            fadeToggle(msx);
                        }
                    }
                });
            }
            
            $(document).ready(function() {
                $('#filters').Filter({
                    action: '{{ route('last_filters') }}',
                });
            });

            $(document).on('click', '.select_filter', function(){
                if ($('#filters').hasClass('active')) {
                    $('.products').removeClass('preload2');
                    $('.products').css('padding-top', '50px');
                } else {
                    $('.products').addClass('preload2');
                    $('.products').css('padding-top', $('.panels').outerHeight() + 'px');
                }

                $(this).toggleClass('active');
                $('#filters').toggleClass('active');
            });

            function reset_filter() {
                $.ajax({
                    url: '{{ $new_url }}',
                    beforeSend: function() {
                        $('.products').addClass('preload');
                    },
                    success: function(html) {
                        $('#products').html($(html).find('#products').html());
                        selectize('.selectize');
                        wishlist.getList();
                        window.history.pushState('', '', '{{ $new_url }}');
                        $('.products').removeClass('preload preload2');
                        $('#filters').Filter({
                            action: '{{ route('last_filters') }}',
                        });
                    }
                });
            }

            function delete_filter(id) {
                $('#value-' + id).prop('checked', false).trigger('change');
                $('select.target [value="' + id + '"]').parent().val('').trigger('change');
            }
        </script>
    @endif
    <script>
        $(document).ready(function(){
            if ($('.content_bottom .forms').length && $('.products .product').length >= 15) {
                var forms = $('.forms').detach();
                $('.products .product:nth-last-child(10)').after(forms);
            }
        });

        $(document).on('click', '.load_more', function () {
            var url = $(this).attr('href');

            $.ajax({
                url: url,
                type: 'get',
                dataType: 'html',
                beforeSend: function() {
                    $('.products').addClass('preload');
                },
                success: function (html) {
                    $('.products').append($(html).find('.products').html());
                    $('.sorts').html($(html).find('.sorts').html());
                    selectize('.selectize');
                    wishlist.getList();
                    $('.products').removeClass('preload preload2');
                    $('#content h1').text($(html).find('#content h1').text());
                    $('.bottom').html($(html).find('.bottom').html());
                    window.history.pushState('', '', url);
                }
            });

            return false;
        });
    </script>
@endsection