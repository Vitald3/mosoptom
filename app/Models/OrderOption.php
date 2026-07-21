<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderOption extends Model
{
    protected $table = 'order_option';

    public function product_option_values() {
        return $this->hasOne(ProductOptionValues::class, 'id', 'product_option_value_id');
    }
}
