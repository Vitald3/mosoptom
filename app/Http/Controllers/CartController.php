<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Extensions;
	use App\Models\Cart;
	use App\Models\Orders;
	use App\Models\OrderProduct;
	use App\Models\ProductDiscount;
	use App\Models\OrderOption;
	use App\Models\OrderTotal;
	use App\Models\StatusDescription;
	use Carbon\Carbon;
	use App\Models\CartOption;
	use App\Models\Products;
	use App\Mail\SendEmail;
	use Illuminate\Support\Facades\Mail;
	
	class CartController extends Controller
	{
		private $settings = [];
		private $products = [];
		private $count = 0;
		private $price = 0;
		
		public function __construct() {
			$this->settings = session('settings');
			$this->default_language = session('default_language');
			$this->lang = session('lang');
		}
		
		public function mini_cart($saleday = '') {
			$data['products'] = $this->getProducts();
			$totals = $this->getTotals();
			
			if (!empty($totals['totals'])) {
				$data['totals'] = $totals['totals'];
			} else {
				$data['totals'] = [];
			}
			
			$data['saleday'] = $saleday;
			$data['cart_count_text'] = sprintf(__('locale.text_cart_product'), num_decline($this->count, [__('locale.text_prod1'), __('locale.text_prod2'), __('locale.text_prod3')]));
			
			if (request()->ajax()) {
				return html_entity_decode(view('pages.site.minicart', $data)->render());
			} else {
				return view('pages.site.minicart', $data);
			}
		}
		
		public function getCount() {
			return $this->count;
		}
		
		public function getprice() {
			return $this->price;
		}
		
		public function remove(Request $request) {
			$json = [];
			
			if ($request->cart_id) {
				Cart::where('id', $request->cart_id)->delete();
				CartOption::where('cart_id', $request->cart_id)->delete();
				
				$json['html'] = $this->mini_cart();
				
				session()->forget('shipping_methods');
				session()->forget('shipping_method');
				session()->forget('payment_methods');
				session()->forget('payment_method');
			} else {
				$json['error'] = 'Error';
			}
			
			return response()->json($json);
		}
		
		public function edit(Request $request) {
			$json = [];
			
			if ($request->cart_id) {
				if ($request->quantity) {
					Cart::where('id', $request->cart_id)->update([
						'quantity' => $request->quantity
					]);
				} else {
					Cart::where('id', $request->cart_id)->delete();
				}
				
				$json['html'] = $this->mini_cart();
				
				session()->forget('shipping_methods');
				session()->forget('shipping_method');
				session()->forget('payment_methods');
				session()->forget('payment_method');
			} else {
				$json['error'] = 'Error';
			}
			
			return response()->json($json);
		}
		
		public function oneclick(Request $request) {
			$validate = \Validator::make($request->all(), [
				'name' => 'required',
				'email' => 'required|email',
				'phone' => 'required'
			]);
			
			if ($validate->fails()) {
				return response()->json(['errors' => $validate->errors()->messages()]);
			} else {
				$products = $this->getProducts();
				$totals = $this->getTotals();
				
				$order = new Orders;
				$order->firstname = $request->name;
				$order->lastname = '';
				$order->phone = $request->phone;
				$order->email = $request->email;
				$order->customer_id = session('customer_id');
				$order->customer_group_id = session('customer_group_id');
				$order->order_status_id = session('settings.order_status_id');
				$order->currency_code = session('currency_code');
				$order->lang = session('lang');
				$order->currency_id = session('currency')['id'];
				$order->currency_value = session('currency')['value'];
				$order->total = $totals['total'];
				$order->shipping_method = '';
				$order->shipping_title = '';
				$order->payment_method = '';
				$order->payment_title = '';
				$order->comment = '';
				$order->type = 0;
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
						if (!empty($option['values'])) {
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
				
				$this->clear();
				
				if (session('old_cart')) {
					foreach ((array)session('old_cart') as $old) {
						$old = $old['product'];
						$cart = new Cart;
						$cart->product_id = $old['product_id'];
						$cart->customer_id = $old['customer_id'];
						$cart->session_id = $old['session_id'];
						$cart->quantity = $old['quantity'];
						
						$cart->save();
						
						if ($old['options']) {
							foreach ($old['options'] as $option) {
								$co = new CartOption;
								$co->cart_id = $old['cart_id'];
								$co->option_id = $option['option_id'];
								$co->product_option_id = $option['product_option_id'];
								$co->value = $option['value'];
								$co->product_option_value_id = $option['product_option_value_id'];
								
								$co->save();
							}
						}
					}
					
					session()->forget('old_cart');
				}
				
				$setting = session('settings');
				
				$status = StatusDescription::where('status_id', session('settings.order_status_id'))->value('name');
				
				$data = [
					'params' => [
						'logo' => $setting['logo_mail'],
						'name' => !empty($setting['name'][session('lang')]) ? $setting['name'][session('lang')] : '',
						'url' => url(''),
						'text' => view('email.order', ['order_id' => $order->id, 'comment' => '', 'address' => '', 'order_status' => $status, 'phone' => $request->phone, 'payment_method' => '', 'shipping_method' => '', 'email' => $request->email, 'date_added' => now()->format('Y-m-d'), 'products' => $products, 'totals' => $totals['totals'], $request->all()])->render()
					],
					'subject' => __('locale.text_new_order'),
					'template' => 'email.default'
				];
				
				Mail::later(Carbon::now()->addSeconds(5), new SendEmail($data));
				
				$type = 'error';
				
				if (Mail::failures()) {
					$message = Mail::failures();
				} else {
					$type = 'success';
					$message = __('locale.text_write_success');
				}
				
				return response()->json([$type => $message]);
			}
		}
		
		public function add(Request $request) {
			$json = [];
			
			if ($request->product_id) {
				$product = Products::with([
					'metaLang:product_id,name',
					'product_option' => function($query) {
					$query->with([
						'product_option_values' => function($query) {
							$query->join('option_value_description as ovd', 'ovd.option_value_id', '=', 'product_option_values.option_value_id')
								->select('product_option_values.id', 'product_option_values.quantity', 'product_option_values.product_option_id', 'ovd.name');
						}
					])
						->join('options as o', 'o.id', '=', 'product_option.option_id')
						->join('option_description as od', 'od.option_id', '=', 'o.id')
						->select('od.name', 'product_option.id', 'product_option.required', 'o.type', 'o.id as option_id', 'product_option.product_id', 'product_option.value')
						->where('od.lang', $this->lang)
						->where('o.status', 1);
				}
				])->select('id')->where('id', $request->product_id)->first();
				
				if (!empty($product)) {
					if ($request->quantity) {
						$quantity = (int)$request->quantity;
					} else {
						$quantity = 1;
					}
					
					if ($request->option) {
						$options = array_filter($request->option);
					} else {
						$options = [];
					}
					
					foreach ($product->product_option as $product_option) {
						if (!empty($options[$product_option->option_id][$product_option->id])) $types[$product_option->option_id] = $product_option->type;
						
						if ($product_option->required && !isset($options[$product_option->option_id][$product_option->id])) {
							$json['error']['option'][$product_option->id] = sprintf(__('locale.error_required'), $product_option->name);
						}
						
						if (!$product_option->product_option_values->isEmpty() && !empty($options[$product_option->option_id][$product_option->id]) && in_array($product_option->type, ['select', 'radio', 'color', 'checkbox'])) {
							foreach ($product_option->product_option_values as $product_option_values) {
								if ($options[$product_option->option_id][$product_option->id] == $product_option_values->id && $product_option_values->quantity < $quantity || $product_option_values->quantity <= 0) {
									$values_name[] = $product_option_values->name;
									$json['error']['option'][$product_option->id] = sprintf(__('locale.error_quantity'), $product_option_values->name);
								}
							}
						}
					}
					
					if (!$json) {
						if ($request->get('oneclick')) {
							$old_carts = Cart::where('customer_id', session('customer_id', 0))->where('session_id', csrf_token())->get();
							$old_cart = [];
							
							foreach ($old_carts as $old) {
								$old_cart[] = [
									'product' => $old,
									'options' => CartOption::where('cart_id', $old->cart_id)->get()
								];
							}
							
							if ($old_cart) session(['old_cart' => $old_cart]);
							
							$this->clear();
						}
						
						if ($options) {
                            $cart = new Cart;
                            $cart->product_id = $request->product_id;
                            $cart->customer_id = session('customer_id', 0);
                            $cart->session_id = csrf_token();
                            $cart->quantity = $quantity;

                            $cart->save();

							foreach ($options as $option_id => $option) {
								foreach ($option as $product_option_id => $product_option_value_id) {
									if (is_array($product_option_value_id)) {
										foreach ($product_option_value_id as $value_id) {
											$co = new CartOption;
											$co->cart_id = $cart->id;
											$co->option_id = $option_id;
											$co->product_option_id = $product_option_id;
											$co->value = '';
											$co->product_option_value_id = $value_id;
											
											$co->save();
										}
									} else {
										$co = new CartOption;
										$co->cart_id = $cart->id;
										$co->option_id = $option_id;
										$co->product_option_id = $product_option_id;
										
										if (isset($types[$option_id]) && !in_array($types[$option_id], ['text', 'textarea', 'date', 'datetime'])) {
											$co->value = null;
											$co->product_option_value_id = $product_option_value_id;
										} else {
											$co->value = $product_option_value_id;
											$co->product_option_value_id = null;
										}
										
										$co->save();
									}
								}
							}
						} else {
						    $prev_cart = Cart::select('quantity')
                                ->where('customer_id', session('customer_id', 0))
                                ->where('session_id', csrf_token())
                                ->where('product_id', $request->product_id)->value('quantity');

						    if (!$prev_cart) {
                                $cart = new Cart;
                                $cart->product_id = $request->product_id;
                                $cart->customer_id = session('customer_id', 0);
                                $cart->session_id = csrf_token();
                                $cart->quantity = $quantity;

                                $cart->save();
                            } else {
                                Cart::where('customer_id', session('customer_id', 0))
                                    ->where('session_id', csrf_token())
                                    ->where('product_id', $request->product_id)->update([
                                        'quantity' => $prev_cart + $quantity
                                    ]);
                            }
                        }
						
						$json = [
							'html' => $this->mini_cart(),
							'total' => $this->getCount() > 99 ? '99+' : $this->getCount(),
							'price' => $this->price
						];
						
						session()->forget('shipping_methods');
						session()->forget('shipping_method');
						session()->forget('payment_methods');
						session()->forget('payment_method');
					}
				}
			} else {
				$json['error'] = 'Error';
			}
			
			return response()->json($json);
		}
		
		public function clear() {
			Cart::where('session_id', csrf_token())
				->where('customer_id', session('customer_id', 0))
				->where(function($query) {
					if (!is_null(auth()->user())) {
						$query->where('user_id', auth()->user()->id);
					}
				})->delete();
		}
		
		public function getProducts() {
			$cart = Cart::getProducts();
			$this->products = [];
			$this->count = 0;
			$this->price = 0;
			
			if (!$cart->isEmpty()) {
				foreach ($cart as $item) {
					$product = !is_null($item->products) ? $item->products : [];
					
					if ($product) {
						$price = $product->price;
						$discounts = [];
						$discount_quantity = 0;
						
						foreach ($cart as $cart_2) {
							if ($item->product_id == $cart_2->product_id) {
								$discount_quantity += $cart_2->quantity;
							}
						}
						
						if ($product->product_discount_cart) {
							$discounts = $product->product_discount_cart;
							$product_discount_cart = $product->product_discount_cart->where('quantity', '<=', $discount_quantity)->sortByDesc('quantity')->take(1)->first();
							
							if (!empty($product_discount_cart)) {
								$price = $product_discount_cart->price;
							}
						}
						
						/*$discount = ProductDiscount::select('price')
							->where('product_id', $item->product_id)
							->where('customer_group_id', session('customer_group_id'))
							->where('quantity', '<=', $discount_quantity)
							->whereRaw("((date_start = '0000-00-00' OR date_start < '" . now()->format('Y-m-d') . "') AND (date_end = '0000-00-00' OR date_end > '" . now()->format('Y-m-d') . "'))")
							->orderBy('quantity', 'desc')
							->orderBy('price')
							->limit(1)
							->value('price');
						
						if ($discount) {
							$price = $discount;
						}*/
						
						if ($product->product_special_one) {
							$price = $product->product_special_one->price;
						}
						
						$options = [];
						
						if (!$item->cart_option->isEmpty()) {
							foreach ($item->cart_option as $cart_option) {
								$values = [];
								
								if (!in_array($cart_option->type, ['select', 'radio', 'color', 'checkbox'])) {
									foreach ($cart_option->product_option->product_option_values as $product_option_values) {
										$price += $product_option_values->price;
										
										$values[] = [
											'id' => $product_option_values->id,
											'price' => $product_option_values->price,
											'value' => $product_option_values->option_value_description->name
										];
									}
								}
								
								$options[] = [
									'id' => $cart_option->option_id,
									'product_option_id' => $cart_option->product_option_id,
									'type' => $cart_option->options->type,
									'name' => $cart_option->options->metaLang->name,
									'value' => !empty($cart_option->product_option->value) ? $cart_option->product_option->value : '',
									'values' => $values
								];
							}
						}
						
						$this->count += $item->quantity;
						$this->price += $price * $item->quantity;
						
						$this->products[] = [
							'cart_id' => $item->id,
							'id' => $item->product_id,
							'quantity' => $item->quantity,
							'image' => resize_image($product->image, 135, 135),
							'name' => $product->metaLang->name,
							'model' => $product->model,
							'reward' => $product->reward,
							'discounts' => $discounts,
							'discount' => format_price($price, session('currency')),
							'price_int' => $price,
							'total_int' => $price * $item->quantity,
							'price' => format_price($price, session('currency')),
							'total' => format_price($price * $item->quantity, session('currency')),
							'options' => $options,
							'url' => $product->getSlug()
						];
					}
				}
			}
			
			return $this->products;
		}
		
		public function getTotals() {
			if (!$this->products) return [];
			
			$totals = [];
			$total = 0;
			
			$total_data = [
				'totals' => &$totals,
				'total'  => &$total
			];
			
			$results = Extensions::getExtensions('total');
			
			foreach ($results as $result) {
				$module = '\App\Http\Controllers\Extensions\Total\\' . ucfirst($result['code']) . 'Controller';
				$module = new $module;
				$module->getTotal($total_data, $result['setting'], $this->products);
			}
			
			$sort_order = [];
			
			foreach ($totals as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}
			
			array_multisort($sort_order, SORT_ASC, $totals);
			
			foreach ($totals as &$total) {
				$total['value_int'] = $total['value'];
				$total['value'] = format_price($total['value'], session('currency'));
			}
			
			return $total_data;
		}
	}