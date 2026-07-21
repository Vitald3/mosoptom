<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Products;
	use App\Models\Filters;
	use App\Models\Categories;
	use Illuminate\Support\Facades\Route;
	use DB;
	
	class NewController extends Controller
	{
		public $settings = [];
		private $breadcrumb = '';
		public $lang;
		private $currency = [];
		
		public function __construct()
		{
			$this->settings = session('settings');
			$this->lang = session('lang');
			
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			
			$classes = array('breadcrumb', 'breadcrumb-item');
			$this->breadcrumbs->addCssClasses($classes);
			$this->breadcrumbs->setDivider('');
			$this->breadcrumbs->addCrumb(__('locale.home'), url(''));
			
			$this->region = session('region');
			$this->currency = session('currency');
		}
		
		public function index(Request $request) {
			$header = new HeaderController;
			$region = config('app.region_code');
			$page_url = true;
			$sort_url = true;
			$filter_title = '';
			$price_range = '';
			$price_start = 0;
			$price_end = 0;
			$params = explode('/', $request->params);
			$path = '';
			$header->setRobots('noindex, nofollow');
			$this->breadcrumbs->addCrumb(__('locale.text_new_page'), route(session('route_url') . '_new'));
			$data['products'] = [];
			$data['total'] = 0;
			$page = 0;
			
			$count = count($params);
			$page_count = $count - 2;
			
			if (isset($params[$page_count]) && $params[$page_count] === 'page') {
				if (!empty($params[$page_count+1]) && (int)$params[$page_count+1] && is_numeric($params[$page_count+1])) {
					$page = $params[$page_count+1];
					unset($params[$page_count]);
					unset($params[$page_count+1]);
				}
			}
			
			$sort_count = count($params) - 2;
			
			if (isset($params[$sort_count]) && in_array($params[$sort_count], ['sort-name', 'sort-popular', 'sort-price'])) {
				if (!empty($params[$sort_count+1]) && in_array($params[$sort_count+1], ['asc', 'desc'])) {
					$sort = str_replace('sort-', '', $params[$sort_count]);
					$order = $params[$sort_count+1];
					unset($params[$sort_count]);
					unset($params[$sort_count+1]);
				}
			}
			
			if (!$page) {
				$page = 1;
				$page_url = false;
			}
			
			if (!isset($sort)) {
				$sort_url = false;
				$sort = 'price';
			}
			
			if (!isset($order)) {
				$order = 'asc';
			}
			
			$meta = [
				'name' => __('locale.text_new_page'),
				'meta_title' => __('locale.text_new_page'),
				'meta_description' => '',
				'meta_keywords' => ''
			];
			
			$stylesheet[] = [
				'href' => asset('assets/site/css/category.css'),
				'rel' => 'stylesheet'
			];
			
			$stylesheet[] = [
				'href' => asset('assets/site/css/media/category.css'),
				'rel' => 'stylesheet'
			];
			
			$data['title'] = __('locale.text_new_page');
			
			$data['order'] = $order;
			$data['canonical'] = false;
			$data['class'] = 'catalog_new';
			$data['filters'] = [];
			$data['select_filters'] = [];
			
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
					$filters = Filters::join('filter_description as fd', 'fd.filter_id', '=', 'filters.id')
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
							if ($filter->type != 'slider') {
								$value_slug_new = $value_slug_param[$filter->slug];
								
								$filter_values = $filter->select('fv.id', 'fv.slug', 'fvd.name')
									->join('filter_values as fv', 'fv.filter_id', '=', 'filters.id')
									->join('filter_value_description as fvd', 'fvd.filter_value_id', '=', 'fv.id')
									->where('fvd.lang', $this->lang)
									->whereIn('fv.slug', $value_slug_new)
									->orderBy('fvd.name')
									->get();
								
								if (!$filter_values->isEmpty()) {
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
								$value_slug_new = $value_slug_param[$filter->slug];
								
								if (isset($value_slug_new[0])) {
									$value_slug_new = explode('-', $value_slug_new[0]);
									
									if (!is_numeric($value_slug_new[0]) || (isset($value_slug_new[1]) && !is_numeric($value_slug_new[1])) || count($value_slug_new) > 2) {
										foreach ($value_slug_new as &$s) {
											$s = preg_replace('/[0-9]/i', '', $s);
										}
										
										$slug .= '/' . $filter->slug . '/' . implode('-', $value_slug_new);
										continue;
									}
									
									$min[$filter->id] = $value_slug_new[0];
									$max[$filter->id] = isset($value_slug_new[1]) ? $value_slug_new[1] : 0;
									
									if ($value_slug_new) {
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
											
											$slug .= '/' . $filter->slug . '/' . implode('-', $value_slug_new);
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
							$path = $path . '/' . implode('/', $params);
						}
					} else {
						$path = $path . implode('/', $params);
					}
				} else {
					$path = $path . implode('/', $params);
				}
			}
			
			$aliasPath = route(session('route_url') . '_new') . ($filter_ids ? '/' . ($price_range ? 'price/' . $price_start . '-' . $price_end . '/' : '') . implode('/', $params) : ($price_range ? '/price/' . $price_start . '-' . $price_end : ''));
			
			$path = route(session('route_url') . '_new') . ($this->lang != session('settings.default_language') ? '/' . $this->lang : '') . ($region ? '/' . $region . '/' : '') . $path;
			
			if ($aliasPath != $path) {
				abort(404);
			}
			
			$results = Products::with([
				'product_special_one:product_id,price',
				'product_discount:product_id,price',
				'product_filter:product_id,filter_value_id'
			])
				->select('products.id', 'pd.name', 'products.model', 'products.price', 'products.image')
				->join('product_description as pd', 'pd.product_id', '=', 'products.id');
			
			foreach ($filter_ids as $filter_id => $filter_value_ids) {
				$results->join('product_filter as pf' . $filter_id, 'pf' . $filter_id . '.product_id', '=', 'products.id')
					->join('filter_values as fv' . $filter_id, 'fv' . $filter_id . '.id', '=', 'pf' . $filter_id . '.filter_value_id')
					->join('filters as f' . $filter_id, 'f' . $filter_id . '.id', '=', 'fv' . $filter_id . '.filter_id');
			}
			
			if ($price_start && $price_start != $price_end) {
				$results->where(function ($query) use ($price_start, $price_end) {
					$query->whereHas('product_discount_one', function ($query) use ($price_start, $price_end) {
						$query->wherebetween('price', [$price_start, $price_end]);
					});
					
					$query->OrwhereHas('product_special_one', function ($query) use ($price_start, $price_end) {
						$query->wherebetween('price', [$price_start, $price_end]);
					});
					
					$query->wherebetween('products.price', [$price_start, $price_end]);
				});
			}
			
			$results->where('products.status', 1)
				->where('products.created_at', '>=', DB::raw('DATE_SUB(NOW(), INTERVAL 15 DAY)'))
				->where('pd.lang', $this->lang)
				->where(function ($query) use ($filter_ids) {
					foreach ($filter_ids as $filter_id => $filter_value_ids) {
						$query->whereRaw("pf" . (int)$filter_id . ".filter_id = " . (int)$filter_id . " and pf" . (int)$filter_id . ".filter_value_id in (?)", [implode(',', $filter_value_ids)]);
					}
				});
			
			$results = $results->orderBy('products.created_at', 'desc')->paginate(session('settings.limit_sait', 25), ['*'], 'page', $page);
			
			if (!$results->isEmpty()) {
				if (in_array($sort, ['name', 'price', 'popular'])) {
					$results->setCollection(
						$results->{$order == 'desc' ? 'sortByDesc' : 'sortBy'}(function ($query) use ($sort) {
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
				
				if ($page > $results->lastPage()) {
					return redirect($path . '/page/' . $results->lastPage(), 301);
				}
				
				if ($page == 1 && $page_url) {
					return redirect($path, 301);
				}
				
				$data['products'] = $results;
				$data['total'] = $results->total();
			}
			
			$ids = [];
			$filter_value_ids = [];
			$prices = [];
			
			foreach ($results as $product) {
				$ids[] = $product->id;
				
				if (!$product->product_filter->isEmpty()) {
					foreach ($product->product_filter as $pf) {
						$filter_value_ids[] = $pf->filter_value_id;
					}
				}
				
				if (round($product->price)) $prices[] = $product->price;
				if (!empty($product->product_special_one->price)) $prices[] = $product->product_special_one->price;
				if (!empty($product->product_discount_one->price)) $prices[] = $product->product_discount_one->price;
			}
			
			$filters = Filters::with([
				'metaLang:filter_id,name,description',
				'filter_values' => function ($query) use ($filter_value_ids) {
					$query->with([
						'filter_value_description' => function ($query) use ($filter_value_ids) {
							$query->select('id', 'filter_value_id', 'name')->orderBy('name');
						}
					])->select('id', 'filter_id', 'sort', 'top', 'slug')->whereIn('filter_values.id', $filter_value_ids)->orderBy('sort');
				}
			])
				->select('filters.id', 'filters.type', 'filters.slug')
				->join('product_filter as pf', 'pf.filter_id', '=', 'filters.id')
				->where('filters.status', 1)
				->whereIn('pf.product_id', $ids)
				->groupBy('filters.id')
				->orderBy('sort')
				->get();
			
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
			
			if ($prices) {
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
			
			if ($data['select_filters']) {
				$data['active_filter'] = num_decline(count($data['select_filters']), [__('locale.text_filter_4'), __('locale.text_filter_4')], false) . ' <b>' . num_decline(count($data['select_filters']), [__('locale.text_filter_1'), __('locale.text_filter_2'), __('locale.text_filter_3')]) . '</b>';
			} else {
				$data['active_filter'] = '';
			}
			
			$per = $data['total'] - (session('settings.limit_sait', 25) * $page);
			$data['more'] = $per < session('settings.limit_sait', 25) ? $per : session('settings.limit_sait');
			
			if (isset($results) && $prev = $results->previousPageUrl()) {
				$header->setLinkData([
					[
						'href' => route(session('route_url') . '_new') . ($sort_url ? ('/' . 'sort-' . $sort . '/' . $order) : '') . str_replace(['?page=', '&page='], '/page/', str_replace(url()->current(), '', $prev)),
						'rel' => 'prev'
					]
				]);
			}
			
			if (isset($results) && $last = $results->nextPageUrl()) {
				$data['next'] = route(session('route_url') . '_new') . ($sort_url ? ('/' . 'sort-' . $sort . '/' . $order) : '') . str_replace(['?page=', '&page='], '/page/', str_replace(url()->current(), '', $last));
				
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
					'url' => route(session('route_url') . '_new') . $url . '/sort-price/asc' . ($page > 1 ? '/page/' . $page : ''),
					'active' => $sort == 'price' && $data['order'] == 'asc' ? 1 : 0
				],
				[
					'name' => __('locale.text_sort_price_desc'),
					'url' => route(session('route_url') . '_new') . $url . '/sort-price/desc' . ($page > 1 ? '/page/' . $page : ''),
					'active' => $sort == 'price' && $data['order'] == 'desc' ? 1 : 0
				],
				[
					'name' => __('locale.text_sort_name_asc'),
					'url' => route(session('route_url') . '_new') . $url . '/sort-name/asc' . ($page > 1 ? '/page/' . $page : ''),
					'active' => $sort == 'name' && $data['order'] == 'asc' ? 1 : 0
				],
				[
					'name' => __('locale.text_sort_name_desc'),
					'url' => route(session('route_url') . '_new') . $url . '/sort-name/asc' . ($page > 1 ? '/page/' . $page : ''),
					'active' => $sort == 'name' && $data['order'] == 'desc' ? 1 : 0
				],
				[
					'name' => __('locale.text_sort_popular_asc'),
					'url' => route(session('route_url') . '_new') . $url . '/sort-popular/asc' . ($page > 1 ? '/page/' . $page : ''),
					'active' => $sort == 'popular' && $data['order'] == 'asc' ? 1 : 0
				],
				[
					'name' => __('locale.text_sort_popular_desc'),
					'url' => route(session('route_url') . '_new') . $url . '/sort-popular/desc' . ($page > 1 ? '/page/' . $page : ''),
					'active' => $sort == 'popular' && $data['order'] == 'desc' ? 1 : 0
				]
			];
			
			$data['price_range'] = $price_range;
			$data['new_url'] = route(session('route_url') . '_new');
			
			$header->setMeta($meta);
			
			$content = new GetContentController(0);
			$data['content_top'] = $content->getPosition('top');
			$data['content_bottom'] = $content->getPosition('bottom');
			$header->setStyle($content->getHtmlStyle());
			$header->setLinkStyle($content->getLinkStyle());
			$header->setScript($content->getScript());
			$header->setLinkData($stylesheet);
			$header->setBreadcrumbs($this->breadcrumbs->render());
			
			$data = array_merge($data, $header->data());
			$cart = new CartController;
			$data['cart'] = $cart->mini_cart($content->getModuleById('saleday'));
			$cart_count = $cart->getCount();
			$data['cart_count'] = $cart_count > 99 ? '99+' : $cart_count;
			$region_code = config('app.region_code');
			$this->region['code'] = $region_code ? $region_code . '/' : '';
			
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
			
			return render_view(view('pages.site.new', $data), $this->region);
		}
		
		public function filter(Request $request) {
			$json = [];
			
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
					$price = (array)Products::distinct()->select('products.id', 'products.price')->with([
						'product_special_one:product_id,price',
						'product_discount_one:product_id,price'
					])->where('products.created_at', '>=', DB::raw('DATE_SUB(NOW(), INTERVAL 15 DAY)'))->get()->toArray();
					
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
							$url .= '/price/' . $min . '-' . $max;
						} else {
							$url .= '/price/' . $min;
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
			
			if (isset($url)) {
				$url = route(session('route_url') . '_new') . (!empty($url) ? $url : '');
				
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
					$products = Products::distinct()
						->with(
							[
								'product_special_one:product_id,price',
								'product_discount:product_id,price'
							]
						)
						->select('products.id', 'products.price', 'products.created_at')
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
						->where('products.created_at', '>=', DB::raw('DATE_SUB(NOW(), INTERVAL 15 DAY)'))
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