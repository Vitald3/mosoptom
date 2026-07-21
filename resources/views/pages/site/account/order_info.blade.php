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
    <link rel="stylesheet" href="{{ asset('assets/site/css/media/account.css') }}" />
@endsection
@section('content')
    <section id="success" class="container">
        <div class="account_container flex-2">
            <div class="account_left">
                @include('pages.site.account.menu')
            </div>
            <div class="account_right">
                <h1 class="mb0">{{ $title }}</h1>
                <div class="table table-fixed">
                    <table>
                        <thead class="hid-xs">
                        <tr>
                            <td>{{ __('locale.column_id') }}</td>
                            <td>{{ __('locale.column_date') }}</td>
                            <td>{{ __('locale.column_quantity') }}</td>
                            <td>{{ __('locale.column_total') }}</td>
                            <td>{{ __('locale.column_status') }}</td>
                            <td>{{ __('locale.column_shipping') }}</td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="pointer mob_flex">
                            <td class="fbold"><span class="hid-sm hid-lg hid-ms">{{ __('locale.column_id') }}</span><span>№{{ $order->id }}</span></td>
                            <td><span class="hid-sm hid-lg hid-ms">{{ __('locale.column_date') }}</span><span>{{ date('d.m.Y', \strtotime($order->created_at)) }}</span></td>
                            <td><span class="hid-sm hid-lg hid-ms">{{ __('locale.column_quantity') }}</span><span>{{ $order->products->sum('quantity') }} {{ __('locale.text_sht') }}</span></td>
                            <td class="fbold"><span class="hid-sm hid-lg hid-ms">{{ __('locale.column_total') }}</span><span>{{ format_price($order->total, session('currency')) }}</span></td>
                            <td><span class="hid-sm hid-lg hid-ms">{{ __('locale.column_status') }}</span><div class="flex"><span style="display: inline-block;width: 9px;height: 9px;border-radius: 50%;margin-right: 7px;margin-bottom: 1px;background: {{ $order->color ? $order->color : '#eee' }}"></span><span class="widf">{{ $order->status }}</span></div></td>
                            <td><span class="hid-sm hid-lg hid-ms">{{ __('locale.column_shipping') }}</span><span>{{ \Illuminate\Support\Str::limit($order->shipping_title, 18, '...') }}</span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="table table-fixed">
                    <table class="not_border">
                        <tbody>
                            @foreach($order->products as $product)
                            <tr class="wrap_s">
                                <td class="wname"><img style="border-radius: 25px" src="{{ resize_image($product->image, 138, 140) }}" /></td>
                                <td class="w72">{{ \Illuminate\Support\Str::limit($product->name, 50, '...') }}</td>
                                <td style="font-weight: 500;font-size: 18px;line-height: 22px">{{ $product->quantity }} шт</td>
                                <td><span style="font-weight: 500;font-size: 12px;line-height: 22px;color: #484848;opacity: 0.5">{{ format_price($product->price, session('currency')) }}/шт</span></td>
                                <td class="fbold total">{{ format_price($product->total, session('currency')) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt30 flex-2">
                    <div class="h1">{{ __('locale.text_total_sum') }}</div>
                    <div class="h1">{{ $order->total }}</div>
                </div>
                @if(!empty($order->history))
                    <div class="table table-fixed mt30">
                        <table class="not_border">
                            <tbody>
                            @foreach($order->history as $history)
                                <tr>
                                    <td>{{ $history->comment }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection