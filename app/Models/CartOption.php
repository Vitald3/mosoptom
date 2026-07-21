<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartOption extends Model
{
    protected $table = 'cart_option';

    public function options()
    {
        return $this->hasOne(Options::class, 'id', 'option_id');
    }

    public function product_option()
    {
        return $this->hasOne(ProductOption::class, 'id', 'product_option_id');
    }

    public function product_option_values()
    {
        return $this->hasMany(ProductOptionValues::class, 'id', 'product_option_value_id');
    }
}