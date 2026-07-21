<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilterCategory extends Model
{
    protected $table = 'filter_category';

    public function metaLangCategory()
    {
        return $this->hasMany('App\Models\CategoryDescription', 'category_id', 'category_id')->where('lang', config('app.locale'));
    }
}
