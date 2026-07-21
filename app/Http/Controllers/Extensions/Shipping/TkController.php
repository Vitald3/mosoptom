<?php
	
	namespace App\Http\Controllers\Extensions\Shipping;
	use App\Http\Controllers\Controller;
	
	use Illuminate\Http\Request;
	use App\Models\Settings;
	
	class TkController extends Controller {
		public $title = 'Транспортными компаниями';
		public $slug = 'tk';
		public $type = 'setting';
		
		public function getTitle() {
			return __('locale.text_tk');
		}
		
		public function quote($result) {
			$quote = [];
			
			foreach ($result['tk'] as $tk) {
				$quote[] = [
					'name' => $tk['name'],
					'code' => str_slug($tk['name'])
				];
			}
			
			return $quote;
		}
		
		public function getRequired() {
			return [
				'tk.address' => 'required',
				'tk.tk' => 'required'
			];
		}
		
		public function cost($result) {
			$cost = 0;
			
			foreach ($result['tk'] as $key => $tk) {
				if ((!session('shipping_fields.tk.tk', 0) && $key == 0) || (session('shipping_fields.tk.tk') == str_slug($tk['name']))) {
					$cost = (float)$tk['cost'];
					break;
				}
			}
			
			return $cost;
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