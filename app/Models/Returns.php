<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Returns extends Model
{
    protected $table = 'returns';

    public function getHistory() {
        return $this->hasMany(ReturnHistory::class, 'return_id');
    }
}