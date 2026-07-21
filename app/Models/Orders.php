<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $table = 'orders';

    protected $casts = [
        'fields' => 'array'
    ];

    public function customer()
    {
        return $this->hasOne(Customers::class, 'id', 'customer_id');
    }

    public function products()
    {
        return $this->hasMany(OrderProduct::class, 'order_id');
    }

    public function options()
    {
        return $this->hasMany(OrderOption::class, 'order_product_id');
    }
	
	public function totals() {
		return $this->hasMany(OrderTotal::class, 'order_id');
	}
	
	public function history() {
		return $this->hasMany(OrderHistory::class, 'order_id');
	}
}
