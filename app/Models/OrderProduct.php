<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $table = 'order_product';

    public function options() {
        return $this->hasMany(OrderOption::class, 'order_id', 'order_id');
    }
}
