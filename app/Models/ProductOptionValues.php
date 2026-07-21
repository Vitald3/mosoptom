<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOptionValues extends Model
{
    protected $table = 'product_option_values';

    public function option_value_description()
    {
        return $this->hasOne(OptionValueDescription::class, 'option_value_id', 'option_value_id');
    }

    public function product_option()
    {
        return $this->hasOne(ProductOption::class, 'id', 'product_option_id');
    }

    public function option_description()
    {
        return $this->hasOne(OptionDescription::class, 'option_id', 'option_id');
    }
	
	public function metaLang()
	{
		return $this->hasOne(OptionValueDescription::class, 'option_value_id', 'option_value_id')->where('lang', session('lang'));
	}
}