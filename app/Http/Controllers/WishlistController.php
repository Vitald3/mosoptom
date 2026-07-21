<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Products;
	use App\Models\CustomerWishlist;
	
	class WishlistController extends Controller
	{
		private $settings = [];
		private $products = [];
		private $count = 0;
		
		public function __construct() {
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			$this->breadcrumbs->addCssClasses(array('breadcrumb', 'breadcrumb-item'));
			$this->breadcrumbs->setDivider('');
			$this->breadcrumbs->addCrumb(__('locale.home'), route(session('route_url') . '_home'));
			
			$this->settings = session('settings');
			$this->lang = session('lang');
			$this->region = session('region');
		}

		public function getCount() {
			$count = count(CustomerWishlist::getWishlist());
			
			return $count ? $count : '';
		}
		
		public function remove(Request $request) {
			$json = [];
			
			if ($request->product_id) {
				CustomerWishlist::remove($request);
				
				$json['count'] = (int)$this->getCount() - 1;
			} else {
				$json['error'] = 'Error';
			}
			
			return response()->json($json);
		}
		
		public function getWishlistIds(Request $request) {
			$json = CustomerWishlist::getWishlist();
			
			return response()->json($json);
		}
		
		public function add(Request $request) {
			$json = [];
			
			if ($request->product_id) {
				$product = Products::join('product_description as pd', 'pd.product_id', '=', 'products.id')->select('products.id')->where('products.id', $request->product_id)->where('pd.lang', session('lang'))->first();
				
				if (!empty($product)) {
					CustomerWishlist::add($request);
					$json['count'] = (int)$this->getCount() + 1;
				} else {
					$json['error'] = __('locale.text_product_empty');
				}
			} else {
				$json['error'] = 'Error';
			}
			
			return response()->json($json);
		}
		
		public function index(Request $request) {
			$header = new HeaderController;
			
			if ($request->page) {
				$page = $request->page;
			} else {
				$page = 1;
			}
			
			$meta = [
				'meta_title' => __('locale.text_wishlist'),
				'meta_description' => '',
				'meta_keywords' => ''
			];
			
			$header->setMeta($meta);
			
			$this->breadcrumbs->addCrumb(__('locale.text_wishlist'), route(session('route_url') . '_wishlist'));
			$header->setBreadcrumbs($this->breadcrumbs->render());
			
			$content = new GetContentController(7);
			$data['content_top'] = $content->getPosition('top');
			$data['content_bottom'] = $content->getPosition('bottom');
			$header->setStyle($content->getHtmlStyle());
			$header->setLinkStyle($content->getLinkStyle());
			$header->setScript($content->getScript());
			$cart = new CartController;
			$data['cart'] = $cart->mini_cart($content->getModuleById('saleday'));
			$cart_count = $cart->getCount();
			$data['cart_count'] = $cart_count > 99 ? '99+' : $cart_count;
			$region_code = config('app.region_code');
			$this->region['code'] = $region_code ? $region_code . '/' : '';
			
			$data = array_merge($data, $header->data());
			$data['class'] = 'wishlist';
			$data['canonical'] = '';
			$header->setRobots('noindex, nofollow');
			$data['title'] = __('locale.text_wishlist');
			
			$product_ids = [];
			
			foreach (CustomerWishlist::getWishlist() as $wishlist) {
				$product_ids[] = $wishlist['product_id'];
			}
			
			$data['products'] = Products::with([
				'product_special_one:product_id,price',
				'product_discount_one:product_id,price',
			])
				->join('product_description as pd', 'pd.product_id', '=', 'products.id')
				->select('products.id', 'products.price', 'products.model', 'products.image', 'pd.name')
				->where('products.status', 1)
				->whereIn('products.id', $product_ids)
				->where('pd.lang', $this->lang)
				->paginate(session('settings.limit_sait', 25), ['*'], 'page', $page);
			
			return render_view(view('pages.site.wishlist', $data), $this->region, false);
		}
	}