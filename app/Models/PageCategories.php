<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageCategories extends Model
{
    protected $table = 'page_categories';
	
	public function getSlug() {
		$PathRouteService = app(\App\Helpers\PathRouteService::class);
		return $PathRouteService->getRoute('page_category_' . session('lang') . '_id=' . $this->id);
	}

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
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

    public function category_name()
    {
        return $this->children2()->where('status', 1)->with('category_name');
    }

    public function children2()
    {
        return $this->belongsTo(PageCategories::class, 'parent_id', 'id')
            ->distinct('pcd.name')
            ->join('page_category_description as pcd', 'pcd.category_id', '=', 'page_categories.id')
            ->select('pcd.name', 'page_categories.id', 'page_categories.parent_id')
            ->where('pcd.lang', config('app.locale'));
    }

    public function children()
    {
        return $this->hasMany('App\Models\PageCategories', 'parent_id');
    }

    public function meta()
    {
        return $this->hasMany('App\Models\PageCategoryDescription', 'category_id');
    }

    public function metaLang()
    {
        return $this->hasOne('App\Models\PageCategoryDescription', 'category_id')->where('lang', config('app.locale'));
    }

    public function pages()
    {
        return $this->hasMany(Pages::class, 'parent_id');
    }
}
