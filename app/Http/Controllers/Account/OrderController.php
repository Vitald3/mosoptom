<?php
	
	namespace App\Http\Controllers\Account;
	
	use Illuminate\Http\Request;
	use App\Http\Controllers\Controller;
	use App\Http\Controllers\CartController;
	use App\Http\Controllers\HeaderController;
	use App\Http\Controllers\GetContentController;
	use App\Models\Orders;
	
	class OrderController extends Controller
	{
		public function __construct()
		{
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			$this->breadcrumbs->addCssClasses(array('breadcrumb', 'breadcrumb-item'));
			$this->breadcrumbs->setDivider('');
			$this->breadcrumbs->addCrumb(__('locale.home'), route(session('route_url') . '_home'));
			
			$this->settings = session('settings');
			$this->lang = session('lang');
			$this->region = session('region');
		}
		
		public function index(Request $request) {
			$header = new HeaderController;
			
			if ($request->page) {
				$page = $request->page;
			} else {
				$page = 1;
			}
			
			if ($request->status) {
				$status = $request->status;
			} else {
				$status = 1;
			}
			
			$data['status'] = $status;
			
			if ($status == 2) {
				$sort = 'created_at';
				$order = 'asc';
			} else {
				$sort = 'created_at';
				$order = 'desc';
			}
			
			$data['page'] = $page;
			
			$meta = [
				'meta_title' => __('locale.text_account_order'),
				'meta_description' => '',
				'meta_keywords' => ''
			];
			
			$header->setMeta($meta);
			
			$this->breadcrumbs->addCrumb(__('locale.text_account_order'), route(session('route_url') . '_account_order'));
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
			$data['class'] = 'account_form_of_service';
			$data['canonical'] = '';
			$header->setRobots('noindex, nofollow');
			$data['title'] = __('locale.text_account_order');
			
			$data['orders'] = Orders::with('products:order_id,quantity')
				->join('status as s', 's.id', '=', 'orders.order_status_id')
				->join('status_description as sd', 'sd.status_id', '=', 's.id')
				->select('orders.id', 'orders.order_status_id', 'orders.created_at', 'orders.total', 'orders.shipping_title', 'sd.name as status', 's.color')
				->where(function($query) use($status) {
					$query->where('orders.customer_id', session('customer_id'))->where('sd.lang', $this->lang);
					
					if (!in_array($status, [1, 2])) {
						$query->where('orders.order_status_id', $status);
					}
				})
				->orderBy($sort, $order)
				->paginate(session('settings.limit_sait', 25), ['*'], 'page', $page);
		
			return render_view(view('pages.site.account.order_list', $data), $this->region, false);
		}
		
		public function info(Request $request) {
			$header = new HeaderController;
			
			$meta = [
				'meta_title' => sprintf(__('locale.text_account_order_info'), $request->id),
				'meta_description' => '',
				'meta_keywords' => ''
			];
			
			$header->setMeta($meta);
			
			$this->breadcrumbs->addCrumb(sprintf(__('locale.text_account_order_info'), $request->id), route(session('route_url') . '_account_order'));
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
			$data['class'] = 'account_form_of_service';
			$data['canonical'] = '';
			$header->setRobots('noindex, nofollow');
			$data['title'] = sprintf(__('locale.text_account_order_info'), $request->id);
			
			$data['order'] = Orders::with([
				'products' => function($query) {
					$query->join('products as p', 'p.id', '=', 'order_product.product_id')->select('order_product.order_id', 'order_product.name', 'order_product.quantity', 'order_product.price', 'order_product.total', 'p.image');
				},
				'history' => function($query) {
					$query->select('order_id', 'comment')->where('comment', '<>', '');
				},
			])
				->join('status as s', 's.id', '=', 'orders.order_status_id')
				->join('status_description as sd', 'sd.status_id', '=', 's.id')
				->select('orders.id', 'orders.order_status_id', 'orders.created_at', 'orders.total', 'orders.shipping_title', 'sd.name as status', 's.color')
				->where('orders.customer_id', session('customer_id'))
				->where('orders.id', $request->id)
				->firstOrFail();
			
			return render_view(view('pages.site.account.order_info', $data), $this->region, false);
		}
	}