<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LayoutExtension extends Model
{
    protected $table = 'layout_extension';

    public static function getLayoutModules($layout_id) {
        return self::where('layout_id', $layout_id)->orderBy('sort')->get();
    }
	
	public function extensions() {
		return $this->hasOne(Extensions::class, 'id', 'extension_id');
	}
}
