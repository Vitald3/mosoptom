<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Cache;
	use App\Models\Pages;
	use App\Models\Attributes;
	use App\Models\PageImage;
	use App\Models\PageAttribute;
	use App\Models\PageAttributeImage;
	use App\Models\PageCategories;
	use App\Models\PageDescription;
	use App\Models\Languages;
	use App\Models\Layouts;
	
	class PagesController extends Controller {
		private $breadcrumbs;
		private $settings;
		
		public function __construct()
		{
			$this->settings = session('settings');
			$this->lang = session('lang');
			
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			
			$classes = array('breadcrumb', 'breadcrumb-item');
			$this->breadcrumbs->addCssClasses($classes);
			$this->breadcrumbs->setDivider('');
			
			$this->breadcrumbs->addCrumb(__('locale.home'), url('admin'));
			
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
		
		public function index(Request $request) {
			$where = [];
			
			$language_default = session('default_language');
			
			if (!is_null($request->status)) {
				$where[] = ['pages.status', '=', $request->status];
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
			
			if (!is_null($request->category_id)) {
				$where[] = ['pages.parent_id', '=', $request->category_id];
				$category_id = $request->category_id;
			} else {
				$category_id = 0;
			}
			
			if (!is_null($request->category)) {
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
			
			$sort_name = url('admin/pages', ['sort' => 'pd.name', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_sort = url('admin/pages', ['sort' => 'pages.sort', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_status = url('admin/pages', ['sort' => 'pages.status', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			
			if (in_array($sort, ['pd.name', 'pages.sort', 'pages.status'])) {
				$pages = Pages::distinct()->select('pages.sort', 'pages.id', 'pages.parent_id', 'pages.status', 'pd.name')->leftjoin('page_description as pd', 'pd.page_id', '=', 'pages.id')->where($where)->orderBy($sort, $order)->paginate($limit);
			} else {
				$pages = Pages::distinct()->select('pages.sort', 'pages.id', 'pages.parent_id', 'pages.status', 'pd.name')->leftjoin('page_description as pd', 'pd.page_id', '=', 'pages.id')->where($where)->orderBy('pd.name')->paginate($limit);
			}
			
			$this->breadcrumbs->addCrumb('Статьи', url('admin/pages') . $this->params);
			$breadcrumbs = $this->breadcrumbs->render();
			$params = $this->params;
			$params_array = $this->params_array;
			
			$categories = $this->getCategories();
			
			return view('pages.pages', compact('params', 'params_array', 'breadcrumbs', 'categories', 'category', 'category_id', 'sort_name', 'sort_sort', 'sort_status', 'pages', 'name', 'status', 'sort', 'order'));
		}
		
		public function add() {
			$langs = Languages::orderBy('name', 'asc')->get();
			$categories = $this->getCategories();
			$attributes2 = Attributes::with('metaLang')->where('status', 1)->get();
			$layouts = Layouts::orderBy('name', 'asc')->get();
			$this->breadcrumbs->addCrumb('Статьи', url('admin/pages') . $this->params);
			$this->breadcrumbs->addCrumb('Создать', url('admin/page_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.page-edit', ['breadcrumbs' => $breadcrumbs, 'layout_id' => old('layout_id'), 'css' => old('css'), 'layouts' => $layouts, 'attribute_im' => [], 'attribute_images' => [], 'attribute_descriptions' => [], 'attributes2' => $attributes2, 'attributes' => (array)old('attributes'), 'categories' => $categories, 'langs' => $langs, 'name' => old('name'), 'parent_id' => old('parent_id'), 'slug' => old('slug'), 'meta' => old('meta'), 'bottom' => old('bottom'), 'top' => old('top'), 'sort' => old('sort'), 'status' => old('status'), 'image' => old('image'), 'images' => (array)old('images'), 'action' => asset('admin/page_save') . $this->params, 'id' => '', 'action2' => asset('admin/page_add_image')]);
		}
		
		private function getCategories()
		{
			$categories_name = [];
			
			$categories = PageCategories::with('metaLang')->where('status', 1)->get()->keyBy('id');
			
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
		
		public function edit($id)
		{
			$page = Pages::where('id', $id)->first();
			
			if (!empty($page)) {
				extract($page->toArray());
				$langs = Languages::orderBy('name', 'asc')->get();
				$categories = $this->getCategories();
				$layouts = Layouts::orderBy('name', 'asc')->get();
				
				$images = PageImage::where('page_id', $id)->pluck('image')->toArray();
				$attribute_descriptions = [];
				
				$attributes = PageAttribute::where('page_id', $id)->get();
				$attribute_images = [];
				$attribute_im = [];
				
				foreach ($attributes as $attribute) {
					if (!is_null($attribute->product_attribute_image)) {
						$attribute_images[$attribute->attribute_id] = $attribute->page_attribute_image()->where('attribute_id', $attribute->attribute_id)->get()->toArray();
					}
					
					$attribute_im[] = $attribute->attribute_id;
					
					$attribute_descriptions[$attribute->attribute_id]['attribute_id'] = $attribute->attribute_id;
					$descriptions = [];
					
					foreach (PageAttribute::where([['page_id', $id], ['attribute_id', $attribute->attribute_id]])->get()->toArray() as $ad) {
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
				
				foreach ($page->meta as $description) {
					$meta[$description['lang']] = $description;
				}
				
				$this->breadcrumbs->addCrumb('Статьи', url('admin/pages') . $this->params);
				$this->breadcrumbs->addCrumb('Редактировать', url('admin/page/' . $id));
				$breadcrumbs = $this->breadcrumbs->render();
				$action = asset('admin/page_save') . $this->params;
				$action2 = asset('admin/page_add_image');
				
				return view('pages.page-edit', compact('breadcrumbs', 'attribute_im', 'css', 'attribute_images', 'attribute_descriptions', 'attributes', 'attributes2', 'images', 'layouts', 'categories', 'langs', 'layout_id', 'parent_id', 'slug', 'meta', 'bottom', 'top', 'sort', 'status', 'image', 'action', 'id', 'action2'));
			} else {
				return redirect('admin/pages' . $this->params)->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete(Request $request) {
			if ($request->selected) {
				$policy = $this->settings['policy'];
				
				$message = 'Операция успешна';
				$type = 'success';
				
				foreach ($request->selected as $s) {
					if ($s == $policy) {
						$message = 'Нельзя удалить страницу обработки персональных данных';
						$type = 'error';
					} else {
						Pages::where('id', $s)->delete();
						PageDescription::where('page_id', $s)->delete();
					}
				}
			} else {
				$message = 'Выделите пункты для удаления';
				$type = 'error';
			}
			
			return redirect('admin/pages' . $this->params)->with($type, $message);
		}
		
		public function save(Request $request) {
			$this->validate($request, [
				'meta.*.name' => 'required',
				'meta.*.meta_title' => 'required',
				'slug' => 'required|not_in:sort-name,sort-popular,sort-price,page,blog,catalog,price|max:255|alpha_dash',
				'layout_id' => 'required'
			]);
			
			$policy = false;
			
			if (!is_null($request->id)) {
				if ($this->settings['policy'] == $request->id && $request->status == 0) {
					$request->status = 1;
					$policy = true;
				}
				
				$pages['slug'] = $request->slug;
				$pages['layout_id'] = $request->layout_id;
				$pages['image'] = $request->image ? $request->image : '';
				$pages['css'] = $request->css ? $request->css : '';
				$pages['video'] = $request->video ? $request->video : '';
				$pages['bottom'] = $request->bottom ? $request->bottom : 0;
				$pages['top'] = $request->top ? $request->top : 0;
				$pages['parent_id'] = $request->parent_id ? $request->parent_id : 0;
				$pages['sort'] = $request->sort ? $request->sort : 0;
				$pages['status'] = $request->status;
				
				Pages::where('id', $request->id)->update($pages);
				
				PageDescription::where('page_id', $request->id)->delete();
				
				foreach ($request->meta as $lang => $meta) {
					$pd = new PageDescription;
					$pd->lang = $lang;
					$pd->page_id = $request->id;
					$pd->name = $meta['name'];
					$pd->meta_title = $meta['meta_title'];
					$pd->meta_description = !empty($meta['meta_description']) ? $meta['meta_description'] : '';
					$pd->meta_keywords = !empty($meta['meta_keywords']) ? $meta['meta_keywords'] : '';
					$pd->description = !empty($meta['description']) ? $meta['description'] : '';
					$pd->html = !empty($meta['html']) ? $meta['html'] : '';
					
					$pd->save();
				}
				
				PageImage::where('page_id', $request->id)->delete();
				
				if (!empty($request->images)) {
					foreach ($request->images as $image) {
						$pi = new PageImage;
						$pi->image = $image;
						$pi->page_id = $request->id;
						
						$pi->save();
					}
				}
				
				PageAttribute::where('page_id', $request->id)->delete();
				PageAttributeImage::where('page_id', $request->id)->delete();
				
				if (!empty($request->page_attribute)) {
					foreach ($request->page_attribute as $attribute) {
						if (isset($attribute['image'])) {
							foreach ($attribute['image'] as $image) {
								if (!is_null($image)) {
									$pai = new PageAttributeImage;
									$pai->image = $image;
									$pai->page_id = $request->id;
									$pai->attribute_id = $attribute['attribute_id'];
									
									$pai->save();
								}
							}
						}
						
						foreach ($attribute['page_attribute_description'] as $key => $description) {
							$pa = new PageAttribute;
							$pa->page_id = $request->id;
							$pa->attribute_id = $attribute['attribute_id'];
							$pa->text = isset($description['text']) ? $description['text'] : '';
							$pa->lang = $key;
							
							$pa->save();
						}
					}
				}
			} else {
				$pages = new pages;
				$pages->slug = $request->slug;
				$pages->layout_id = $request->layout_id;
				$pages->image = $request->image ? $request->image : '';
				$pages->css = $request->css ? $request->css : '';
				$pages->video = $request->video ? $request->video : '';
				$pages->bottom = $request->bottom ? $request->bottom : 0;
				$pages->top = $request->top ? $request->top : 0;
				$pages->parent_id = $request->parent_id ? $request->parent_id : 0;
				$pages->sort = $request->sort ? $request->sort : 0;
				$pages->status = $request->status;
				
				$pages->save();
				
				foreach ($request->meta as $lang => $meta) {
					$pd = new PageDescription;
					$pd->lang = $lang;
					$pd->page_id = $pages->id;
					$pd->name = $meta['name'];
					$pd->meta_title = $meta['meta_title'];
					$pd->meta_description = !empty($meta['meta_description']) ? $meta['meta_description'] : '';
					$pd->meta_keywords = !empty($meta['meta_keywords']) ? $meta['meta_keywords'] : '';
					$pd->description = !empty($meta['description']) ? $meta['description'] : '';
					$pd->html = !empty($meta['html']) ? $meta['html'] : '';
					
					$pd->save();
				}
				
				if (!empty($request->images)) {
					foreach ($request->images as $image) {
						$pi = new PageImage;
						$pi->image = $image;
						$pi->page_id = $pages->id;
						
						$pi->save();
					}
				}
				
				if (!empty($request->page_attribute)) {
					foreach ($request->page_attribute as $attribute) {
						if (isset($attribute['image'])) {
							foreach ($attribute['image'] as $image) {
								if (!is_null($image)) {
									$pai = new PageAttributeImage;
									$pai->image = $image;
									$pai->page_id = $pages->id;
									$pai->attribute_id = $attribute['attribute_id'];
									
									$pai->save();
								}
							}
						}
						
						foreach ($attribute['page_attribute_description'] as $key => $description) {
							$pa = new PageAttribute;
							$pa->page_id = $pages->id;
							$pa->attribute_id = $attribute['attribute_id'];
							$pa->text = isset($description['text']) ? $description['text'] : '';
							$pa->lang = $key;
							
							$pa->save();
						}
					}
				}
			}
			
			$routes = app(\App\Helpers\PathRouteService::class);
			Cache::put('seo_url', $routes->getRoutes());
			
			if ($policy) {
				return redirect('admin/pages' . $this->params)->with('error', 'Нельзя выключать страницу обработки персональных данных');
			} else {
				return redirect('admin/pages' . $this->params)->with('success', 'Операция успешна');
			}
		}
		
		public function addImage(Request $request) {
			if ($request->hasFile('file')) {
				$files = $request->file('file');
				
				$images = [];
				
				if (!is_array($files)) {
					$name = $files->getClientOriginalName();
					$files->move('assets/site/img/pages', $name);
					$images[] = 'assets/site/img/pages/' . $name;
				} else {
					foreach ($files as $file) {
						$name = $file->getClientOriginalName();
						$file->move('assets/site/img/pages', $name);
						$images[] = 'assets/site/img/pages/' . $name;
					}
				}
				
				return response()->json($images);
			}
		}
		
		public function show(Request $request) {
			$header = new HeaderController;
			$id = (int)$request->get('page_id');
			$page = (int)$request->get('page');
			if (!$page) $page = 1;
			
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
			
			$results = Pages::with([
				'metaLang:page_id,name,meta_title,meta_description,meta_keywords,description,html',
			])->select('id', 'image', 'parent_id', 'css', 'slug', 'layout_id', 'updated_at', 'created_at')->where([['status', 1], ['id', $id]])->firstOrFail();
			
			$this->breadcrumbs->addCrumb($results->metaLang->name, $results->getSlug());
			
			$meta = $results->metaLang->toArray();
			$data['class'] = 'body_page body_page-' .  $id;
			
			if ($results->css) $header->setStyle('<style>' . $results->css . '</style>');
			
			$data['image'] = resize_image($results->image, 970, 560);
			if ($results->image) {
                $data['popup'] = asset($results->image);
            } else {
                $data['popup'] = false;
            }
			$data['thumb'] = resize_image($results->image, 170, 130);
			
			$data['attributes'] = $results->page_attribute()
				->with([
					'page_attribute_imageById' => function($query) use ($id) {
						$query->where('page_id', $id);
					}
				])
				->join('attributes as a', 'a.id', '=', 'page_attribute.attribute_id')
				->join('attribute_description as ad', 'ad.attribute_id', '=', 'a.id')
				->select('a.image', 'page_attribute.page_id', 'page_attribute.attribute_id', 'ad.name', 'page_attribute.text')
				->where('ad.lang', $this->lang)
				->orderBy('a.sort')
				->orderBy('page_attribute.text')
				->get();
			
			$data['images'] = [];
			$data['thumbs'] = [];
			$data['thumbs2'] = [];
			
			if (!$results->page_image->isEmpty()) {
				$x = 1;
				
				foreach ($results->page_image as $images) {
					$x++;
					
					$data['images'][] = [
						'image' => resize_image($images['image'], 970, 560),
						'popup' => asset($images['image']),
						'alt' => $meta['name'] . sprintf(__('locale.v25'), $x)
					];
					
					$data['thumbs'][] = [
						'image' => resize_image($images['image'], 170, 130),
						'alt' => $meta['name'] . sprintf(__('locale.v25'), $x)
					];
					
					$data['thumbs2'][] = [
						'image' => resize_image($images['image'], 270, 270),
						'popup' => asset($images['image']),
						'alt' => $meta['name'] . sprintf(__('locale.v25'), $x)
					];
				}
			}
			
			if (!empty(strip_tags($meta['html']))) {
				$header->setLinkData([
					[
						'href' => asset('assets/site/css/page.css'),
						'rel' => 'stylesheet'
					],
					[
						'href' => asset('assets/site/css/media/page.css'),
						'rel' => 'stylesheet'
					]
				]);
			} else {
				$header->setLinkData([
					[
						'href' => asset('assets/site/css/page.css'),
						'rel' => 'stylesheet'
					],
					[
						'href' => asset('assets/site/css/media/pages.css'),
						'rel' => 'stylesheet'
					]
				]);
			}
			
			$data['canonical'] = $request->get('path') . '/' . $results->slug;
			$data['title'] = $meta['name'];
			$data['updated_at'] = $results->updated_at;
			$data['created_at'] = $results->created_at;
			
			if ($page == 1) {
				$data['description'] = html_entity_decode($meta['description']);
				$data['html'] = html_entity_decode($meta['html']);
			} else {
				$data['description'] = '';
				$data['html'] = '';
				$meta['meta_description'] = false;
				$meta['meta_keywords'] = false;
			}
			
			if ($page > 1) {
				$data['title'] .= sprintf(__('locale.v16'), $page);
				$meta['meta_title'] .= sprintf(__('locale.v16'), $page);
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
			
			return render_view(view('pages.site.page', $data), $this->region, false);
		}
	}
