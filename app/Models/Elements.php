<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Elements extends Model
{
    protected $table = 'elements';

    protected $casts = [
        'setting' => 'array'
    ];
}
