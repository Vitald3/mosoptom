<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attributes extends Model
{
    protected $table = 'attributes';

    public function meta()
    {
        return $this->hasMany('App\Models\AttributeDescription', 'attribute_id');
    }

    public function metaLang()
    {
        return $this->hasMany('App\Models\AttributeDescription', 'attribute_id')->where('lang', config('app.locale'));
    }

    public function product_attribute()
    {
        return $this->hasMany('App\Models\ProductAttribute', 'attribute_id');
    }
}
