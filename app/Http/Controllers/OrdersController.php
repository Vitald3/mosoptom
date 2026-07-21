<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Orders;
	use App\Models\OrderProduct;
	use App\Models\OrderOption;
	use App\Models\OrderTotal;
	use App\Models\Cart;
	use App\Models\Currencies;
	use App\Models\CustomerGroups;
	use App\Models\Status;
	use App\Models\Extensions;
	use App\Models\Languages;
	use Illuminate\Support\Facades\Cache;
	
	class OrdersController extends Controller
	{
		private $currency = [];
		
		public function __construct() {
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			
			$classes = array('breadcrumb', 'breadcrumb-item');
			$this->breadcrumbs->addCssClasses($classes);
			$this->breadcrumbs->setDivider('');
			
			$this->breadcrumbs->addCrumb(__('locale.home'), url('admin'));
			
			$this->settings = session('settings');
			$this->lang = session('lang');
			
			$this->params_array = request()->query();
			$params = [];
			
			if (!empty($this->params_array)) {
				foreach ($this->params_array as $key => $param) {
					$params[] = $key . '=' . $param;
				}
			}
			
			$this->params = !empty($this->params) ? '?' . implode('&', $params) : '';
			
			if (Cache::has('currencies')) {
				$this->currency = Cache::get('currencies');
			}
		}
		
		public function index(Request $request){
			$where = [];
			
			if (!is_null($request->status)) {
				$where[] = ['order_status_id', '=', $request->status];
				$status = $request->status;
			} else {
				$status = '';
			}
			
			if (!is_null($request->id)) {
				$where[] = ['id', '=', $request->id];
				$id = $request->id;
			} else {
				$id = '';
			}
			
			if (!is_null($request->created_at)) {
				$created_at = $request->created_at;
			} else {
				$created_at = '';
			}
			
			if (!is_null($request->customer)) {
				$customer = $request->customer;
			} else {
				$customer = '';
			}
			
			if ($request->sort) {
				$sort = $request->sort;
			} else {
				$sort = 'created_at';
			}
			
			if ($request->order) {
				$order = $request->order;
			} else {
				$order = 'desc';
			}
			
			$limit = session('settings.limit', 25);
			
			$sort_customer = url('admin/orders', ['sort' => 'customer', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_id = url('admin/orders', ['sort' => 'id', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_total = url('admin/orders', ['sort' => 'total', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_created_at = url('admin/orders', ['sort' => 'created_at', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_status = url('admin/orders', ['sort' => 'order_status_id', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			
			if (in_array($sort, ['customer', 'order_status_id', 'id', 'total', 'created_at'])) {
				$orders = Orders::selectRaw("id, concat(firstname, ' ', lastname) as customer, customer_id, order_status_id, created_at, total")
					->where($where)
					->where(function($query) use($customer) {
						if ($customer) {
							$query->where('firstname', 'like', '%' . $customer . '%')->Orwhere('lastname', 'like', '%' . $customer . '%');
						}
					})
					->where(function($query) use($created_at) {
						if ($created_at) {
							$query->whereRaw("date(created_at) = date('" . $created_at . "')");
						}
					})
					->orderBy($sort, $order)
					->paginate($limit);
			} else {
				$orders = Orders::selectRaw("id, concat(firstname, ' ', lastname) as customer, customer_id, order_status_id, created_at, total")
					->where($where)
					->where(function($query) use($customer) {
						if ($customer) {
							$query->where('firstname', 'like', '%' . $customer . '%')->Orwhere('lastname', 'like', '%' . $customer . '%');
						}
					})
					->where(function($query) use($created_at) {
						if ($created_at) {
							$query->whereRaw("date(created_at) = date('" . $created_at . "')");
						}
					})
					->orderBy('orders.created_at')
					->paginate($limit);
			}
			
			$this->breadcrumbs->addCrumb('Заказы', url('admin/orders') . $this->params);
			$breadcrumbs = $this->breadcrumbs->render();
			$statuses = Status::join('status_description as st', 'st.status_id', '=', 'status.id')->select('status.id', 'st.name')->where('st.lang', $this->lang)->where('status.type', 1)->get()->keyBy('id');
			$params = $this->params;
			$params_array = $this->params_array;
			
			return view('pages.orders', compact('params', 'params_array', 'breadcrumbs', 'statuses', 'created_at', 'sort_created_at', 'sort_customer', 'sort_status', 'sort_id', 'orders', 'sort_total', 'customer', 'id', 'status', 'sort', 'order'));
		}
		
		public function add() {
			$this->breadcrumbs->addCrumb('Заказы', url('admin/orders') . $this->params);
			$this->breadcrumbs->addCrumb('Создать', url('admin/order_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			$currencies = Currencies::select('title', 'id', 'code')->orderBy('title')->where('status', 1)->get();
			$customer_groups = CustomerGroups::join('customer_group_description as cgd', 'cgd.customer_group_id', '=', 'customer_groups.id')->select('customer_groups.id', 'cgd.name')->where('customer_groups.status', 1)->orderBy('cgd.name')->get();
			
			$cart = new \App\Http\Controllers\ApiController;
			$products = $cart->getProducts();
			
			if ($products) {
				$totals = $cart->getTotals()['totals'];
			} else {
				$totals = [];
			}
			
			$coupon = session('coupon');
			$reward = session('reward');
			$shipping_methods = [];
			$payment_methods = [];
			
			$results = Extensions::getExtensions('shipping');
			
			foreach ($results as $result) {
				$module = '\App\Http\Controllers\Extensions\Shipping\\' . ucfirst($result['code']) . 'Controller';
				$module = new $module;
				
				$shipping_methods[$module->slug] = [
					'code' => $module->slug,
					'title' => $module->getTitle(),
					'cost' => $module->cost($result['setting']),
					'quote' => $module->quote($result['setting'])
				];
			}
			
			session(['shipping_methods' => $shipping_methods]);
			
			$results = Extensions::getExtensions('payment');
			
			foreach ($results as $result) {
				$module = '\App\Http\Controllers\Extensions\Payment\\' . ucfirst($result['code']) . 'Controller';
				$module = new $module;
				
				$payment_methods[$module->slug] = [
					'code' => $module->slug,
					'title' => $module->title,
				];
			}
			
			session(['payment_methods' => $payment_methods]);
			
			if (!is_null(session('shipping_method'))) {
				$shipping_method = session('shipping_method.code');
			} else {
				$shipping_method = '';
			}
			
			if (!is_null(session('payment_method'))) {
				$payment_method = session('payment_method.code');
			} else {
				$payment_method = '';
			}
			
			$statuses = Status::join('status_description as st', 'st.status_id', '=', 'status.id')->select('status.id', 'st.name')->where('st.lang', $this->lang)->where('status.type', 1)->get()->keyBy('id');
			
			return view('pages.order-edit', ['statuses' => $statuses, 'payment_method' => $payment_method, 'shipping_method' => $shipping_method, 'payment_methods' => $payment_methods, 'shipping_methods' => $shipping_methods, 'coupon' => $coupon, 'reward' => $reward, 'products' => $products, 'totals' => $totals, 'customer_groups' => $customer_groups, 'breadcrumbs' => $breadcrumbs, 'currencies' => $currencies, 'type' => old('type'), 'company' => old('company'), 'inn' => old('inn'), 'fields' => (array)old('fields'), 'customer' => old('customer'), 'currency_code' => old('currency_code'), 'customer_id' => old('customer_id'), 'customer_group_id' => old('customer_group_id'), 'firstname' => old('firstname'), 'lastname' => old('lastname'), 'email' => old('email'), 'phone' => old('phone'), 'comment' => old('comment'), 'order_status_id' => old('order_status_id'), 'action' => asset('admin/order_save') . $this->params, 'id' => '']);
		}
		
		public function edit($id)
		{
			$data = Orders::with('customer:id,firstname,lastname')->where('id', $id)->first();
			
			if (!empty($data)) {
				extract($data->toArray());
				$action = asset('admin/order_save') . $this->params;
				
				$this->breadcrumbs->addCrumb('Заказы', url('admin/orders') . $this->params);
				$this->breadcrumbs->addCrumb('Редактировать', url('admin/order/' . $id));
				$breadcrumbs = $this->breadcrumbs->render();
				
				$currencies = Currencies::select('title', 'id', 'code')->orderBy('title')->where('status', 1)->get();
				$customer_groups = CustomerGroups::join('customer_group_description as cgd', 'cgd.customer_group_id', '=', 'customer_groups.id')->select('customer_groups.id', 'cgd.name')->where('customer_groups.status', 1)->orderBy('cgd.name')->get();
				
				$cart = new \App\Http\Controllers\ApiController;
				$products = $cart->getProducts();
				
				if ($products) {
					$totals = $cart->getTotals()['totals'];
				} else {
					$data->load([
						'products' => function ($query) {
							$query->with([
								'options' => function($query) {
									$query->with('product_option_values:id,quantity')->select('order_id', 'order_product_id', 'option_id', 'product_option_id', 'product_option_value_id', 'name', 'value', 'type');
								}
							])->select('id', 'order_id', 'product_id', 'price', 'total', 'reward', 'model', 'name', 'quantity');
						},
						'totals:order_id,code,title,value'
					]);
					
					$products = [];
					$totals = [];
					$currency_code = session('currency_code', $this->settings['currency_code']);
					
					$currency = $this->currency[$currency_code];
					
					if (!$data->products->isEmpty()) {
						foreach ($data->products as $product) {
							$options = [];
							
							if (!$product->options->isEmpty()) {
								foreach ($product->options as $option) {
									$options[] = [
										'option_id' => $option->option_id,
										'product_option_id' => $option->product_option_id,
										'product_option_value_id' => $option->product_option_value_id,
										'type' => $option->type,
										'name' => $option->name,
										'value' => $option->value
									];
								}
							}
							
							$products[] = [
								'product_id' => $product->product_id,
								'quantity' => $product->quantity,
								'name' => $product->name,
								'model' => $product->model,
								'reward' => $product->reward,
								'price_int' => $product->price,
								'total_int' => $product->total,
								'price' => format_price($product->price, $currency),
								'total' => format_price($product->total, $currency),
								'options' => $options
							];
						}
					}
					
					if (!$data->totals->isEmpty()) {
						foreach ($data->totals as $total) {
							$totals[] = [
								'code' => $total['code'],
								'title' => $total['title'],
								'value_int' => $total['value'],
								'value' => format_price($total['value'], $currency)
							];
						}
					}
				}
				
				$customer = isset($customer) ? $customer['firstname'] . ' ' . $customer['lastname'] : '';
				$coupon = session('coupon');
				$reward = session('reward');
				$shipping_methods = [];
				$payment_methods = [];
				
				$results = Extensions::getExtensions('shipping');
				
				foreach ($results as $result) {
					$module = '\App\Http\Controllers\Extensions\Shipping\\' . ucfirst($result['code']) . 'Controller';
					$module = new $module;
					
					$shipping_methods[$module->slug] = [
						'code' => $module->slug,
						'title' => $module->getTitle(),
						'cost' => $module->cost($result['setting']),
						'quote' => $module->quote($result['setting'])
					];
				}
				
				session(['shipping_methods' => $shipping_methods]);
				
				$results = Extensions::getExtensions('payment');
				
				foreach ($results as $result) {
					$module = '\App\Http\Controllers\Extensions\Payment\\' . ucfirst($result['code']) . 'Controller';
					$module = new $module;
					
					$payment_methods[$module->slug] = [
						'code' => $module->slug,
						'title' => $module->title,
					];
				}
				
				session(['payment_methods' => $payment_methods]);
				
				if (session('shipping_method')) {
					$shipping_method_ = explode('.', $shipping_method);
					
					$shipping_method = session('shipping_method.code') . (isset($shipping_method_[1]) ? '.' . $shipping_method_[1] : '');
				}
				
				if (session('payment_method')) {
					$payment_method = session('payment_method.code');
				}
				
				$statuses = Status::join('status_description as st', 'st.status_id', '=', 'status.id')->select('status.id', 'st.name')->where('st.lang', $this->lang)->where('status.type', 1)->get()->keyBy('id');
				
				return view('pages.order-edit', compact('statuses', 'type', 'company', 'inn', 'fields', 'payment_method', 'shipping_method', 'payment_methods', 'shipping_methods', 'coupon', 'reward', 'products', 'totals', 'customer_groups', 'breadcrumbs', 'currencies', 'customer', 'currency_code', 'customer_id', 'customer_group_id', 'firstname', 'lastname', 'email', 'phone', 'comment', 'order_status_id', 'comment', 'action', 'id'));
			} else {
				return redirect('admin/orders' . $this->params)->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete(Request $request) {
			if ($request->selected) {
				foreach ($request->selected as $s) {
					Orders::where('id', $s)->delete();
					OrderProduct::where('order_id', $s)->delete();
					OrderOption::where('order_id', $s)->delete();
					OrderTotal::where('order_id', $s)->delete();
					Cart::where('session_id', csrf_token())->delete();
				}
				
				$message = 'Операция успешна';
				$type = 'success';
			} else {
				$message = 'Выделите пункты для удаления';
				$type = 'error';
			}
			
			return redirect('admin/orders' . $this->params)->with($type, $message);
		}
		
		public function save(Request $request) {
			$this->validate($request, [
				'shipping_method' => 'required',
				'payment_method' => 'required',
				'firstname' => 'required',
				'lastname' => 'required',
				'email' => 'required|email',
				'phone' => 'required',
				'product.*' => 'required',
				'order_status_id' => 'required|integer',
				'currency_code' => 'required',
				'type' => 'required'
			]);
			
			$cart = new \App\Http\Controllers\ApiController($this->settings);
			$products = $cart->getProducts();
			$totals = $cart->getTotals();
			
			$shipping_method = explode('.', $request->shipping_method);
			
			if (isset($shipping_method[1])) {
				$shipping_method = $shipping_method[0];
			} else {
				$shipping_method = $request->shipping_method;
			}
			
			if (!is_null($request->id)) {
				$order_id = $request->id;
				OrderProduct::where('order_id', $request->id)->delete();
				OrderOption::where('order_id', $request->id)->delete();
				
				foreach ($products as $product) {
					$op = new OrderProduct;
					$op->order_id = $request->id;
					$op->product_id = $product['product_id'];
					$op->quantity = $product['quantity'];
					$op->reward = $product['reward'];
					$op->name = $product['name'];
					$op->model = $product['model'];
					$op->price = $product['price_int'];
					$op->total = $product['total_int'];
					
					$op->save();
					
					foreach ($product['options'] as $option) {
						$oo = new OrderOption;
						$oo->order_id = $request->id;
						$oo->order_product_id = $op->id;
						$oo->option_id = $option['option_id'];
						$oo->product_option_id = $option['product_option_id'];
						$oo->product_option_value_id = $option['product_option_value_id'];
						$oo->name = $option['name'];
						$oo->value = $option['value'];
						$oo->type = $option['type'];
						
						$oo->save();
					}
				}
				
				OrderTotal::where('order_id', $request->id)->delete();
				
				foreach ($totals['totals'] as $total) {
					$ot = new OrderTotal;
					$ot->order_id = $request->id;
					$ot->code = $total['code'];
					$ot->title = $total['title'];
					$ot->value = $total['value_int'];
					$ot->sort_order = $total['sort_order'];
					
					$ot->save();
				}
				
				$order['firstname'] = $request->firstname;
				$order['lastname'] = $request->lastname;
				$order['phone'] = $request->phone;
				$order['email'] = $request->email;
				$order['type'] = $request->type;
				$order['fields'] = $request->fields ? $request->fields : [];
				$order['customer_id'] = $request->customer_id ? $request->customer_id : null;
				$order['company'] = $request->company ? $request->company : '';
				$order['inn'] = $request->inn ? $request->inn : '';
				$order['customer_group_id'] = $request->customer_group_id ? $request->customer_group_id : 0;
				$order['currency_code'] = $request->currency_code;
				$order['lang'] = $this->lang;
				$order['currency_id'] = $this->currency[$request->currency_code]['id'];
				$order['currency_value'] = $this->currency[$request->currency_code]['value'];
				$order['total'] = $totals['total'];
				$order['shipping_method'] = $shipping_method;
				$order['shipping_title'] = $request->session()->get('shipping_methods.' . $shipping_method . '.title');
				$order['payment_method'] = $request->payment_method;
				$order['payment_title'] = $request->session()->get('payment_methods.' . $request->payment_method . '.title');
				$order['ip'] = $request->ip();
				
				Orders::where('id', $request->id)->update($order);
			} else {
				$order = new Orders;
				$order->firstname = $request->firstname;
				$order->lastname = $request->lastname;
				$order->phone = $request->phone;
				$order->email = $request->email;
				$order->type = $request->type;
				$order->fields = $request->fields ? $request->fields : [];
				$order->company = $request->company ? $request->company : '';
				$order->inn = $request->inn ? $request->inn : '';
				$order->customer_id = $request->customer_id ? $request->customer_id : null;
				$order->customer_group_id = $request->customer_group_id ? $request->customer_group_id : 0;
				$order->order_status_id = null;
				$order->currency_code = $request->currency_code;
				$order->lang = $this->lang;
				$order->currency_id = $this->currency[$request->currency_code]['id'];
				$order->currency_value = $this->currency[$request->currency_code]['value'];
				$order->total = $totals['total'];
				$order->shipping_method = $shipping_method;
				$order->shipping_title = $request->session()->get('shipping_methods.' . $shipping_method . '.title');
				$order->payment_method = $request->payment_method;
				$order->payment_title = $request->session()->get('payment_methods.' . $request->payment_method . '.title');
				$order->comment = null;
				$order->ip = $request->ip();
				
				$order->save();
				
				$order_id = $order->id;
				
				foreach ($products as $product) {
					$op = new OrderProduct;
					$op->order_id = $order->id;
					$op->product_id = $product['product_id'];
					$op->quantity = $product['quantity'];
					$op->reward = $product['reward'];
					$op->name = $product['name'];
					$op->model = $product['model'];
					$op->price = $product['price_int'];
					$op->total = $product['total_int'];
					
					$op->save();
					
					foreach ($product['options'] as $option) {
						$oo = new OrderOption;
						$oo->order_id = $request->id;
						$oo->order_product_id = $op->id;
						$oo->option_id = $option['option_id'];
						$oo->product_option_id = $option['product_option_id'];
						$oo->product_option_value_id = $option['product_option_value_id'];
						$oo->name = $option['name'];
						$oo->value = $option['value'];
						$oo->type = $option['type'];
						
						$oo->save();
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
			}
			
			$request->session()->forget('shipping_method');
			$request->session()->forget('shipping_methods');
			$request->session()->forget('payment_method');
			$request->session()->forget('payment_methods');
			$request->session()->forget('coupon');
			$request->session()->forget('reward');
			$request->session()->forget('action_product');
			
			$notify = $request->notify ? $request->notify : false;
			
			$api = new \App\Http\Controllers\ApiController;
			$api->update_order($order_id, $request->order_status_id, $request->comment, $notify);
			
			return redirect('admin/orders' . $this->params)->with('success', 'Операция успешна');
		}
	}
