<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Filters extends Model
{
    protected $table = 'filters';

    public function meta()
    {
        return $this->hasMany('App\Models\FilterDescription', 'filter_id');
    }

    public function filter_values()
    {
        return $this->hasMany(FilterValues::class, 'filter_id');
    }

    public function metaLang()
    {
        return $this->hasOne('App\Models\FilterDescription', 'filter_id')->where('lang', config('app.locale'));
    }

    public function categories()
    {
        return $this->hasMany('App\Models\FilterCategory', 'filter_id');
    }

    public function metaLangCategory()
    {
        return $this->hasMany('App\Models\CategoryDescription', 'category_id', 'category_id')->where('lang', config('app.locale'));
    }
}
