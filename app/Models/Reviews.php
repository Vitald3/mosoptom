<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    protected $table = 'reviews';
	
	public function product()
	{
		return $this->hasOne(ProductDescription::class, 'product_id', 'product_id')->select('product_description.product_id', 'product_description.name')->where('product_description.lang', config('app.locale'));
	}
	
	public function getProduct()
	{
		return $this->hasOne(Products::class, 'id', 'product_id');
	}
	
	public function social()
	{
		return $this->hasMany(CustomerSocial::class, 'customer_id', 'customer_id');
	}
	
	public function str_date()
	{
		$arr = [1 => __('locale.text_month_1'), 2 => __('locale.text_month_2'), 3 => __('locale.text_month_3'), 4 => __('locale.text_month_4'), 5 => __('locale.text_month_5'), 6 => __('locale.text_month_6'), 7 => __('locale.text_month_7'), 8 => __('locale.text_month_8'), 9 => __('locale.text_month_9'), 10 => __('locale.text_month_10'), 11 => __('locale.text_month_11'), 12 => __('locale.text_month_12')];
		
		$day = date('j', \strtotime($this->created_at));
		$month = date('n', \strtotime($this->created_at));
		$year = date('Y', \strtotime($this->created_at));
		
		return $day . ' ' . $arr[$month] . ' ' . $year;
	}
}
