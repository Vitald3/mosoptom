<?php
	
	namespace App\Http\Controllers;
	
	use App\Models\Categories;
	use App\Models\CategoryDescription;
	use App\Models\FilterCategory;
	use App\Models\FilterProduct;
	use App\Models\FilterProductValue;
	use App\Models\Filters;
	use App\Models\Languages;
	use App\Models\Layouts;
	use App\Models\ProductCategory;
	use App\Models\Products;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Cache;
	
	class CategoriesController extends Controller
	{
		public function __construct($routes = []) {
			$this->settings = session('settings');
			$this->lang = session('lang');
			
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			
			$classes = array('breadcrumb', 'breadcrumb-item');
			$this->breadcrumbs->addCssClasses($classes);
			$this->breadcrumbs->setDivider('');
			
			if (preg_match("/\/admin/", \Request::getUri())) {
				$this->breadcrumbs->addCrumb(__('locale.home'), url('admin'));
			} else {
				$this->breadcrumbs->addCrumb(__('locale.home'), url(''));
			}
			
			$this->params_array = request()->query();
			$params = [];
			
			if (!empty($this->params_array)) {
				foreach ($this->params_array as $key => $param) {
					$params[] = $key . '=' . $param;
				}
			}
			
			$this->params = !empty($this->params) ? '?' . implode('&', $params) : '';
			$this->region = session('region');
			$this->currency = session('currency');
		}
		
		public function index(Request $request){
			$where = [];
			
			$language_default = session('default_language');
			
			if (!is_null($request->status)) {
				$where[] = ['categories.status', '=', $request->status];
				$status = $request->status;
			} else {
				$status = '';
			}
			
			if (!is_null($request->name)) {
				$where[] = ['cd.name', 'like', '%' . $request->name . '%'];
				$name = $request->name;
			} else {
				$name = '';
			}
			
			$where[] = ['cd.lang', '=', $language_default];
			
			if ($request->sort) {
				$sort = $request->sort;
			} else {
				$sort = 'cd.name';
			}
			
			if ($request->order) {
				$order = $request->order;
			} else {
				$order = 'asc';
			}
			
			$limit = session('settings.limit', 25);
			
			$sort_name = url('admin/categories', ['sort' => 'cd.name', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_sort = url('admin/categories', ['sort' => 'categories.sort', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_status = url('admin/categories', ['sort' => 'categories.status', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			
			if (in_array($sort, ['cd.name', 'categories.sort', 'categories.status'])) {
				$categories = Categories::select('categories.sort', 'categories.status', 'categories.id', 'categories.parent_id', 'cd.name as name')
					->join('category_description as cd', 'cd.category_id', '=', 'categories.id')
					->where($where)
					->orderBy($sort, $order)
					->paginate($limit);
			} else {
				$categories = Categories::select('categories.sort', 'categories.status', 'categories.id', 'categories.parent_id', 'cd.name as name')
					->join('category_description as cd', 'cd.category_id', '=', 'categories.id')
					->where($where)
					->orderBy('cd.name')
					->paginate($limit);
			}
			
			$this->breadcrumbs->addCrumb('Категории', url('admin/categories'));
			$breadcrumbs = $this->breadcrumbs->render();
			$params = $this->params;
			$params_array = $this->params_array;
			
			return view('pages.categories', compact('params', 'params_array', 'breadcrumbs', 'sort_sort', 'sort_status', 'sort_name', 'categories', 'status', 'name', 'sort', 'order'));
		}
		
		public function category_autocomplete(Request $request) {
			$json = [];
			
			if ($request->term) {
				$language_default = session('default_language');
				
				$where[] = ['cd.name', 'like', '%' . $request->term . '%'];
				$where[] = ['cd.lang', '=', $language_default];
				$where[] = ['categories.status', '=', 1];
				
				if ($request->id) {
					$where[] = ['categories.id', '!=', $request->id];
				}
				
				foreach (Categories::join('category_description as cd', 'cd.category_id', '=', 'categories.id')->where($where)->limit(5)->pluck('cd.name', 'categories.id') as $key => $c) {
					$json[] = ['id' => $key, 'value' => $c];
				}
			}
			
			return response()->json($json);
		}
		
		public function add() {
			$langs = Languages::orderBy('name', 'asc')->get();
			$layouts = Layouts::orderBy('name', 'asc')->get();
			
			$this->breadcrumbs->addCrumb('Категории', url('admin/categories'));
			$this->breadcrumbs->addCrumb('Создать', url('admin/category_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.category-edit', ['breadcrumbs' => $breadcrumbs, 'layouts' => $layouts, 'layout_id' => old('layout_id'), 'langs' => $langs, 'parent' => old('parent'), 'image' => old('image'), 'image2' => old('image2'), 'image3' => old('image3'), 'meta' => old('meta'), 'parent_id' => old('parent_id'), 'sort' => old('sort'), 'top' => old('top'), 'slug' => old('slug'), 'status' => old('status'), 'action' => asset('admin/category_save') . $this->params, 'id' => '']);
		}
		
		public function edit($id)
		{
			$category = Categories::with('meta')->where('categories.id', $id)->first();
			
			if (!empty($category)) {
				extract($category->toArray());
				$langs = Languages::orderBy('name', 'asc')->get();
				$layouts = Layouts::orderBy('name', 'asc')->get();
				
				$language_default = session('default_language');
				
				$parent = Categories::join('category_description as cd', 'cd.category_id', '=', 'categories.id')->select('cd.name')->where([['cd.lang', $language_default], ['categories.id', $category->parent_id]])->value('cd.name');
				
				$meta = [];
				
				foreach ($category->meta as $description) {
					$meta[$description['lang']] = $description;
				}
				
				$this->breadcrumbs->addCrumb('Категории', url('admin/categories'));
				$this->breadcrumbs->addCrumb('Редактировать', url('admin/category_add'));
				$breadcrumbs = $this->breadcrumbs->render();
				$action = asset('admin/category_save') . $this->params;
				
				return view('pages.category-edit', compact('breadcrumbs', 'layouts', 'layout_id', 'langs', 'parent', 'meta', 'parent_id', 'image', 'image2', 'image3', 'status', 'top', 'slug', 'sort', 'action', 'id'));
			} else {
				return redirect('admin/categories')->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete(Request $request) {
			if ($request->selected) {
				foreach ($request->selected as $s) {
					Categories::where('id', $s)->delete();
					CategoryDescription::where('category_id', $s)->delete();
					Products::where('parent_id', $s)->update(['parent_id', 0]);
					ProductCategory::where('category_id', $s)->delete();
				}
				
				$message = 'Операция успешна';
				$type = 'success';
			} else {
				$message = 'Выделите пункты для удаления';
				$type = 'error';
			}
			
			return redirect('admin/categories' . $this->params)->with($type, $message);
		}
		
		public function save(Request $request) {
			$this->validate($request, [
				'meta.*.name' => 'required',
				'meta.*.meta_title' => 'required',
				'layout_id' => 'required',
				'slug' => 'required|not_in:sort-name,sort-popular,sort-price,page,news,catalog,price|max:255|unique:categories,slug' . (!is_null($request->id) ? ',' . $request->id . ',id' : '') .'|alpha_dash'
			]);
			
			if (!is_null($request->id)) {
				$category['slug'] = $request->slug;
				$category['layout_id'] = $request->layout_id;
				$category['top'] = $request->top ? $request->top : 0;
				$category['image'] = $request->image ? $request->image : '';
				$category['image2'] = $request->image2 ? $request->image2 : '';
				$category['image3'] = $request->image3 ? $request->image3 : '';
				$category['sort'] = $request->sort ? $request->sort : 0;
				$category['parent_id'] = $request->parent_id ? $request->parent_id : 0;
				$category['status'] = $request->status ? $request->status : 0;
				
				Categories::where('id', $request->id)->update($category);
				
				CategoryDescription::where('category_id', $request->id)->delete();
				
				foreach ($request->meta as $lang => $meta) {
					$cd = new CategoryDescription;
					$cd->lang = $lang;
					$cd->category_id = $request->id;
					$cd->name = $meta['name'];
					$cd->meta_title = $meta['meta_title'];
					$cd->meta_description = !empty($meta['meta_description']) ? $meta['meta_description'] : '';
					$cd->meta_keywords = !empty($meta['meta_keywords']) ? $meta['meta_keywords'] : '';
					$cd->description = !empty($meta['description']) ? $meta['description'] : '';
					
					$cd->save();
				}
			} else {
				$category = new Categories;
				$category->slug = $request->slug;
				$category->layout_id = $request->layout_id;
				$category->image = $request->image ? $request->image : '';
				$category->image2 = $request->image2 ? $request->image2 : '';
				$category->image3 = $request->image3 ? $request->image3 : '';
				$category->top = $request->top ? $request->top : 0;
				$category->sort = $request->sort ? $request->sort : 0;
				$category->parent_id = $request->parent_id ? $request->parent_id : 0;
				$category->status = $request->status ? $request->status : 0;
				
				$category->save();
				
				foreach ($request->meta as $lang => $meta) {
					$cd = new CategoryDescription;
					$cd->lang = $lang;
					$cd->category_id = $category->id;
					$cd->name = $meta['name'];
					$cd->meta_title = $meta['meta_title'];
					$cd->meta_description = !empty($meta['meta_description']) ? $meta['meta_description'] : '';
					$cd->meta_keywords = !empty($meta['meta_keywords']) ? $meta['meta_keywords'] : '';
					$cd->description = !empty($meta['description']) ? $meta['description'] : '';
					
					$cd->save();
				}
			}
			
			$routes = app(\App\Helpers\PathRouteService::class);
			Cache::put('seo_url', $routes->getRoutes());
			
			return redirect('admin/categories' . $this->params)->with('success', 'Операция успешна');
		}
		
		public function show(Request $request) {
			$header = new HeaderController;
			$region = config('app.region_code');
			$id = (int)$request->get('category_id');
			$page = (int)$request->get('page');
			$page_url = true;
			$sort_url = true;
			$sort = $request->get('sort');
			$order = $request->get('order');
			$filter_title = '';
			$price_range = '';
			$price_start = 0;
			$price_end = 0;
			$params = (array)$request->get('params');
			$href[] = url('');
			
			if (!$page) {
				$page = 1;
				$page_url = false;
			}
			
			if (!$sort) {
				$sort_url = false;
			}
			
			foreach ($request->get('paths') as $slug) {
				$results = Categories::join('category_description as cd', 'cd.category_id', '=', 'categories.id')
					->select('categories.id', 'categories.layout_id', 'categories.image', 'cd.name', 'cd.meta_title', 'cd.meta_description', 'cd.meta_keywords', 'cd.description')
					->where('categories.status', 1)
					->where('categories.slug', $slug)
					->firstOrFail();
				
				$href[] = $slug;
				$breadcrumbs[$results->id] = ['name' => $results->name, 'url' => $results->getSlug()];
			}
			
			$path = implode('/', (array)$request->get('paths'));
			
			foreach ($params as $key => $slug) {
				if ($key == 0 && isset($params[$key + 1]) && $slug === 'price' && ((!preg_match('/[^0-9-]/i', $params[$key + 1]) || is_numeric($params[$key + 1])) && count(explode('-', $params[$key + 1])) == 2)) {
					$price_range = $params[1];
					unset($params[0]);
					unset($params[1]);
					$path = $path . '/price/' . $price_range;
				}
			}
			
			$filter_ids = [];
			
			if ($params || $price_range) {
				$slugs = [];
				$value_slug_param = [];
				
				if ($price_range) {
					$header->setRobots('noindex, nofollow');
					$price_range = explode('-', $price_range);
					
					$price_start = min($price_range);
					$price_end = max($price_range);
				}
				
				foreach ($params as $key => $param) {
					if ($key % 2 == 0) {
						$slugs[$param] = $param;
						$filter = $param;
						continue;
					}
					
					if (isset($filter)) {
						$values = explode('+', $param);
						
						foreach ($values as $value) {
							$value_slug_param[$filter][] = $value;
						}
					}
				}
				
				if ($value_slug_param && $slugs) {
					$filters = $results->filters()
						->join('filter_description as fd', 'fd.filter_id', '=', 'filters.id')
						->distinct()
						->select('filters.id', 'filters.slug', 'filters.type', 'fd.name')
						->where('filters.status', 1)
						->where('fd.lang', $this->lang)
						->whereIn('slug', $slugs)
						->orderBy('fd.name')
						->get();
					
					if (!$filters->isEmpty()) {
						$slug = '';
						
						foreach ($filters as $filter_row => $filter) {
							if ($filter_row >= 2) {
								$header->setRobots('noindex, nofollow');
							}
							
							if ($filter->type != 'slider') {
								$value_slug_search = $value_slug_param[$filter->slug];
								
								$filter_values = $filter->select('fv.id', 'fv.slug', 'fvd.name')
									->join('filter_values as fv', 'fv.filter_id', '=', 'filters.id')
									->join('filter_value_description as fvd', 'fvd.filter_value_id', '=', 'fv.id')
									->where('fvd.lang', $this->lang)
									->whereIn('fv.slug', $value_slug_search)
									->orderBy('fvd.name')
									->get();
								
								if (!$filter_values->isEmpty()) {
									if ($filter_values->count() >= 2) {
										$header->setRobots('noindex, nofollow');
									}
									
									$value_slug = [];
									
									$filter_title_description = '';
									
									foreach ($filter_values as $fvr => $value) {
										$value_slug[] = $value->slug;
										$filter_ids[$filter->id][] = $value->id;
										
										if (!empty($value->name) && ($fvr < 2 || $filter_row < 3)) {
											if ($filter_title_description) {
												$filter_title_description .= ', ';
											}
											
											$filter_title_description .= $value->name;
										} else {
											break;
										}
									}
									
									if ($value_slug) {
										$filter_title .= ' ' . $filter_title_description;
										$slug .= '/' . $filter->slug . '/' . implode('+', $value_slug);
									}
								}
							} else {
								$value_slug_search = $value_slug_param[$filter->slug];
								
								if (isset($value_slug_search[0])) {
									$value_slug_search = explode('-', $value_slug_search[0]);
									
									if (!is_numeric($value_slug_search[0]) || (isset($value_slug_search[1]) && !is_numeric($value_slug_search[1])) || count($value_slug_search) > 2) {
										foreach ($value_slug_search as &$s) {
											$s = preg_replace('/[0-9]/i', '', $s);
										}
										
										$slug .= '/' . $filter->slug . '/' . implode('-', $value_slug_search);
										continue;
									}
									
									$min[$filter->id] = $value_slug_search[0];
									$max[$filter->id] = isset($value_slug_search[1]) ? $value_slug_search[1] : 0;
									
									if ($value_slug_search) {
										if ($min[$filter->id] == $max[$filter->id] || !$max[$filter->id]) {
											$filter_values = $filter->filter_values()
												->select('filter_values.id', 'fvd.name')
												->join('filter_value_description as fvd', 'fvd.filter_value_id', '=', 'filter_values.id')
												->where('fvd.name', 'like', '%' . $min[$filter->id] . '%')
												->where('fvd.lang', $this->lang)
												->orderBy('fvd.name')
												->get('filter_values.id');
										} else {
											$filter_values = $filter->filter_values()
												->select('filter_values.id', 'fvd.name')
												->join('filter_value_description as fvd', 'fvd.filter_value_id', '=', 'filter_values.id')
												->whereRaw("CONVERT(SUBSTRING(fvd.name, LOCATE('-', fvd.name) + 1), SIGNED INTEGER) between " . (float)$min[$filter->id] . " and " . (float)$max[$filter->id])
												->where('fvd.lang', $this->lang)
												->orderBy('fvd.name')
												->get('filter_values.id');
										}
										
										$postfix = '';
										
										if (!$filter_values->isEmpty()) {
											$postfix = preg_replace('/[0-9]/i', '', $filter_values[0]['name']);
											
											foreach ($filter_values as $value) {
												$filter_ids[$filter->id][] = $value->id;
											}
											
											$slug .= '/' . $filter->slug . '/' . implode('-', $value_slug_search);
										}
										
										$filter_title_description = $min[$filter->id] . (!empty($max[$filter->id]) ? ' - ' . $max[$filter->id] : '') . $postfix;
										$filter_title .= ', ' . $filter->name . ': ' . $filter_title_description;
									}
								}
							}
						}
						
						if ($slug) {
							$path = $path . $slug;
						} else {
							$path = $path. '/' . implode('/', $params);
						}
					}
					else {
						$path = $path . implode('/', $params);
					}
				} else {
					$path = $path . implode('/', $params);
				}
			}
			
			$aliasPath = $results->getSlug() . ($filter_ids ? '/' . ($price_range ? 'price/' . $price_start . '-' . $price_end . '/' : '') . implode('/', $params) : ($price_range ? '/price/' . $price_start . '-' . $price_end : ''));
			
			$path = url('') . ($this->lang != session('settings.default_language') ? '/' . $this->lang : '/') . ($region ? '/' . $region . '/' : '') . $path;
		
			if ($aliasPath != $path) {
				abort(404);
			}
			
			$data['title'] = $results->name;
			
			if (!$order) {
				$order = 'asc';
			}
			
			if (!$sort) {
				$sort = 'price';
			} else {
				$header->setRobots('noindex, nofollow');
			}
			
			$data['order'] = $order;
			$data['canonical'] = $path;
			$data['class'] = 'catalog_product';
			$data['category_id'] = $id;
			
			$meta = [
				'name' => $results->name,
				'meta_title' => $results->meta_title,
				'meta_description' => $results->meta_description,
				'meta_keywords' => $results->meta_keywords,
				'description' => $results->description
			];
			
			$stylesheet[] = [
				'href' => asset('assets/site/css/category.css'),
				'rel' => 'stylesheet'
			];
			
			$stylesheet[] = [
				'href' => asset('assets/site/css/media/category.css'),
				'rel' => 'stylesheet'
			];
			
			$header->setMeta($meta);
			
			$data = array_merge($data, $header->data());
			$region_code = config('app.region_code');
			$this->region['code'] = $region_code ? $region_code . '/' : '';
			
			$categories = $this->getAside($data['categories'], $id);
			
			$key = 1;
			$html = '<ul itemscope itemtype="http://schema.org/BreadcrumbList" class="breadcrumbs breadcrumb breadcrumb-item"><li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . route(session('route_url') . '_home') . '"><span itemprop="name">' . __('locale.home') . '</span></a><meta itemprop="position" content="1"></li>';
			
			foreach ($data['categories'] as $category) {
				if (isset($breadcrumbs[$category['id']])) {
					$key++;
					$html_cat = '';
					
					$html .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">' . (array_key_last($breadcrumbs) != $category['id'] ? '<a itemprop="item" href="' . $breadcrumbs[$category['id']]['url'] . '">' : '') . '<span itemprop="name">' . $breadcrumbs[$category['id']]['name'] . '</span>' . ($category['children'] ? '<svg style="margin-left: 4px" xmlns="http://www.w3.org/2000/svg" width="7" height="7" viewBox="0 0 7 7" fill="none"><g clip-path="url(#clip0_432_56334)"><path d="M3.49998 5.49605C3.37453 5.49605 3.24909 5.44815 3.15344 5.35255L0.143599 2.34268C-0.0478663 2.15122 -0.0478663 1.84079 0.143599 1.6494C0.334987 1.45801 0.645353 1.45801 0.836834 1.6494L3.49998 2.33415L6.16314 1.64949C6.35461 1.45811 6.66494 1.45811 6.85632 1.64949C7.04787 1.84088 7.04787 2.15131 6.85632 2.34277L3.84652 5.35264C3.75083 5.44826 3.62539 5.49605 3.49998 5.49605Z" fill="#797979"/></g><defs><clipPath id="clip0_432_56334"><rect width="7" height="7" fill="white"/></clipPath></defs></svg>' : '') . (array_key_last($breadcrumbs) != $category['id'] ? '</a>' : '') . '<meta itemprop="position" content="' . $key . '">';
					
					foreach ($category['children'] as $children) {
						$html_cat .= '<li><a href="' . $children['url'] . '"><span>' . $children['name'] . '</span></a></li>';
					}
					
					if ($html_cat) $html_cat = '<ul class="list-un-styled overflow-y podcats">' . $html_cat . '</ul>';
					$html .= $html_cat . '</li>';
					
					$html .= $this->breadcrumbs($category['children'], $breadcrumbs, $key);
				}
			}
			
			$html .= '</ul>';
			
			$data['breadcrumbs'] = $html;
			
			$data['aside'] = $categories;
			
			if (!$results->parent_id && $id != 67) {
				$content = new GetContentController($results->layout_id, ['extension.module.bestseller']);
				$data['content_top'] = $content->getPosition('top');
				$data['content_bottom'] = $content->getPosition('bottom');
				
				if ($filter_ids) {
					$products = $results->products()
						->with(
							[
								'product_special_one:product_id,price',
								'product_discount:product_id,price'
							]
						)
						->select('products.id', 'products.image', 'products.price', 'pd.name as name')
						->join('product_description as pd', 'pd.product_id', '=', 'products.id');
					
					foreach ($filter_ids as $filter_id => $filter_value_ids) {
						$products->join('product_filter as pf' . $filter_id, 'pf' . $filter_id . '.product_id', '=', 'products.id')
						->join('filter_values as fv' . $filter_id, 'fv' . $filter_id . '.id', '=', 'pf' . $filter_id . '.filter_value_id')
						->join('filters as f' . $filter_id, 'f' . $filter_id . '.id', '=', 'fv' . $filter_id . '.filter_id');
					}
					
					if ($price_start && $price_start != $price_end) {
						$products->where(function($query) use ($price_start, $price_end) {
							$query->whereHas('product_discount_one', function ($query) use ($price_start, $price_end) {
								$query->wherebetween('price', [$price_start, $price_end]);
							});
							
							$query->OrwhereHas('product_special_one', function ($query) use ($price_start, $price_end) {
								$query->wherebetween('price', [$price_start, $price_end]);
							});
							
							$query->wherebetween('products.price', [$price_start, $price_end]);
						});
					}
					
					$products->where('products.status', 1)
						->where(function($query) use($filter_ids) {
							foreach ($filter_ids as $filter_id => $filter_value_ids) {
								$query->whereRaw("pf" . (int)$filter_id . ".filter_id = " . (int)$filter_id . " and pf" . (int)$filter_id . ".filter_value_id in (?)", [implode(',', $filter_value_ids)]);
							}
						})
						->where('products.slug', '!=', '')
						->where('pd.lang', $this->lang)
						->get();
					
					$products = $products->paginate(session('settings.limit_sait', 25), ['*'], 'page', $page);
				}
				else {
					if (!$params) {
						$products = $results->products()
							->with(
								[
									'product_special_one:product_id,price',
									'product_discount:product_id,price'
								]
							)
							->select('products.id', 'products.image', 'products.price', 'pd.name as name')
							->join('product_description as pd', 'pd.product_id', '=', 'products.id')
							->where(function ($query) use ($price_start, $price_end) {
								$query->where('products.status', 1);
								
								if ($price_start && $price_start != $price_end) {
									$query->where(function($query) use ($price_start, $price_end) {
										$query->whereHas('product_discount_one', function ($query) use ($price_start, $price_end) {
											$query->wherebetween('price', [$price_start, $price_end]);
										});
										
										$query->OrwhereHas('product_special_one', function ($query) use ($price_start, $price_end) {
											$query->wherebetween('price', [$price_start, $price_end]);
										});
										
										$query->wherebetween('products.price', [$price_start, $price_end]);
									});
								}
							})
							->where('products.slug', '!=', '')
							->where('pd.lang', $this->lang)
							->paginate(session('settings.limit_sait', 25), ['*'], 'page', $page);
					}
				}
				
				if (isset($products)) {
					if (in_array($sort, ['name', 'price', 'popular'])) {
						$products->setCollection(
							$products->{$order == 'desc' ? 'sortByDesc' : 'sortBy'}(function ($query) use ($sort) {
								if ($sort == 'price') {
									return isset($query->product_discount[0]) ? $query->product_discount[0]->price : (!is_null($query->product_special_one) ? $query->product_special_one->price : $query->price);
								} elseif (in_array($sort, ['name', 'sort'])) {
									return $query->name;
								} else {
									return $query->{$sort};
								}
							})
						)->{$order == 'desc' ? 'sortByDesc' : 'sortBy'}('sort')->groupBy('products.id');
					}
					
					if ($page > $products->lastPage()) {
						return redirect($request->get('path') . '/page/' . $products->lastPage(), 301);
					}
					
					if ($page == 1 && $page_url) {
						return redirect($request->get('path'), 301);
					}
					
					$data['products'] = $products;
					$data['total'] = $products->total();
				} else {
					$data['products'] = [];
					$data['total'] = 0;
				}
				
				$data['filters'] = [];
				$data['top'] = [];
				$data['select_filters'] = [];
				
				if ($filter_ids) {
					if ($price_start && $price_start != $price_end) {
						$filters_ids = FilterCategory::distinct()->select('p.id', 'pf.filter_value_id')
							->join('product_category as pc', 'pc.category_id', '=', 'filter_category.category_id')
							->join('product_filter as pf', 'pf.product_id', '=', 'pc.product_id')
							->join('products as p', 'p.id', '=', 'pf.product_id')
							->wherebetween('p.price', [$price_start, $price_end]);
						
						$filters_ids->where(function($query) use ($price_start, $price_end) {
							$query->whereExists(function($query) use($price_start, $price_end) {
								$query->select(\DB::raw(1))
									->from('product_discount')
									->whereRaw("customer_group_id = ? and price between ? and ? and ((date_start = '0000-00-00' or date_start < ?) and (date_end = '0000-00-00' or date_end > ?))", [session('customer_group_id'), $price_start, $price_end, now()->format('Y-m-d'), now()->format('Y-m-d')]);
							});
							
							$query->OrwhereExists(function($query) use($price_start, $price_end) {
								$query->select(\DB::raw(1))
									->from('product_special')
									->whereRaw("customer_group_id = ? and price between ? and ? and ((date_start = '0000-00-00' or date_start < ?) and (date_end = '0000-00-00' or date_end > ?))", [session('customer_group_id'), $price_start, $price_end, now()->format('Y-m-d'), now()->format('Y-m-d')]);
							});
						});
						
						$filters_ids = $filters_ids->where('filter_category.category_id', $id)
							->whereIn('filter_category.filter_id', array_keys($filter_ids))
							->where('p.status', 1)->get();
					} else {
						$filters_ids = FilterCategory::select('p.id', 'pf.filter_value_id')
							->join('product_category as pc', 'pc.category_id', '=', 'filter_category.category_id')
							->join('product_filter as pf', 'pf.product_id', '=', 'pc.product_id')
							->join('products as p', 'p.id', '=', 'pf.product_id')
							->where('filter_category.category_id', $id)
							->whereIn('filter_category.filter_id', array_keys($filter_ids))
							->where('p.status', 1)->get();
					}
					
					$ids = [];
					$filter_value_ids = [];
					
					foreach ($filters_ids as $filters_id) {
						$ids[] = $filters_id->id;
						$filter_value_ids[] = $filters_id->filter_value_id;
					}
					
					$filter_products = FilterProduct::whereIn('product_id', $ids)->whereNotIn('filter_value_id', $filter_value_ids)->pluck('filter_value_id');
					foreach($filter_products as $filter_product) $filter_value_ids[] = $filter_product;
					
					$filters = $results->filters()
						->with(
							[
								'metaLang:filter_id,name,description',
								'filter_values' => function ($query) use($filter_value_ids) {
									$query->with([
										'filter_value_description' => function ($query) use($filter_value_ids) {
											$query->select('id', 'filter_value_id', 'name')->orderBy('name');
										}
									])->select('id', 'filter_id', 'sort', 'top', 'slug')->whereIn('filter_values.id', $filter_value_ids)->orderBy('sort');
								}
							]
						)
						->select('filters.id', 'filters.type', 'filters.slug')
						->join('product_filter as pf', 'pf.filter_id', '=', 'filters.id')
						->where('filters.status', 1)
						->whereIn('pf.product_id', $ids)
						->groupBy('filters.id')
						->orderBy('sort')
						->get();
				}
				else {
					if ($price_start && $price_start != $price_end) {
						$filter_value_ids = FilterCategory::distinct()->select('pf.filter_value_id as id')
							->join('product_category as pc', 'pc.category_id', '=', 'filter_category.category_id')
							->join('product_filter as pf', 'pf.product_id', '=', 'pc.product_id')
							->join('products as p', 'p.id', '=', 'pf.product_id')
							->wherebetween('p.price', [$price_start, $price_end]);
						
						$filter_value_ids->where(function($query) use ($price_start, $price_end) {
							$query->whereExists(function($query) use($price_start, $price_end) {
								$query->select(\DB::raw(1))
									->from('product_discount')
									->whereRaw("product_id = p.id and customer_group_id = ? and price between ? and ? and ((date_start = '0000-00-00' or date_start < ?) and (date_end = '0000-00-00' or date_end > ?))", [session('customer_group_id'), $price_start, $price_end, now()->format('Y-m-d'), now()->format('Y-m-d')]);
							});
							
							$query->OrwhereExists(function($query) use($price_start, $price_end) {
								$query->select(\DB::raw(1))
									->from('product_special')
									->whereRaw("product_id = p.id and customer_group_id = ? and price between ? and ? and ((date_start = '0000-00-00' or date_start < ?) and (date_end = '0000-00-00' or date_end > ?))", [session('customer_group_id'), $price_start, $price_end, now()->format('Y-m-d'), now()->format('Y-m-d')]);
							});
						});
						
						$filter_value_ids = $filter_value_ids->where('filter_category.category_id', $id)
							->where('p.status', 1)->get();
						
						$filter_value_ids = $filter_value_ids->toArray();
					} else {
						$fids = \DB::select("select distinct `filter_values`.`id` as `id` from `filter_category`
                        inner join `product_category` as `pc` on `pc`.`category_id` = `filter_category`.`category_id`
                        inner join `product_filter` on `product_filter`.`product_id` = `pc`.`product_id`
                        inner join `filter_values` on `filter_values`.`id` = `product_filter`.`filter_value_id` and `filter_values`.`filter_id` = filter_category.filter_id
                        where `filter_category`.`category_id` = " . (int)$id);
						
						$filter_value_ids = [];
						
						foreach ($fids as $i) {
							$filter_value_ids[] = $i->id;
						}
					}
					
					$filters = $results->filters()
						->with(
							[
								'metaLang:filter_id,name,description',
								'filter_values' => function ($query) use ($filter_value_ids) {
									$query->with([
										'filter_value_description' => function ($query) {
											$query->select('id', 'filter_value_id', 'name')->orderBy('name');
										}
									])->select('id', 'filter_id', 'sort', 'top', 'slug')->whereIn('filter_values.id', $filter_value_ids)->orderBy('sort');
								}
							]
						)
						->select('filters.id', 'filters.type', 'filters.slug')
						->join('product_filter as pf', 'pf.filter_id', '=', 'filters.id')
						->where('filters.status', 1)
						->whereIn('pf.filter_value_id', $filter_value_ids)
						->groupBy('filters.id')
						->orderBy('sort')
						->get();
				}
				
				if (!$filters->isEmpty()) {
					foreach ($filters as $filter) {
						$values = [];
						$slider_values = [];
						$active = false;
						
						foreach ($filter->filter_values as $value) {
							if (!is_null($value->filter_value_description)) {
								if ($filter->type == 'slider') {
									$slider_values[] = preg_replace('~\D+~', '', $value->filter_value_description->name);
								} else {
									if (isset($filter_ids[$filter->id]) && in_array($value->id, $filter_ids[$filter->id])) {
										$active = in_array($value->id, $filter_ids[$filter->id]);
										
										$data['select_filters'][$filter->id]['name'] = $filter->metaLang->name;
										
										$data['select_filters'][$filter->id]['values'][] = [
											'id' => $value->id,
											'name' => $value->filter_value_description->name,
										];
									} else {
										$active = false;
									}
									
									$values[] = [
										'id' => $value->id,
										'name' => $value->filter_value_description->name,
										'active' => $active
									];
								}
								
								if ($value->top) $data['top'][] = [
									'url' => $results->getSlug() . '/' . $filter->slug . '/' . $value->slug,
									'name' => $value->filter_value_description->name,
									'active' => $active
								];
							}
						}
						
						if ($slider_values) {
							$values[] = [
								'start' => round(isset($min[$filter->id]) ? $min[$filter->id] : min($slider_values)),
								'end' => round(isset($max[$filter->id]) ? $max[$filter->id] : max($slider_values)),
								'min' => round(min($slider_values)),
								'max' => round(max($slider_values))
							];
							
							if ($values[0]['start'] == $values[0]['end']) {
								continue;
							}
						}
						
						if ($values) {
							$data['filters'][] = [
								'id' => $filter->id,
								'name' => $filter->metaLang->name,
								'description' => $filter->metaLang->description,
								'type' => $filter->type,
								'values' => $values,
								'active' => $active
							];
						}
					}
				}
				
				if ($data['select_filters']) {
					$data['active_filter'] = num_decline(count($data['select_filters']), [__('locale.text_filter_4'), __('locale.text_filter_4')], false) . ' <b>' . num_decline(count($data['select_filters']), [__('locale.text_filter_1'), __('locale.text_filter_2'), __('locale.text_filter_3')]) . '</b>';
				} else {
					$data['active_filter'] = '';
				}
				
				$price = (array)$results->products()->with([
					'product_special_one:product_id,price',
					'product_discount_one:product_id,price'
				])->get()->toArray();
				
				if ($price) {
					$prices = [];
					
					foreach ($price as $price2) {
						if (round($price2['price'])) $prices[] = $price2['price'];
						if (!empty($price2['product_special_one']['price'])) $prices[] = $price2['product_special_one']['price'];
						if (!empty($price2['product_discount_one']['price'])) $prices[] = $price2['product_discount_one']['price'];
					}
					
					$min = round(min($prices));
					$max = round(max($prices));
					
					if ($min != $max) {
						$stylesheet[] = [
							'href' => asset('assets/site/css/nouislider.min.css'),
							'rel' => 'stylesheet'
						];
						
						$price_filter = [
							'id' => 'p',
							'name' => __('locale.text_filter_price'),
							'description' => '',
							'type' => 'slider',
							'values' => [[
								'start' => round($price_start ? $price_start : $min),
								'end' => round($price_end ? $price_end : $max),
								'min' => $min,
								'max' => $max
							]],
							'active' => true
						];
						
						if ($data['filters']) {
							array_unshift($data['filters'], $price_filter);
						}
					}
				}
				
				$per = $data['total'] - (session('settings.limit_sait', 25) * $page);
				$data['more'] = $per < session('settings.limit_sait', 25) ? $per : session('settings.limit_sait');
				
				if (isset($products) && $prev = $products->previousPageUrl()) {
					$header->setLinkData([
						[
							'href' => $request->get('path') . ($sort_url ? ('/' . 'sort-' . $sort . '/' . $order) : '') . str_replace(['?page=', '&page='], '/page/', str_replace(url()->current(), '', $prev)),
							'rel' => 'prev'
						]
					]);
				}
				
				if (isset($products) && $last = $products->nextPageUrl()) {
					$data['next'] = $request->get('path') . ($sort_url ? ('/' . 'sort-' . $sort . '/' . $order) : '') . str_replace(['?page=', '&page='], '/page/', str_replace(url()->current(), '', $last));
					
					$header->setLinkData([
						[
							'href' => $data['next'],
							'rel' => 'next'
						]
					]);
				} else {
					$data['next'] = false;
				}
				
				$url = $filter_ids || $price_range ? '/' . ($price_range ? 'price/' . $price_start . '-' . $price_end . ($filter_ids ? '/' : '') : '') . implode('/', $params) : '';
				
				$data['sorts'] = [
					[
						'name' => __('locale.text_sort_price_asc'),
						'url' => app(\App\Helpers\PathRouteService::class)->getRoute('category_' . $this->lang . '_id=' . $results->id) . $url . '/sort-price/asc' . ($page > 1 ? '/page/' . $page : ''),
						'active' => $sort == 'price' && $data['order'] == 'asc' ? 1 : 0
					],
					[
						'name' => __('locale.text_sort_price_desc'),
						'url' => app(\App\Helpers\PathRouteService::class)->getRoute('category_' . $this->lang . '_id=' . $results->id) . $url . '/sort-price/desc' . ($page > 1 ? '/page/' . $page : ''),
						'active' => $sort == 'price' && $data['order'] == 'desc' ? 1 : 0
					],
					[
						'name' => __('locale.text_sort_name_asc'),
						'url' => app(\App\Helpers\PathRouteService::class)->getRoute('category_' . $this->lang . '_id=' . $results->id) . $url . '/sort-name/asc' . ($page > 1 ? '/page/' . $page : ''),
						'active' => $sort == 'name' && $data['order'] == 'asc' ? 1 : 0
					],
					[
						'name' => __('locale.text_sort_name_desc'),
						'url' => app(\App\Helpers\PathRouteService::class)->getRoute('category_' . $this->lang . '_id=' . $results->id) . $url . '/sort-name/asc' . ($page > 1 ? '/page/' . $page : ''),
						'active' => $sort == 'name' && $data['order'] == 'desc' ? 1 : 0
					],
					[
						'name' => __('locale.text_sort_popular_asc'),
						'url' => app(\App\Helpers\PathRouteService::class)->getRoute('category_' . $this->lang . '_id=' . $results->id) . $url . '/sort-popular/asc' . ($page > 1 ? '/page/' . $page : ''),
						'active' => $sort == 'popular' && $data['order'] == 'asc' ? 1 : 0
					],
					[
						'name' => __('locale.text_sort_popular_desc'),
						'url' => app(\App\Helpers\PathRouteService::class)->getRoute('category_' . $this->lang . '_id=' . $results->id) . $url . '/sort-popular/desc' . ($page > 1 ? '/page/' . $page : ''),
						'active' => $sort == 'popular' && $data['order'] == 'desc' ? 1 : 0
					]
				];
				
				if ($filter_title) {
					$data['meta_title'] = sprintf(__('locale.meta_title_filter'), $data['title'] . $filter_title, session('settings.name')[$this->lang]);
					$filter_title .= ' {FORMAT1}';
					$data['title'] .= $filter_title;
				} else {
					if (empty($meta['meta_title'])) $meta['meta_title'] = sprintf(__('locale.meta_title_filter_2'), $data['title'], session('settings.name')[$this->lang]);
				}
				
				if ($page > 1) {
					$data['title'] .= sprintf(__('locale.text_page'), $page);
					$data['meta_title'] .= sprintf(__('locale.text_page'), $page);
				}
				
				if ($page == 1) {
					if (!$params) {
						$data['description'] = html_entity_decode($meta['description']);
					} else {
						$data['description'] = '';
					}
					
					if ($filter_title) {
						$data['meta_description'] = sprintf(__('locale.meta_description_filter'), $data['title'], session('settings.name')[$this->lang]);
					}
				} else {
					$data['description'] = '';
					$data['meta_description'] = false;
					$data['meta_keywords'] = false;
				}
			}
			else {
				$data['filters'] = [];
				$data['top'] = [];
				$data['select_filters'] = [];
				$data['sorts'] = [];
				$data['products'] = [];
				$data['description'] = html_entity_decode($meta['description']);
				$content = new GetContentController($results->layout_id);
				$data['content_top'] = $content->getPosition('top');
				$data['content_bottom'] = $content->getPosition('bottom');
			}

            $stylesheet[] = [
                'href' => asset('assets/site/css/popularfolder.css'),
                'rel' => 'stylesheet'
            ];
			
			$cart = new CartController;
			$data['cart'] = $cart->mini_cart($content->getModuleById('saleday'));
			$cart_count = $cart->getCount();
			$data['cart_count'] = $cart_count > 99 ? '99+' : $cart_count;
			$header->setStyle($content->getHtmlStyle());
			$header->setLinkStyle($content->getLinkStyle());
			$header->setScript($content->getScript());
			$header->setLinkData($stylesheet);
			$data['style'] = $header->getStyle();
			$data['links'] = $header->getLinkStyle();
			$data['scripts'] = $header->getScript();
			$data['category_url'] = $results->getSlug();
			$data['price_range'] = $price_range;
			
			return render_view(view('pages.site.category', $data), $this->region);
		}
		
		public function breadcrumbs($categories, $breadcrumbs, $key) {
			$html = '';
			
			foreach ($categories as $category) {
				if (isset($breadcrumbs[$category['id']])) {
					$key++;
					$html_cat = '';
					
					$html .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">' . (array_key_last($breadcrumbs) != $category['id'] ? '<a itemprop="item" href="' . $breadcrumbs[$category['id']]['url'] . '">' : '') . '<span itemprop="name">' . $breadcrumbs[$category['id']]['name'] . '</span>' . ($category['children'] ? '<svg style="margin-left: 4px" xmlns="http://www.w3.org/2000/svg" width="7" height="7" viewBox="0 0 7 7" fill="none"><g clip-path="url(#clip0_432_56334)"><path d="M3.49998 5.49605C3.37453 5.49605 3.24909 5.44815 3.15344 5.35255L0.143599 2.34268C-0.0478663 2.15122 -0.0478663 1.84079 0.143599 1.6494C0.334987 1.45801 0.645353 1.45801 0.836834 1.6494L3.49998 2.33415L6.16314 1.64949C6.35461 1.45811 6.66494 1.45811 6.85632 1.64949C7.04787 1.84088 7.04787 2.15131 6.85632 2.34277L3.84652 5.35264C3.75083 5.44826 3.62539 5.49605 3.49998 5.49605Z" fill="#797979"/></g><defs><clipPath id="clip0_432_56334"><rect width="7" height="7" fill="white"/></clipPath></defs></svg>' : '') . (array_key_last($breadcrumbs) != $category['id'] ? '</a>' : '') . '<meta itemprop="position" content="' . $key . '">';
					
					foreach ($category['children'] as $children) {
						$html_cat .= '<li><a href="' . $children['url'] . '"><span>' . $children['name'] . '</span></a></li>';
					}
					
					if ($html_cat) $html_cat = '<ul class="list-un-styled overflow-y podcats">' . $html_cat . '</ul>';
					$html .= $html_cat . '</li>';
					
					$html .= $this->breadcrumbs($category['children'], $breadcrumbs, $key);
				}
			}
			
			return $html;
		}

		private function getAside($categories, $id) {
            $aside = false;

            foreach ($categories as $category) {
                if ($category['id'] == $id && !empty($category['children'])) {
                    $aside = $category['children'];
                    break;
                } elseif (!empty($category['children'])) {
                    $aside = $this->getAside($category['children'], $id);
                }
            }

            return $aside;
        }
		
		public function filter(Request $request) {
			$json = [];
			
			if ($request->category_id) {
				$url = '';
				
				if ($request->filter || $request->filter_range) {
					$ids = [];
					$value_ids = [];
					
					foreach ((array)$request->filter as $filter_id => $value) {
						$ids[] = $filter_id;
						
						if (is_array($value)) {
							foreach ($value as $filter_value_id) {
								$value_ids[] = $filter_value_id;
							}
						} else {
							$value_ids[] = $value;
						}
					}
					
					foreach ((array)$request->filter_range as $filter_id => $value) {
						if ($filter_id !== 'p') {
							$ids[] = $filter_id;
						}
					}
					
					if (isset($request->filter_range['p'])) {
						$results = Categories::select('id')->where('status', 1)->where('id', $request->category_id)->firstOrFail();
						
						$price = (array)$results->products()->distinct()->select('products.id', 'products.price')->with([
							'product_special_one:product_id,price',
							'product_discount_one:product_id,price'
						])->get()->toArray();
						
						$start = 0;
						$end = 0;
						
						if ($price) {
							$prices = [];
							
							foreach ($price as $price2) {
								if (round($price2['price'])) $prices[] = $price2['price'];
								if (!empty($price2['product_special_one']['price'])) $prices[] = $price2['product_special_one']['price'];
								if (!empty($price2['product_discount_one']['price'])) $prices[] = $price2['product_discount_one']['price'];
							}
							
							$start = (int)round(min($prices));
							$end = (int)round(max($prices));
						}
						
						$min = (int)$request->filter_range['p'][0];
						$max = (int)$request->filter_range['p'][1];
						
						if ($start < $min || $end > $max) {
							if ($min && $min != $max) {
								$url .= '/price/' . (!$min ? 0 : $min) . '-' . $max;
							}
						}
					}
					
					$filter_url = Filters::distinct()->select('filters.id', 'filters.slug', 'filters.type')
						->join('filter_description as fd', 'fd.filter_id', '=', 'filters.id')
						->where('filters.status', 1)
						->where('fd.lang', $this->lang)
						->whereIn('filters.id', $ids)
						->orderBy('fd.name')
						->get();
					
					if (!$filter_url->isEmpty()) {
						foreach ($filter_url as $f) {
							if ($f->type != 'slider') {
								$f->load(['filter_values' => function($query) use ($value_ids) {
									$query->join('filter_value_description as fvd', 'fvd.filter_value_id', '=', 'filter_values.id')
										->where('fvd.lang', $this->lang)
										->whereIn('filter_values.id', $value_ids)
										->orderBy('fvd.name');
								}]);
								
								if (!is_null($f->filter_values)) {
									$value_slug = [];
									
									foreach ($f->filter_values as $value) {
										$value_slug[] = $value->slug;
									}
									
									if ($value_slug) {
										$url .= '/' . $f->slug . '/' . implode('+', $value_slug);
									}
								}
							} elseif ($f->type == 'slider' && isset($request->filter_range[$f->id]) && $range = $request->filter_range[$f->id]) {
								$min = min($range);
								$max = max($range);
								
								if ($min != $max) {
									$url .= '/' . $f->slug . '/' . (!$min ? 0 : $min) . '-' . $max;
								}
							}
						}
					}
				}
			}
			
			if (isset($url)) {
				$url = app(\App\Helpers\PathRouteService::class)->getRoute('category_' . session('lang') . '_id=' . $request->category_id, session('lang'), config('app.region_code')) . (!empty($url) ? $url : '');
				
				if (!$request->only_url) {
					$arrContextOptions = array(
						"ssl" => array(
							"verify_peer" => false,
							"verify_peer_name" => false,
						),
					);
					
					$json['html'] = file_get_contents($url, false, stream_context_create($arrContextOptions));
				}
				elseif($request->filter) {
					$products = $results->products()
						->with(
							[
								'product_special_one:product_id,price',
								'product_discount:product_id,price'
							]
						)
						->select('products.id', 'products.price')
						->join('product_description as pd', 'pd.product_id', '=', 'products.id');
					
					foreach ((array)$request->filter as $filter_id => $value) {
						$products->join('product_filter as pf' . $filter_id, 'pf' . $filter_id . '.product_id', '=', 'products.id')
							->join('filter_values as fv' . $filter_id, 'fv' . $filter_id . '.id', '=', 'pf' . $filter_id . '.filter_value_id')
							->join('filters as f' . $filter_id, 'f' . $filter_id . '.id', '=', 'fv' . $filter_id . '.filter_id');
					}
					
					if (isset($min) && isset($max) && $min != $max) {
						$products->where(function($query) use ($min, $max) {
							$query->whereHas('product_discount_one', function ($query) use ($min, $max) {
								$query->wherebetween('price', [$min, $max]);
							});
							
							$query->OrwhereHas('product_special_one', function ($query) use ($min, $max) {
								$query->wherebetween('price', [$min, $max]);
							});
							
							$query->wherebetween('products.price', [$min, $max]);
						});
					}
					
					$products = $products->where('products.status', 1)
						->where(function($query) use($request) {
							foreach ((array)$request->filter as $filter_id => $value) {
								$ids[] = $filter_id;
								
								if (is_array($value)) {
									$query->whereRaw("pf" . (int)$filter_id . ".filter_id = " . (int)$filter_id . " and pf" . (int)$filter_id . ".filter_value_id in (?)", [implode(',', $value)]);
								} else {
									$query->whereRaw("pf" . (int)$filter_id . ".filter_id = " . (int)$filter_id . " and pf" . (int)$filter_id . ".filter_value_id in (?)", [$value]);
								}
							}
						})
						->where('products.slug', '!=', '')
						->where('pd.lang', $this->lang)
						->count();
					
					$json['count_product_text'] = sprintf(__('locale.text_filter_count'), num_decline($products, [__('locale.text_prod1'), __('locale.text_prod2'), __('locale.text_prod3')]));
					$json['count_product'] = $products;
				}
				
				$json['url'] = $url;
				
				if ($request->_token) {
					$json['token'] = $request->_token;
				}
			}
			
			return response()->json($json);
		}
	}