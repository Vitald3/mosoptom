<?php
	
	namespace App\Http\Controllers\Extensions\Module;
	use App\Http\Controllers\Controller;
	
	use Illuminate\Http\Request;
	use App\Models\Settings;
	use App\Models\Products;
	use App\Models\LayoutExtension;
	use App\Models\Languages;
	use DB;
	
	class BestsellerController extends Controller {
		public $title = 'Популярные товары';
		public $slug = 'bestseller';
		public $type = 'setting';
		private $links = [];
		private $scripts = [];
		
		public function __construct() {
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
			$limit = $setting['limit'] ? $setting['limit'] : 12;
			
			if (session('category_id')) {
				$products = Products::with([
					'product_special_one:product_id,price',
					'product_discount:product_id,price',
				])
					->join('product_description as pd', 'pd.product_id', '=', 'products.id')
					->join('product_category as pc', 'pc.product_id', '=', 'products.id')
					->join('order_product as op', 'op.product_id', '=', 'products.id')
					->select('products.id', 'products.price', 'products.image', 'products.model', 'pd.name')
					->where('products.status', 1)
					->where('pd.lang', $this->lang)
					->where('pc.category_id', session('category_id'))
					->where('op.created_at', '>=', DB::raw('DATE_SUB(NOW(), INTERVAL 1 YEAR)'))
					->orderBy('op.created_at', 'desc')
					->limit($limit)
					->get();
			} else {
				$products = Products::with([
					'product_special_one:product_id,price',
					'product_discount_one:product_id,price',
				])
					->join('product_description as pd', 'pd.product_id', '=', 'products.id')
					->join('order_product as op', 'op.product_id', '=', 'products.id')
					->select('products.id', 'products.price', 'products.image', 'products.model', 'pd.name')
					->where('products.status', 1)
					->where('pd.lang', $this->lang)
					->where('op.created_at', '>=', DB::raw('DATE_SUB(NOW(), INTERVAL 1 YEAR)'))
					->orderBy('op.created_at', 'desc')
					->limit($limit)
					->get();
			}
			
			if (!$products->isEmpty()) {
				$data['title'] = !empty($setting['title'][$this->lang]) ? $setting['title'][$this->lang] : '';
				
				$module++;
				
				$data['module'] = $module;
				$data['products'] = $products;
				
				return view('pages.site.extensions.module.bestseller', $data);
			}
		}
		
		public function edit() {
			$extension = Settings::where('code', 'extension.module.' . $this->slug)->value('value');
			$langs = Languages::orderBy('name')->get();
			return ['setting' => old('setting', $extension), 'langs' => $langs, 'action' => asset('admin/extension/module/' . $this->slug . '/save')];
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
			$this->validate($request, [
				'setting.name' => 'required',
				'setting.title.*' => 'required'
			]);
			
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
