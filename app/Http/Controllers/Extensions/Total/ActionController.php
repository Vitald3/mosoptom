<?php
	
	namespace App\Http\Controllers\Extensions\Total;
	use App\Http\Controllers\Controller;
	
	use Illuminate\Http\Request;
	use App\Models\Settings;
	use App\Models\Coupon;
	use App\Models\CouponHistory;
	use App\Models\CouponProduct;
	use App\Models\CouponCategory;
	
	class ActionController extends Controller {
		public $title = 'Проверка товаров';
		public $slug = 'action';
		public $type = 'setting';
		
		public function index() {
			$extension = Settings::where('code', 'extension.total.' . $this->slug)->value('value');
			
			if ($extension && $extension['status'] == 1) {
				return view('pages.site.extensions.total.action', ['cost' => format_price($extension['cost'], session('currency'))]);
			}
		}
		
		public function setTotal($request) {
			if ($request->action) {
				session(['action_product' => $request->action]);
				return true;
			} else {
				$request->session()->forget('action_product');
				return false;
			}
		}
		
		public function getTotal($total, $setting, $products) {
			if (!is_null(session('action_product'))) {
				$totals = 0;
				
				foreach ($products as $product) {
					$totals += $product['quantity'] * (float)$setting['cost'];
				}
				
				if ($totals > 0) {
					$total['totals'][] = array(
						'code'       => $this->slug,
						'title'      => __('locale.text_total_action'),
						'value'      => $totals,
						'sort_order' => $setting['sort_order']
					);
					
					$total['total'] += $totals;
				}
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