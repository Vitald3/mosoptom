<?php

namespace App\Http\Controllers\Extensions\Total;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\Customers;
use App\Models\CustomerReward;

class RewardController extends Controller {
    public $title = 'Баллы';
    public $slug = 'reward';
    public $type = 'setting';

    public function getTotal($total, $setting, $products) {
        if (!is_null(session('reward'))) {
            $points = Customers::getRewardPoints();

            if (session('reward') <= $points) {
                $discount_total = 0;

                $points_total = 0;

                foreach ($products as $product) {
                    if ($product['points']) {
                        $points_total += $product['points'];
                    }
                }

                $points = min($points, $points_total);

                foreach ($products as $product) {
                    $discount = 0;

                    if ($product['points']) {
                        $discount = $product['total_int'] * (session('reward') / $points_total);
                    }

                    $discount_total += $discount;
                }

                $total['totals'][] = array(
                    'code'       => $this->slug,
                    'title'      => sprintf(__('locale.text_reward'), session('reward')),
                    'value'      => -$discount_total,
                    'sort_order' => $setting['sort_order']
                );

                $total['total'] -= $discount_total;
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
		$points = 0;
		
		$start = strpos($order_total['title'], '(') + 1;
		$end = strrpos($order_total['title'], ')');
		
		if ($start && $end) {
			$points = substr($order_total['title'], $start, $end - $start);
		}

		if (CustomerReward::select('points')->where('customer_id', $order_info->customer_id)->sum('points') >= $points) {
			$reward = new CustomerReward;
			$reward->customer_id = $order_info->customer_id;
			$reward->order_id = $order_info->order_id;
			$reward->description = sprintf(__('locale.text_reward_order_id'), (int)$order_info->order_id);
			$reward->points = (float)-$points;
		} else {
			return session('settings.fraud_status_id');
		}
	}
	
	public function unconfirm($order_id) {
		CustomerReward::where('order_id', $order_id)->where('points', '<', 0)->delete();
	}
}