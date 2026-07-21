<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OptionValues extends Model
{
    protected $table = 'option_values';

    public function option_value_description()
    {
        return $this->hasOne(OptionValueDescription::class, 'option_value_id');
    }
}
