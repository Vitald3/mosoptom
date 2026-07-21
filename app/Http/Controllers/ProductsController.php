<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Categories;
	use App\Models\Products;
	use App\Models\Options;
	use App\Models\Reviews;
	use App\Models\Manufacturers;
	use App\Models\Status;
	use App\Models\ProductSpecial;
	use App\Models\ProductDiscount;
	use App\Models\ProductRelated;
	use App\Models\CustomerGroups;
	use App\Models\CategoryDescription;
	use App\Models\ProductDescription;
	use App\Models\ProductCategory;
	use App\Models\Filters;
	use App\Models\FilterProduct;
	use App\Models\ProductOption;
	use App\Models\ProductOptionValues;
	use App\Models\FilterValues;
	use App\Models\FilterValueDescription;
	use App\Models\ProductImage;
	use App\Models\ProductReward;
	use App\Models\ProductAttribute;
	use App\Models\ProductAttributeImage;
	use App\Models\Attributes;
	use App\Models\AttributeDescription;
	use App\Models\Layouts;
	use App\Models\Languages;
	use Illuminate\Support\Facades\Cache;
	use Carbon\Carbon;
	
	class ProductsController extends Controller
	{
		private $breadcrumbs;
		
		public function __construct() {
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			
			$classes = array('breadcrumb', 'breadcrumb-item');
			$this->breadcrumbs->addCssClasses($classes);
			$this->breadcrumbs->setDivider('');
			
			if (preg_match("/\/admin/", \Request::getUri())) {
				$this->breadcrumbs->addCrumb(__('locale.home'), url('admin'));
			} else {
				$this->breadcrumbs->addCrumb(__('locale.home'), url(''));
			}
			
			$this->lang = session('lang');
			
			$this->params_array = request()->query();
			$params = [];
			
			if (!empty($this->params_array)) {
				foreach ($this->params_array as $key => $param) {
					$params[] = $key . '=' . $param;
				}
			}
			
			$this->params = !empty($params) ? '?' . implode('&', $params) : '';
			$this->region = session('region');
			$this->currency = session('currency');
		}
		
		public function product_autocomplete(Request $request) {
			$json = [];
			
			if ($request->term) {
				$language_default = $this->lang;
				
				$where[] = ['pd.name', 'like', '%' . $request->term . '%'];
				$where[] = ['pd.lang', '=', $language_default];
				$where[] = ['products.status', '=', 1];
				
				if ($request->id) {
					$where[] = ['products.id', '!=', $request->id];
				}
				
				$products = Products::select('pd.name', 'products.id', 'products.model')
					->join('product_description as pd', 'pd.product_id', '=', 'products.id')
					->limit(5)
					->where($where);
				
				if ($request->special) {
					$products->join('product_special as ps', 'ps.product_id', '=', 'products.id');
				}
				
				if ($request->option) {
					$products->with([
						'product_option' => function($query) {
							$query->with([
								'product_option_values' => function($query) {
									$query->join('option_value_description as ovd', 'ovd.option_value_id', '=', 'product_option_values.option_value_id')
										->select('product_option_values.id', 'product_option_values.product_option_id', 'ovd.name');
								}
							])
								->join('options as o', 'o.id', '=', 'product_option.option_id')
								->join('option_description as od', 'od.option_id', '=', 'o.id')
								->select('od.name', 'product_option.id', 'product_option.required', 'o.type', 'o.id as option_id', 'product_option.product_id', 'product_option.value');
						}
					]);
				}
				
				$json = $products->groupBy('products.id')->get();
			}
			
			return response()->json($json);
		}
		
		public function index(Request $request){
			$where = [];
			
			$language_default = session('default_language');
			
			if (!is_null($request->status)) {
				$where[] = ['products.status', '=', $request->status];
				$status = $request->status;
			} else {
				$status = '';
			}
			
			if (!is_null($request->name)) {
				$where[] = ['pd.name', 'like', '%' . $request->name . '%'];
				$name = $request->name;
			} else {
				$name = '';
			}
			
			if (!empty($request->category_id)) {
				$where[] = ['products.parent_id', '=', $request->category_id];
				$category_id = $request->category_id;
			} else {
				$category_id = 0;
			}
			
			if (!empty($request->category)) {
				$category = $request->category;
			} else {
				$category = '';
			}
			
			$where[] = ['pd.lang', '=', $language_default];
			
			if ($request->sort) {
				$sort = $request->sort;
			} else {
				$sort = 'pd.name';
			}
			
			if ($request->order) {
				$order = $request->order;
			} else {
				$order = 'asc';
			}
			
			$limit = session('settings.limit', 25);
			
			$sort_name = url('admin/products', ['sort' => 'pd.name', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_model = url('admin/products', ['sort' => 'products.model', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_sort = url('admin/products', ['sort' => 'products.sort', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_quantity = url('admin/products', ['sort' => 'products.quantity', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_price = url('admin/products', ['sort' => 'products.price', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_status = url('admin/products', ['sort' => 'products.status', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			
			if (in_array($sort, ['pd.name', 'products.sort', 'products.status', 'products.quantity', 'products.price'])) {
				$products = Products::with(['product_category.metaLangCategory', 'product_special_one:product_id,price'])
					->select('products.sort', 'products.model', 'products.price', 'products.quantity', 'products.status', 'products.id', 'pd.name', 'products.parent_id')
					->join('product_description as pd', 'pd.product_id', '=', 'products.id')
					->where($where)
					->orderBy($sort, $order)
					->paginate($limit);
			} else {
				$products = Products::with(['product_category.metaLangCategory', 'product_special_one:product_id,price'])
					->select('products.sort', 'products.model', 'products.price', 'products.quantity', 'products.status', 'products.id', 'pd.name', 'products.parent_id')
					->join('product_description as pd', 'pd.product_id', '=', 'products.id')
					->where($where)
					->orderBy('pd.name')
					->paginate($limit);
			}
			
			if ($sort === 'products.price') {
				$products->setCollection(
					$products->{$order == 'desc' ? 'sortByDesc' : 'sortBy'}(function ($query) {
						if (!empty($query->product_special_one)) return $query->product_special_one->price;
					})
				)->{$order == 'desc' ? 'sortByDesc' : 'sortBy'}('products.price');
			}
			
			if (!$products->isEmpty()) {
				foreach ($products as &$product) {
					$categories = [];
					
					if (!is_null($product->product_category)) {
						foreach ($product->product_category as $product_category) {
							if ($product_category->category_id == $product->parent_id) {
								$categories[] = '<b>' . $product_category->metaLangCategory[0]['name'] . '</b>';
							} else {
								$categories[] = $product_category->metaLangCategory[0]['name'];
							}
						}
						
						if (count($categories) > 8) {
							$categories = array_slice($categories, 0, 4);
							
							$categories[4 - 1] .= ' <i>... и еще <b>' . ($product->categories->count() - 8) . '</b></i>';
						}
						
						$product->categories = implode(' • ', $categories);
					}
				}
			}
			
			$this->breadcrumbs->addCrumb('Товары', url('admin/products') . $this->params);
			$breadcrumbs = $this->breadcrumbs->render();
			$params = $this->params;
			$params_array = $this->params_array;
			
			return view('pages.products', compact('params', 'params_array', 'sort_price', 'params', 'breadcrumbs', 'sort_quantity', 'category', 'sort_sort', 'sort_model', 'sort_status', 'sort_name', 'products', 'category_id', 'status', 'name', 'sort', 'order'));
		}
		
		private function getCategories()
		{
			$categories_name = [];
			
			$categories = Categories::with('metaLang')->where('status', 1)->get()->keyBy('id');
			
			foreach ($categories as $id => $category) {
				$name = $this->getCategory($category, $categories);
				
				$categories_name[$id] = implode(' > ', $name);
			}
			
			return $categories_name;
		}
		
		private function getCategory($collection, $collections, array $name = [])
		{
			$n = !is_null($collection->metaLang) ? $collection->metaLang->name : '';
			array_unshift($name, $n);
			
			if (!is_null($collection->parent_id) && isset($collections[$collection->parent_id])) {
				$name = $this->getCategory($collections[$collection->parent_id], $collections, $name);
			}
			
			return $name;
		}
		
		public function add() {
			$langs = Languages::orderBy('name', 'asc')->get();
			$categories = $this->getCategories();
			$attributes2 = Attributes::with('metaLang')->where('status', 1)->get();
			$filters = Filters::with('metaLang')->where('status', 1)->get();
			$layouts = Layouts::orderBy('name', 'asc')->get();
			
			$this->breadcrumbs->addCrumb('Товары', url('admin/products') . $this->params);
			$this->breadcrumbs->addCrumb('Создать', url('admin/product_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			$customer_groups = CustomerGroups::join('customer_group_description as cgd', 'cgd.customer_group_id', '=', 'customer_groups.id')->select('customer_groups.id', 'cgd.name')->where('customer_groups.status', 1)->orderBy('cgd.name')->get();
			$status = Status::join('status_description as st', 'st.status_id', '=', 'status.id')->select('st.name', 'status.id', 'status.type')->where('status.type', 3)->orderBy('st.name')->get();
			
			$options = Options::with([
				'metaLang:option_id,name',
				'option_values' => function($query) {
					$query->with([
						'option_value_description' => function($query) {
							$query->select('option_value_id', 'name')->where('lang', $this->lang);
						}
					])->select('id', 'option_id')->orderBy('sort_order');
				},
				'product_option' => function($query) {
					$query->with('product_option_values')->select('id', 'required', 'option_id', 'product_id', 'value')->where('product_id', 0);
				}
			])->select('id', 'type')->where('status', 1)->orderBy('sort_order')->get();
			
			return view('pages.product-edit', ['options' => $options, 'quantity' => old('quantity', 0), 'reward' => old('reward'), 'product_related' => (array)old('product_related'), 'product_discount' => (array)old('product_discount'), 'product_option_ids' => [], 'product_rewards' => [], 'manufacturer' => old('manufacturer'), 'weight' => '', 'manufacturer_id' => old('manufacturer_id'), 'statuses' => $status, 'customer_groups' => $customer_groups, 'breadcrumbs' => $breadcrumbs, 'layouts' => $layouts, 'product_filter_value' => [], 'product_special' => (array)old('product_special'), 'layout_id' => old('layout_id'),  'stock_status_id' => old('stock_status_id'), 'attribute_im' => [], 'attribute_images' => [], 'filters' => $filters, 'product_filter' => (array)old('product_filter'), 'langs' => $langs, 'attribute_descriptions' => [], 'attributes2' => $attributes2, 'categories' => $categories, 'product_category' => (array)old('product_category'), 'meta' => old('meta'), 'image' => old('image'), 'images' => (array)old('images'), 'attributes' => (array)old('attributes'), 'parent_id' => (int)old('parent_id'), 'sort' => old('sort'), 'price' => old('price'), 'slug' => old('slug'), 'model' => old('model'), 'status' => old('status'), 'action' => asset('admin/product_save') . $this->params, 'action2' => asset('admin/product_add_image'), 'id' => '']);
		}
		
		public function edit($id)
		{
			$data = Products::with(['meta', 'product_special', 'product_discount', 'product_option', 'product_reward',
				'product_related' => function($query) {
					$query->join('product_description as pd', 'pd.product_id', '=', 'product_related.related_id')
						->select('product_related.product_id', 'product_related.related_id', 'pd.name');
				}])->where('id', $id)->first();
			
			if (!empty($data)) {
				$options = Options::with([
					'metaLang:option_id,name',
					'option_values' => function($query) {
						$query->with([
							'option_value_description' => function($query) {
								$query->select('option_value_id', 'name')->where('lang', $this->lang);
							}
						])->select('id', 'option_id')->orderBy('sort_order');
					},
					'product_option' => function($query) use($id) {
						$query->with('product_option_values')->select('id', 'required', 'option_id', 'product_id', 'value')->where('product_id', $id);
					}
				])->select('id', 'type')->where('status', 1)->orderBy('sort_order')->get();
				
				$this->breadcrumbs->addCrumb('Товары', url('admin/products') . $this->params);
				$this->breadcrumbs->addCrumb('Редактировать', url('admin/product/' . $id));
				$breadcrumbs = $this->breadcrumbs->render();
				
				$customer_groups = CustomerGroups::join('customer_group_description as cgd', 'cgd.customer_group_id', '=', 'customer_groups.id')->select('customer_groups.id', 'cgd.name')->where('customer_groups.status', 1)->orderBy('cgd.name')->get();
				$statuses = Status::join('status_description as st', 'st.status_id', '=', 'status.id')->select('st.name', 'status.id', 'status.type')->where('status.type', 2)->orderBy('st.name')->get();
				
				extract($data->toArray());
				$action = asset('admin/product_save' . $this->params);
				
				$product_option_ids = [];
				
				if (!empty($product_option)) {
					foreach ($product_option as $po) {
						$product_option_ids[] = $po['option_id'];
					}
				}
				
				$product_rewards = [];
				
				if (!empty($product_reward)) {
					foreach ($product_reward as $pr) {
						$product_rewards[$pr['customer_group_id']] = ['reward' => $pr['reward']];
					}
				}
				
				$product_category = ProductCategory::where('product_id', $id)->pluck('category_id')->toArray();
				$product_filters = FilterProduct::where('product_id', $id)->get();
				
				$product_filter = [];
				$product_filter_value = [];
				
				foreach ($product_filters as $pf) {
					$product_filter[] = $pf->filter_id;
					$product_filter_value[] = $pf->filter_value_id;
				}
				
				$filters = Filters::with([
					'metaLang',
					'filter_values.filter_value_description'
				])->where('status', 1)->get();
				
				$langs = Languages::orderBy('name', 'asc')->get();
				$categories = $this->getCategories();
				$images = ProductImage::where('product_id', $id)->pluck('image')->toArray();
				$layouts = Layouts::orderBy('name', 'asc')->get();
				
				$attribute_descriptions = [];
				
				$attributes = ProductAttribute::where('product_id', $id)->get();
				$attribute_images = [];
				$attribute_im = [];
				
				foreach ($attributes as $attribute) {
					if (!is_null($attribute->product_attribute_image)) {
						$attribute_images[$attribute->attribute_id] = $attribute->product_attribute_image()->where('attribute_id', $attribute->attribute_id)->get()->toArray();
					}
					
					$attribute_im[] = $attribute->attribute_id;
					
					$attribute_descriptions[$attribute->attribute_id]['attribute_id'] = $attribute->attribute_id;
					$descriptions = [];
					
					foreach (ProductAttribute::where([['product_id', $id], ['attribute_id', $attribute->attribute_id]])->get()->toArray() as $ad) {
						$descriptions[$ad['lang']] = $ad;
					}
					
					$attribute_descriptions[$attribute->attribute_id]['descriptions'] = $descriptions;
				}
				
				$attributes2 = Attributes::with([
					'meta' => function ($query) {
						$query->where('lang', $this->lang);
					}
				])->where('status', 1)->get();
				
				$meta = [];
				
				foreach ($data->meta as $description) {
					$meta[$description['lang']] = $description;
				}
				
				$action2 = asset('admin/product_add_image');
				$manufacturer = Manufacturers::select('name')->where('id', $manufacturer_id)->value('name');
				
				return view('pages.product-edit', compact('product_option_ids', 'quantity', 'product_rewards', 'product_related', 'product_discount', 'reward', 'options', 'weight', 'manufacturer', 'manufacturer_id', 'statuses', 'status', 'stock_status_id', 'product_special', 'breadcrumbs', 'customer_groups', 'layouts', 'product_filter_value', 'layout_id', 'attribute_im', 'attribute_images', 'filters', 'product_filter', 'langs', 'attribute_descriptions', 'attributes', 'attributes2', 'categories', 'product_category', 'meta', 'image', 'images', 'parent_id', 'status', 'model', 'price', 'slug', 'sort', 'action', 'action2', 'id'));
			} else {
				return redirect('admin/products' . $this->params)->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete(Request $request) {
			if ($request->selected) {
				foreach ($request->selected as $s) {
					Products::find($s)->delete();
					ProductDescription::where('product_id', $s)->delete();
					ProductImage::where('product_id', $s)->delete();
					ProductReward::where('product_id', $s)->delete();
					ProductCategory::where('product_id', $s)->delete();
					FilterProduct::where('product_id', $s)->delete();
					ProductAttributeImage::where('product_id', $s)->delete();
					ProductAttribute::where('product_id', $s)->delete();
					ProductSpecial::where('product_id', $s)->delete();
					ProductDiscount::where('product_id', $s)->delete();
					ProductRelated::where('product_id', $s)->delete();
					
					foreach (ProductOption::where('product_id', $s)->pluck('id') as $id) {
						ProductOptionValues::where('product_option_id', $id)->delete();
					}
					
					ProductOption::where('product_id', $s)->delete();
				}
				
				$message = 'Операция успешна';
				$type = 'success';
			} else {
				$message = 'Выделите пункты для удаления';
				$type = 'error';
			}
			
			return redirect('admin/products' . $this->params)->with($type, $message);
		}
		
		public function copy(Request $request) {
			if ($request->selected) {
				foreach ($request->selected as $key => $s) {
					$product = Products::find($s)->replicate();
					$product->slug = '';
					$product->popular = 0;
					$product->status = 0;
					$product->created_at = Carbon::now();
					$product->sort++;
					$product->save();
					
					foreach (ProductDescription::where('product_id', $s)->get() as $meta) {
						$pd = new ProductDescription;
						$pd->lang = $meta->lang;
						$pd->product_id = $product->id;
						$pd->name = $meta['name'];
						$pd->meta_title = $meta['meta_title'];
						$pd->meta_description = !empty($meta['meta_description']) ? $meta['meta_description'] : '';
						$pd->meta_keywords = !empty($meta['meta_keywords']) ? $meta['meta_keywords'] : '';
						$pd->description = !empty($meta['description']) ? $meta['description'] : '';
						
						$pd->save();
					}
					
					$product_category = ProductCategory::where('product_id', $s)->get();
					
					if (!empty($product_category)) {
						foreach ($product_category as $category) {
							$pc = new ProductCategory;
							$pc->category_id = $category->category_id;
							$pc->product_id = $product->id;
							
							$pc->save();
						}
					}
					
					$product_images = ProductImage::where('product_id', $s)->get();
					
					if (!empty($product_images)) {
						foreach ($product_images as $image) {
							$pi = new ProductImage;
							$pi->image = $image->image;
							$pi->product_id = $product->id;
							
							$pi->save();
						}
					}
					
					$product_specials = ProductSpecial::where('product_id', $s)->get();
					
					if (!empty($product_specials)) {
						foreach ($product_specials as $special) {
							$ps = new ProductSpecial;
							$ps->price = $special->price;
							$ps->date_start = $special->date_start;
							$ps->date_end = $special->date_end;
							$ps->price = $special->price;
							$ps->product_id = $product->id;
							
							$ps->save();
						}
					}
					
					$product_discounts = ProductDiscount::where('product_id', $s)->get();
					
					if (!empty($product_discounts)) {
						foreach ($product_discounts as $discount) {
							$pd = new ProductDiscount;
							$pd->price = $discount->price;
							$pd->quantity = $discount->quantity;
							$pd->date_start = $discount->date_start;
							$pd->date_end = $discount->date_end;
							$pd->price = $discount->price;
							$pd->product_id = $product->id;
							
							$pd->save();
						}
					}
					
					$product_rewards = ProductReward::where('product_id', $s)->get();
					
					if (!empty($product_rewards)) {
						foreach ($product_rewards as $key => $reward) {
							$pr = new ProductReward;
							$pr->reward = $reward['reward'];
							$pr->product_id = $request->id;
							$pr->customer_group_id = $key;
							
							$pr->save();
						}
					}
					
					$product_attribute = ProductAttribute::where('product_id', $s)->get();
					
					if (!empty($product_attribute)) {
						foreach ($product_attribute as $attribute) {
							$pa = new ProductAttribute;
							$pa->product_id = $product->id;
							$pa->attribute_id = $attribute->attribute_id;
							$pa->text = $attribute->text;
							$pa->lang = $attribute->lang;
							
							$pa->save();
						}
					}
					
					$product_filter = FilterProduct::where('product_id', $s)->get();
					
					if (!empty($product_filter)) {
						foreach ($product_filter as $product_filter) {
							$values = FilterValues::select('id')->where('filter_id', $product_filter->filter_id)->get();
							
							foreach ($values as $value) {
								$pc = new FilterProduct;
								$pc->filter_id = $product_filter->filter_id;
								$pc->filter_value_id = $value->id;
								$pc->product_id = $product->id;
								$pc->save();
							}
						}
					}
					
					$product_option = ProductOption::where('product_id', $s)->get();
					
					if (!$product_option->isEmpty()) {
						foreach ($product_option as $option) {
							$po = new ProductOption;
							$po->option_id = $option->option_id;
							$po->product_id = $s;
							$po->required = $option->required;
							$po->value = !is_null($option->value) ? $option->value : '';
							
							$po->save();
							
							$product_option_values = ProductOptionValues::where('product_option_id', $option->id)->get();
							
							if (!$product_option_values->isEmpty()) {
								foreach ($product_option_values as $product_option_value) {
									$pov = new ProductOptionValues;
									$pov->product_id = $s;
									$pov->option_id = $product_option_value->option_id;
									$pov->option_value_id = $product_option_value->option_value_id;
									$pov->quantity = $product_option_value->quantity;
									$pov->price = $product_option_value->price ? $product_option_value->price : 0;
									$pov->image = !is_null($product_option_value->image) ? $product_option_value->image : '';
									$pov->weight = $product_option_value->weight ? $product_option_value->weight : 0;
									$pov->reward = $product_option_value->reward ? $product_option_value->reward : 0;
									$pov->product_option_id = $option->id;
									
									$pov->save();
								}
							}
						}
					}
				}
				
				return redirect('admin/products' . $this->params)->with('success', 'Операция успешна');
			} else {
				return redirect('admin/products' . $this->params)->with('error', 'Выберите хотя бы 1 строку');
			}
		}
		
		public function save(Request $request) {
			$this->validate($request, [
				'meta.*.name' => 'required',
				'meta.*.meta_title' => 'required',
				'layout_id' => 'required',
				'stock_status_id' => 'required',
				'slug' => 'required|not_in:sort-name,sort-popular,sort-price,page,price,news,catalog|max:255|unique:products,slug' . (!is_null($request->id) ? ',' . $request->id . ',id' : '') .'|alpha_dash'
			]);
			
			if (!is_null($request->id)) {
				$product['slug'] = $request->slug;
				$product['model'] = $request->model ? $request->model : '';
				$product['image'] = $request->image ? $request->image : '';
				$product['sort'] = $request->sort ? $request->sort : 0;
				$product['weight'] = $request->weight ? $request->weight : 0;
				$product['layout_id'] = $request->layout_id;
				$product['stock_status_id'] = $request->stock_status_id;
				$product['price'] = $request->price ? $request->price : 0;
				$product['manufacturer_id'] = $request->manufacturer_id ? $request->manufacturer_id : null;
				$product['popular'] = $request->popular ? $request->popular : 0;
				$product['quantity'] = $request->quantity ? $request->quantity : 0;
				$product['parent_id'] = $request->parent_id ? $request->parent_id : 0;
				$product['reward'] = $request->reward ? $request->reward : 0;
				$product['status'] = $request->status ? $request->status : 0;
				
				Products::where('id', $request->id)->update($product);
				
				ProductDescription::where('product_id', $request->id)->delete();
				
				foreach ($request->meta as $lang => $meta) {
					$pd = new ProductDescription;
					$pd->lang = $lang;
					$pd->product_id = $request->id;
					$pd->name = $meta['name'];
					$pd->meta_title = $meta['meta_title'];
					$pd->meta_description = !empty($meta['meta_description']) ? $meta['meta_description'] : '';
					$pd->meta_keywords = !empty($meta['meta_keywords']) ? $meta['meta_keywords'] : '';
					$pd->description = !empty($meta['description']) ? $meta['description'] : '';
					
					$pd->save();
				}
				
				ProductCategory::where('product_id', $request->id)->delete();
				
				if (!is_null($request->product_category)) {
					foreach ($request->product_category as $category) {
						$pc = new ProductCategory;
						$pc->category_id = $category;
						$pc->product_id = $request->id;
						
						$pc->save();
					}
				}
				
				ProductImage::where('product_id', $request->id)->delete();
				
				if (!empty($request->images)) {
					foreach ($request->images as $image) {
						$pi = new ProductImage;
						$pi->image = $image;
						$pi->product_id = $request->id;
						
						$pi->save();
					}
				}
				
				ProductReward::where('product_id', $request->id)->delete();
				
				if (!is_null($request->product_reward)) {
					foreach ($request->product_reward as $key => $reward) {
						if (!empty($reward['reward'])) {
							$pr = new ProductReward;
							$pr->reward = $reward['reward'];
							$pr->product_id = $request->id;
							$pr->customer_group_id = $key;
							
							$pr->save();
						}
					}
				}
				
				ProductSpecial::where('product_id', $request->id)->delete();
				
				if (!empty($request->product_special)) {
					foreach ($request->product_special as $special) {
						$ps = new ProductSpecial;
						$ps->price = $special['price'];
						$ps->product_id = $request->id;
						$ps->customer_group_id = $special['customer_group_id'];
						$ps->date_start = !empty($special['date_start']) ? $special['date_start'] : '0000-00-00';
						$ps->date_end = !empty($special['date_end']) ? $special['date_end'] : '0000-00-00';
						
						$ps->save();
					}
				}
				
				ProductDiscount::where('product_id', $request->id)->delete();
				
				if (!empty($request->product_discount)) {
					foreach ($request->product_discount as $discount) {
						$pd = new ProductDiscount;
						$pd->price = $discount['price'];
						$pd->quantity = $discount['quantity'];
						$pd->product_id = $request->id;
						$pd->customer_group_id = $discount['customer_group_id'];
						$pd->date_start = !empty($discount['date_start']) ? $discount['date_start'] : '0000-00-00';
						$pd->date_end = !empty($discount['date_end']) ? $discount['date_end'] : '0000-00-00';
						
						$pd->save();
					}
				}
				
				ProductRelated::where('product_id', $request->id)->delete();
				
				if (!empty($request->product_related)) {
					foreach ($request->product_related as $product_related) {
						$pr = new ProductRelated;
						$pr->related_id = $product_related;
						$pr->product_id = $request->id;
						
						$pr->save();
					}
				}
				
				ProductAttribute::where('product_id', $request->id)->delete();
				ProductAttributeImage::where('product_id', $request->id)->delete();
				
				if (!empty($request->product_attribute)) {
					foreach ($request->product_attribute as $attribute) {
						if (isset($attribute['image'])) {
							foreach ($attribute['image'] as $image) {
								if (!is_null($image)) {
									$pai = new ProductAttributeImage;
									$pai->image = $image;
									$pai->product_id = $request->id;
									$pai->attribute_id = $attribute['attribute_id'];
									
									$pai->save();
								}
							}
						}
						
						foreach ($attribute['product_attribute_description'] as $key => $description) {
							$pa = new ProductAttribute;
							$pa->product_id = $request->id;
							$pa->attribute_id = $attribute['attribute_id'];
							$pa->text = isset($description['text']) ? $description['text'] : '';
							$pa->lang = $key;
							
							$pa->save();
						}
					}
				}
				
				FilterProduct::where('product_id', $request->id)->delete();
				
				if (!empty($request->product_filter_values)) {
					foreach ($request->product_filter_values as $filter_id => $filter_values) {
						foreach ($filter_values as $value) {
							$pf = new FilterProduct;
							$pf->filter_value_id = $value;
							$pf->filter_id = $filter_id;
							$pf->product_id = $request->id;
							
							$pf->save();
						}
					}
				}
				
				ProductOption::where('product_id', $request->id)->delete();
				ProductOptionValues::where('product_id', $request->id)->delete();
				
				if (!empty($request->product_option)) {
					foreach ($request->product_option as $option) {
						$po = new ProductOption;
						$po->option_id = $option['option_id'];
						$po->product_id = $request->id;
						$po->required = $option['required'];
						$po->value = isset($option['value']) ? $option['value'] : '';
						
						$po->save();
						
						if (!empty($option['product_option_values'])) {
							foreach ($option['product_option_values'] as $product_option_value) {
								$pov = new ProductOptionValues;
								$pov->product_id = $request->id;
								$pov->option_id = $option['option_id'];
								$pov->option_value_id = $product_option_value['option_value_id'];
								$pov->quantity = $product_option_value['quantity'];
								$pov->price = $product_option_value['price'] ? $product_option_value['price'] : 0;
								$pov->image = isset($product_option_value['image']) ? $product_option_value['image'] : '';
								$pov->weight = $product_option_value['weight'] ? $product_option_value['weight'] : 0;
								$pov->reward = $product_option_value['reward'] ? $product_option_value['reward'] : 0;
								$pov->product_option_id = $po->id;
								
								$pov->save();
							}
						}
					}
				}
			} else {
				$product = new Products;
				$product->slug = $request->slug;
				$product->layout_id = $request->layout_id;
				$product->stock_status_id = $request->stock_status_id;
				$product->model = $request->model ? $request->model : '';
				$product->image = $request->image ? $request->image : '';
				$product->manufacturer_id = $request->manufacturer_id ? $request->manufacturer_id : null;
				$product->sort = $request->sort ? $request->sort : 0;
				$product->weight = $request->weight ? $request->weight : 0;
				$product->price = $request->price ? $request->price : 0;
				$product->popular = $request->popular ? $request->popular : 0;
				$product->quantity = $request->quantity ? $request->quantity : 0;
				$product->parent_id = $request->parent_id ? $request->parent_id : 0;
				$product->reward = $request->reward ? $request->reward : 0;
				$product->status = $request->status ? $request->status : 0;
				
				$product->save();
				
				foreach ($request->meta as $lang => $meta) {
					$pd = new ProductDescription;
					$pd->lang = $lang;
					$pd->product_id = $product->id;
					$pd->name = $meta['name'];
					$pd->meta_title = $meta['meta_title'];
					$pd->meta_description = !empty($meta['meta_description']) ? $meta['meta_description'] : '';
					$pd->meta_keywords = !empty($meta['meta_keywords']) ? $meta['meta_keywords'] : '';
					$pd->description = !empty($meta['description']) ? $meta['description'] : '';
					
					$pd->save();
				}
				
				if (!is_null($request->product_category)) {
					foreach ($request->product_category as $category) {
						$pc = new ProductCategory;
						$pc->category_id = $category;
						$pc->product_id = $product->id;
						
						$pc->save();
					}
				}
				
				if (!empty($request->images)) {
					foreach ($request->images as $image) {
						$pi = new ProductImage;
						$pi->image = $image;
						$pi->product_id = $product->id;
						
						$pi->save();
					}
				}
				
				if (!is_null($request->product_reward)) {
					foreach ($request->product_reward as $key => $reward) {
						if (!empty($reward['reward'])) {
							$pr = new ProductReward;
							$pr->reward = $reward['reward'];
							$pr->product_id = $product->id;
							$pr->customer_group_id = $key;
							
							$pr->save();
						}
					}
				}
				
				if (!empty($request->product_special)) {
					foreach ($request->product_special as $special) {
						$ps = new ProductSpecial;
						$ps->price = $special['price'];
						$ps->product_id = $product->id;
						$ps->customer_group_id = $special['customer_group_id'];
						$ps->date_start = !empty($special['date_start']) ? $special['date_start'] : '0000-00-00';
						$ps->date_end = !empty($special['date_end']) ? $special['date_end'] : '0000-00-00';
						
						$ps->save();
					}
				}
				
				if (!empty($request->product_discount)) {
					foreach ($request->product_discount as $discount) {
						$pd = new ProductDiscount;
						$pd->price = $discount['price'];
						$pd->quantity = $discount['quantity'];
						$pd->product_id = $product->id;
						$pd->customer_group_id = $discount['customer_group_id'];
						$pd->date_start = !empty($discount['date_start']) ? $discount['date_start'] : '0000-00-00';
						$pd->date_end = !empty($discount['date_end']) ? $discount['date_end'] : '0000-00-00';
						
						$pd->save();
					}
				}
				
				if (!empty($request->product_related)) {
					foreach ($request->product_related as $product_related) {
						$pr = new ProductRelated;
						$pr->related_id = $product_related;
						$pr->product_id = $product->id;
						
						$pr->save();
					}
				}
				
				if (!empty($request->product_attribute)) {
					foreach ($request->product_attribute as $attribute) {
						if (isset($attribute['image'])) {
							foreach ($attribute['image'] as $image) {
								if (!is_null($image)) {
									$pai = new ProductAttributeImage;
									$pai->image = $image;
									$pai->product_id = $product->id;
									$pai->attribute_id = $attribute['attribute_id'];
									
									$pai->save();
								}
							}
						}
						
						foreach ($attribute['product_attribute_description'] as $key => $description) {
							$pa = new ProductAttribute;
							$pa->product_id = $product->id;
							$pa->attribute_id = $attribute['attribute_id'];
							$pa->text = isset($description['text']) ? $description['text'] : '';
							$pa->lang = $key;
							
							$pa->save();
						}
					}
				}
				
				if (!empty($request->product_filter_values)) {
					foreach ($request->product_filter_values as $filter_id => $filter_values) {
						foreach ($filter_values as $value) {
							$pf = new FilterProduct;
							$pf->filter_value_id = $value;
							$pf->filter_id = $filter_id;
							$pf->product_id = $product->id;
							
							$pf->save();
						}
					}
				}
				
				if (!empty($request->product_option)) {
					foreach ($request->product_option as $option) {
						$po = new ProductOption;
						$po->option_id = $option['option_id'];
						$po->product_id = $product->id;
						$po->required = $option['required'];
						$po->value = isset($option['value']) ? $option['value'] : '';
						
						$po->save();
						
						if (!empty($option['product_option_values'])) {
							foreach ($option['product_option_values'] as $product_option_value) {
								$pov = new ProductOptionValues;
								$pov->product_id = $product->id;
								$pov->option_id = $option['option_id'];
								$pov->option_value_id = $product_option_value['option_value_id'];
								$pov->quantity = $product_option_value['quantity'];
								$pov->price = $product_option_value['price'] ? $product_option_value['price'] : 0;
								$pov->image = isset($product_option_value['image']) ? $product_option_value['image'] : '';
								$pov->weight = $product_option_value['weight'] ? $product_option_value['weight'] : 0;
								$pov->reward = $product_option_value['reward'] ? $product_option_value['reward'] : 0;
								$pov->product_option_id = $po->id;
								
								$pov->save();
							}
						}
					}
				}
			}
			
			$routes = app(\App\Helpers\PathRouteService::class);
			Cache::put('seo_url', $routes->getRoutes());
			
			return redirect('admin/products' . $this->params)->with('success', 'Операция успешна');
		}
		
		public function addImage(Request $request) {
			if ($request->hasFile('file')) {
				$files = $request->file('file');
				
				$images = [];
				
				if (!is_array($files)) {
					$name = $files->getClientOriginalName();
					$files->move('assets/site/img/products', $name);
					$images[] = 'assets/site/img/products/' . $name;
				} else {
					foreach ($files as $file) {
						$name = $file->getClientOriginalName();
						$file->move('assets/site/img/products', $name);
						$images[] = 'assets/site/img/products/' . $name;
					}
				}
				
				return response()->json($images);
			}
		}
		
		public function show(Request $request) {
			$header = new HeaderController;
			
			$id = $request->get('product_id');
			
			foreach ($request->get('paths') as $slug) {
				$results = Categories::join('category_description as cd', 'cd.category_id', '=', 'categories.id')
					->select('categories.id', 'categories.layout_id', 'categories.image', 'cd.name', 'cd.meta_title', 'cd.meta_description', 'cd.meta_keywords', 'cd.description')
					->where('categories.status', 1)
					->where('categories.slug', $slug)
					->firstOrFail();
				
				$href[] = $slug;
				$breadcrumbs[$results->id] = ['name' => $results->name, 'url' => $results->getSlug()];
			}
			
			$results = Products::with([
				'category:id',
				'stock_status' => function($query) {
					$query->join('status_description as sd', 'sd.status_id', '=', 'status.id')->select('status.id', 'status.color', 'sd.name');
				},
				'product_special_one:product_id,price',
				'product_discount:product_id,price,quantity',
				'product_related' => function($query) {
					$query->with([
						'product_special_one:product_id,price',
						'product_discount:product_id,price',
					])
						->join('products as products', 'product_related.related_id', '=', 'products.id')
						->join('product_description as pd', 'pd.product_id', '=', 'products.id')
						->select('product_related.product_id', 'product_related.related_id', 'products.price', 'products.id', 'products.image', 'products.model', 'pd.name')
						->where('products.status', 1)
						->where('pd.lang', $this->lang)
						->limit(6)
						->orderBy('products.created_at', 'desc');
				},
				'product_attribute' => function($query) {
					$query->join('attributes as a', 'a.id', '=', 'product_attribute.attribute_id')
						->join('attribute_description as ad', 'ad.attribute_id', '=', 'a.id')
						->join('attribute_groups as ag', 'ag.id', '=', 'a.attribute_group_id')
						->join('attribute_group_description as agd', 'agd.attribute_group_id', '=', 'ag.id')
						->select('a.image', 'product_attribute.product_id', 'product_attribute.attribute_id', 'ad.name', 'agd.name as group', 'product_attribute.text')
						->where('ad.lang', $this->lang)
						->where('agd.lang', $this->lang)
						->where('ag.status', 1)
						->orderBy('a.sort')
						->orderBy('product_attribute.text');
				},
				'product_option' => function($query) {
					$query->with([
							'product_option_values' => function($query) {
								$query->with([
									'metaLang:option_value_id,name'
								])->select('id', 'product_option_id', 'option_value_id', 'quantity', 'image')->where('quantity', '>', 0);
							}
						])
						->join('options as o', 'o.id', '=', 'product_option.option_id')
						->join('option_description as od', 'od.option_id', '=', 'o.id')
						->select('o.type', 'product_option.product_id', 'product_option.option_id', 'product_option.id', 'product_option.required', 'product_option.value', 'od.name')
						->where('od.lang', $this->lang)
						->where('o.status', 1);
				}
			])
				->join('product_description as pd', 'pd.product_id', '=', 'products.id')
				->select('products.id', 'products.price', 'products.image', 'products.slug', 'products.created_at', 'products.stock_status_id', 'products.layout_id', 'products.model', 'name', 'meta_title', 'meta_description', 'meta_keywords', 'description')
				->where('products.status', 1)->where('products.id', $id)->firstOrFail();

			$data['discounts'] = $results->product_discount;
			$data['product_id'] = $id;
			
			if (session('customer_id') || !session('settings.price_logged')) {
				$data['price'] = format_price($results->price, session('currency'));
			} else {
				$data['price'] = false;
			}
			
			if ((session('customer_id') || !session('settings.price_logged')) && $results->product_special_one) {
				$data['price'] = format_price($results->product_special_one->price, session('currency'));
			}
			
			$HtmlblockController = new \App\Http\Controllers\Extensions\Module\HtmlblockController;
			$data['delivery_block'] = $HtmlblockController->getModule(10);
			
			if ($style = $HtmlblockController->getHtmlStyle()) {
				$header->setStyle($style);
			}
			
			$data['canonical'] = $request->get('path') . '/' . $results->slug;
			
			if ($results->stock_status) {
				$data['stock'] = $results->stock_status->name;
				$data['stock_id'] = $results->stock_status->id;
				$data['stock_color'] = $results->stock_status->color;
			} else {
				$data['stock'] = false;
				$data['stock_id'] = 0;
				$data['stock_color'] = '#fff';
			}
			
			$data['created'] = Carbon::now()->diffInDays($results->created_at);
			$data['created'] = $data['created'] == 0 ? 1 : $data['created'];
			$data['day_new'] = num_decline($data['created'], [__('locale.text_day'), __('locale.text_day_2'), __('locale.text_day_3')]);
			
			$meta = [
				'name' => $results->name,
				'meta_title' => $results->meta_title,
				'meta_description' => $results->meta_description,
				'meta_keywords' => $results->meta_keywords,
				'description' => $results->description
			];
			
			session(['product_visited.' . $id => $id]);
			
			$data['class'] = 'catalog_product';
			$data['model'] = $results->model;
			
			$stylesheet[] = [
				'href' => asset('assets/site/css/category.css'),
				'rel' => 'stylesheet'
			];
			
			$stylesheet[] = [
				'href' => asset('assets/site/css/product.css'),
				'rel' => 'stylesheet'
			];
			
			$stylesheet[] = [
				'href' => asset('assets/site/css/media/product.css'),
				'rel' => 'stylesheet'
			];
			
			$data['image'] = resize_image($results->image, 943, 800);
			$data['popup'] = asset($results->image);
			$data['thumb'] = resize_image($results->image, 123, 123);
			$data['thumb2'] = resize_image($results->image, 80, 60);
			$data['title'] = $meta['name'];
			$data['model'] = $results->model;

			$data['attributes'] = $results->product_attribute->groupBy('group');
			$data['options'] = $results->product_option;
			
			$data['reviews'] = $results->reviews()->with([
				'getProduct' => function($query) {
					$query->join('product_description as pd', 'pd.product_id', '=', 'products.id')
						->select('products.id', 'products.image', 'pd.name')
						->where('pd.lang', $this->lang)
						->where('products.status', 1);
				},
				'social:customer_id,social,text'
			])->orderBy('created_at', 'desc')->paginate(session('settings.limit', 25));
			
			if ($data['reviews']->currentPage() < $data['reviews']->lastPage()) {
				$data['next_review'] = $data['reviews']->currentPage() + 1;
			} else {
				$data['next_review'] = false;
			}
			
			$data['last_review'] = $data['reviews']->lastPage();
			
			$data['products'] = $results->product_related;
			
			if (!$data['products']->isEmpty() || !$results->product_image->isEmpty()) {
				$stylesheet[] = [
					'href' => asset('assets/site/css/owl.carousel.min.css'),
					'rel' => 'stylesheet'
				];
				
				$header->setScript('<script src="' . asset('assets/site/js/owl.carousel.min.js') . '"></script>');
			}
			
			$data['images'] = [];
			$data['thumbs'] = [];
			
			if (!$results->product_image->isEmpty()) {
				$x = 1;
				
				foreach ($results->product_image as $images) {
					$x++;
					
					$data['images'][] = [
						'image' => resize_image($images['image'], 943, 800),
						'popup' => asset($images['image']),
						'alt' => $meta['name'] . sprintf(__('locale.text_alt'), $x)
					];
					
					$data['thumbs'][] = [
						'image' => resize_image($images['image'], 123, 123),
						'popup' => resize_image($images['image'], 943, 800),
						'alt' => $meta['name'] . sprintf(__('locale.text_alt'), $x)
					];
				}
			}
			
			$data['review_count'] = num_decline(Reviews::where('product_id', $id)->count(), [__('locale.text_review'), __('locale.text_review_2'), __('locale.text_review_3')]);
			$data['rating'] = Reviews::select('rating')->where('product_id', $id)->avg('rating');
			
			$data['description'] = html_entity_decode($meta['description']);
			
			$header->setMeta($meta);
			
			$content = new GetContentController($results->layout_id);
			$data['content_top'] = $content->getPosition('top');
			$data['content_bottom'] = $content->getPosition('bottom');
			$header->setStyle($content->getHtmlStyle());
			$header->setLinkStyle($content->getLinkStyle());
			$header->setScript($content->getScript());
			$header->setLinkData($stylesheet);
			$cart = new CartController;
			$data['cart'] = $cart->mini_cart($content->getModuleById('saleday'));
			$cart_count = $cart->getCount();
			$data['cart_count'] = $cart_count > 99 ? '99+' : $cart_count;
			$region_code = config('app.region_code');
			$this->region['code'] = $region_code ? $region_code . '/' : '';
			
			$data = array_merge($data, $header->data());
			
			$key = 1;
			$html = '<ul itemscope itemtype="http://schema.org/BreadcrumbList" class="breadcrumbs breadcrumb breadcrumb-item"><li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . route(session('route_url') . '_home') . '"><span itemprop="name">' . __('locale.home') . '</span></a><meta itemprop="position" content="1"></li>';
			
			foreach ($data['categories'] as $category) {
				if (isset($breadcrumbs[$category['id']])) {
					$key++;
					$html_cat = '';
					
					$html .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . $breadcrumbs[$category['id']]['url'] . '"><span itemprop="name">' . $breadcrumbs[$category['id']]['name'] . '</span>' . ($category['children'] ? '<svg style="margin-left: 4px" xmlns="http://www.w3.org/2000/svg" width="7" height="7" viewBox="0 0 7 7" fill="none"><g clip-path="url(#clip0_432_56334)"><path d="M3.49998 5.49605C3.37453 5.49605 3.24909 5.44815 3.15344 5.35255L0.143599 2.34268C-0.0478663 2.15122 -0.0478663 1.84079 0.143599 1.6494C0.334987 1.45801 0.645353 1.45801 0.836834 1.6494L3.49998 2.33415L6.16314 1.64949C6.35461 1.45811 6.66494 1.45811 6.85632 1.64949C7.04787 1.84088 7.04787 2.15131 6.85632 2.34277L3.84652 5.35264C3.75083 5.44826 3.62539 5.49605 3.49998 5.49605Z" fill="#797979"/></g><defs><clipPath id="clip0_432_56334"><rect width="7" height="7" fill="white"/></clipPath></defs></svg>' : '') . '</a><meta itemprop="position" content="' . $key . '">';
					
					foreach ($category['children'] as $children) {
						$html_cat .= '<li><a href="' . $children['url'] . '"><span>' . $children['name'] . '</span></a></li>';
					}
					
					if ($html_cat) $html_cat = '<ul class="list-un-styled overflow-y podcats">' . $html_cat . '</ul>';
					$html .= $html_cat . '</li>';
					
					$html .= $this->breadcrumbs($category['children'], $breadcrumbs, $key);
				}
			}
			
			$key = $key + (isset($breadcrumbs) ? count($breadcrumbs) : 0);
			
			$html .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name">' . $meta['name'] . '</span><meta itemprop="position" content="' . $key . '"></li>';
			
			$html .= '</ul>';
			
			$data['breadcrumbs'] = $html;
			
			return render_view(view('pages.site.product', $data), $this->region, false);
		}
		
		public function breadcrumbs($categories, $breadcrumbs, $key) {
			$html = '';
			
			foreach ($categories as $category) {
				if (isset($breadcrumbs[$category['id']])) {
					$key++;
					$html_cat = '';
					
					$html .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . $breadcrumbs[$category['id']]['url'] . '"><span itemprop="name">' . $breadcrumbs[$category['id']]['name'] . '</span>' . ($category['children'] ? '<svg style="margin-left: 4px" xmlns="http://www.w3.org/2000/svg" width="7" height="7" viewBox="0 0 7 7" fill="none"><g clip-path="url(#clip0_432_56334)"><path d="M3.49998 5.49605C3.37453 5.49605 3.24909 5.44815 3.15344 5.35255L0.143599 2.34268C-0.0478663 2.15122 -0.0478663 1.84079 0.143599 1.6494C0.334987 1.45801 0.645353 1.45801 0.836834 1.6494L3.49998 2.33415L6.16314 1.64949C6.35461 1.45811 6.66494 1.45811 6.85632 1.64949C7.04787 1.84088 7.04787 2.15131 6.85632 2.34277L3.84652 5.35264C3.75083 5.44826 3.62539 5.49605 3.49998 5.49605Z" fill="#797979"/></g><defs><clipPath id="clip0_432_56334"><rect width="7" height="7" fill="white"/></clipPath></defs></svg>' : '') . '</a><meta itemprop="position" content="' . $key . '">';
					
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
	}