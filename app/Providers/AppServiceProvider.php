<?php
	
	namespace App\Providers;
	
	use Illuminate\Support\ServiceProvider;
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Support\Facades\Request;
	use Illuminate\Support\Facades\App;
	use Illuminate\Support\Facades\Cache;
	use App\Models\Regions;
	use App\Models\Customers;
	use App\Models\Currencies;
	use App\Models\Languages;
	use App\Models\Settings;
	
	class AppServiceProvider extends ServiceProvider
	{
		/**
		 * Register any application services.
		 *
		 * @return void
		 */
		public function register()
		{
			//
		}
		
		/**
		 * Bootstrap any application services.
		 *
		 * @return void
		 */
		public function boot()
		{
			$language_default = config('app.locale');
			
			if(\Schema::hasTable('settings')) {
				$setting = Settings::addSelect('value')->where('code', 'settings')->value('value');
				
				if (!empty($setting['default_language'])) {
					$language_default = $setting['default_language'];
				}
				
				session(['settings' => $setting]);
				
				if (!empty($setting['customer_group_id']) && is_null(session('customer_group_id'))) {
					session(['customer_group_id' => $setting['customer_group_id']]);
				}
				
				if (!is_null(session('customer_id'))) {
					$customer = Customers::with([
						'address' => function ($query) {
							$query->selectRaw("customer_id,id,address,address2");
						},
						'social' => function ($query) {
							$query->selectRaw("customer_id,social,text");
						},
						'legal',
						'emails' => function ($query) {
							$query->selectRaw("customer_id,email");
						},
						'phones' => function ($query) {
							$query->selectRaw("customer_id,phone");
						}
					])->where('status', 1)->where('id', session('customer_id'))->first();
					
					if (!empty($customer)) {
						$customer = $customer->toArray();
						
						if ($customer['type'] == 1 && !empty($customer['legal']['firstname'])) {
							$customer['firstname'] = $customer['legal']['firstname'];
						}
						
						session(['customer' => $customer]);
						session(['customer_id' => $customer['id']]);
					} else {
						session(['customer' => null]);
						session(['customer_id' => null]);
					}
				}
				
				session(['currency_code' => $setting['currency_code']]);
				
				if (Cache::has('currencies')) {
					$code = session('currency_code');
					$currencies = Cache::get('currencies');
					session(['currency' => !empty($currencies[$code]) ? $currencies[$code]->toArray() : []]);
				} else {
					$currencies = Currencies::select('id', 'title', 'code', 'value', 'symbol', 'position', 'decimal')->where('status', 1)->get()->keyBy('code');
					Cache::set('currencies', $currencies);
					
					$code = session('currency_code');
					session(['currency' => !empty($currencies[$code]) ? $currencies[$code]->toArray() : []]);
				}
				
				session(['currency_with_code.' . $code => session('currency')]);
			}
			
			session(['languages' => Languages::where('status', 1)->orderBy('sort')->get()->keyBy('code')]);
			
			session(['default_language' => $language_default]);
			
			if ($language_default != config('app.locale_prefix')) {
				$lang = config('app.locale');
			} else {
				$lang = $language_default;
			}
			
			if (!$lang) $lang = config('app.locale');
			
			session(['lang' => $lang]);
			
			if (in_array(Request::segment(1), config('app.alt_langs'))) {
				App::setLocale(Request::segment(1));
				config(['app.locale_prefix' => Request::segment(1)]);
			}
			
			if (Cache::has('regions')) {
				$regions = Cache::get('regions');
			}
			else {
				$regions = Regions::with('meta')->select('id', 'slug')->where('status', 1)->get()->keyBy('slug')->toArray();
				
				foreach ($regions as &$region) {
					if (!empty($region['meta'])) {
						foreach ($region['meta'] as $key => $meta) {
							$region['meta'][$meta['lang']] = [
								'name' => $meta['name'],
								'format1' => $meta['format1'],
								'format2' => $meta['format1'],
								'format3' => $meta['format1'],
								'meta_title' => $meta['meta_title'],
								'meta_description' => $meta['meta_description'],
								'meta_keywords' => $meta['meta_keywords']
							];
							
							unset($region['meta'][$key]);
						}
					}
				}
				
				Cache::put('regions', $regions);
			}
			
			if (!empty($regions[Request::segment(1)])) {
				config(['app.region_code' => Request::segment(1)]);
			} elseif (!empty($regions[Request::segment(2)])) {
				config(['app.region_code' => Request::segment(2)]);
			}
			
			$region_code = config('app.region_code');
			session(['route_url' => $lang . ($region_code ? '_' . $region_code : '')]);
			
			if (!empty($regions[config('app.region_code')]['meta'][$lang])) {
				session(['region' => $regions[config('app.region_code')]['meta'][$lang]]);
			} else if(!empty($setting)) {
				if (!empty($setting['format1'][$lang])) {
					$format1 = $setting['format1'][$lang];
				} else {
					$format1 = '';
				}
				
				if (!empty($setting['format2'][$lang])) {
					$format2 = $setting['format2'][$lang];
				} else {
					$format2 = '';
				}
				
				if (!empty($setting['format3'][$lang])) {
					$format3 = $setting['format3'][$lang];
				} else {
					$format3 = '';
				}
				
				if (!empty($setting['meta_title'][$lang])) {
					$meta_title = $setting['meta_title'][$lang];
				} else {
					$meta_title = __('locale.name');
				}
				
				if (!empty($setting['meta_title'][$lang])) {
					$meta_description = $setting['meta_description'][$lang];
				} else {
					$meta_description = '';
				}
				
				if (!empty($setting['meta_title'][$lang])) {
					$meta_keywords = $setting['meta_keywords'][$lang];
				} else {
					$meta_keywords = '';
				}
				
				$region_code = config('app.region_code');
				
				session(['region' => ['meta_title' => $meta_title, 'meta_description' => $meta_description, 'meta_keywords' => $meta_keywords, 'code' => $region_code, 'code' => $region_code, 'name' => '', 'format1' => $format1, 'format2' => $format2, 'format3' => $format3]]);
			}
			
			Schema::defaultStringLength(191);
		}
	}
