<?php
	
	namespace App\Helpers;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Collection;
	use App\Models\Languages;
	use App\Models\Products;
	use App\Models\Pages;
	use App\Models\Categories;
	use App\Models\PageCategories;
	use Illuminate\Support\Facades\Cache;
	
	class PathRouteService
	{
		private $routes = [];
		private $prefix = '';
		private $default_language;
		
		public function __construct() {
			$settings = session('settings');
			$this->default_language = !empty($settings['default_language']) ? $settings['default_language'] : '';
			$this->lang = session('lang');
			$this->region = config('app.region_code');
			$this->url = url('');
			
			if (!preg_match("/\/admin/", \Request::getUri()) && Cache::has('seo_url')) {
				$this->routes = Cache::get('seo_url');
			} else {
				$this->determineCategoriesRoutes();
			}
		}
		
		public function getRoutes()
		{
			return $this->routes;
		}
		
		public function getRoute($id, $lang = '', $region = '')
		{
			if (isset($this->routes[$id])) {
				$lang = !$lang ? $this->lang : $lang;
				$region = !$region ? $this->region : $region;
				
				if ($region) {
					$url = $this->url . ($lang ? '/' . $lang : '');
					$route = str_replace($url, $url . $region, $this->routes[$id]);
				} else {
					$route = $this->routes[$id];
				}
			} else {
				$route = url('/' . $lang);
			}
			
			return $route;
		}
		
		private function determineCategoriesRoutes()
		{
			$langs = Languages::where('status', 1)->orderBy('sort')->get()->pluck('code');
			
			$categories = Categories::select('id', 'parent_id', 'slug')->where('slug', '!=', '')->get()->keyBy('id');
			
			foreach ($categories as $id => $category) {
				$slugs = $this->determineCategorySlugs($category, $categories);
				
				foreach ($langs as $lang) {
					$lang2 = $lang == $this->default_language ? '' : $lang . '/';
					$this->routes['category_' . $lang . '_id=' . $id] = url($lang2 . implode('/', $slugs));
				}
			}
			
			$products = Products::select('id', 'parent_id', 'slug')->where('slug', '!=', '')->get()->keyBy('id');
			
			foreach ($products as $id => $product) {
				foreach ($langs as $lang) {
					$lang2 = $lang == $this->default_language ? '' : $lang . '/';
					
					if (!is_null($product->parent_id)) {
						$this->routes['product_' . $lang . '_id=' . $id] = $this->getRoute('category_' . $lang . '_id=' . $product->parent_id, $lang2) . '/' . $product->slug;
					} else {
						$this->routes['product_' . $lang . '_id=' . $id] = url($lang2 . $product->slug);
					}
				}
			}
			
			$page_categories = PageCategories::select('id', 'parent_id', 'slug')->where('slug', '!=', '')->get()->keyBy('id');
			
			foreach ($page_categories as $id => $category) {
				$slugs = $this->determineCategorySlugs($category, $page_categories);
				
				foreach ($langs as $lang) {
					$lang2 = $lang == $this->default_language ? '' : $lang . '/';
					$this->routes['page_category_' . $lang . '_id=' . $id] = url($lang2 . implode('/', $slugs));
				}
			}
			
			$pages = Pages::select('id', 'parent_id', 'slug')->where('slug', '!=', '')->get()->keyBy('id');
			
			foreach ($pages as $id => $page) {
				foreach ($langs as $lang) {
					$lang2 = $lang == $this->default_language ? '' : $lang . '/';
					
					if (!is_null($page->parent_id)) {
						$this->routes['page_' . $lang . '_id=' . $id] = $this->getRoute('page_category_' . $lang . '_id=' . $page->parent_id, $lang2) . '/' . $page->slug;
					} else {
						$this->routes['page_' . $lang . '_id=' . $id] = url($lang2 . $page->slug);
					}
				}
			}
		}
		
		private function determineCategorySlugs($collection, Collection $collections, array $slugs = [])
		{
			array_unshift($slugs, $collection->slug);
			
			if (!is_null($collection->parent_id) && isset($collections[$collection->parent_id])) {
				$slugs = $this->determineCategorySlugs($collections[$collection->parent_id], $collections, $slugs);
			}
			
			return $slugs;
		}
	}
