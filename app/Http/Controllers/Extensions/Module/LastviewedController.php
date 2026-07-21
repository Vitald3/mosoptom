<?php
	
	namespace App\Http\Controllers\Extensions\Module;
	use App\Http\Controllers\Controller;
	
	use Illuminate\Http\Request;
	use App\Models\Settings;
	use App\Models\LayoutExtension;
	use App\Models\Products;
	use App\Models\Languages;
	
	class LastviewedController extends Controller {
		public $title = 'Последние просмотренные';
		public $slug = 'lastviewed';
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
			
			if (session('product_visited')) {
				$limit = !empty($setting['limit']) ? $setting['limit'] : 4;
				$product_ids = array_slice((array)session('product_visited'), 0, (int)$limit);
				
				$products = Products::with([
					'product_special_one:product_id,price',
					'product_discount:product_id,price',
				])
					->join('product_description as pd', 'pd.product_id', '=', 'products.id')
					->select('products.id', 'products.price', 'products.image', 'pd.name')
					->whereIn('products.id', $product_ids)
					->where('pd.lang', $this->lang)
					->where('products.status', 1)
					->orderBy('products.created_at', 'desc')
					->get();
				
				if (!$products->isEmpty()) {
					$data['width'] = !empty($setting['width']) ? $setting['width'] : 288;
					$data['height'] = !empty($setting['height']) ? $setting['height'] : 250;
					
					$data['products'] = $products;
					
					$data['text_last_wishlist'] = sprintf(__('locale.text_last_wishlist'), num_decline($products->count(), ['товар', 'товара', 'товаров']), count((array)session('customer_wishlist')));
					
					$module++;
					
					$data['module'] = $module;
					
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
					
					$script = 'var product_visited_' . $module . ' = $(\'.products_visited' . $module . ' .owl-carousel\');';
					
					$script .= 'product_visited_' . $module . '.owlCarousel({
                        items: 3,
                        margin: 40,
                        nav: false,
                        dots: false,
                        responsive:{
                            640:{
                                margin: 30,
                                items: 3,
                                autoWidth: true,
                            },
                             320:{
                                margin: 30,
                                items: 2,
                                autoWidth: true,
                            }
                        }
                    });
                    
                    $(document).on(\'click\', \'.products_visited' . $module . ' .last_next\', function() {
                        product_visited_' . $module . '.trigger(\'next.owl.carousel\');
                        return false;
                    });
                    
                    $(document).on(\'click\', \'.products_visited' . $module . ' .last_prev\', function() {
                        product_visited_' . $module . '.trigger(\'prev.owl.carousel\', [300]);
                        return false;
                    });';
					
					$this->setScript([
						'text' => $script
					]);
					
					return view('pages.site.extensions.module.lastviewed', $data);
				}
			}
		}
		
		public function edit(Request $request) {
			$extension = Settings::where('code', 'extension.module.' . $this->slug)->value('value');
			
			return ['setting' => old('setting', $extension), 'action' => asset('admin/extension/module/' . $this->slug . '/save')];
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
				'setting.name' => 'required'
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
