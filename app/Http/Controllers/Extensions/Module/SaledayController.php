<?php
	
	namespace App\Http\Controllers\Extensions\Module;
	use App\Http\Controllers\Controller;
	
	use Illuminate\Http\Request;
	use App\Models\Settings;
	use App\Models\LayoutExtension;
	use App\Models\Products;
	use App\Models\Languages;
	
	class SaledayController extends Controller {
		public $title = 'Предложение дня';
		public $slug = 'saleday';
		public $type = 'setting';
		private $links = [];
		private $scripts = [];
		private $lang;
		private $settings = [];
		
		public function __construct() {
			$this->settings = session('settings');
			$this->lang = session('lang');
		}
		
		public function getLinkStyle() {
			return $this->links;
		}
		
		private function setLinkStyle($link) {
			$this->links[] = $link;
		}
		
		public function getScript() {
			return $this->scripts;
		}
		
		private function setScript($script) {
			$this->scripts[] = $script;
		}
		
		public function index($setting) {
			static $module = 0;
			
			if (!empty($setting['rand'])) {
				$product = Products::with('product_discount:product_id,price')
					->join('product_special as ps', 'ps.product_id', '=', 'products.id')
					->join('product_description as pd', 'pd.product_id', '=', 'products.id')
					->select('products.id', 'products.price as price', 'ps.price as special', 'products.image', 'pd.name', 'ps.date_start', 'ps.date_end')
					->where('products.status', 1)
					->where('products.price', '!=', 'ps.price')
					->where('pd.lang', $this->lang)
					->whereRaw("ps.customer_group_id = '" . (int)session('customer_group_id') . "' AND (ps.date_start < NOW() || ps.date_start = '0000-00-00') AND ps.date_end > NOW()")
					->inRandomOrder()
					->first();
			} else if (!empty($setting['product_id'])) {
				$product = Products::with('product_discount:product_id,price')
					->join('product_special as ps', 'ps.product_id', '=', 'products.id')
					->join('product_description as pd', 'pd.product_id', '=', 'products.id')
					->select('products.id', 'products.price as price', 'ps.price as special', 'products.image', 'pd.name', 'ps.date_start', 'ps.date_end')
					->where('products.status', 1)
					->where('products.price', '!=', 'ps.price')
					->where('pd.lang', $this->lang)
					->whereRaw("ps.customer_group_id = '" . (int)session('customer_group_id') . "' AND (ps.date_start < NOW() || ps.date_start = '0000-00-00') AND ps.date_end > NOW()")
					->where('products.id', $setting['product_id'])
					->first();
			}
			
			if (!empty($product)) {
				$this->setScript(
					[
						'src' => asset('assets/site/js/timer.js')
					]
				);
				
				$this->setScript([
					'text' => 'sale_timer(\'.saleday-' . $module . '\', ' . date('Y', \strtotime($product->date_end)) . ', ' . date('m', \strtotime($product->date_end)) . ', ' . date('d', \strtotime($product->date_end)) . ');'
				]);
				
				$discount = false;
				
				if (session('customer_id') || !session('settings.price_logged')) {
					if (isset($product->product_discount[0])) {
						$price = format_price($product->product_discount[0]->price, session('currency'));
						$discount = format_price($product->product_discount[$product->product_discount->count() - 1]->price, session('currency'));
					} else {
						$price = format_price($product->price, session('currency'));
					}
				} else {
					$price = false;
				}
				
				if (session('customer_id') || !$this->settings['price_logged']) {
					$special = format_price($product->special, session('currency', []));
					$sale = round(100 - ($product->special * 100 / $product->price));
				} else {
					$special = false;
					$sale = false;
				}
				
				$data = [
					'id' => $product->id,
					'url' => $product->getSlug(),
					'name' => \Illuminate\Support\Str::limit($product->name, 40, '...'),
					'image' => resize_image($product->image, 190, 190),
					'price' => $price,
					'sale' => $sale,
					'discount' => $discount,
					'special' => $special,
					'module' => $module,
					'title' => $setting['title'][$this->lang]
				];
				
				$module++;
				
				return view('pages.site.extensions.module.saleday', $data);
			}
		}
		
		public function edit(Request $request) {
			$extension = Settings::where('code', 'extension.module.' . $this->slug)->value('value');
			
			if (!empty($extension['product_id'])) {
				$product = Products::join('product_description as pd', 'pd.product_id', '=', 'products.id')->select('pd.name')->where('products.id', $extension['product_id'])->value('pd.name');
			} else {
				$product = '';
			}
			
			$langs = Languages::orderBy('name')->get();
			
			return ['setting' => old('setting', $extension), 'product' => $product, 'langs' => $langs, 'action' => asset('admin/extension/module/' . $this->slug . '/save')];
		}
		
		public function delete(Request $request) {
			if ($request->code) {
				Settings::where('code', 'extension.module.' . $request->code)->delete();
				LayoutExtension::where('code', $request->code)->delete();
				return 'Модуль ' . $this->title . ' успешно удален';
			} else {
				return 'Произошла ошибка';
			}
		}
		
		public function save(Request $request) {
			if (empty($request->setting['product_id'])) {
				$this->validate($request, [
					'setting.name' => 'required',
					'setting.title.*' => 'required',
					'setting.rand' => 'required',
				]);
			} else if (empty($request->setting['rand'])) {
				$this->validate($request, [
					'setting.name' => 'required',
					'setting.title.*' => 'required',
					'setting.product_id' => 'required',
				]);
			} else {
				$this->validate($request, [
					'setting.name' => 'required',
					'setting.title.*' => 'required',
					'setting.rand' => 'required',
					'setting.product_id' => 'required',
				]);
			}
			
			$setting = [];
			
			if (!is_null($request->setting)) {
				foreach ($request->setting as $key => $s) {
					if (!is_null($s)) $setting[$key] = !is_array($s) ? $s : array_filter($s);
				}
			}
			
			Settings::where('code', 'extension.module.' . $this->slug)->delete();
			
			$settings = new Settings;
			$settings->code = 'extension.module.' . $this->slug;
			$settings->value = $setting;
			
			$settings->save();
			
			return 'Модуль ' . $this->title . ' успешно изменен';
		}
	}
