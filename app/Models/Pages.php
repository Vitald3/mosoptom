<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pages extends Model
{
    protected $table = 'pages';
	
	public function getSlug() {
		$PathRouteService = app(\App\Helpers\PathRouteService::class);
		return $PathRouteService->getRoute('page_' . session('lang') . '_id=' . $this->id);
	}

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
	
	public function category_name()
	{
		return $this->belongsTo(PageCategories::class, 'parent_id', 'id');
	}
	
	public function str_date()
	{
		$arr = [1 => __('locale.text_month_1'), 2 => __('locale.text_month_2'), 3 => __('locale.text_month_3'), 4 => __('locale.text_month_4'), 5 => __('locale.text_month_5'), 6 => __('locale.text_month_6'), 7 => __('locale.text_month_7'), 8 => __('locale.text_month_8'), 9 => __('locale.text_month_9'), 10 => __('locale.text_month_10'), 11 => __('locale.text_month_11'), 12 => __('locale.text_month_12')];
		
		$day = date('j', \strtotime($this->created_at));
		$month = date('n', \strtotime($this->created_at));
		
		return $day . ' ' . $arr[$month];
	}

    public function meta()
    {
        return $this->hasMany('App\Models\PageDescription', 'page_id');
    }

    public function metaLang()
    {
        return $this->hasOne('App\Models\PageDescription', 'page_id')->where('lang', config('app.locale'));
    }

    public function page_attribute()
    {
        return $this->hasMany('App\Models\PageAttribute', 'page_id')->where('page_attribute.lang', config('app.locale'));
    }

    public function images()
    {
        return $this->hasMany('App\Models\PageImage', 'page_id')->orderBy('created_at');
    }

    public function page_image()
    {
        return $this->hasMany('App\Models\PageImage', 'page_id')->orderBy('created_at');
    }

    public function attributes()
    {
        return $this->belongsToMany(AttributeDescription::class, 'page_attribute', 'page_id', 'attribute_id', '', 'attribute_id');
    }
}
