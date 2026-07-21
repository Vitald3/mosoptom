<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $table = 'product_attribute';

    public function metaLangCategory()
    {
        return $this->hasMany('App\Models\AttributeDescription', 'attribute_id', 'attribute_id')->where('lang', config('app.locale'));
    }

    public function attribute_image()
    {
        return $this->hasOne('App\Models\Attributes', 'id', 'attribute_id')->select('id', 'image');
    }

    public function product_attribute_image()
    {
        return $this->hasMany('App\Models\ProductAttributeImage', 'product_id', 'product_id');
    }

    public function product_attribute_imageById()
    {
        return $this->hasMany('App\Models\ProductAttributeImage', 'attribute_id', 'attribute_id');
    }
}
