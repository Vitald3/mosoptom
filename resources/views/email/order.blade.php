<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #edf2f7; border-left: 1px solid #edf2f7; margin-bottom: 20px;">
    <thead>
    <tr>
        <td style="font-size: 12px; border-right: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;" colspan="2">{{ __('locale.text_order_detail') }}</td>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="font-size: 12px;	border-right: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; text-align: left; padding: 7px;"><b>{{ __('locale.text_order_id') }}</b> {{ $order_id }}<br />
            <b>{{ __('locale.text_date_added') }}</b> {{ $date_added }}<br />
            @if($payment_method)
                <b>{{ __('locale.text_payment_method') }}</b> {{ $payment_method }}<br />
            @endif
            @if($shipping_method)
                <b>{{ __('locale.text_shipping_method') }}</b> {{ $shipping_method }}
            @endif
        </td>
        <td style="font-size: 12px;	border-right: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; text-align: left; padding: 7px;"><b>{{ __('locale.text_email') }}</b> {{ $email }}<br />
            <b>{{ __('locale.text_telephone') }}</b> {{ $phone }}<br />
            <b>{{ __('locale.text_order_status') }}</b> {{ $order_status }}<br /></td>
    </tr>
    </tbody>
</table>
@if($comment)
    <table style="border-collapse: collapse; width: 100%; border-top: 1px solid #edf2f7; border-left: 1px solid #edf2f7; margin-bottom: 20px;">
        <thead>
        <tr>
            <td style="font-size: 12px; border-right: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;">{{ __('locale.text_instruction') }}</td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="font-size: 12px;	border-right: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; text-align: left; padding: 7px;">{!! $comment !!}</td>
        </tr>
        </tbody>
    </table>
@endif
@if($address)
    <table style="border-collapse: collapse; width: 100%; border-top: 1px solid #edf2f7; border-left: 1px solid #edf2f7; margin-bottom: 20px;">
        <thead>
        <tr>
            <td style="font-size: 12px; border-right: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;">{{ __('locale.text_address') }}</td>
        </thead>
        <tbody>
        <tr>
            <td style="font-size: 12px;	border-right: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; text-align: left; padding: 7px;">{!! $address !!}</td>
        </tr>
        </tbody>
    </table>
@endif
<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #edf2f7; border-left: 1px solid #edf2f7; margin-bottom: 20px;">
    <thead>
    <tr>
        <td style="font-size: 12px; border-right: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;">{{ __('locale.text_product') }}</td>
        <td style="font-size: 12px; border-right: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;">{{ __('locale.sku') }}</td>
        <td style="font-size: 12px; border-right: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;">{{ __('locale.column_quantity') }}</td>
        <td style="font-size: 12px; border-right: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;">{{ __('locale.column_total') }}</td>
        <td style="font-size: 12px; border-right: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;">{{ __('locale.text_total') }}</td>
    </tr>
    </thead>
    <tbody>
    @foreach($products as $product)
        <tr>
            <td style="font-size: 12px; border-right: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; text-align: left; padding: 7px;">{{ $product['name'] }}
                @if(!empty($product['option']))
                    @foreach($product['option'] as $option)<br />
                    &nbsp;<small> - {{ $option['name'] }}: {{ $option['value'] }}</small>
                    @endforeach
                @endif
            </td>
            <td style="font-size: 12px; border-right: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; text-align: left; padding: 7px;">{{ $product['model'] }}</td>
            <td style="font-size: 12px;	border-right: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; text-align: right; padding: 7px;">{{ $product['quantity'] }}</td>
            <td style="font-size: 12px;	border-right: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; text-align: right; padding: 7px;">{{ format_price($product['price'], session('currency')) }}</td>
            <td style="font-size: 12px;	border-right: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; text-align: right; padding: 7px;">{{ format_price($product['total'], session('currency')) }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    @foreach($totals as $total)
        <tr>
            <td style="font-size: 12px;	border-right: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; text-align: right; padding: 7px;" colspan="4"><b>{{ $total['title'] }}:</b></td>
            <td style="font-size: 12px;	border-right: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; text-align: right; padding: 7px;">{{ $total['value'] }}</td>
        </tr>
    @endforeach
    </tfoot>
</table>
{!! isset($text) ? $text : '' !!}