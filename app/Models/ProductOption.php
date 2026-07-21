<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    protected $table = 'product_option';

    public function product_option_values()
    {
        return $this->hasMany(ProductOptionValues::class, 'product_option_id');
    }
}