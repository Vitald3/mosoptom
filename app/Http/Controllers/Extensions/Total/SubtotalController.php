<?php

namespace App\Http\Controllers\Extensions\Total;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Settings;

class SubtotalController extends Controller {
    public $title = 'Сумма заказа';
    public $slug = 'subtotal';
    public $type = 'setting';

    public function getTotal($total, $setting, $products) {
        $sub_total = 0;
        $quantity = 0;

        foreach ($products as $product) {
			$sub_total += $product['total_int'];
			$quantity += $product['quantity'];
        }

        $total['totals'][] = array(
            'code'       => $this->slug,
            'title'      => sprintf(__('locale.text_sub_total'), $quantity),
            'value'      => $sub_total,
            'sort_order' => $setting['sort_order']
        );

        $total['total'] += $sub_total;
    }

    public function edit(Request $request) {
        $extension = Settings::where('code', 'extension.' . $request->type . '.' . $this->slug)->value('value');

        return ['setting' => old('setting', $extension), 'action' => asset('admin/extension/total/' . $this->slug . '/save')];
    }

    public function delete(Request $request) {
        if ($request->code) {
            Settings::where('code', 'extension.' . $request->type . '.' . $request->code)->delete();
            return 'Модуль ' . $this->title . ' успешно удален';
        } else {
            return 'Произошла ошибка';
        }
    }

    public function save(Request $request) {
        $setting = [];

        if (!is_null($request->setting)) {
            foreach ($request->setting as $key => $s) {
                if (!is_null($s)) $setting[$key] = !is_array($s) ? $s : array_filter($s);
            }
        }

        Settings::where('code', 'extension.' . $request->type . '.' . $this->slug)->delete();

        $settings = new Settings;
        $settings->code = 'extension.' . $request->type . '.' . $this->slug;
        $settings->value = $setting;

        $settings->save();

        return 'Модуль ' . $this->title . ' успешно изменен';
    }
}