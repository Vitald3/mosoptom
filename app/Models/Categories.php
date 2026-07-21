<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'slug',
    ];
	
	public function getSlug() {
		$PathRouteService = app(\App\Helpers\PathRouteService::class);
		return $PathRouteService->getRoute('category_' . session('lang') . '_id=' . $this->id);
	}

    public function parent()
    {
        return $this->belongsTo(Categories::class, 'parent_id')->join('category_description as cd', 'cd.category_id', '=', 'categories.id')->select('name', 'categories.id', 'categories.parent_id');
    }

    public function getParentsName() {
        if($this->parent) {
            return $this->parent->getParentsName(). " > " . $this->name;
        } else {
            return $this->name;
        }
    }

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
	
	public function children()
	{
		return $this->hasMany('App\Models\Categories', 'parent_id');
	}

    public function allChildren()
    {
        return $this->children()->where('status', 1)->with('allChildren');
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'parent_id', 'id')
            ->distinct('cd.name')
            ->join('category_description as cd', 'cd.category_id', '=', 'categories.id')
            ->select('cd.name', 'categories.id', 'categories.parent_id')
            ->where('cd.lang', config('app.locale'));
    }

    public function category_name()
    {
        return $this->category()->where('status', 1)->with('category_name');
    }

    public function products()
    {
        return $this->belongsToMany(Products::class, 'product_category', 'category_id', 'product_id');
    }

    public function filters()
    {
        return $this->belongsToMany(Filters::class, 'filter_category', 'category_id', 'filter_id')->where('filter_category.filter_id', '!=', '');
    }

    public function product_filter()
    {
        return $this->hasMany(FilterProduct::class, 'product_id');
    }

    public function filters2()
    {
        return $this->filters()->with('product_filter');
    }

    public function meta()
    {
        return $this->hasMany('App\Models\CategoryDescription', 'category_id');
    }

    public function metaLang()
    {
        return $this->hasOne('App\Models\CategoryDescription', 'category_id')->where('lang', config('app.locale'));
    }
	
	public static function getCategories() {
		$categories = self::with([
			'metaLang:category_id,name',
		])->select('id', 'parent_id', 'image', 'image2', 'image3')->where('status', 1)->orderBy('sort')->get();
		
		return self::buildTree($categories, 0);
	}
	
	public static function buildTree($categories, $parent_id = 0) {
		$found = $categories->filter(function($item) use ($parent_id){
			return $item->parent_id == $parent_id;
		});
		
		foreach ($found as &$cat) {
			$children = self::buildTree($categories, $cat->id);
			$cat->children = $children;
		}
		
		return $found;
	}
}
