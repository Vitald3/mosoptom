<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilterValues extends Model
{
    protected $table = 'filter_values';

    public function filter_value_description()
    {
        return $this->hasOne('App\Models\FilterValueDescription', 'filter_value_id', 'id')->where('lang', config('app.locale'));
    }

    public function filter_value_description2()
    {
        return $this->hasMany('App\Models\FilterValueDescription', 'filter_value_id', 'id');
    }
}
