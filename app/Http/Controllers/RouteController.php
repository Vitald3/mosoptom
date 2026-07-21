<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	
	class RouteController extends Controller
	{
		public function index($query, $prefix) {
			$request = new Request;
			$path = explode('/', $query);
			$count = count($path);
			$page_count = $count - 2;
			$routes = app(\App\Helpers\PathRouteService::class)->getRoutes();
			
			if (isset($path[$page_count]) && $path[$page_count] === 'page') {
				if (!empty($path[$page_count+1]) && (int)$path[$page_count+1] && is_numeric($path[$page_count+1])) {
					$request->merge(['page' => $path[$page_count+1]]);
					unset($path[$page_count]);
					unset($path[$page_count+1]);
				}
			}
			
			$sort_count = count($path) - 2;
			
			if (isset($path[$sort_count]) && in_array($path[$sort_count], ['sort-name', 'sort-popular', 'sort-price'])) {
				if (!empty($path[$sort_count+1]) && in_array($path[$sort_count+1], ['asc', 'desc'])) {
					$sort = str_replace('sort-', '', $path[$sort_count]);
					$request->merge(['sort' => $sort]);
					$request->merge(['order' => $path[$sort_count+1]]);
					unset($path[$sort_count]);
					unset($path[$sort_count+1]);
				}
			}
			
			$path_str = implode('/', $path);
			$request->merge(['path' => $path_str]);
			
			$path_str = str_replace([url('') . '/' . $prefix . '/', url('') . '/' . $prefix], url(''), $path_str);
			
			$region = config('app.region_code');
			
			if ($region) {
				$path_str = str_replace([url('') . '/' . $region . '/', url('') . '/' . $region], url(''), $path_str);
			}
			
			$controller = false;
			$error = false;
			
			if ($result = array_search(url('') . '/' . $path_str, $routes)) {
				if (strpos($result, 'page_' . $prefix . '_id=') !== false) {
					if ($routes[$result] != url('') . '/' . $path_str) {
						$error = true;
					} else {
						$id = (int)str_replace('page_' . $prefix . '_id=', '', $result);
						$request->merge(['page_id' => $id]);
						session(['page_id' => $id]);
						unset($path[count($path) - 1]);
						$controller = new \App\Http\Controllers\PagesController;
					}
				} elseif (strpos($result, 'page_category_' . $prefix . '_id=') !== false) {
					if ($routes[$result] != url('') . '/' . $path_str) {
						$error = true;
					} else {
						$id = (int)str_replace('page_category_' . $prefix . '_id=', '', $result);
						$request->merge(['page_category_id' => $id]);
						session(['page_category_id' => $id]);
						$controller = new \App\Http\Controllers\PageCategoryController;
					}
				} elseif (strpos($result, 'category_' . $prefix . '_id=') !== false) {
					if ($routes[$result] != url('') . '/' . $path_str) {
						$error = true;
					} else {
						$id = (int)str_replace('category_' . $prefix . '_id=', '', $result);
						$request->merge(['category_id' => $id]);
						session(['category_id' => $id]);
						$controller = new \App\Http\Controllers\CategoriesController;
					}
				} elseif (strpos($result, 'product_' . $prefix . '_id=') !== false) {
					if ($routes[$result] != url('') . '/' . $path_str) {
						$error = true;
					} else {
						$id = (int)str_replace('product_' . $prefix . '_id=', '', $result);
						$request->merge(['product_id' => $id]);
						session(['product_id' => $id]);
						unset($path[count($path) - 1]);
						$controller = new \App\Http\Controllers\ProductsController;
					}
				}
			}
			
			if (!$controller) {
				$url_array = array_reverse(explode('/', $path_str));
				$u = url('');
				$urls_split = [];
				
				foreach ($url_array as $key => $url) {
					$urls_split[] = $url;
					unset($url_array[$key]);
					$replace_url = $u . '/' . implode('/', array_reverse($url_array));
					
					if ($result = array_search($replace_url, $routes)) {
						$id = (int)str_replace('category_' . $prefix . '_id=', '', $result);
						$params = explode('/', str_replace($routes[$result] . '/', '', $replace_url . '/' . implode('/', array_reverse($urls_split))));
						$request->merge(['category_id' => $id]);
						$request->merge(['params' => $params]);
						session(['category_id' => $id]);
						
						$path = array_diff($path, array_reverse($params));
				
						$controller = new \App\Http\Controllers\CategoriesController;
						break;
					}
				}
			}
			
			if (!isset($path)) $path = [];
			
			$request->merge(['paths' => $path]);
			
			if (!$controller || $error) {
				$controller = new \App\Http\Controllers\ErrorController;
			}
			
			return $controller->show($request);
		}
	}