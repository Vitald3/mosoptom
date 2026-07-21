<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponCategory extends Model
{
    protected $table = 'coupon_category';

    public function product_category()
    {
        return $this->hasMany(ProductCategory::class, 'category_id');
    }
}