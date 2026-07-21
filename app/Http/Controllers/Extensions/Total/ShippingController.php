<?php

namespace App\Http\Controllers\Extensions\Total;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Settings;

class ShippingController extends Controller {
    public $title = 'Доставка';
    public $slug = 'shipping';
    public $type = 'setting';

    public function getTotal($total, $setting) {
        if (!is_null(session('shipping_method'))) {
            $total['totals'][] = array(
                'code'       => $this->slug,
                'title'      => session('shipping_method.title'),
                'value'      => session('shipping_method.cost'),
                'sort_order' => $setting['sort_order']
            );

            $total['total'] += session('shipping_method.cost', 0);
        }
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