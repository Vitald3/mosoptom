<?php
	
	namespace App\Http\Controllers\Extensions\Module;
	use App\Http\Controllers\Controller;
	
	use Illuminate\Http\Request;
	use App\Models\LayoutExtension;
	use App\Models\Extensions;
	use App\Models\Settings;
	use App\Models\Products;
	
	class AttentionController extends Controller {
		public $title = 'Обратите внимание';
		public $slug = 'attention';
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

			if (!empty($setting['products'])) {
				$products = Products::with([
					'product_special_one:product_id,price',
					'product_discount:product_id,price',
				])
					->join('product_description as pd', 'pd.product_id', '=', 'products.id')
					->select('products.id', 'products.price', 'products.model', 'products.image', 'pd.name')
					->whereIn('products.id', $setting['products'])
					->where('pd.lang', $this->lang)
					->where('products.status', 1)
					->orderBy('products.created_at', 'desc')
					->get();
				
				if (!$products->isEmpty()) {
					$data['width'] = !empty($setting['width']) ? $setting['width'] : 288;
					$data['height'] = !empty($setting['height']) ? $setting['height'] : 250;
					
					$data['products'] = $products;
				
					$this->setLinkStyle(
						[
							'href' => asset('assets/site/css/owl.carousel.min.css'),
							'rel' => 'stylesheet'
						]
					);
					
					$this->setScript(
						[
							'src' => asset('assets/site/js/owl.carousel.min.js')
						]
					);
					
					$script = 'var attention_' . $module . ' = $(\'.attention' . $module . '\');';
					
					$script .= 'attention_' . $module . '.owlCarousel({
                    loop: true,
                    items: 1,
                    singleItems: true,
                    margin: 40,
                    nav: true,
                    navText: [\'<span style="display: inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none"><circle cx="20" cy="20" r="20" fill="white"/><g clip-path="url(#clip0_432_54444)"><path d="M16.5815 20C16.5815 19.7849 16.6637 19.5699 16.8275 19.4059L21.9873 14.2462C22.3155 13.9179 22.8477 13.9179 23.1758 14.2462C23.5039 14.5743 23.5039 15.1063 23.1758 15.4346L18.6101 20L23.1756 24.5654C23.5037 24.8936 23.5037 25.4256 23.1756 25.7537C22.8476 26.0821 22.3154 26.0821 21.9872 25.7537L16.8274 20.594C16.6635 20.43 16.5815 20.215 16.5815 20Z" fill="#54B0AC"/></g><defs><clipPath id="clip0_432_54444"><rect width="12" height="12" fill="white" transform="translate(26 14) rotate(90)"/></clipPath></defs></svg></span>\', \'<span style="display: inline-block;transform: rotate(180deg)"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none"><circle cx="20" cy="20" r="20" fill="white"/><g clip-path="url(#clip0_432_54444)"><path d="M16.5815 20C16.5815 19.7849 16.6637 19.5699 16.8275 19.4059L21.9873 14.2462C22.3155 13.9179 22.8477 13.9179 23.1758 14.2462C23.5039 14.5743 23.5039 15.1063 23.1758 15.4346L18.6101 20L23.1756 24.5654C23.5037 24.8936 23.5037 25.4256 23.1756 25.7537C22.8476 26.0821 22.3154 26.0821 21.9872 25.7537L16.8274 20.594C16.6635 20.43 16.5815 20.215 16.5815 20Z" fill="#54B0AC"/></g><defs><clipPath id="clip0_432_54444"><rect width="12" height="12" fill="white" transform="translate(26 14) rotate(90)"/></clipPath></defs></svg></span>\'],
                    dots: false
                });';
					
					$this->setScript([
						'text' => $script
					]);
					
					$data['module'] = $module;
					
					$module++;
					
					return view('pages.site.extensions.module.attention', $data);
				}
			}
		}
		
		public function edit(Request $request) {
			$extension = Settings::where('code', 'extension.module.' . $this->slug)->value('value');
			
			if (!is_null(old('setting.products', $extension))) {
				$products = Products::join('product_description as pd', 'pd.product_id', '=', 'products.id')->select('pd.name', 'products.id')->whereIn('products.id', (array)old('setting.products', $extension))->get()->keyBy('id');
			} else {
				$products = [];
			}
			
			return ['setting' => old('setting', $extension), 'products' => $products, 'action' => asset('admin/extension/module/' . $this->slug . '/save')];
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
				'setting.products' => 'required',
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
