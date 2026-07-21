<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Attributes;
	use App\Models\ProductAttribute;
	use App\Models\AttributeDescription;
	use App\Models\Filters;
	use App\Models\FilterDescription;
	use App\Models\FilterCategory;
	use App\Models\FilterProduct;
	use App\Models\FilterValues;
	use App\Models\FilterValueDescription;
	use App\Models\Categories;
	use App\Models\CategoryDescription;
	use App\Models\Languages;
	use App\Models\FilterProductValue;
	use DB;
	use Carbon\Carbon;
	
	class FiltersController extends Controller
	{
		private $settings = [];
		private $breadcrumbs;
		
		public function __construct() {
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			
			$classes = array('breadcrumb', 'breadcrumb-item');
			$this->breadcrumbs->addCssClasses($classes);
			$this->breadcrumbs->setDivider('');
			
			$this->breadcrumbs->addCrumb(__('locale.home'), url('admin'));
			$this->settings = session('settings');
			$this->lang = session('lang');
			
			$this->params_array = request()->query();
			$params = [];
			
			if (!empty($this->params_array)) {
				foreach ($this->params_array as $key => $param) {
					$params[] = $key . '=' . $param;
				}
			}
			
			$this->params = !empty($this->params) ? '?' . implode('&', $params) : '';
		}
		
		public function index(Request $request){
			$where = [];
			
			$language_default = session('default_language');
			
			if (!is_null($request->status)) {
				$where[] = ['filters.status', '=', $request->status];
				$status = $request->status;
			} else {
				$status = '';
			}
			
			if (!is_null($request->name)) {
				$where[] = ['fd.name', 'like', '%' . $request->name . '%'];
				$name = $request->name;
			} else {
				$name = '';
			}
			
			if (!empty($request->category_id)) {
				$where[] = ['fc.category_id', '=', $request->category_id];
				$category_id = $request->category_id;
			} else {
				$category_id = 0;
			}
			
			if (!is_null($request->category)) {
				$category = $request->category;
			} else {
				$category = '';
			}
			
			$where[] = ['fd.lang', '=', $language_default];
			
			if ($request->sort) {
				$sort = $request->sort;
			} else {
				$sort = 'fd.name';
			}
			
			if ($request->order) {
				$order = $request->order;
			} else {
				$order = 'asc';
			}
			
			$limit = session('settings.limit');
			
			$sort_name = url('admin/filters', ['sort' => 'fd.name', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_sort = url('admin/filters', ['sort' => 'filters.sort', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_status = url('admin/filters', ['sort' => 'filters.status', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			
			if (in_array($sort, ['fd.name', 'filters.sort', 'filters.status'])) {
				if ($category_id) {
					$filters = Filters::with('categories.metaLangCategory')->distinct()->select('filters.sort', 'filters.id', 'filters.status', 'fd.name')->leftjoin('filter_description as fd', 'fd.filter_id', '=', 'filters.id')->leftjoin('filter_category as fc', 'fc.filter_id', '=', 'filters.id')->leftjoin('category_description as cd', 'cd.category_id', '=', 'fc.category_id')->where($where)->orderBy($sort, $order)->paginate($limit);
				} else {
					$filters = Filters::with('categories.metaLangCategory')->distinct()->select('filters.sort', 'filters.id', 'filters.status', 'fd.name')->leftjoin('filter_description as fd', 'fd.filter_id', '=', 'filters.id')->where($where)->orderBy($sort, $order)->paginate($limit);
				}
			} else {
				if ($category_id) {
					$filters = Filters::with('categories.metaLangCategory')->distinct()->select('filters.sort', 'filters.id', 'filters.status', 'fd.name')->leftjoin('filter_description as fd', 'fd.filter_id', '=', 'filters.id')->leftjoin('filter_category as fc', 'fc.filter_id', '=', 'filters.id')->leftjoin('category_description as cd', 'cd.category_id', '=', 'fc.category_id')->where($where)->orderBy('fd.name')->paginate($limit);
				} else {
					$filters = Filters::with('categories.metaLangCategory')->distinct()->select('filters.sort', 'filters.id', 'filters.status', 'fd.name')->leftjoin('filter_description as fd', 'fd.filter_id', '=', 'filters.id')->where($where)->orderBy('fd.name')->paginate($limit);
				}
			}
			
			if (!$filters->isEmpty()) {
				foreach ($filters as &$filter) {
					$categories = [];
					
					if (!is_null($filter->categories)) {
						foreach ($filter->categories as $category2) {
							$categories[] = $category2->metaLangCategory[0]['name'];
						}
						
						if (count($categories) > 4) {
							$categories = array_slice($categories, 0, 4);
							
							$categories[4 - 1] .= ' <i>... и еще <b>' . ($filter->categories->count() - 4) . '</b></i>';
						}
						
						$filter->categories = implode(' • ', $categories);
					}
				}
			}
			
			$this->breadcrumbs->addCrumb('Фильтры', url('admin/filters') . $this->params);
			$breadcrumbs = $this->breadcrumbs->render();
			$params = $this->params;
			$params_array = $this->params_array;
			
			return view('pages.filters', compact('params', 'params_array', 'breadcrumbs', 'category', 'category_id', 'sort_name', 'sort_sort', 'sort_status', 'filters', 'name', 'status', 'sort', 'order'));
		}
		
		public function filter_autocomplete(Request $request) {
			$json = [];
			
			if ($request->term) {
				$language_default = session('default_language');
				
				$where[] = ['fd.name', 'like', '%' . $request->term . '%'];
				$where[] = ['fd.lang', '=', $language_default];
				$where[] = ['filters.status', '=', 1];
				
				if ($request->id) {
					$where[] = ['filters.id', '!=', $request->id];
				}
				
				foreach (Filters::join('filter_description as fd', 'fd.filter_id', '=', 'filters.id')->where($where)->limit(5)->pluck('fd.name', 'filters.id') as $key => $c) {
					$json[] = ['id' => $key, 'value' => $c];
				}
			}
			
			return response()->json($json);
		}
		
		public function add() {
			$langs = Languages::orderBy('name', 'asc')->get();
			$categories = Categories::with('metaLang')->where('status', 1)->get();
			
			$this->breadcrumbs->addCrumb('Фильтры', url('admin/filters') . $this->params);
			$this->breadcrumbs->addCrumb('Создать', url('admin/filter_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.filter-edit', ['breadcrumbs' => $breadcrumbs, 'categories' => $categories, 'filter_values' => (array)old('filter_values'), 'filter_category' => (array)old('filter_category'), 'langs' => $langs, 'meta' => old('meta'), 'image' => old('image'), 'type' => old('type'), 'sort' => old('sort'), 'slug' => old('slug'), 'status' => old('status'), 'action' => asset('admin/filter_save') . $this->params, 'action2' => asset('admin/add_image'), 'id' => '']);
		}
		
		public function edit($id)
		{
			$filter = Filters::with('meta')->where('id', $id)->first();
			
			if (!empty($filter)) {
				$langs = Languages::orderBy('name', 'asc')->get();
				$filter_category = FilterCategory::where('filter_id', $id)->pluck('category_id')->toArray();
				$categories = Categories::with('metaLang')->where('status', 1)->get();
				
				$filter_value = FilterValues::with('filter_value_description2:filter_value_id,name,lang')->where('filter_id', $id)->get()->toArray();
				$filter_values = [];
				
				foreach ($filter_value as $value) {
					if (!empty($value['filter_value_description2'])) {
						foreach ($value['filter_value_description2'] as $value_description) {
							$value['description'][$value_description['lang']] = ['name' => $value_description['name']];
						}
					}
					
					$filter_values[] = $value;
				}
				
				$meta = [];
				
				foreach ($filter->meta as $description) {
					$meta[$description['lang']] = $description;
				}
				
				$this->breadcrumbs->addCrumb('Фильтры', url('admin/filters') . $this->params);
				$this->breadcrumbs->addCrumb('Редактировать', url('admin/filter/' . $id));
				$breadcrumbs = $this->breadcrumbs->render();
				
				return view('pages.filter-edit', ['breadcrumbs' => $breadcrumbs, 'categories' => $categories, 'filter_values' => old('filter_values') ? (array)old('filter_values') : $filter_values, 'filter_category' => $filter_category, 'langs' => $langs, 'meta' => old('meta') ? (array)old('meta') : $meta, 'image' => old('image') ? old('image') : $filter->image, 'type' => old('type') ? old('type') : $filter->type, 'status' => old('status') ? old('status') : $filter->status, 'sort' => old('sort') ? old('sort') : $filter->sort, 'slug' => old('slug') ? old('slug') : $filter->slug, 'action' => asset('admin/filter_save') . $this->params, 'action2' => asset('admin/add_image'), 'id' => $id]);
			} else {
				return redirect('admin/filters' . $this->params)->with('error', 'Идентификатор не найден');
			}
		}
		
		public function copy(Request $request) {
			return view('pages.filter_copy', compact($request));
		}
		
		public function filters_add(Request $request) {
			$this->validate($request, [
				'type' => 'required'
			]);
			
			if (!is_null($request->delete)) {
				$filters = Filters::select('id')->where('attribute', 1)->get();
				
				foreach ($filters as $filter) {
					Filters::where('id', $filter->id)->delete();
					FilterDescription::where('filter_id', $filter->id)->delete();
					FilterCategory::where('filter_id', $filter->id)->delete();
					FilterProduct::where('filter_id', $filter->id)->delete();
					
					$filter_values = FilterValues::select('id')->where('filter_id', $filter->id)->get();
					
					foreach ($filter_values as $filter_value) {
						FilterValueDescription::where('filter_value_id', $filter_value->id)->delete();
					}
					
					FilterValues::where('filter_id', $filter->id)->delete();
				}
			}
			
			$o = 50;
			$v = 100;
			
			$attributes = Attributes::selectRaw("(attributes.id + " . (int)$o . ") as id, attributes.status, attributes.sort, ad.name")->join('attribute_description as ad', 'ad.attribute_id', '=', 'attributes.id')->where('ad.lang', $this->lang)->get()->toArray();
			
			if (empty($attributes)) {
				return redirect('admin/filters')->with('error', 'Создайте характеристики');
			}
			
			foreach ($attributes as &$attribute) {
				$attribute['slug'] = str_slug($attribute['name']);
				unset($attribute['name']);
				$attribute['attribute'] = 1;
				$attribute['type'] = $request->type;
				$attribute['created_at'] = Carbon::now();
				$attribute['updated_at'] = Carbon::now();
			}
			
			Filters::insertOrIgnore($attributes);
			
			$attribute_description = AttributeDescription::selectRaw("id, (attribute_id + " . (int)$o . ") as filter_id, lang, name")->get()->toArray();
			
			foreach ($attribute_description as &$attribute) {
				$attribute['created_at'] = Carbon::now();
				$attribute['updated_at'] = Carbon::now();
			}
			
			if (!is_null($request->category)) {
				$categories = Categories::where('status', 1)->get();
				$filters = Filters::where('status', 1)->where('attribute', 1)->get();
				
				if (!$categories->isEmpty() && !$filters->isEmpty()) {
					foreach ($categories as $category) {
						foreach ($filters as $filter) {
							$fc = new FilterCategory;
							$fc->filter_id = $filter->id;
							$fc->category_id = $category->id;
							
							$fc->save();
						}
					}
				}
			}
			
			FilterDescription::insertOrIgnore($attribute_description);
			
			$filter_values = ProductAttribute::selectRaw("(attribute_id + " . (int)$o . ") as filter_id, (CRC32(CONCAT(attribute_id, '.', text)) + " . (int)$v . ") as id, text")
				->where('lang', $this->lang)
				->groupBy(DB::raw("CRC32(CONCAT(attribute_id, '.', text))"))
				->get()
				->toArray();
			
			foreach ($filter_values as $x => &$attribute) {
				$attribute['slug'] = str_slug($attribute['text']);
				unset($attribute['text']);
				$attribute['sort'] = $x;
				$attribute['created_at'] = Carbon::now();
				$attribute['updated_at'] = Carbon::now();
			}
			
			FilterValues::insertOrIgnore($filter_values);
			
			$filter_value_description = ProductAttribute::selectRaw("id, (CRC32(CONCAT(attribute_id, '.', text)) + " . (int)$v . ") as filter_value_id, lang, text")
				->where('lang', $this->lang)
				->groupBy(DB::raw("CRC32(CONCAT(attribute_id, '.', text))"))
				->get()
				->toArray();
			
			foreach ($filter_value_description as &$attribute) {
				$attribute['name'] = $attribute['text'];
				unset($attribute['text']);
				$attribute['created_at'] = Carbon::now();
				$attribute['updated_at'] = Carbon::now();
			}
			
			FilterValueDescription::insertOrIgnore($filter_value_description);
			
			$product_filter = ProductAttribute::selectRaw("(attribute_id + " . (int)$o . ") as filter_id, (CRC32(CONCAT(attribute_id, '.', text)) + " . (int)$v . ") as filter_value_id, product_id")
				->where('lang', $this->lang)
				->get()->toArray();
			
			foreach ($product_filter as &$attribute) {
				$attribute['created_at'] = Carbon::now();
				$attribute['updated_at'] = Carbon::now();
			}
			
			FilterProduct::insertOrIgnore($product_filter);
			
			$langs = Languages::orderBy('name', 'asc')->get();
			
			foreach ($langs as $lang) {
				if ($lang->code != $this->lang) {
					$filter_value_description = ProductAttribute::selectRaw("(product_attribute.attribute_id + " . (int)$o . ") as filter_id,
                (select (CRC32(CONCAT(pa2.attribute_id, '.', pa2.text)) + " . (int)$v . ") from product_attribute as pa2
                where pa2.lang = '" . htmlspecialchars($this->lang) . "'
                and pa2.product_id = product_attribute.product_id
                and pa2.attribute_id = product_attribute.attribute_id LIMIT 1
                ) as filter_value_id, '" . htmlspecialchars($lang->code) . "' as lang, product_attribute.text")
						->where('product_attribute.lang', $lang->code)
						->groupBy(DB::raw("CRC32(CONCAT(product_attribute.attribute_id, '.', product_attribute.text))"))
						->get()
						->toArray();
					
					foreach ($filter_value_description as &$attribute) {
						$attribute['name'] = $attribute['text'];
						unset($attribute['filter_id']);
						unset($attribute['text']);
						$attribute['created_at'] = Carbon::now();
						$attribute['updated_at'] = Carbon::now();
					}
					
					FilterValueDescription::insertOrIgnore($filter_value_description);
				}
			}
			
			return redirect('admin/filters' . $this->params)->with('success', 'Операция успешна');
		}
		
		public function delete(Request $request) {
			if ($request->selected) {
				foreach ($request->selected as $s) {
					Filters::where('id', $s)->delete();
					FilterDescription::where('filter_id', $s)->delete();
					FilterCategory::where('filter_id', $s)->delete();
					
					FilterProduct::where('filter_id', $s)->delete();
					
					foreach (FilterValues::where('filter_id', $s)->pluck('id') as $fv) {
						FilterValueDescription::where('filter_value_id', $fv)->delete();
					}
					
					FilterValues::where('filter_id', $s)->delete();
				}
				
				$message = 'Операция успешна';
				$type = 'success';
			} else {
				$message = 'Выделите пункты для удаления';
				$type = 'error';
			}
			
			return redirect('admin/filters' . $this->params)->with($type, $message);
		}
		
		public function save(Request $request) {
			$this->validate($request, [
				'meta.*.name' => 'required',
				'type' => 'required',
				'filter_values.*.description.*.name' => 'required',
				'filter_values.*.slug' => 'required|not_in:sort-name,sort-popular,sort-price,page,price',
				'filter_category.*' => 'required|integer',
				'slug' => 'required|not_in:sort-name,sort-popular,sort-price,page,price|max:255|unique:filters,slug' . (!is_null($request->id) ? ',' . $request->id . ',id' : '') .'|alpha_dash'
			]);
			
			if (!is_null($request->id)) {
				$filter['type'] = $request->type;
				$filter['slug'] = $request->slug;
				$filter['attribute'] = 0;
				$filter['sort'] = $request->sort ? $request->sort : 0;
				$filter['status'] = $request->status ? $request->status : 0;
				
				Filters::where('id', $request->id)->update($filter);
				
				FilterDescription::where('filter_id', $request->id)->delete();
				
				foreach ($request->meta as $lang => $meta) {
					$fd = new FilterDescription;
					$fd->lang = $lang;
					$fd->filter_id = $request->id;
					$fd->name = $meta['name'];
					$fd->description = !empty($meta['description']) ? $meta['description'] : '';
					
					$fd->save();
				}
				
				FilterCategory::where('filter_id', $request->id)->delete();
				
				if (!is_null($request->filter_category)) {
					foreach ($request->filter_category as $category) {
						$fc = new FilterCategory;
						$fc->filter_id = $request->id;
						$fc->category_id = $category;
						
						$fc->save();
					}
				}
				
				if (!is_null($request->filter_values)) {
					foreach ($request->filter_values as $filter_values) {
						if ($filter_values['id']) {
							FilterValues::where('id', $filter_values['id'])->update([
								'filter_id' => $request->id,
								'slug' => str_slug($filter_values['slug']),
								'top' => isset($filter_values['top']) ? $filter_values['top'] : 0,
								'sort' => isset($filter_values['sort']) ? $filter_values['sort'] : 0
							]);
							
							foreach ($filter_values['description'] as $lang => $meta) {
								FilterValueDescription::where('filter_value_id', $filter_values['id'])->where('lang', $lang)->update([
									'name' => $meta['name'],
								]);
							}
						} else {
							$fv = new FilterValues;
							$fv->filter_id = $request->id;
							$fv->slug = str_slug($filter_values['slug']);
							$fv->top = isset($filter_values['top']) ? $filter_values['top'] : 0;
							$fv->sort = isset($filter_values['sort']) ? $filter_values['sort'] : 0;
							
							$fv->save();
							
							foreach ($filter_values['description'] as $lang => $meta) {
								$fvd = new FilterValueDescription;
								$fvd->lang = $lang;
								$fvd->filter_value_id = $fv->id;
								$fvd->name = $meta['name'];
								
								$fvd->save();
							}
						}
					}
				}
			} else {
				$filter = new Filters;
				$filter->type = $request->type;
				$filter->slug = $request->slug;
				$filter->attribute = 0;
				$filter->sort = $request->sort ? $request->sort : 0;
				$filter->status = $request->status ? $request->status : 0;
				
				$filter->save();
				
				foreach ($request->meta as $lang => $meta) {
					$fd = new FilterDescription;
					$fd->lang = $lang;
					$fd->filter_id = $filter->id;
					$fd->name = $meta['name'];
					$fd->description = !empty($meta['description']) ? $meta['description'] : '';
					
					$fd->save();
				}
				
				if (!is_null($request->filter_category)) {
					foreach ($request->filter_category as $category) {
						$fc = new FilterCategory;
						$fc->filter_id = $filter->id;
						$fc->category_id = $category;
						
						$fc->save();
					}
				}
				
				if (!is_null($request->filter_values)) {
					foreach ($request->filter_values as $filter_values) {
						$fv = new FilterValues;
						$fv->filter_id = $filter->id;
						$fv->slug = str_slug($filter_values['slug']);
						$fv->top = isset($filter_values['top']) ? $filter_values['top'] : 0;
						$fv->sort = isset($filter_values['sort']) ? $filter_values['sort'] : 0;
						
						$fv->save();
						
						foreach ($filter_values['description'] as $lang => $meta) {
							$fvd = new FilterValueDescription;
							$fvd->lang = $lang;
							$fvd->filter_value_id = $fv->id;
							$fvd->name = $meta['name'];
							
							$fvd->save();
						}
					}
				}
			}
			
			return redirect('admin/filters' . $this->params)->with('success', 'Операция успешна');
		}
	}
