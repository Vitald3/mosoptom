<?php
	
	namespace App\Http\Controllers\Extensions\Total;
	use App\Http\Controllers\Controller;
	
	use Illuminate\Http\Request;
	use App\Models\Settings;
	use App\Models\Coupon;
	use App\Models\CouponHistory;
	use App\Models\CouponProduct;
	use App\Models\CouponCategory;
	
	class CouponController extends Controller {
		public $title = 'Купон';
		public $slug = 'coupon';
		public $type = 'setting';
		
		public function getTotal($total, $setting, $products) {
			if (!is_null(session('coupon'))) {
				$sub_total = 0;
				
				foreach ($products as $product) {
					$sub_total += $product['total_int'];
				}
				
				$coupon_info = Coupon::getCoupon(session('coupon'), $products);
				
				if ($coupon_info) {
					$discount_total = 0;
					
					if ($coupon_info['product']) {
						$sub_total = 0;
						
						foreach ($products as $product) {
							if (in_array($product['product_id'], $coupon_info['product'])) {
								$sub_total += $product['total_int'];
							}
						}
					}
					
					if ($coupon_info['type'] == 'F') {
						$coupon_info['discount'] = min($coupon_info['discount'], $sub_total);
					}
					
					foreach ($products as $product) {
						$discount = 0;
						
						if (!$coupon_info['product']) {
							$status = true;
						} else {
							$status = in_array($product['product_id'], $coupon_info['product']);
						}
						
						if ($status) {
							if ($coupon_info['type'] == 'F') {
								$discount = $coupon_info['discount'] * ($product['total_int'] / $sub_total);
							} elseif ($coupon_info['type'] == 'P') {
								$discount = $product['total_int'] / 100 * $coupon_info['discount'];
							}
						}
						
						$discount_total += $discount;
					}
					
					if ($discount_total > $total['total']) {
						$discount_total = $total['total'];
					}
					
					if ($discount_total > 0) {
						$total['totals'][] = array(
							'code'       => $this->slug,
							'title'      => sprintf(__('locale.text_coupon'), session('coupon')),
							'value'      => -$discount_total,
							'sort_order' => $setting['sort_order']
						);
						
						$total['total'] -= $discount_total;
					}
				}
			}
		}
		
		public function edit(Request $request) {
			$extension = Settings::where('code', 'extension.' . $request->type . '.' . $this->slug)->value('value');
			
			return ['setting' => old('setting', $extension), 'action' => asset('admin/extension/total/' . $this->slug . '/save')];
		}
		
		public function delete(Request $request) {
			if ($request->code) {
				Settings::where('code', 'extension.' . $request->type . '.' . $request->code)->delete();
				return 'Модуль ' . $this->title . ' успешно удален';
			} else {
				return 'Произошла ошибка';
			}
		}
		
		public function save(Request $request) {
			$setting = [];
			
			if (!is_null($request->setting)) {
				foreach ($request->setting as $key => $s) {
					if (!is_null($s)) $setting[$key] = !is_array($s) ? $s : array_filter($s);
				}
			}
			
			Settings::where('code', 'extension.' . $request->type . '.' . $this->slug)->delete();
			
			$settings = new Settings;
			$settings->code = 'extension.' . $request->type . '.' . $this->slug;
			$settings->value = $setting;
			
			$settings->save();
			
			return 'Модуль ' . $this->title . ' успешно изменен';
		}
		
		public function confirm($order_info, $order_total) {
			$code = '';
			
			$start = strpos($order_total['title'], '(') + 1;
			$end = strrpos($order_total['title'], ')');
			
			if ($start && $end) {
				$code = substr($order_total['title'], $start, $end - $start);
			}
			
			if ($code) {
				$status = true;
				
				$coupon_query = Coupon::where('code', $code)->where('status', 1)->first();
				
				if ($coupon_query) {
					$coupon_total = Coupon::getTotalCouponHistoriesByCoupon($code);
					
					if ($coupon_query->uses_total > 0 && ($coupon_total >= $coupon_query->uses_total)) {
						$status = false;
					}
					
					if ($order_info->customer_id) {
						$customer_total = Coupon::getTotalCouponHistoriesByCustomerId($code, $order_info->customer_id);
						
						if ($coupon_query->row->uses_customer > 0 && ($customer_total >= $coupon_query->row->uses_customer)) {
							$status = false;
						}
					}
				} else {
					$status = false;
				}
				
				if ($status) {
					$history = new CouponHistory;
					$history->coupon_id = $coupon_query->coupon_id;
					$history->order_id = $order_info->order_id;
					$history->customer_id = $order_info->customer_id;
					$history->amount = $order_total['value'];
					
					$history->save();
				} else {
					return session('settings.fraud_status_id');
				}
			}
		}
		
		public function unconfirm($order_id) {
			CouponHistory::where('order_id', $order_id)->delete();
		}
	}