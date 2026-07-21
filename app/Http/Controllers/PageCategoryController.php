<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Cache;
	use App\Models\Pages;
	use App\Models\PageCategories;
	use App\Models\PageCategoryDescription;
	use App\Models\Settings;
	use App\Models\Layouts;
	use App\Models\Languages;
	use App\Helpers\Helper;
	use Carbon\Carbon;
	
	class PageCategoryController extends Controller
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
		}
		
		public function index(Request $request){
			$where = [];
			
			$language_default = session('default_language');
			
			if (!is_null($request->status)) {
				$where[] = ['page_categories.status', '=', $request->status];
				$status = $request->status;
			} else {
				$status = '';
			}
			
			if (!is_null($request->name)) {
				$where[] = ['pcd.name', 'like', '%' . $request->name . '%'];
				$name = $request->name;
			} else {
				$name = '';
			}
			
			$where[] = ['pcd.lang', '=', $language_default];
			
			if ($request->sort) {
				$sort = $request->sort;
			} else {
				$sort = 'pcd.name';
			}
			
			if ($request->order) {
				$order = $request->order;
			} else {
				$order = 'asc';
			}
			
			$limit = session('settings.limit', 25);
			
			$sort_name = url('admin/pages', ['sort' => 'pcd.name', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_sort = url('admin/pages', ['sort' => 'page_categories.sort', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_status = url('admin/pages', ['sort' => 'page_categories.status', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			
			if (in_array($sort, ['pcd.name', 'page_categories.sort', 'page_categories.status'])) {
				$categories = PageCategories::select('page_categories.sort', 'page_categories.status', 'page_categories.id', 'pcd.name as name')
					->join('page_category_description as pcd', 'pcd.category_id', '=', 'page_categories.id')
					->where($where)
					->orderBy($sort, $order)
					->paginate($limit);
			} else {
				$categories = PageCategories::select('page_categories.sort', 'page_categories.status', 'page_categories.id', 'pcd.name as name')
					->join('page_category_description as pcd', 'pcd.category_id', '=', 'page_categories.id')
					->where($where)
					->orderBy('pcd.name')
					->paginate($limit);
			}
			
			$this->breadcrumbs->addCrumb('Категории статей', url('admin/page_categories') . $this->params);
			$breadcrumbs = $this->breadcrumbs->render();
			$params = $this->params;
			$params_array = $this->params_array;
			
			return view('pages.page_categories', compact('params', 'params_array', 'breadcrumbs', 'sort_sort', 'sort_status', 'sort_name', 'categories', 'status', 'name', 'sort', 'order'));
		}
		
		public function category_autocomplete(Request $request) {
			$json = [];
			
			if ($request->term) {
				$language_default = session('default_language');
				
				$where[] = ['pcd.name', 'like', '%' . $request->term . '%'];
				$where[] = ['pcd.lang', '=', $language_default];
				$where[] = ['page_categories.status', '=', 1];
				
				if ($request->id) {
					$where[] = ['page_categories.id', '!=', $request->id];
				}
				
				foreach (PageCategories::join('page_category_description as pcd', 'pcd.category_id', '=', 'page_categories.id')->where($where)->limit(5)->pluck('pcd.name', 'page_categories.id') as $key => $c) {
					$json[] = ['id' => $key, 'value' => $c];
				}
			}
			
			return response()->json($json);
		}
		
		public function add() {
			$langs = Languages::orderBy('name', 'asc')->get();
			$layouts = Layouts::orderBy('name', 'asc')->get();
			$this->breadcrumbs->addCrumb('Категории статей', url('admin/page_categories') . $this->params);
			$this->breadcrumbs->addCrumb('Создать', url('admin/page_category_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.page_category-edit', ['breadcrumbs' => $breadcrumbs, 'layout_id' => old('layout_id'), 'layouts' => $layouts, 'langs' => $langs, 'parent' => old('parent'), 'meta' => old('meta'), 'image' => old('image'), 'parent_id' => old('parent_id'), 'sort' => old('sort'), 'slug' => old('slug'), 'status' => old('status'), 'action' => asset('admin/page_category_save') . $this->params, 'id' => '']);
		}
		
		public function edit($id)
		{
			$category = PageCategories::with('meta')->where('id', $id)->first();
			
			if (!empty($category)) {
				extract($category->toArray());
				$langs = Languages::orderBy('name', 'asc')->get();
				$layouts = Layouts::orderBy('name', 'asc')->get();
				
				$language_default = session('default_language');
				
				$parent = PageCategories::leftjoin('page_category_description as pcd', 'pcd.category_id', '=', 'page_categories.id')->select('pcd.name')->where([['pcd.lang', $language_default], ['page_categories.id', $category->parent_id]])->value('pcd.name');
				
				$meta = [];
				
				foreach ($category->meta as $description) {
					$meta[$description['lang']] = $description;
				}
				
				$this->breadcrumbs->addCrumb('Категории статей', url('admin/page_categories') . $this->params);
				$this->breadcrumbs->addCrumb('Редактировать', url('admin/page_category/' . $id));
				$breadcrumbs = $this->breadcrumbs->render();
				$action = asset('admin/page_category_save') . $this->params;
				
				return view('pages.page_category-edit', compact('breadcrumbs', 'layout_id', 'layouts', 'langs', 'parent', 'meta', 'parent_id', 'image', 'status', 'slug', 'sort', 'action', 'id'));
			} else {
				return redirect('admin/page_categories' . $this->params)->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete(Request $request) {
			if ($request->selected) {
				$message = 'Операция успешна';
				$type = 'success';
				
				foreach ($request->selected as $s) {
					if (!$count = Pages::where('parent_id', $s)->count()) {
						PageCategories::where('id', $s)->delete();
						PageCategoryDescription::where('category_id', $s)->delete();
						Pages::where('parent_id', $s)->update(['parent_id' => 0]);
					} else {
						$name = PageCategoryDescription::where('category_id', $s)->value('name');
						$message = 'Категорию "' . $name . '" нельзя удалить, так как она присвоена ' . Helper::num_decline($count, 'статье, статьям, статьи');
						$type = 'error';
					}
				}
			} else {
				$message = 'Выделите пункты для удаления';
				$type = 'error';
			}
			
			return redirect('admin/page_categories' . $this->params)->with($type, $message);
		}
		
		public function save(Request $request) {
			$this->validate($request, [
				'meta.*.name' => 'required',
				'meta.*.meta_title' => 'required',
				'layout_id' => 'required',
				'slug' => 'required|not_in:sort-name,sort-popular,sort-price,page,news,catalog,price|max:255|unique:page_categories,slug' . (!is_null($request->id) ? ',' . $request->id . ',id' : '') .'|alpha_dash'
			]);
			
			if (!is_null($request->id)) {
				$category['slug'] = $request->slug;
				$category['layout_id'] = $request->layout_id;
				$category['image'] = $request->image ? $request->image : '';
				$category['sort'] = $request->sort ? $request->sort : 0;
				$category['parent_id'] = $request->parent_id ? $request->parent_id : 0;
				$category['status'] = $request->status ? $request->status : 0;
				
				PageCategories::where('id', $request->id)->update($category);
				
				PageCategoryDescription::where('category_id', $request->id)->delete();
				
				foreach ($request->meta as $lang => $meta) {
					$cd = new PageCategoryDescription;
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
				$category = new PageCategories;
				$category->slug = $request->slug;
				$category->layout_id = $request->layout_id;
				$category->image = $request->image ? $request->image : '';
				$category->sort = $request->sort ? $request->sort : 0;
				$category->parent_id = $request->parent_id ? $request->parent_id : 0;
				$category->status = $request->status ? $request->status : 0;
				
				$category->save();
				
				foreach ($request->meta as $lang => $meta) {
					$cd = new PageCategoryDescription;
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
			
			return redirect('admin/page_categories' . $this->params)->with('success', 'Операция успешна');
		}
		
		public function show(Request $request) {
			$header = new HeaderController;
			$id = (int)$request->get('page_category_id');
			$page = (int)$request->get('page');
			$page_url = true;
			$sort_url = true;
			$sort = $request->get('sort');
			$order = $request->get('order');
			
			if (!$page) {
				$page = 1;
				$page_url = false;
			}
			
			if (!$sort) {
				$sort_url = false;
			}
			
			foreach ($request->get('paths') as $slug) {
				$results = PageCategories::with([
					'metaLang' => function($query) {
						$query->select('category_id', 'name');
					}
				])->select('id')->where([['status', 1], ['slug', $slug]])->firstOrFail();
				
				if (!empty($results->metaLang)) {
					$this->breadcrumbs->addCrumb($results->metaLang->name, $results->getSlug());
				}
			}
			
			$results = PageCategories::with([
				'metaLang' => function($query) {
					$query->select('category_id', 'name', 'description', 'meta_title', 'meta_description', 'meta_keywords');
				}
			])->select('id', 'image', 'layout_id')->where([['status', 1], ['id', $id]])->firstOrFail();
			
			$meta = $results->metaLang->toArray();
			
			if (!$order) {
				$order = 'asc';
			}
			
			if (!$sort) {
				$sort = 'pd.name';
			} else {
				$header->setRobots('noindex, nofollow');
			}
			
			$header->setLinkData([
				[
					'href' => asset('assets/site/css/page_category.css'),
					'rel' => 'stylesheet'
				],
				[
					'href' => asset('assets/site/css/media/page_category.css'),
					'rel' => 'stylesheet'
				]
			]);
			
			$data['page_categories'] = [];
			
			$categories = $results->children()->select('id')->orderBy('sort')->get();
			
			foreach ($categories as $category) {
				$data['page_categories'][] = [
					'name' => $category->metaLang->name,
					'url' => $category->getSlug()
				];
			}
			
			$data['arr'] = [1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель', 5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август', 9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь'];
			
			$pages = $results->pages()
				->with(
					[
						'page_attribute' => function ($query) {
							$query->select('page_id', 'attribute_id', 'text')->limit(4);
						}
					]
				)
				->join('page_description as pd', 'pd.page_id', '=', 'pages.id')
				->select('pages.id', 'pages.created_at', 'pd.name', 'pd.description')
				->where('pages.status', 1)
				->orderBy($sort, $order)
				->paginate(session('settings.sayt_limit', 25), ['*'], 'page', $page);
			
			if (!$pages->isEmpty()) {
				if ($page > $pages->lastPage()) {
					return redirect($request->get('path') . '/page/' . $pages->lastPage(), 301);
				}
				
				if ($page == 1 && $page_url) {
					return redirect($request->get('path'), 301);
				}
				
				$data['pages'] = $pages;
			}
			
			if (!$pages->isEmpty() && $last = $pages->nextPageUrl()) {
				$data['next'] = $request->get('path') . ($sort_url ? ('/' . $sort . '/' . $order) : '') . str_replace(['?page=', '&page='], '/page/', str_replace(url()->current(), '', $last));
			} else {
				$data['next'] = false;
			}
			
			$data['class'] = 'page_category';
			$data['canonical'] = $request->get('path');
			$data['title'] = $meta['name'];
			$data['updated_at'] = $results->updated_at;
			$data['created_at'] = $results->created_at;
			
			if ($page == 1) {
				$data['description'] = html_entity_decode($meta['description']);
			} else {
				$data['description'] = '';
				$meta['meta_description'] = false;
				$meta['meta_keywords'] = false;
			}
			
			if ($page > 1) {
				$data['title'] .= sprintf(__('locale.text_page'), $page);
				$meta['meta_title'] .= sprintf(__('locale.text_page'), $page);
			}
			
			$header->setMeta($meta);
			
			$content = new GetContentController($results->layout_id);
			$data['content_top'] = $content->getPosition('top');
			$data['content_bottom'] = $content->getPosition('bottom');
			$header->setStyle($content->getHtmlStyle());
			$header->setLinkStyle($content->getLinkStyle());
			$header->setScript($content->getScript());
			$cart = new CartController;
			$data['cart'] = $cart->mini_cart($content->getModuleById('saleday'));
			$cart_count = $cart->getCount();
			$data['cart_count'] = $cart_count > 99 ? '99+' : $cart_count;
			$region_code = config('app.region_code');
			$this->region['code'] = $region_code ? $region_code . '/' : '';
			
			$header->setBreadcrumbs($this->breadcrumbs->render());
			$data = array_merge($data, $header->data());
			
			return render_view(view('pages.site.page_category', $data), $this->region);
		}
	}
