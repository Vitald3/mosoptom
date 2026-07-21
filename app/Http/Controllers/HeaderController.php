<?php
	
	namespace App\Http\Controllers;
	
	use App\Models\Categories;
	use App\Models\Cart;
	use App\Models\Extensions;
	use App\Models\Customers;
	use App\Models\CustomerWishlist;
	use App\Models\Languages;
	use App\Http\Controllers\Extensions\Module\MenuController;
	use App\Http\Controllers\Extensions\Module\AttentionController;
	use Illuminate\Support\Facades\Cache;
	use Illuminate\Support\Facades\Route;
	
	class HeaderController extends Controller {
		private $meta = [];
		private $styles = [];
		private $links = [];
		private $scripts = [];
		public $settings = [];
		private $breadcrumb = '';
		private $robots = false;
		private $region = [];
		public $lang = '';
		public $langs = [];
		
		public function __construct() {
			$this->settings = session('settings');
			$this->default_language = session('default_language');
			$this->lang = session('lang');
			$this->langs = session('languages');
		}
		
		public function setMeta($meta = []) {
			$this->meta = $meta;
		}
		
		public function setRobots($robots) {
			$this->robots = $robots;
		}
		
		public function getRobots() {
			return $this->robots;
		}
		
		public function getLangs() {
			return $this->langs;
		}
		
		public function setStyle($style) {
			if (!empty($style)) $this->styles[] = $style;
		}
		
		public function getStyle() {
			return !empty($this->styles) ? implode('', $this->styles) : '';
		}
		
		public function setLinkStyle($links) {
			if (!empty($links)) {
				foreach ((array)$links as $link) {
					if (!in_array($link, $this->links)) {
						$this->links[] = '<link rel="' . $link['rel'] . '" href="' . $link['href'] . '" />';
					}
				}
			}
		}
		
		public function getLinkStyle() {
			$links = '';
			$links_array = [];
			
			foreach ((array)$this->links as $link) {
				if (!in_array($link, $links_array)) {
					$links_array[] = $link;
					$links .= $link;
				}
			}
			
			return !empty($this->links) ? implode('', array_unique($this->links)) : '';
		}
		
		public function getBreadcrumbs() {
			return $this->breadcrumb;
		}
		
		public function setLinkData($links) {
			if (!empty($links)) {
				foreach ((array)$links as $link) {
					$link = '<link rel="' . $link['rel'] . '" href="' . $link['href'] . '" />';
					
					if (!in_array($link, $this->links))
					$this->links[] = $link;
				}
			}
		}
		
		public function setScript($script) {
			if (!empty($script)) $this->scripts[] = $script;
		}
		
		public function getScript() {
			return !empty($this->scripts) ? implode('', $this->scripts) : '';
		}
		
		public function setBreadcrumbs($breadcrumbs = []) {
			$this->breadcrumb = $breadcrumbs;
		}
		
		public function data() {
			$settings = $this->settings;
			$PathRouteService = app(\App\Helpers\PathRouteService::class);
			
			$data['region'] = '';
			$region_code = config('app.region_code');
			
			if (request()->get('search')) {
				$data['search'] = urldecode(request()->get('search'));
			} else {
				$data['search'] = '';
			}
			
			$data['customer'] = session('customer');
			$data['wishlist_count'] = CustomerWishlist::getTotalWishlist();
			
			if (Cache::has('regions')) {
				$data['regions'] = Cache::get('regions');
				
				if (!empty($data['regions'][$region_code]['meta'][$this->lang]['name'])) {
					$data['region'] = $data['regions'][$region_code]['meta'][$this->lang]['name'];
				}
			} else {
				$data['regions'] = [];
			}
			
			$data['lang'] = $this->lang;
			
			$data['route'] = str_replace($this->lang . '_', '', Route::currentRouteName());
			$data['path'] = isset(Route::current()->parameters['path']) ? [Route::current()->parameters['path']] : [];
			
			if (!empty($settings['logo'])) {
				$data['logo'] = asset($settings['logo']);
			} else {
				$data['logo'] = asset('assets/site/img/Logo.png');
			}
			
			if (!empty($settings['favicon'])) {
				$data['favicon'] = asset($settings['favicon']);
			} else {
				$data['favicon'] = '';
			}
			
			if (!empty($settings['phone'])) {
				$data['phone'] = $settings['phone'];
			} else {
				$data['phone'] = '';
			}
			
			if (!empty($settings['phone2'])) {
				$data['phone2'] = $settings['phone2'];
			} else {
				$data['phone2'] = '';
			}
			
			if (!empty($settings['meta_title'][$this->lang])) {
				$data['name'] = nl2br($settings['name'][$this->lang]);
			} else {
				$data['name'] = __('locale.name');
			}
			
			if (!empty($settings['meta_title'][$this->lang])) {
				$data['open'] = $settings['open'][$this->lang];
			} else {
				$data['open'] = '';
			}
			
			if (!empty($this->meta['meta_title'])) {
				$data['meta_title'] = $this->meta['meta_title'];
			} else {
				$data['meta_title'] = '';
			}
			
			if (!empty($this->meta['meta_description'])) {
				$data['meta_description'] = $this->meta['meta_description'];
			} else {
				$data['meta_description'] = '';
			}
			
			if (!empty($this->meta['meta_keywords'])) {
				$data['meta_keywords'] = $this->meta['meta_keywords'];
			} else {
				$data['meta_keywords'] = '';
			}
			
			if (!empty($settings['whatsapp'])) {
				$data['whatsapp'] = $settings['whatsapp'];
			} else {
				$data['whatsapp'] = '';
			}
			
			if (!empty($settings['telegram'])) {
				$data['telegram'] = $settings['telegram'];
			} else {
				$data['telegram'] = '';
			}
			
			if (!empty($settings['viber'])) {
				$data['viber'] = $settings['viber'];
			} else {
				$data['viber'] = '';
			}
			
			if (!empty($settings['vk'])) {
				$data['vk'] = $settings['vk'];
			} else {
				$data['vk'] = '';
			}
			
			$data['categories'] = $this->getCategories();
			
			$items = new MenuController;
			$data['menu'] = $items->getModule(1);
			
			if ($style = $items->getHtmlStyle()) {
				$this->setStyle($style);
			}
			
			$data['pages_footer'] = $items->getModule(8);
			
			if ($style = $items->getHtmlStyle()) {
				$this->setStyle($style);
			}
			
			$attention = Extensions::getSettingModule('attention');
			
			if (!empty($attention)) {
				$data['attention'] = $attention['html'];
				
				if ($style = $attention['style']) {
					foreach ($style as $link) {
						$this->links[] = '<link rel="' . $link['rel'] . '" href="' . $link['href'] . '" />';
					}
				}
				
				if ($script = $attention['script']) {
					foreach ($script as $s) {
						if (isset($s['src'])) {
							$this->scripts[] = '<script src="' . $s['src'] . '"></script>';
						} else {
							$this->scripts[] = '<script>' . $s['text'] . '</script>';
						}
					}
				}
			}
			
			$data['about_us'] = $PathRouteService->getRoute('page_' . $this->lang . '_id=4');
			$data['contacts'] = $PathRouteService->getRoute('page_' . $this->lang . '_id=19');
			$data['garanty'] = $PathRouteService->getRoute('page_' . $this->lang . '_id=11');
			$data['review_link'] = $PathRouteService->getRoute('page_' . $this->lang . '_id=22');
			$data['minimal'] = $PathRouteService->getRoute('page_' . $this->lang . '_id=15');
			$data['sp'] = $PathRouteService->getRoute('page_' . $this->lang . '_id=8');
			$data['order_payment'] = $PathRouteService->getRoute('page_' . $this->lang . '_id=7');
			$data['delivery_link'] = $PathRouteService->getRoute('page_' . $this->lang . '_id=6');
			
			if (!empty($this->settings['policy'])) {
				$data['policy'] = $PathRouteService->getRoute('page_' . $this->lang . '_id=' . $this->settings['policy']);
			} else {
				$data['policy'] = false;
			}
			
			$data['oferta_link'] = $PathRouteService->getRoute('page_' . $this->lang . '_id=24');
			$data['soglashenie_link'] = $PathRouteService->getRoute('page_' . $this->lang . '_id=5');
			
			$this->setLinkStyle([
				[
					'href' => asset('assets/site/css/saleday.css'),
					'rel' => 'stylesheet'
				]
			]);
		
			$data['style'] = $this->getStyle();
			$data['links'] = $this->getLinkStyle();
			$data['scripts'] = $this->getScript();
			$data['robots'] = $this->getRobots();
			$data['base'] = route($this->default_language . ($region_code ? '_' . $region_code : '') . '_home');
			
			$data['languages'] = $this->langs;
			$data['breadcrumbs'] = $this->getBreadcrumbs();
			
			return $data;
		}
		
		public function getCategories() {
			$categories = [];
			
			$products_sale = \App\Models\Products::with('product_discount:product_id,price')
				->join('product_special as ps', 'ps.product_id', '=', 'products.id')
				->join('product_description as pd', 'pd.product_id', '=', 'products.id')
				->select('products.id', 'products.parent_id', 'products.price as price', 'ps.price as special', 'products.image', 'pd.name', 'ps.date_start', 'ps.date_end')
				->where('products.price', '!=', 'ps.price')
				->whereRaw("ps.customer_group_id = '" . (int)session('customer_group_id') . "' AND (ps.date_start < NOW() || ps.date_start = '0000-00-00') AND ps.date_end > NOW()")
				->where('pd.lang', config('app.locale'))
				->where('products.status', 1)
				->groupBy('parent_id')
			    ->get()->keyBy('parent_id');
			
			foreach (Categories::getCategories() as $category) {
				$products = [];
				
				if (isset($products_sale[$category->id])) {
					$discount = false;
					$product = $products_sale[$category->id];
					
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
					
					$products = [
						'id' => $product->id,
						'url' => $product->getSlug(),
						'name' => \Illuminate\Support\Str::limit($product->name, 40, '...'),
						'image' => resize_image($product->image, 190, 190),
						'price' => $price,
						'sale' => $sale,
						'discount' => $discount,
						'special' => $special
					];
				}
				
				$categories[] = [
					'id' => $category->id,
					'name' => $category->metaLang['name'],
					'image' => !empty($category['image2']) ? asset($category['image2']) : '',
					'image_menu' => !empty($category['image3']) ? asset($category['image3']) : '',
					'image_cat' => !empty($category['image']) ? asset($category['image']) : '',
					'url' => $category->getSlug(),
					'children' => $this->getSubCategories($category->children, $products_sale),
					'product' => $products
				];
			}
			
			return $categories;
		}
		
		private function getSubCategories($categories, $products_sale) {
			$data = [];
			
			foreach ($categories as $category) {
				$products = [];
				
				if (isset($products_sale[$category->id])) {
					$discount = false;
					$product = $products_sale[$category->id];
					
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
					
					$products = [
						'id' => $product->id,
						'url' => $product->getSlug(),
						'name' => \Illuminate\Support\Str::limit($product->name, 40, '...'),
						'image' => resize_image($product->image, 190, 190),
						'price' => $price,
						'sale' => $sale,
						'discount' => $discount,
						'special' => $special
					];
				}
				
				$data[] = [
					'id' => $category->id,
					'name' => $category->metaLang['name'],
					'image' => !empty($category['image2']) ? asset($category['image2']) : '',
					'image_menu' => !empty($category['image3']) ? asset($category['image3']) : '',
					'image_cat' => !empty($category['image']) ? asset($category['image']) : '',
					'url' => $category->getSlug(),
					'children' => $this->getSubCategories($category->children, $products_sale),
					'product' => $products
				];
			}
			
			return $data;
		}
	}