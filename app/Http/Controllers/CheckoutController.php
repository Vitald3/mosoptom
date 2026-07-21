<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Extensions;
	use App\Models\Orders;
	use App\Models\OrderProduct;
	use App\Models\OrderOption;
	use App\Models\OrderTotal;
	use Illuminate\Support\Facades\Route;
	
	class CheckoutController extends Controller
	{
		public $settings = [];
		private $breadcrumb = '';
		public $lang;
		
		public function __construct()
		{
			$this->settings = session('settings');
			$this->lang = session('lang');
			
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			
			$classes = array('breadcrumb', 'breadcrumb-item');
			$this->breadcrumbs->addCssClasses($classes);
			$this->breadcrumbs->setDivider('');
			$this->breadcrumbs->addCrumb(__('locale.home'), url(''));
			
			$this->region = session('region');
			$this->currency = session('currency');
		}
		
		public function index(Request $request) {
			$cart = new \App\Http\Controllers\CartController;
			
			if (!$cart->getProducts()) {
				return redirect()->back();
			}
			
			$header = new HeaderController;
			$header->setRobots('noindex, nofollow');
			$this->breadcrumbs->addCrumb(__('locale.text_checkout_title'), route(session('route_url') . '_checkout'));
			
			$meta = [
				'name' => __('locale.text_checkout_title'),
				'meta_title' => __('locale.text_checkout_title'),
				'meta_description' => '',
				'meta_keywords' => ''
			];
			
			$stylesheet[] = [
				'href' => asset('assets/site/css/checkout.css'),
				'rel' => 'stylesheet'
			];
			
			$stylesheet[] = [
				'href' => asset('assets/site/css/media/checkout.css'),
				'rel' => 'stylesheet'
			];
			
			$data['customer'] = session('customer');
			$data['title'] = __('locale.text_checkout_title');
			$data['canonical'] = false;
			$data['class'] = 'checkout';
			$header->setMeta($meta);
			
			$content = new GetContentController(0);
			$data['content_top'] = $content->getPosition('top');
			$data['content_bottom'] = $content->getPosition('bottom');
			$header->setStyle($content->getHtmlStyle());
			$header->setLinkStyle($content->getLinkStyle());
			$header->setScript($content->getScript());
			$header->setLinkData($stylesheet);
			$header->setBreadcrumbs($this->breadcrumbs->render());
			
			$data = array_merge($data, $header->data());
			$data['cart'] = $cart->mini_cart($content->getModuleById('saleday'));
			$data['totals'] = $cart->getTotals()['totals'];
			
			if ($cart->getprice() < 5000) {
				$data['warning'] = __('locale.text_minimal_price');
			} else {
				$data['warning'] = '';
			}
			
			$cart_count = $cart->getCount();
			$data['cart_count'] = $cart_count > 99 ? '99+' : $cart_count;
			$region_code = config('app.region_code');
			$this->region['code'] = $region_code ? $region_code . '/' : '';
			
			$shipping_methods = [];
			$payment_methods = [];
			
			$results = Extensions::getExtensions('shipping');
			
			foreach ($results as $result) {
				$module = '\App\Http\Controllers\Extensions\Shipping\\' . ucfirst($result['code']) . 'Controller';
				$module = new $module;
				
				$shipping_methods[$module->slug] = [
					'code' => $module->slug,
					'title' => $module->getTitle(),
					'required' => $module->getRequired(),
					'cost' => $module->cost($result['setting']),
					'fields' => session('shipping_fields.' . $module->slug, []),
					'html' => \View::exists('pages.site.extensions.shipping.' . $module->slug) ? view('pages.site.extensions.shipping.' . $module->slug, $result['setting'])->render() : ''
				];
			}
			
			session(['shipping_methods' => $shipping_methods]);
			$data['shipping_methods'] = $shipping_methods;
			
			$results = Extensions::getExtensions('payment');
			
			foreach ($results as $result) {
				$module = '\App\Http\Controllers\Extensions\Payment\\' . ucfirst($result['code']) . 'Controller';
				$module = new $module;
				
				$payment_methods[$module->slug] = [
					'code' => $module->slug,
					'title' => $module->getTitle(),
					'text' => $module->getText()
				];
			}
			
			session(['payment_methods' => $payment_methods]);
			$data['payment_methods'] = $payment_methods;
			
			if (!is_null(session('shipping_method'))) {
				$shipping_method = session('shipping_method.code');
			} else {
				$shipping_method = '';
			}
			
			$data['shipping_method_'] = $shipping_method;
			
			if (!is_null(session('payment_method'))) {
				$payment_method = session('payment_method.code');
			} else {
				$payment_method = '';
			}
			
			$data['payment_method_'] = $payment_method;
			
			$action = new \App\Http\Controllers\Extensions\Total\ActionController;
			$data['action'] = $action->index();
			
			return render_view(view('pages.site.checkout', $data), $this->region, false);
		}
		
		public function total_set(Request $request) {
			$json = [];
			
			if ($request->module && file_exists(base_path('app/Http/Controllers/Extensions/Total/' . ucfirst($request->module) . 'Controller.php'))) {
				$module = '\App\Http\Controllers\Extensions\Total\\' . ucfirst($request->module) . 'Controller';
				$module = new $module;
				$json['success'] = $module->setTotal($request);
			}
			
			return response()->json($json);
		}
		
		public function shipping(Request $request) {
			$json = [];
			
			if ($request->shipping_method) {
				if ($request->quote) {
					session(['shipping_fields.' . $request->shipping_method . '.' . $request->shipping_method => $request->quote]);
				}
				
				$shipping_methods = [];
				
				$results = Extensions::getExtensions('shipping');
				
				foreach ($results as $result) {
					$module = '\App\Http\Controllers\Extensions\Shipping\\' . ucfirst($result['code']) . 'Controller';
					$module = new $module;
					
					$shipping_methods[$module->slug] = [
						'code' => $module->slug,
						'title' => $module->getTitle(),
						'cost' => $module->cost($result['setting']),
						'fields' => session('shipping_fields.' . $module->slug, []),
						'html' => \View::exists('pages.site.extensions.shipping.' . $module->slug) ? view('pages.site.extensions.shipping.' . $module->slug, $result['setting'])->render() : ''
					];
				}
				
				session(['shipping_methods' => $shipping_methods]);
				
				if ($shipping_methods) {
					if (isset($shipping_methods[$request->shipping_method])) {
						session(['shipping_method' => $shipping_methods[$request->shipping_method]]);
						
						$cart = new \App\Http\Controllers\CartController;
						
						$json = [
							'products' => $cart->getProducts(),
							'totals' => $cart->getTotals()['totals']
						];
					} else {
						$json['error'] = 'Выбранный метод доставки не найден';
					}
				} else {
					$json['error'] = 'Методы доставки не найдены';
				}
			} else {
				$json['error'] = 'Выберите метод доставки';
			}
			
			return response()->json($json);
		}
		
		public function payment(Request $request) {
			$json = [];
			
			if ($request->payment_method) {
				if (!is_null(session('payment_methods'))) {
					$payment_methods = session('payment_methods');
					
					if (isset($payment_methods[$request->payment_method])) {
						session(['payment_method' => $payment_methods[$request->payment_method]]);
						
						$cart = new \App\Http\Controllers\CartController;
						
						$json = [
							'products' => $cart->getProducts(),
							'totals' => $cart->getTotals()['totals']
						];
					} else {
						$json['error'] = 'Выбранный метод оплаты не найден';
					}
				} else {
					$json['error'] = 'Методы оплаты не найдены';
				}
			} else {
				$json['error'] = 'Выберите метод оплаты';
			}
			
			return response()->json($json);
		}
		
		public function save(Request $request) {
			$json = [];
			
			$cart = new \App\Http\Controllers\CartController;
			
			if (!$cart->getProducts()) {
				return response()->json(['redirect' => url('')]);
			}
			
			if ($cart->getprice() < 5000) {
				return response()->json(['error' => __('locale.text_minimal_price')]);
			}
			
			$shipping_required = session('shipping_methods.' . $request->shipping_method . '.required', []);
			
			if ($request->type == 0) {
				$validate = \Validator::make($request->all(), array_merge($shipping_required, [
					'shipping_method' => 'required',
					'payment_method' => 'required',
					'firstname' => 'required',
					'lastname' => 'required',
					'email' => 'required|email',
					'phone' => 'required'
				]));
				
				$firstname = $request->firstname;
				$lastname = $request->lastname;
				$email = $request->email;
				$phone = $request->phone;
				$inn = '';
				$company = '';
			} else {
				$validate = \Validator::make($request->all(), array_merge($shipping_required, [
					'shipping_method' => 'required',
					'payment_method' => 'required',
					'legal.firstname' => 'required',
					'legal.lastname' => 'required',
					'legal.email' => 'required|email',
					'legal.phone' => 'required',
					'legal.inn' => 'required',
					'legal.company' => 'required',
					$shipping_required
				]));
				
				if (!$validate->fails()) {
					$firstname = $request->legal['firstname'];
					$lastname = $request->legal['lastname'];
					$email = $request->legal['email'];
					$phone = $request->legal['phone'];
					$inn = $request->legal['inn'];
					$company = $request->legal['company'];
				}
			}
			
			if ($validate->fails()) {
				$errors = [];
				
				foreach ($validate->errors()->messages() as $key => $error) {
					if (strpos($key, '.') !== false) {
						$key = str_replace('.', '[', $key) . ']';
					}
					
					$errors[$key] = $error;
				}
				
				return response()->json(['errors' => $errors]);
			} else {
				$products = $cart->getProducts();
				$totals = $cart->getTotals();
				
				$order = new Orders;
				$order->firstname = $firstname;
				$order->lastname = $lastname;
				$order->phone = $phone;
				$order->email = $email;
				$order->type = $request->type;
				$order->customer_id = session('customer_id', null);
				$order->customer_group_id = session('customer_group_id', 0);
				$order->order_status_id = null;
				$order->currency_code = session('currency_code');
				$order->lang = session('lang');
				$order->currency_id = session('currency')['id'];
				$order->currency_value = session('currency')['value'];
				$order->total = $totals['total'];
				$order->fields = isset($request->{$request->shipping_method}) ? $request->{$request->shipping_method} : [];
				$order->inn = $inn;
				$order->company = $company;
				$order->shipping_method = $request->shipping_method . (isset($request->{$request->shipping_method}[$request->shipping_method]) ? '.' . $request->{$request->shipping_method}[$request->shipping_method] : '');
				$order->shipping_title = session('shipping_methods.' . $request->shipping_method . '.title');
				$order->payment_method = $request->payment_method;
				$order->payment_title = session('payment_methods.' . $request->payment_method . '.title');
				$order->comment = '';
				$order->ip = $request->ip();
				
				$order->save();
				
				foreach ($products as $product) {
					$op = new OrderProduct;
					$op->order_id = $order->id;
					$op->product_id = $product['id'];
					$op->quantity = $product['quantity'];
					$op->reward = $product['reward'];
					$op->name = $product['name'];
					$op->model = $product['model'];
					$op->price = $product['price_int'];
					$op->total = $product['total_int'];
					
					$op->save();
					
					foreach ($product['options'] as $option) {
						if ($option['values']) {
							foreach ($option['values'] as $value) {
								$oo = new OrderOption;
								$oo->order_id = $order->id;
								$oo->order_product_id = $op->id;
								$oo->option_id = $option['id'];
								$oo->product_option_id = $option['product_option_id'];
								$oo->product_option_value_id = $value['id'];
								$oo->name = $option['name'];
								$oo->value = $value['value'];
								$oo->type = $option['type'];
								
								$oo->save();
							}
						} else {
							$oo = new OrderOption;
							$oo->order_id = $order->id;
							$oo->order_product_id = $op->id;
							$oo->option_id = $option['id'];
							$oo->product_option_id = $option['product_option_id'];
							$oo->product_option_value_id = null;
							$oo->name = $option['name'];
							$oo->value = $option['value'];
							$oo->type = $option['type'];
							
							$oo->save();
						}
					}
				}
				
				foreach ($totals['totals'] as $total) {
					$ot = new OrderTotal;
					$ot->order_id = $order->id;
					$ot->code = $total['code'];
					$ot->title = $total['title'];
					$ot->value = $total['value_int'];
					$ot->sort_order = $total['sort_order'];
					
					$ot->save();
				}
				
				$request->session()->put(['order_id' => $order->id]);
				
				$payment = '\App\Http\Controllers\Extensions\Payment\\' . ucfirst($request->payment_method) . 'Controller';
				$payment = new $payment;
				
				if (!property_exists($payment,'index')) {
					$payment->confirm();
					$json['redirect'] = route(session('route_url') . '_checkout_success');
				} else {
					$json['payment'] = $payment->index();
				}
				
				return response()->json($json);
			}
		}
		
		public function success(Request $request) {
			if ($request->session()->has('order_id')) {
				$data['order_id'] = $request->session()->get('order_id');
				$data['order_totals'] = OrderTotal::select('code', 'title', 'value')->where('order_id', $request->session()->get('order_id'))->get();
				
				$request->session()->forget('order_id');
				$request->session()->forget('coupon');
				$request->session()->forget('reward');
				$request->session()->forget('action_product');
				$request->session()->forget('shipping_method');
				$request->session()->forget('shipping_methods');
				$request->session()->forget('payment_method');
				$request->session()->forget('payment_methods');
				
				$stylesheet[] = [
					'href' => asset('assets/site/css/checkout.css'),
					'rel' => 'stylesheet'
				];
				
				$stylesheet[] = [
					'href' => asset('assets/site/css/media/checkout.css'),
					'rel' => 'stylesheet'
				];
				
				$cart = new \App\Http\Controllers\CartController;
				$cart->clear();
				$header = new HeaderController;
				$header->setRobots('noindex, nofollow');
				$this->breadcrumbs->addCrumb(__('locale.text_checkout_success'), route(session('route_url') . '_checkout_success'));
				
				$meta = [
					'name' => __('locale.text_checkout_success'),
					'meta_title' => __('locale.text_checkout_success'),
					'meta_description' => '',
					'meta_keywords' => ''
				];
				
				$data['customer'] = session('customer');
				$data['title'] = __('locale.text_checkout_success');
				$data['canonical'] = false;
				$data['class'] = 'checkout_success';
				$header->setMeta($meta);
				
				$content = new GetContentController(0);
				$data['content_top'] = $content->getPosition('top');
				$data['content_bottom'] = $content->getPosition('bottom');
				$header->setStyle($content->getHtmlStyle());
				$header->setLinkStyle($content->getLinkStyle());
				$header->setScript($content->getScript());
				$header->setLinkData($stylesheet);
				$header->setBreadcrumbs($this->breadcrumbs->render());
				
				$data = array_merge($data, $header->data());
				$data['cart'] = $cart->mini_cart($content->getModuleById('saleday'));
				$data['totals'] = $cart->getTotals();
				$data['totals'] = isset($data['totals']['totals']) ? $data['totals']['totals'] : [];
				$data['cart_count'] = 0;
				$region_code = config('app.region_code');
				$this->region['code'] = $region_code ? $region_code . '/' : '';
				
				return render_view(view('pages.site.checkout_success', $data), $this->region);
			} else {
				return redirect('');
			}
		}
	}