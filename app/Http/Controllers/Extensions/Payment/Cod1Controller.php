<?php

namespace App\Http\Controllers\Extensions\Payment;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\Status;

class Cod1Controller extends Controller {
	public $title = 'На расчетный счет';
    public $slug = 'cod1';
    public $type = 'setting';
    
    public function getText() {
		return __('locale.text_cod1');
	}
	
	public function getTitle() {
		return __('locale.text_cod1_title');
	}
	
	public function confirm() {
		$extension = Settings::where('code', 'extension.payment.' . $this->slug)->value('value');
		$api = new \App\Http\Controllers\ApiController;
		$api->update_order(session('order_id'), $extension['order_status_id']);
	}

    public function edit(Request $request) {
        $extension = Settings::where('code', 'extension.' . $request->type . '.' . $this->slug)->value('value');
        $settings = Settings::addSelect('value')->where('code', 'settings')->value('value');
        $lang = session('lang');
        $statuses = Status::join('status_description as st', 'st.status_id', '=', 'status.id')->select('status.id', 'st.name')->where('st.lang', $lang)->where('status.type', 1)->get()->keyBy('id');

        return ['statuses' => $statuses, 'setting' => old('setting', $extension), 'action' => asset('admin/extension/' . $request->type . '/' . $this->slug . '/save')];
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