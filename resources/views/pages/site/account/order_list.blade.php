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
                <div class="flex-2 wrap mb30 green_theme">
                    <h1 class="mb0">{{ $title }}</h1>
                    <select class="selectize" onchange="location='{{ route(session('route_url') . '_account_order') . ($page > 1 ? '/page/' . $page : '') }}?status=' + this.value;">
                        <option value="1"{{ $status == 1 || !$status ? ' selected' : '' }}>{{ __('locale.text_account_order_3') }}</option>
                        <option value="2"{{ $status == 2 ? ' selected' : '' }}>{{ __('locale.text_account_order_4') }}</option>
                        <option value="9"{{ $status == 9 ? ' selected' : '' }}>{{ __('locale.text_account_order_5') }}</option>
                        <option value="13"{{ $status == 13 ? ' selected' : '' }}>{{ __('locale.text_account_order_6') }}</option>
                        <option value="7"{{ $status == 7 ? ' selected' : '' }}>{{ __('locale.text_account_order_7') }}</option>
                    </select>
                </div>
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
                        @if(!$orders->isEmpty())
                            @foreach($orders as $order)
                                <tr class="pointer mob_flex" onclick="location='{{ route(session('route_url') . '_account_order_info', $order->id) }}'">
                                    <td class="fbold"><span class="hid-sm hid-lg hid-ms">{{ __('locale.column_id') }}</span><span>№{{ $order->id }}</span></td>
                                    <td><span class="hid-sm hid-lg hid-ms">{{ __('locale.column_date') }}</span><span>{{ date('d.m.Y', \strtotime($order->created_at)) }}</span></td>
                                    <td><span class="hid-sm hid-lg hid-ms">{{ __('locale.column_quantity') }}</span><span>{{ $order->products->sum('quantity') }} {{ __('locale.text_sht') }}</span></td>
                                    <td class="fbold"><span class="hid-sm hid-lg hid-ms">{{ __('locale.column_total') }}</span><span>{{ format_price($order->total, session('currency')) }}</span></td>
                                    <td><span class="hid-sm hid-lg hid-ms">{{ __('locale.column_status') }}</span><div class="flex"><span style="display: inline-block;width: 9px;height: 9px;border-radius: 50%;margin-right: 7px;margin-bottom: 1px;background: {{ $order->color ? $order->color : '#eee' }}"></span><span class="widf">{{ $order->status }}</span></div></td>
                                    <td><span class="hid-sm hid-lg hid-ms">{{ __('locale.column_shipping') }}</span><span>{{ \Illuminate\Support\Str::limit($order->shipping_title, 18, '...') }}</span></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6">{{ __('locale.text_account_order_2') }}</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                {!! $orders->links() !!}
            </div>
        </div>
    </section>
@endsection