<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'cart';

    protected $casts = [
        'option' => 'array'
    ];

    public function products()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }

    public function cart_option()
    {
        return $this->hasMany(CartOption::class, 'cart_id');
    }

    public function product_option()
    {
        return $this->belongsToMany(ProductOption::class, Options::class, 'option_id', 'id');
    }

    public function product_option_values()
    {
        return $this->hasMany(ProductOptionValues::class, 'product_id', 'product_id');
    }
    
    public static function getProducts($user_id = null) {
		return self::with([
			'products' => function ($query) {
				$query->with([
					'metaLang:product_id,name',
					'product_special_one:product_id,price',
					'product_discount_cart:product_id,price,quantity'
				])->select('id', 'price', 'model', 'reward', 'image')->where('status', 1);
			},
			'cart_option' => function($query) {
			    $povi = $query->pluck('product_option_value_id');
			    
				$query->with([
					'options' => function ($query) {
						$query->with([
							'metaLang' => function($query) {
								$query->select('option_id', 'name')->where('lang', session('lang'));
							}
						])->select('id', 'type')->where('status', 1);
					},
					'product_option' => function($query) use($povi) {
						$query->with(['product_option_values' => function ($query) use($povi) {
							$query->with('option_value_description:option_value_id,name')
								->select('id', 'product_option_id', 'option_value_id', 'price')
								->whereIn('id', $povi);
						}])->select('id', 'value');
					}])->select('cart_id', 'option_id', 'product_option_id', 'product_option_value_id')->groupBy('product_option_id')->groupBy('cart_id');
			}
		])
			->select('id', 'product_id', 'quantity')
			->where('session_id', csrf_token())
			->where('customer_id', session('customer_id', 0))
			->where(function($query) use($user_id) {
				if ($user_id) {
					$query->where('user_id', $user_id);
				}
			})
			->get();
	}
}
