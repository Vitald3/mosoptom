<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	
	class ErrorController extends Controller
	{
		public function __construct() {
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			
			$classes = array('breadcrumb', 'breadcrumb-item');
			$this->breadcrumbs->addCssClasses($classes);
			$this->breadcrumbs->setDivider('');
			
			$this->settings = session('settings');
			$this->lang = session('lang');
			$this->breadcrumbs->addCrumb(__('locale.home'), route(session('route_url') . '_home'));
		}
		
		public function show(Request $request) {
			$header = new HeaderController($this);
			
			$meta = [
				'meta_title' => __('locale.404'),
				'meta_description' => '',
				'meta_keywords' => ''
			];
			
			$header->setMeta($meta);
			
			$content = new GetContentController(0);
			$data['content_top'] = $content->getPosition('top');
			$data['content_bottom'] = $content->getPosition('bottom');
			$header->setStyle($content->getHtmlStyle());
			$header->setLinkStyle($content->getLinkStyle());
			$header->setScript($content->getScript());
			$cart = new CartController;
			$data['cart'] = $cart->mini_cart($content->getModuleById('saleday'));
			$cart_count = $cart->getCount();
			$data['cart_count'] = $cart_count > 99 ? '99+' : $cart_count;
			
			$data = array_merge($data, $header->data());
			$data['class'] = 'error_body';
			$data['canonical'] = '';
			
			return response()->view('pages.error-404', $data, 404);
		}
	}