<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Languages extends Model
{
    protected $table = 'languages';
    protected $primaryKey = 'language_id';
    protected $casts = [
        'language_id' => 'increments'
    ];
}
