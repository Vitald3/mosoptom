<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regions extends Model
{
    protected $table = 'regions';

    public function meta()
    {
        return $this->hasMany(RegionDescription::class, 'region_id');
    }

    public function metaLang()
    {
        return $this->hasOne(RegionDescription::class, 'region_id')->where('lang', config('app.locale'));
    }
}
