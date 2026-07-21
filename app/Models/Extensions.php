<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Extensions extends Model
{
    protected $table = 'extensions';

    protected $casts = [
        'setting' => 'array'
    ];

    public static function getModulesByCode($code) {
        return self::where('code', $code)->orderBy('name')->get();
    }

    public static function getExtensions($type) {
        $results = [];

        $extensions = Settings::select('code', 'value')->where([['code', 'like', '%extension.' . $type . '.%'], ['value->status', 1]])->orderBy('value->sort_order')->get();

        foreach ($extensions as $extension) {
            $results[] = array(
                'code' => str_replace('extension.' . $type . '.', '', $extension->code),
                'setting' => $extension->value
            );
        }

        return $results;
    }
	
	public static function getSettingModule($code) {
		$setting = Settings::where('code', 'extension.module.' . $code)->value('value');
		
		if (!empty($setting)) {
			$extension = '\App\Http\Controllers\Extensions\Module\\' . ucfirst($code) . 'Controller';
			$extension = new $extension;
			return ['html' => $extension->index($setting), 'style' => $extension->getLinkStyle(), 'script' => $extension->getScript()];
		}
	}
}
