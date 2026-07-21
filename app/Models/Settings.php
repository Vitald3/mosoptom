<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'settings';
    
    protected $casts = [
        'setting_id' => 'increments',
        'code' => 'text',
        'value' => 'array'
    ];
}
