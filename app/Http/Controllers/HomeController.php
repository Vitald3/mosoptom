<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Customers;
	use App\Http\Controllers\CartController;
	use App\Http\Controllers\HeaderController;
	
	class HomeController extends Controller
	{
		public $region = [];
		
		public function __construct() {
			$this->settings = session('settings');
			$this->lang = session('lang');
			$this->region = session('region');
		}

		public function index()
		{
			$header = new HeaderController;
			
			$meta = [
				'meta_title' => $this->region['meta_title'],
				'meta_description' => $this->region['meta_description'],
				'meta_keywords' => $this->region['meta_keywords']
			];
			
			$header->setMeta($meta);
			
			if (!empty($this->settings) && isset($this->settings['main_layout_id'])) {
				$content = new GetContentController($this->settings['main_layout_id']);
				$data['content_top'] = $content->getPosition('top');
				$data['content_bottom'] = $content->getPosition('bottom');
				$header->setStyle($content->getHtmlStyle());
				$header->setLinkStyle($content->getLinkStyle());
				$header->setScript($content->getScript());
				$cart = new CartController;
				$data['cart'] = $cart->mini_cart($content->getModuleById('saleday'));
				$cart_count = $cart->getCount();
				$data['cart_count'] = $cart_count > 99 ? '99+' : $cart_count;
			}

			$region_code = config('app.region_code');
			$this->region['code'] = $region_code ? $region_code . '/' : '';
			$region_code = $region_code ? '_' . $region_code : '';
			
			$data = array_merge($data, $header->data());
			$data['class'] = 'home';
			$data['canonical'] = route($this->lang . $region_code . '_home');
			$data['title'] = $this->settings['name'][$this->lang];
			
			return render_view(view('pages.site.home', $data), $this->region, false);
		}
	}
