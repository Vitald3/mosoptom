<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageAttribute extends Model
{
    protected $table = 'page_attribute';

    public function metaLangCategory()
    {
        return $this->hasMany('App\Models\AttributeDescription', 'attribute_id', 'attribute_id')->where('lang', config('app.locale'));
    }

    public function attribute_image()
    {
        return $this->hasOne('App\Models\Attributes', 'id', 'attribute_id')->select('id', 'image');
    }

    public function page_attribute_image()
    {
        return $this->hasMany('App\Models\PageAttributeImage', 'page_id', 'page_id');
    }

    public function page_attribute_imageById()
    {
        return $this->hasMany('App\Models\PageAttributeImage', 'attribute_id', 'attribute_id');
    }
}
