<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Customers;
use App\Models\Coupon;
use App\Models\Options;
use App\Models\Extensions;
use App\Models\CartOption;
use App\Models\Products;
use App\Models\Orders;
use App\Models\OrderHistory;
use App\Models\StatusDescription;
use App\Models\ProductOptionValues;
use App\Models\ProductDiscount;
use Carbon\Carbon;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class ApiController extends Controller
{
    private $settings = [];
    private $currency = [];
    private $products = [];

    public function __construct()
    {
        $this->settings = session('settings');
		$this->default_language = session('default_language');
		$this->lang = session('lang');
		$this->currency = session('currency');
    }
	
	public function backup() {
		$folder = $_SERVER['DOCUMENT_ROOT'];
		$db = config('database')['connections']['mysql'];
		$file_name = date("Y-m-d_H:i:s") . '.zip';
		$base_name = ENV('DB_DATABASE') . '.sql';
		exec('mysqldump --user=' . $db['username'] . ' --password=' . $db['password'] . ' --host=' . $db['host'] . ' ' . $db['database'] . ' > ' . $folder . '/' . $base_name . '');
		exec("cd {$folder}", $output);
		exec("zip -r {$file_name} . -x \*.git\* \*.idea\*", $output);
		//exec("zip -r {$folder}/{$file_name} {$folder}", $output);
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . basename($file_name));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file_name));
		readfile($file_name);
		unlink($base_name);
		unlink($file_name);
		exit();
	}
	
	public function cache_clear() {
		$files = array();
		
		$path = array(base_path('assets/site/img/thumbnails/') . '*', base_path('assets/site/img/webp/') . '*');
		
		while (count($path) != 0) {
			$next = array_shift($path);
			
			foreach (glob($next) as $file) {
				if (is_dir($file)) {
					$path[] = $file . '/*';
				}
				
				$files[] = $file;
			}
		}
		
		rsort($files);
		
		foreach ($files as $file) {
			if (is_file($file)) {
				unlink($file);
			} elseif (is_dir($file)) {
				rmdir($file);
			}
		}
		
    	return redirect()->back();
	}
	
	public function update_order($order_id, $order_status_id, $comment = '', $notify = false, $override = false) {
		$order_info = Orders::with([
			'totals:order_id,code,title,value',
			'products' => function($query) {
				$query->with('options:order_id,order_product_id,product_option_value_id,name,value')->select('id', 'order_id', 'product_id', 'quantity', 'name', 'model', 'price', 'total');
			}
		])->where('id', $order_id)->first();
		
		if ($order_info) {
			// Если текущий статус заказа не обработан или завершен, но новый статус обработан или завершен, начните заполнять заказ
			if (!in_array($order_info->order_status_id, array_merge(session('settings.processing_status'), session('settings.complete_status'))) && in_array($order_status_id, array_merge(session('settings.processing_status'), session('settings.complete_status')))) {
				// Купоны и бонусные баллы
				if ($order_info->totals) {
					foreach ($order_info->totals as $order_total) {
						$item = '\App\Http\Controllers\Extensions\Total\\' . ucfirst($order_total['code']) . 'Controller';
						$item = new $item;
						
						if (property_exists($item, 'confirm')) {
							// Подтвердите купон и бонусные баллы
							$fraud_status_id = $item->confirm($order_info, $order_total);
							
							// Если купона и бонусного балла, недостаточно для покрытия транзакции или он уже был использован, возвращается статус мошеннического заказа.
							if ($fraud_status_id) {
								$order_status_id = $fraud_status_id;
							}
						}
					}
				}
				
				if (!$order_info->products->isEmpty()) {
					foreach ($order_info->products as $order_product) {
						Products::where('id', $order_product->product_id)->update([
							'quantity' => \DB::raw("(quantity - " . (int)$order_product->quantity . ")")
						]);
						
						if (!$order_product->options->isEmpty()) {
							foreach ($order_product->options as $order_option) {
								ProductOptionValues::where('id', $order_option->product_option_value_id)->where('subtract', 1)->update([
									'quantity' => \DB::raw("(quantity - " . (int)$order_product->quantity . ")")
								]);
							}
						}
					}
				}
			}
			
			// Обновление заказа
			Orders::where('id', $order_id)->update([
				'order_status_id' => $order_status_id
			]);
			
			$history = new OrderHistory;
			$history->order_id = $order_id;
			$history->order_status_id = $order_status_id;
			$history->notify = $notify;
			$history->comment = $comment;
			
			$history->save();
			
			// Если статус старого заказа соответствует статусу обработки или завершения, но новый статус не соответствует, начните пополнение запасов и удалите историю купонов и вознаграждений
			if (in_array($order_info->order_status_id, array_merge(session('settings.processing_status'), session('settings.complete_status'))) && !in_array($order_status_id, array_merge(session('settings.processing_status'), session('settings.complete_status')))) {
				// Пополнять запасы
				if (!$order_info->products->isEmpty()) {
					foreach ($order_info->products as $order_product) {
						Products::where('id', $order_product->product_id)->update([
							'quantity' => \DB::raw("(quantity + " . (int)$order_product->quantity . ")")
						]);
						
						if (!$order_product->options->isEmpty()) {
							foreach ($order_product->options as $order_option) {
								ProductOptionValues::where('id', $order_option->product_option_value_id)->where('subtract', 1)->update([
									'quantity' => \DB::raw("(quantity + " . (int)$order_product->quantity . ")")
								]);
							}
						}
					}
				}
				
				// Удалить историю купонов и бонусных баллов
				if ($order_info->totals) {
					foreach ($order_info->totals as $order_total) {
						$item = '\App\Http\Controllers\Extensions\Total\\' . ucfirst($order_total['code']) . 'Controller';
						$item = new $item;
						
						if (property_exists($item, 'unconfirm')) {
							$item->unconfirm($order_info, $order_total);
						}
					}
				}
			}
			
			$name = session('settings.name.' . session('lang'), ' ');
			$setting = session('settings');
			
			// Если статус заказа равен 0, а затем становится больше 0, отправьте основное html-письмо
			if (!$order_info->order_status_id && $order_status_id) {
				$order_status = StatusDescription::select('name')
					->where('status_id', $order_status_id)
					->where('lang', session('lang'))
					->value('name');
				
				$subject = sprintf(__('locale.text_new_subject'), $name, $order_id);
				
				$data = array();
				
				$data['title'] = $subject;
				
				$data['text_greeting'] = sprintf(__('locale.text_new_greeting'), $name);
				$data['text_link'] = __('locale.text_new_link');
				$data['text_order_detail'] = __('locale.text_new_order_detail');
				$data['text_instruction'] = __('locale.text_new_instruction');
				$data['text_order_id'] = __('locale.text_new_order_id');
				$data['text_date_added'] = __('locale.text_new_date_added');
				$data['text_payment_method'] = __('locale.text_new_payment_method');
				$data['text_shipping_method'] = __('locale.text_new_shipping_method');
				$data['text_email'] = __('locale.text_new_email');
				$data['text_phone'] = __('locale.text_new_phone');
				$data['text_order_status'] = __('locale.text_new_order_status');
				$data['text_shipping_address'] = __('locale.text_new_shipping_address');
				$data['text_product'] = __('locale.text_new_product');
				$data['text_model'] = __('locale.text_new_model');
				$data['text_quantity'] = __('locale.text_new_quantity');
				$data['text_price'] = __('locale.text_new_price');
				$data['text_total'] = __('locale.text_new_total');
				$data['text_footer'] = __('locale.text_new_footer');
				$data['logo'] = session('settings.logo_mail');
				$data['store_name'] = $name;
				$data['store_url'] = url('');
				$data['customer_id'] = $order_info->customer_id;
				$data['link'] = route(session('route_url') . '_account_order_info', $order_id);
				$data['order_id'] = $order_id;
				$data['date_added'] = date('Y-m-d H:i', \strtotime($order_info->created_at));
				$data['payment_method'] = $order_info->payment_title;
				$data['shipping_method'] = $order_info->shipping_title;
				$data['email'] = $order_info->email;
				$data['phone'] = $order_info->phone;
				$data['order_status'] = $order_status;
				
				$fields = $order_info->fields;
				
				if ($comment && $notify) {
					$data['comment'] = nl2br($comment);
				} else {
					$data['comment'] = '';
				}
				
				$format = '{firstname} {lastname}' . "<br>" . '{company}' . "<br>" . '{address}' . "<br>" . '{kv}';
				
				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address}',
					'{kv}',
					'{comment}'
				);
				
				$replace = array(
					'firstname' => $order_info->firstname,
					'lastname'  => $order_info->lastname,
					'company'   => $order_info->company,
					'address' => isset($fields['address']) ? $fields['address'] : '',
					'kv' => isset($fields['kv']) ? $fields['kv'] : '',
					'comment' => isset($fields['comment']) ? $fields['comment'] : '',
				);
				
				$data['address'] = str_replace(array("\r\n", "\r", "\n\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
			
				// Товары
				$data['products'] = array();
				
				if (!$order_info->products->isEmpty()) {
					foreach ($order_info->products as $product) {
						$option_data = array();
						
						if (!$product->options->isEmpty()) {
							foreach ($product->options as $option) {
								$option_data[] = array(
									'name' => $option['name'],
									'value' => (utf8_strlen($option->value) > 20 ? utf8_substr($option->value, 0, 20) . '..' : $option->value)
								);
							}
						}
						
						$data['products'][] = array(
							'name' => $product->name,
							'model' => $product->model,
							'option' => $option_data,
							'quantity' => $product->quantity,
							'price' => format_price_value($product->price, $order_info->currency_code, $order_info->currency_value),
							'total' => format_price_value($product->total, $order_info->currency_code, $order_info->currency_value)
						);
					}
				}
				
				$data['totals'] = array();
				
				if (!$order_info->totals->isEmpty()) {
					foreach ($order_info->totals as $total) {
						$data['totals'][] = array(
							'title' => $total->title,
							'value' => format_price_value($total->value, $order_info->currency_code, $order_info->currency_value),
						);
					}
				}

				$data_mail = [
					'params' => [
						'logo' => $setting['logo_mail'],
						'name' => $name,
						'url' => url(''),
						'text' => view('email.order', $data)->render()
					],
					'subject' => $subject,
					'template' => 'email.default',
					'email' => $order_info->email
				];
				
				Mail::later(Carbon::now()->addSeconds(5), new SendEmail($data_mail));
				
				// Admin Alert Mail
				$subject = sprintf(__('locale.text_new_order'), html_entity_decode($name, ENT_QUOTES, 'UTF-8'), $order_id);
				
				// HTML Mail
				$data['text_greeting'] = __('locale.text_new_received');
				
				if ($comment) {
					if ($order_info->comment) {
						$data['comment'] = nl2br($comment) . '<br/><br/>' . $order_info->comment;
					} else {
						$data['comment'] = nl2br($comment);
					}
				} else {
					if ($order_info->comment) {
						$data['comment'] = $order_info->comment;
					} else {
						$data['comment'] = '';
					}
				}
				
				$data['text_footer'] = '';
				
				$data['text_link'] = '';
				$data['link'] = '';
				
				$data_mail = [
					'params' => [
						'logo' => $setting['logo_mail'],
						'name' => $name,
						'url' => url(''),
						'text' => view('email.order', $data)->render()
					],
					'subject' => $subject,
					'template' => 'email.default'
				];
				
				Mail::later(Carbon::now()->addSeconds(5), new SendEmail($data_mail));
			}
			
			// Если статус заказа не равен 0, отправьте текстовое сообщение с обновлением по электронной почте
			if ($order_info->order_status_id && $order_status_id && $notify) {
				$subject = sprintf(__('locale.text_update_subject'), html_entity_decode($name, ENT_QUOTES, 'UTF-8'), $order_id);
				
				$message = __('locale.text_update_order') . ' ' . $order_id . "<br>";
				$message .= __('locale.text_update_date_added') . ' ' . date('Y-m-d H:i', \strtotime($order_info->created_at)) . "<br><br>";
				
				$order_status = StatusDescription::select('name')->where('status_id', $order_status_id)->where('lang', session('lang'))->value('name');
				
				if ($order_status) {
					$message .= __('locale.text_update_order_status') . "<br><br>";
					$message .= $order_status . "<br><br>";
				}
				
				if ($order_info->customer_id) {
					$message .= __('locale.text_update_link') . "<br>";
					$message .= route(session('route_url') . '_account_order_info', $order_id) . "<br><br>";
				}
				
				if ($comment) {
					$message .= __('locale.text_update_comment') . "<br><br>";
					$message .= strip_tags($comment) . "<br><br>";
				}
				
				$message .= __('locale.text_update_footer');
				
				$data_mail = [
					'params' => [
						'logo' => $setting['logo_mail'],
						'name' => $name,
						'url' => url(''),
						'text' => $message
					],
					'subject' => $subject,
					'template' => 'email.default',
					'email' => $order_info->email
				];
				
				Mail::later(Carbon::now()->addSeconds(5), new SendEmail($data_mail));
			}
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
                
                $types = [];
	
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
                    $cart = new Cart;
                    $cart->product_id = $request->product_id;
                    $cart->customer_id = 0;
                    $cart->user_id = auth()->user()->id;
                    $cart->session_id = csrf_token();
                    $cart->quantity = $quantity;

                    $cart->save();

                    if ($options) {
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
                    }

                    session()->forget('shipping_methods');
                    session()->forget('shipping_method');
                    session()->forget('payment_methods');
                    session()->forget('payment_method');
                }
            }
        }
        elseif($request->product) {
            $this->clear();

            foreach ($request->product as $product) {
                if (isset($product['option'])) {
                    $options = $product['option'];
                } else {
                    $options = [];
                }

                $cart = new Cart;
                $cart->product_id = $product['product_id'];
                $cart->customer_id = session('customer_id', 0);
                $cart->user_id = auth()->user()->id;
                $cart->session_id = csrf_token();
                $cart->quantity = $product['quantity'];

                $cart->save();

                if ($options) {
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
				
								if (!in_array(Options::select('type')->where('id', $option_id)->value('type'), ['text', 'textarea', 'date', 'datetime'])) {
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
                }
            }
        }

        if (!$json) {
            $json = $this->getProducts();

            session()->forget('shipping_methods');
            session()->forget('shipping_method');
            session()->forget('payment_methods');
            session()->forget('payment_method');
        }

        return response()->json($json);
    }

    public function clear() {
        Cart::where('session_id', csrf_token())
            ->where(function($query) {
                if (!is_null(auth()->user())) {
                    $query->where('user_id', auth()->user()->id);
                }
            })->delete();
    }

    public function getProducts() {
        $products = false;
	
		$cart = Cart::getProducts(auth()->user()->id);

        if (!$cart->isEmpty()) {
            foreach ($cart as $item) {
                $product = !is_null($item->products) ? $item->products : [];

                if ($product) {
					$price = $product->price;
	
					$discount_quantity = 0;
	
					foreach ($cart as $cart_2) {
						if ($item->product_id == $cart_2->product_id) {
							$discount_quantity += $cart_2->quantity;
						}
					}
	
					$discount = ProductDiscount::select('price')
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
					}
	
					if ($product->product_special_one) {
						$price = $product->product_special_one->price;
					}
					
                    $options = [];

                    if (!$item->cart_option->isEmpty()) {
                        foreach ($item->cart_option as $cart_option) {
                            if (in_array($cart_option->options->type, ['select', 'radio', 'color', 'checkbox'])) {
                                foreach ($cart_option->product_option->product_option_values as $product_option_values) {
                                    $price += $product_option_values->price;

                                    $options[] = [
                                        'option_id' => $cart_option->option_id,
                                        'product_option_id' => $cart_option->product_option_id,
                                        'product_option_value_id' => $product_option_values->id,
                                        'type' => $cart_option->options->type,
                                        'name' => $cart_option->options->metaLang->name,
                                        'value' => $product_option_values->option_value_description->name
                                    ];
                                }
                            } else {
                                $options[] = [
                                    'option_id' => $cart_option->option_id,
                                    'product_option_id' => $cart_option->product_option_id,
                                    'product_option_value_id' => 0,
                                    'type' => $cart_option->options->type,
                                    'name' => $cart_option->options->metaLang->name,
                                    'value' => $cart_option->product_option->value
                                ];
                            }
                        }
                    }

                    $products[] = [
                        'cart_id' => $item->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'name' => $product->metaLang->name,
                        'model' => $product->model,
                        'price_int' => $price,
                        'reward' => $product->reward * $item->quantity,
                        'total_int' => $price * $item->quantity,
                        'price' => format_price($price, $this->currency),
                        'total' => format_price($price * $item->quantity, $this->currency),
                        'options' => $options
                    ];
                }
            }
        }

        $this->products = $products;

        return $products;
    }

    public function getTotals() {
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
            $total['value'] = format_price($total['value'], $this->currency);
        }

        return $total_data;
    }

    public function shipping(Request $request) {
        $json = [];

        if ($request->shipping_method) {
			$request->session()->forget('shipping_fields');
        	$shipping_method = explode('.', $request->shipping_method);
        	
        	if (isset($shipping_method[1])) {
				$request->session()->put(['shipping_fields.' . $shipping_method[0] . '.' . $shipping_method[0] => $shipping_method[1]]);
				$shipping_method = $shipping_method[0];
		
				$shipping_methods = [];
		
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
			} else {
				$shipping_method = $request->shipping_method;
			}
        	
            if (!is_null(session('shipping_methods'))) {
                $shipping_methods = session('shipping_methods');

                if (isset($shipping_methods[$shipping_method])) {
                    session(['shipping_method' => $shipping_methods[$shipping_method]]);

                    $json = [
                        'products' => $this->getProducts(),
                        'totals' => $this->getTotals()['totals']
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

    public function update_methods(Request $request) {
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
    }

    public function currency(Request $request) {
        if ($request->currency_code) {
            session(['currency_code' => $request->currency_code]);
        }
    }

    public function coupon(Request $request) {
        $json = [];

        if ($request->coupon) {
            $coupon = $request->coupon;
        } else {
            $coupon = '';
        }

        $coupon_info = Coupon::getCoupon($coupon, $this->getProducts());

        if (empty($coupon)) {
            $json['error'] = __('locale.empty_coupon');

            session()->forget('coupon');
        } elseif ($coupon_info) {
            session(['coupon' => $coupon]);

            $json = [
                'products' => $this->getProducts(),
                'totals' => $this->getTotals()['totals']
            ];
        } else {
            $json['error'] = __('locale.error_coupon');
        }

        return response()->json($json);
    }

    public function reward(Request $request) {
        $json = [];

        $rewards = Customers::getRewardPoints();

        $reward_total = 0;

        foreach ($this->getProducts() as $product) {
            if ($product['reward']) {
                $reward_total += $product['reward'];
            }
        }

        if (!$request->reward || !is_numeric($request->reward) || ($request->reward <= 0)) {
            $json['error'] = __('locale.error_reward');
        }

        if ($request->reward > $rewards) {
            $json['error'] = sprintf(__('locale.error_points'), $request->reward);
        }

        if ($request->reward > $reward_total) {
            $json['error'] = sprintf(__('locale.error_reward_maximum'), $reward_total);
        }

        if (!$json) {
            session(['reward' => abs($request->reward)]);

            $json['success'] = 'Баллы успешно применены';
        }

        return response()->json($json);
    }

    public function payment(Request $request) {
        $json = [];

        if ($request->payment_method) {
            if (session('payment_methods')) {
                $payment_methods = session('payment_methods');

                if (isset($payment_methods[$request->payment_method])) {
                    session(['payment_method' => $payment_methods[$request->payment_method]]);

                    $json['success'] = 'Способ оплаты успешно изменен';
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
}