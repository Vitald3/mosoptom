<?php
	
	namespace App\Http\Controllers\Extensions\Module;
	use App\Http\Controllers\Controller;
	
	use Illuminate\Http\Request;
	use App\Models\Extensions;
	use App\Models\LayoutExtension;
	use App\Models\Languages;
	use App\Models\Products;
	use Str;
	
	class ProductsController extends Controller {
		public $title = 'Товары';
		public $slug = 'products';
		public $type = 'module';
		private $links = [];
		private $scripts = [];
		
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
			
			if (!empty($setting['product'])) {
				$limit = !empty($setting['limit']) ? $setting['limit'] : 4;
				$product_ids = array_slice($setting['product'], 0, (int)$limit);
				
				$products = Products::with([
					'product_special_one:product_id,price',
					'product_discount:product_id,price',
				])
					->join('product_description as pd', 'pd.product_id', '=', 'products.id')
					->select('products.id', 'products.price', 'products.image', 'products.model', 'pd.name')
					->whereIn('products.id', $product_ids)
					->where('pd.lang', $this->lang)
					->where('products.status', 1)
					->orderBy('products.created_at', 'desc')
					->get();
				
				if (!$products->isEmpty()) {
					$data['title'] = !empty($setting['title'][$this->lang]) ? $setting['title'][$this->lang] : '';
					$data['text'] = !empty($setting['text'][$this->lang]) ? $setting['text'][$this->lang] : '';
					$data['width'] = !empty($setting['width']) ? $setting['width'] : 288;
					$data['height'] = !empty($setting['height']) ? $setting['height'] : 250;
					$data['link'] = !empty($setting['link']) ? $setting['link'] : '';
					
					$data['products'] = $products;
					
					$module++;
					
					$data['module'] = $module;
					$data['lang'] = session('lang');
					
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
					
					$script = 'var products_' . $module . ' = $(\'.products_module' . $module . ' .owl-carousel\');';
					
					$script .= 'products_' . $module . '.owlCarousel({
                    loop: true,
                    items: 4,
                    margin: 40,
                    nav: true,
                    navText: [\'<span style="display: inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none"><circle xmlns="http://www.w3.org/2000/svg" cx="20" cy="20" r="18" transform="rotate(-90 20 20)" fill="white" stroke-dashoffset="113.04" stroke-dasharray="113.04" stroke="#54B0AC" stroke-width="3"/><g clip-path="url(#clip0_432_54444)"><path d="M16.5815 20C16.5815 19.7849 16.6637 19.5699 16.8275 19.4059L21.9873 14.2462C22.3155 13.9179 22.8477 13.9179 23.1758 14.2462C23.5039 14.5743 23.5039 15.1063 23.1758 15.4346L18.6101 20L23.1756 24.5654C23.5037 24.8936 23.5037 25.4256 23.1756 25.7537C22.8476 26.0821 22.3154 26.0821 21.9872 25.7537L16.8274 20.594C16.6635 20.43 16.5815 20.215 16.5815 20Z" fill="#54B0AC"/></g><defs><clipPath id="clip0_432_54444"><rect width="12" height="12" fill="white" transform="translate(26 14) rotate(90)"/></clipPath></defs></svg></span>\', \'<span style="display: inline-block;transform: rotate(180deg)"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none"><circle xmlns="http://www.w3.org/2000/svg" cx="20" cy="20" r="18" transform="rotate(-90 20 20)" fill="white" stroke-dashoffset="113.04" stroke-dasharray="113.04" stroke="#54B0AC" stroke-width="3"><animate restart="always" xmlns="http://www.w3.org/2000/svg" attributeName="stroke-dashoffset" dur="5s" begin="0" repeatCount="indefinite" values="113.04;0"/><\'+\'/circle><g clip-path="url(#clip0_432_54444)"><path d="M16.5815 20C16.5815 19.7849 16.6637 19.5699 16.8275 19.4059L21.9873 14.2462C22.3155 13.9179 22.8477 13.9179 23.1758 14.2462C23.5039 14.5743 23.5039 15.1063 23.1758 15.4346L18.6101 20L23.1756 24.5654C23.5037 24.8936 23.5037 25.4256 23.1756 25.7537C22.8476 26.0821 22.3154 26.0821 21.9872 25.7537L16.8274 20.594C16.6635 20.43 16.5815 20.215 16.5815 20Z" fill="#54B0AC"/></g><defs><clipPath id="clip0_432_54444"><rect width="12" height="12" fill="white" transform="translate(26 14) rotate(90)"/></clipPath></defs></svg></span>\'],
                    dots: false,
                    autoplay:true,
                    autoplayTimeout:5000,
                    responsiveClass:true,
                    responsive:{
                    1600:{
                        margin: 40,
                    },
                    1200:{
                        margin: 30,
                    },
                    960:{
                       margin: 30, 
                       items: 3,
                    },
                    640:{
                       margin: 30,
                       items: 3,
                       autoWidth: true,
                    },
                    320:{
                        margin: 30,
                        items: 3,
                        autoWidth: true
                    }
                    }
                });';
					
					$this->setScript([
						'text' => $script
					]);
					
					return view('pages.site.extensions.module.products', $data);
				}
			}
		}
		
		public function add() {
			$langs = Languages::orderBy('name')->get();
			$extensions = Extensions::select('id', 'name')->where('code', 'like', '%products%')->orderBy('created_at')->get()->keyBy('id');
			
			if (!is_null(old('setting.product'))) {
				$products = Products::join('product_description as pd', 'pd.product_id', '=', 'products.id')->select('pd.name', 'products.id')->whereIn('products.id', (array)old('setting.product'))->get()->keyBy('id');
			} else {
				$products = [];
			}
			
			return ['extensions' => $extensions, 'langs' => $langs, 'products' => $products, 'setting' => (array)old('setting'), 'name' => old('name'), 'status' => old('status'), 'action' => asset('admin/extension/module/products/save'), 'id' => ''];
		}
		
		public function edit(Request $request)
		{
			$extension = Extensions::where('id', $request->id)->first();
			
			if (!empty($extension)) {
				$langs = Languages::orderBy('name')->get();
				
				if (!is_null(old('setting.product', $extension->setting['product']))) {
					$products = Products::join('product_description as pd', 'pd.product_id', '=', 'products.id')->select('pd.name', 'products.id')->whereIn('products.id', old('setting.product', $extension->setting['product']))->get()->keyBy('id');
				} else {
					$products = [];
				}
				
				return ['langs' => $langs, 'products' => $products, 'setting' => old('setting') ? (array)old('setting') : $extension->setting, 'name' => old('name') ? old('name') : $extension->name, 'status' => old('status') ? old('status') : $extension->status, 'action' => asset('admin/extension/module/products/save/' . $request->id), 'id' => $request->id];
			} else {
				return redirect('admin/extensions')->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete(Request $request) {
			if ($request->code) {
				Extensions::where('code', $request->code)->where('id', $request->id)->delete();
				LayoutExtension::where('code', $request->code . '.' . $request->id)->delete();
				return 'Модуль ' . $this->title . ' успешно удален';
			} else {
				return 'Произошла ошибка';
			}
		}
		
		public function save(Request $request) {
			$this->validate($request, [
				'name' => 'required',
				'setting.title.*' => 'required',
				'setting.product.*' => 'required'
			]);
			
			$setting = [];
			
			if (!is_null($request->setting)) {
				foreach ($request->setting as $key => $s) {
					if (!is_null($s)) $setting[$key] = !is_array($s) ? $s : array_filter($s);
				}
			}
			
			if (!empty($request->id)) {
				$extensions['name'] = $request->name;
				$extensions['code'] = $this->slug;
				$extensions['setting'] = $setting;
				$extensions['status'] = $request->status ? $request->status : 0;
				
				Extensions::where('id', $request->id)->update($extensions);
			} else {
				$extensions = new Extensions;
				$extensions->name = $request->name;
				$extensions->code = $this->slug;
				$extensions->setting = $setting;
				$extensions->status = $request->status ? $request->status : 0;
				
				$extensions->save();
			}
			
			return 'Модуль ' . $this->title . ' успешно изменен';
		}
	}
