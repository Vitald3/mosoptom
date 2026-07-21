<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeDescription extends Model
{
    protected $table = 'attribute_description';

    public function product_attribute_image()
    {
        return $this->hasMany('App\Models\ProductAttributeImage', 'attribute_id', 'attribute_id');
    }

    public function page_attribute_image()
    {
        return $this->hasMany('App\Models\PageAttributeImage', 'attribute_id', 'attribute_id');
    }
}
