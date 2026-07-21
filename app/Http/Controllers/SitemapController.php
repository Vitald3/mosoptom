<?php
	
	namespace App\Http\Controllers;
	
	use App\Models\Pages;
	use App\Models\Products;
	use App\Models\Categories;
	use App\Models\PageCategories;
	use Illuminate\Support\Facades\Route;
	use App\Http\Controllers\HeaderController;
	use Illuminate\Support\Facades\Cache;
	use Nyholm\Psr7\Request;
	
	class SitemapController extends Controller
	{
		public function __construct() {
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			
			$classes = array('breadcrumb', 'breadcrumb-item');
			$this->breadcrumbs->addCssClasses($classes);
			$this->breadcrumbs->setDivider('');
			
			$this->settings = session('settings');
			$this->lang = session('lang');
			$this->breadcrumbs->addCrumb(__('locale.home'), route(session('route_url') . '_home'));
			$this->region = session('region');
		}
		
		public function index()
		{
			$header = new HeaderController;
			
			$meta['meta_title'] = __('locale.text_sitemap');
			$meta['meta_description'] = false;
			$meta['meta_keywords'] = false;
			
			$header->setMeta($meta);
			$this->breadcrumbs->addCrumb($meta['meta_title'], route(session('route_url') . '_sitemap'));
			$header->setBreadcrumbs($this->breadcrumbs->render());
			
			$data['content_top'] = '';
			$data['content_bottom'] = '';
			
			$data = array_merge($data, $header->data());
			$data['locale'] = __('locale');
			$data['class'] = 'sitemap';
			$data['canonical'] = route(session('route_url') . '_sitemap');
			$data['title'] = $meta['meta_title'];
			
			$content = new GetContentController(0);
			$cart = new CartController;
			$data['cart'] = $cart->mini_cart($content->getModuleById('saleday'));
			$cart_count = $cart->getCount();
			$data['cart_count'] = $cart_count > 99 ? '99+' : $cart_count;
			$region_code = config('app.region_code');
			$this->region['code'] = $region_code ? $region_code . '/' : '';
			
			$data['page_categories'] = [];
			
			$categories = PageCategories::with([
				'children' => function($query) {
					$query->with('metaLang:category_id,name')->select('id', 'parent_id', 'status');
				},
				'pages' => function($query) {
					$query->with('metaLang:page_id,name')->select('parent_id', 'id');
				},
				'metaLang:category_id,name'
			])->select('id')->where([['status', 1], ['parent_id', 0]])->orderBy('sort')->get();
			
			foreach ($categories as $category) {
				$pages = [];
				
				if (!is_null($category->pages)) {
					foreach ($category->pages as $page) {
						$pages[] = [
							'id' => $page->id,
							'name' => $page->metaLang['name'],
							'url' => $page->getSlug()
						];
					}
				}
				
				$data['page_categories'][] = [
					'id' => $category->id,
					'name' => $category->metaLang['name'],
					'url' => $category->getSlug(),
					'pages' => $pages,
					'children' => $this->getCategories($category)
				];
			}
			
			return render_view(view('pages.site.sitemap', $data), $this->region);
		}

		public function xml() {
			$output = '<?xml version="1.0" encoding="UTF-8"?>';
			$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
			
			if (Cache::has('regions')) {
				$regions = Cache::get('regions');
			} else {
				$regions = [];
			}
			
			$output .= '<url>';
			$output .= '<loc>' . route($this->lang . '_home') . '</loc>';
			$output .= '<lastmod>' . date('Y-m-d\TH:i:sP') . '</lastmod>';
			$output .= '<changefreq>weekly</changefreq>';
			$output .= '<priority>0.7</priority>';
			$output .= '</url>';
			
			if (!empty($regions)) {
				foreach ($regions as $slug => $region) {
					$output .= '<url>';
					$output .= '<loc>' . route_region(route($this->lang . '_home'), $slug) . '</loc>';
					$output .= '<lastmod>' . date('Y-m-d\TH:i:sP') . '</lastmod>';
					$output .= '<changefreq>weekly</changefreq>';
					$output .= '<priority>0.7</priority>';
					$output .= '</url>';
				}
			}
			
			$categories = Categories::with([
				'children' => function($query) {
					$query->with('metaLang:category_id,name')->select('id', 'parent_id', 'status');
				},
				'products' => function($query) {
					$query->with('metaLang:product_id,name')->select('products.id', 'products.updated_at', 'category_id', 'image');
				},
				'metaLang:category_id,name'
			])->select('id', 'updated_at')->where([['status', 1], ['parent_id', 0]])->orderBy('sort')->get();
			
			if (!$categories->isEmpty()) {
				foreach ($categories as $category) {
					$output .= '<url>';
					$output .= '<loc>' . $category->getSlug() . '</loc>';
					$output .= '<lastmod>' . date('Y-m-d\TH:i:sP', \strtotime($category->updated_at)) . '</lastmod>';
					$output .= '<changefreq>weekly</changefreq>';
					$output .= '<priority>0.7</priority>';
					$output .= '</url>';
					
					if (!is_null($category->products)) {
						foreach ($category->products as $product) {
							if (!is_null($product->metaLang)) {
								$output .= '<url>';
								$output .= '  <loc>' . $product->getSlug() . '</loc>';
								$output .= '  <changefreq>weekly</changefreq>';
								$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', \strtotime($product->updated_at)) . '</lastmod>';
								$output .= '  <priority>1.0</priority>';
								
								if ($product->image) {
									$output .= '  <image:image>';
									$output .= '  <image:loc>' . asset($product->image) . '</image:loc>';
									$output .= '  <image:caption>' . $product->metaLang['name'] . '</image:caption>';
									$output .= '  <image:title>' . $product->metaLang['name'] . '</image:title>';
									$output .= '  </image:image>';
								}
								
								$output .= '</url>';
							}
						}
					}
					
					if (!is_null($category->children)) {
						$output .= $this->getXmlCategories($category->children, 'category_', $this->lang, '');
					}
				}
				
				if (!empty($regions)) {
					foreach ($regions as $slug => $region) {
						foreach ($categories as $category) {
							$output .= '<url>';
							$output .= '<loc>' . app(\App\Helpers\PathRouteService::class)->getRoute('category_' . $this->lang . '_id=' . $category->id, $this->lang, $slug) . '</loc>';
							$output .= '<lastmod>' . date('Y-m-d\TH:i:sP', \strtotime($category->updated_at)) . '</lastmod>';
							$output .= '<changefreq>weekly</changefreq>';
							$output .= '<priority>0.7</priority>';
							$output .= '</url>';
							
							if (!is_null($category->products)) {
								foreach ($category->products as $product) {
									$output .= '<url>';
									$output .= '  <loc>' . app(\App\Helpers\PathRouteService::class)->getRoute('product_' . $this->lang . '_id=' . $product->id, $this->lang, $slug) . '</loc>';
									$output .= '  <changefreq>weekly</changefreq>';
									$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', \strtotime($product->updated_at)) . '</lastmod>';
									$output .= '  <priority>1.0</priority>';
									
									if ($product->image) {
										$output .= '  <image:image>';
										$output .= '  <image:loc>' . asset($product->image) . '</image:loc>';
										$output .= '  <image:caption>' . $product->metaLang['name'] . '</image:caption>';
										$output .= '  <image:title>' . $product->metaLang['name'] . '</image:title>';
										$output .= '  </image:image>';
									}
									
									$output .= '</url>';
								}
							}
							
							if (!is_null($category->children)) {
								$output .= $this->getXmlCategories($category->children, 'category_', $this->lang, $slug);
							}
						}
					}
				}
			}
			
			$products = Products::with('metaLang:product_id,name')->select('id', 'updated_at', 'image')->where('parent_id', 0)->get();
			
			if (!$products->isEmpty()) {
				foreach ($products as $product) {
					$output .= '<url>';
					$output .= '  <loc>' . $product->getSlug() . '</loc>';
					$output .= '  <changefreq>weekly</changefreq>';
					$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', \strtotime($product->updated_at)) . '</lastmod>';
					$output .= '  <priority>1.0</priority>';
					
					if ($product->image) {
						$output .= '  <image:image>';
						$output .= '  <image:loc>' . asset($product->image) . '</image:loc>';
						$output .= '  <image:caption>' . $product->metaLang['name'] . '</image:caption>';
						$output .= '  <image:title>' . $product->metaLang['name'] . '</image:title>';
						$output .= '  </image:image>';
					}
					
					$output .= '</url>';
				}
				
				if (!empty($regions)) {
					foreach ($regions as $slug => $region) {
						foreach ($products as $product) {
							$output .= '<url>';
							$output .= '  <loc>' . app(\App\Helpers\PathRouteService::class)->getRoute('product_' . $this->lang . '_id=' . $product->id, $this->lang, $slug) . '</loc>';
							$output .= '  <changefreq>weekly</changefreq>';
							$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', \strtotime($product->updated_at)) . '</lastmod>';
							$output .= '  <priority>1.0</priority>';
							
							if ($product->image) {
								$output .= '  <image:image>';
								$output .= '  <image:loc>' . asset($product->image) . '</image:loc>';
								$output .= '  <image:caption>' . $product->metaLang['name'] . '</image:caption>';
								$output .= '  <image:title>' . $product->metaLang['name'] . '</image:title>';
								$output .= '  </image:image>';
							}
							
							$output .= '</url>';
						}
					}
				}
			}
			
			$categories = PageCategories::with([
				'children' => function($query) {
					$query->with('metaLang:category_id,name')->select('id', 'parent_id', 'status');
				},
				'pages' => function($query) {
					$query->with('metaLang:page_id,name')->select('id', 'updated_at', 'parent_id', 'image');
				},
				'metaLang:category_id,name'
			])->select('id', 'updated_at')->where([['status', 1], ['parent_id', 0]])->orderBy('sort')->get();
			
			if (!$categories->isEmpty()) {
				foreach ($categories as $category) {
					$output .= '<url>';
					$output .= '<loc>' . $category->getSlug() . '</loc>';
					$output .= '<lastmod>' . date('Y-m-d\TH:i:sP', \strtotime($category->updated_at)) . '</lastmod>';
					$output .= '<changefreq>weekly</changefreq>';
					$output .= '<priority>0.7</priority>';
					$output .= '</url>';
					
					if (!is_null($category->pages)) {
						foreach ($category->pages as $page) {
							$output .= '<url>';
							$output .= '  <loc>' . $page->getSlug() . '</loc>';
							$output .= '  <changefreq>weekly</changefreq>';
							$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', \strtotime($page->updated_at)) . '</lastmod>';
							$output .= '  <priority>1.0</priority>';
							
							if ($page->image) {
								$output .= '  <image:image>';
								$output .= '  <image:loc>' . asset($page->image) . '</image:loc>';
								$output .= '  <image:caption>' . $page->metaLang['name'] . '</image:caption>';
								$output .= '  <image:title>' . $page->metaLang['name'] . '</image:title>';
								$output .= '  </image:image>';
							}
							
							$output .= '</url>';
						}
					}
					
					if (!is_null($category->children)) {
						$output .= $this->getXmlCategories($category->children, 'page_category_', $this->lang, '');
					}
				}
				
				if (!empty($regions)) {
					foreach ($regions as $slug => $region) {
						foreach ($categories as $category) {
							$output .= '<url>';
							$output .= '<loc>' . app(\App\Helpers\PathRouteService::class)->getRoute('page_category_' . $this->lang . '_id=' . $category->id, $this->lang, $slug) . '</loc>';
							$output .= '<lastmod>' . date('Y-m-d\TH:i:sP', \strtotime($category->updated_at)) . '</lastmod>';
							$output .= '<changefreq>weekly</changefreq>';
							$output .= '<priority>0.7</priority>';
							$output .= '</url>';
							
							if (!is_null($category->pages)) {
								foreach ($category->pages as $page) {
									$output .= '<url>';
									$output .= '  <loc>' . app(\App\Helpers\PathRouteService::class)->getRoute('page_' . $this->lang . '_id=' . $page->id, $this->lang, $slug) . '</loc>';
									$output .= '  <changefreq>weekly</changefreq>';
									$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', \strtotime($page->updated_at)) . '</lastmod>';
									$output .= '  <priority>1.0</priority>';
									
									if ($page->image) {
										$output .= '  <image:image>';
										$output .= '  <image:loc>' . asset($page->image) . '</image:loc>';
										$output .= '  <image:caption>' . $page->metaLang['name'] . '</image:caption>';
										$output .= '  <image:title>' . $page->metaLang['name'] . '</image:title>';
										$output .= '  </image:image>';
									}
									
									$output .= '</url>';
								}
							}
							
							if (!is_null($category->children)) {
								$output .= $this->getXmlCategories($category->children, 'page_category_', $this->lang, '');
							}
						}
					}
				}
			}
			
			$pages = Pages::with('metaLang:page_id,name')->select('id', 'updated_at', 'image')->where('parent_id', 0)->get();
			
			if (!$pages->isEmpty()) {
				foreach ($pages as $page) {
					$output .= '<url>';
					$output .= '  <loc>' . $page->getSlug() . '</loc>';
					$output .= '  <changefreq>weekly</changefreq>';
					$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', \strtotime($page->updated_at)) . '</lastmod>';
					$output .= '  <priority>1.0</priority>';
					
					if ($page->image) {
						$output .= '  <image:image>';
						$output .= '  <image:loc>' . asset($page->image) . '</image:loc>';
						$output .= '  <image:caption>' . $page->metaLang['name'] . '</image:caption>';
						$output .= '  <image:title>' . $page->metaLang['name'] . '</image:title>';
						$output .= '  </image:image>';
					}
					
					$output .= '</url>';
				}
				
				if (!empty($regions)) {
					foreach ($regions as $slug => $region) {
						foreach ($pages as $page) {
							$output .= '<url>';
							$output .= '  <loc>' . app(\App\Helpers\PathRouteService::class)->getRoute('page_' . $this->lang . '_id=' . $page->id, $this->lang, $slug) . '</loc>';
							$output .= '  <changefreq>weekly</changefreq>';
							$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', \strtotime($page->updated_at)) . '</lastmod>';
							$output .= '  <priority>1.0</priority>';
							
							if ($page->image) {
								$output .= '  <image:image>';
								$output .= '  <image:loc>' . asset($page->image) . '</image:loc>';
								$output .= '  <image:caption>' . $page->metaLang['name'] . '</image:caption>';
								$output .= '  <image:title>' . $page->metaLang['name'] . '</image:title>';
								$output .= '  </image:image>';
							}
							
							$output .= '</url>';
						}
					}
				}
			}
			
			$output .= '</urlset>';
			
			return response($output, 200)->header('Content-Type', 'application/xml');
		}
		
		private function getCategories($category) {
			$childrens = [];
			
			if (!empty($category['children'])) {
				foreach ($category['children'] as $children) {
					$pages = [];
					
					if (!is_null($children->pages)) {
						foreach ($children->pages as $page) {
							$pages[] = [
								'id' => $page->id,
								'name' => $page->metaLang['name'],
								'url' => $page->getSlug()
							];
						}
					}
					
					$childrens[] = [
						'id' => $children->id,
						'name' => $children->metaLang['name'],
						'url' => $children->getSlug(),
						'pages' => $pages,
						'children' => $this->getCategories($children)
					];
				}
			}
			
			return $childrens;
		}
		
		private function getXmlCategories($categories, $key, $lang, $slug) {
			$output = '';
			
			foreach ($categories as $category) {
				$output .= '<url>';
				$output .= '<loc>' . app(\App\Helpers\PathRouteService::class)->getRoute($key . $this->lang . '_id=' . $category->id, $lang, $slug) . '</loc>';
				$output .= '<lastmod>' . date('Y-m-d\TH:i:sP', \strtotime($category->updated_at)) . '</lastmod>';
				$output .= '<changefreq>weekly</changefreq>';
				$output .= '<priority>0.7</priority>';
				$output .= '</url>';
				
				if (!is_null($category->pages)) {
					foreach ($category->pages as $page) {
						if (!is_null($page->metaLang)) {
							$output .= '<url>';
							$output .= '  <loc>' . app(\App\Helpers\PathRouteService::class)->getRoute('page_' . $this->lang . '_id=' . $page->id, $lang, $slug) . '</loc>';
							$output .= '  <changefreq>weekly</changefreq>';
							$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', \strtotime($page->updated_at)) . '</lastmod>';
							$output .= '  <priority>1.0</priority>';
							
							if ($page->image) {
								$output .= '  <image:image>';
								$output .= '  <image:loc>' . asset($page->image) . '</image:loc>';
								$output .= '  <image:caption>' . $page->metaLang['name'] . '</image:caption>';
								$output .= '  <image:title>' . $page->metaLang['name'] . '</image:title>';
								$output .= '  </image:image>';
							}
							
							$output .= '</url>';
						}
					}
				}
				
				if (!is_null($category->products)) {
					foreach ($category->products as $product) {
						if (!is_null($product->metaLang)) {
							$output .= '<url>';
							$output .= '  <loc>' . app(\App\Helpers\PathRouteService::class)->getRoute('product_' . $this->lang . '_id=' . $product->id, $lang, $slug) . '</loc>';
							$output .= '  <changefreq>weekly</changefreq>';
							$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', \strtotime($product->updated_at)) . '</lastmod>';
							$output .= '  <priority>1.0</priority>';
							
							if ($product->image) {
								$output .= '  <image:image>';
								$output .= '  <image:loc>' . asset($product->image) . '</image:loc>';
								$output .= '  <image:caption>' . $product->metaLang['name'] . '</image:caption>';
								$output .= '  <image:title>' . $product->metaLang['name'] . '</image:title>';
								$output .= '  </image:image>';
							}
							
							$output .= '</url>';
						}
					}
				}
				
				if (!is_null($category->children)) {
					$output .= $this->getXmlCategories($category->children, $key, $lang, $slug);
				}
			}
			
			return $output;
		}
	}
