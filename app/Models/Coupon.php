<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'coupon';

    public function coupon_products()
    {
        return $this->hasMany(CouponProduct::class, 'coupon_id');
    }

    public function coupon_categories()
    {
        return $this->hasMany(CouponCategory::class, 'coupon_id');
    }

    public function history()
    {
        return $this->hasMany(CouponHistory::class, 'coupon_id');
    }

    public static function getTotalCouponHistoriesByCoupon($coupon) {
        return CouponHistory::join('coupon as c', 'c.id', '=', 'coupon_history.coupon_id')->where('c.code', $coupon)->count();
    }

    public static function getTotalCouponHistoriesByCustomerId($coupon, $customer_id) {
        return CouponHistory::join('coupon as c', 'c.id', '=', 'coupon_history.coupon_id')->where('coupon_history.customer_id', $customer_id)->where('c.code', $coupon)->count();
    }

    public static function getCoupon($code, $products) {
        $sub_total = 0;

        foreach ($products as $product) {
            $sub_total += $product['total_int'];
        }

        $status = true;
        $product_data = [];

        $coupon = Coupon::where('code', $code)->where('status', 1)->whereRaw("((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) AND status = '1'")->first();

        if (!empty($coupon)) {
            if ($coupon->total > $sub_total) {
                $status = false;
            }

            $coupon_total = self::getTotalCouponHistoriesByCoupon($code);

            if ($coupon->uses_total > 0 && ($coupon_total >= $coupon->uses_total)) {
                $status = false;
            }

            if ($coupon->logged && !session('customer_id')) {
                $status = false;
            }

            if (session('customer_id')) {
                $customer_total = self::getTotalCouponHistoriesByCustomerId($code, session('customer_id'));

                if ($coupon->uses_customer > 0 && ($customer_total >= $coupon->uses_customer)) {
                    $status = false;
                }
            }

            $coupon_product_data = [];

            $coupon_product = CouponProduct::where('coupon_id', $coupon->id)->pluck('product_id');

            foreach ($coupon_product as $product) {
                $coupon_product_data[] = $product;
            }

            if ($coupon_product_data) {
                $coupon_category = CouponCategory::with([
                    'product_category' => function($query) use($coupon_product_data) {
                        $query->distinct()->select('category_id', 'product_id')->where('product_id', $coupon_product_data);
                    }
                ])
                    ->select('category_id')
                    ->where('coupon_id', $coupon->id)
                    ->first();

                if (!empty($coupon_category)) {
                    foreach ($coupon_category->product_category as $product_category) {
                        $product_data[] = $product_category['product_id'];
                    }
                }

                foreach ($products as $product) {
                    if (in_array($product['product_id'], $coupon_product_data)) {
                        $product_data[] = $product['product_id'];
                        break;
                    }
                }
            }

            if (!$product_data) {
                $status = false;
            }
        } else {
            $status = false;
        }

        if ($status) {
            return array(
                'coupon_id'     => $coupon->coupon_id,
                'code'          => $coupon->code,
                'name'          => $coupon->name,
                'type'          => $coupon->type,
                'discount'      => $coupon->discount,
                'shipping'      => $coupon->shipping,
                'total'         => $coupon->total,
                'product'       => $product_data,
                'date_start'    => $coupon->date_start,
                'date_end'      => $coupon->date_end,
                'uses_total'    => $coupon->uses_total,
                'uses_customer' => $coupon->uses_customer,
                'status'        => $coupon->status,
                'created_at'    => $coupon->created_at
            );
        }
    }
}