<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Options extends Model
{
    protected $table = 'options';

    public function meta()
    {
        return $this->hasMany(OptionDescription::class, 'option_id');
    }

    public function metaLang()
    {
        return $this->hasOne(OptionDescription::class, 'option_id')->where('lang', config('app.locale'));
    }

    public function option_values()
    {
        return $this->hasMany(OptionValues::class, 'option_id');
    }

    public function product_option_values()
    {
        return $this->hasOne(ProductOptionValues::class, 'option_id');
    }

    public function product_option()
    {
        return $this->hasMany(ProductOption::class, 'option_id');
    }
}
