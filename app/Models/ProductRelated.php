<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRelated extends Model
{
    protected $table = 'product_related';
	
	public function getSlug() {
		return app(\App\Helpers\PathRouteService::class)->getRoute('product_' . session('lang') . '_id=' . $this->related_id);
	}
	
	public function product_special_one()
	{
		return $this->hasOne(ProductSpecial::class, 'product_id')->select('product_id', 'price', 'customer_group_id', 'date_start', 'date_end')
			->whereRaw("customer_group_id = '" . (int)session('customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < '" . now()->format('Y-m-d') . "') AND (date_end = '0000-00-00' OR date_end > '" . now()->format('Y-m-d') . "'))")
			->orderBy('created_at');
	}
	
	public function product_discount()
	{
		return $this->hasMany(ProductDiscount::class, 'product_id')
			->distinct()
			->select('product_id', 'price', 'quantity', 'customer_group_id', 'date_start', 'date_end')
			->whereRaw("customer_group_id = '" . (int)session('customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < '" . now()->format('Y-m-d') . "') AND (date_end = '0000-00-00' OR date_end > '" . now()->format('Y-m-d') . "'))")
			->orderBy('quantity');
	}
}