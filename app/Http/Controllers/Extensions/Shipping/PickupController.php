<?php

namespace App\Http\Controllers\Extensions\Shipping;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Settings;

class PickupController extends Controller {
    public $title = 'Самовывоз';
    public $slug = 'pickup';
    public $type = 'setting';
	
	public function getTitle() {
		return __('locale.text_pickup');
	}
	
	public function getRequired() {
		return [];
	}
	
	public function cost($result) {
		$cost = !empty($result['setting']['cost']) ? $result['setting']['cost'] : 0;
		return $cost;
	}
	
	public function quote($result) {
		return [];
	}

    public function edit(Request $request) {
        $extension = Settings::where('code', 'extension.' . $request->type . '.' . $this->slug)->value('value');

        return ['setting' => old('setting', $extension), 'action' => asset('admin/extension/' . $request->type . '/' . $this->slug . '/save')];
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