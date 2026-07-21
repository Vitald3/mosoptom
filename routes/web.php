<?php
	
	use Illuminate\Http\Request;
	
	$language_default = session('default_language');
	$all_langs = (array)config('app.all_langs');
	$regions = session('regions');
	
	Route::group(['prefix' => 'admin'], function() {
		Route::get('register', 'Auth\RegisterController@showRegistrationForm');
		Route::post('register', 'Auth\RegisterController@register');
		Route::get('login', 'Auth\LoginController@showLoginForm');
		Route::post('login', 'Auth\LoginController@login');
		Route::get('logout', 'Auth\LoginController@logout');
		
		Route::get('forgot-password', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('forget.password.get');
		Route::post('forgot-password', 'Auth\ForgotPasswordController@submitForgetPasswordForm')->name('forget.password.post');
		Route::post('password/reset-password', 'Auth\ForgotPasswordController@submitResetPasswordForm')->name('reset.password.get');
		Route::get('password/reset/{token}', 'Auth\ForgotPasswordController@showResetPasswordForm')->name('reset.password.post');
		Route::post('password/update', 'Auth\ForgotPasswordController@submitResetPasswordForm');
		
		Route::group(['middleware' => ['role:look|edit|create|delete']], function() {
			Route::get('settings', 'SettingsController@index');
			Route::get('languages/{sort?}/{order?}', 'LanguagesController@index');
			Route::get('', 'DashboardController@index');
			Route::get('products/{sort?}/{order?}', 'ProductsController@index');
			Route::get('attributes/{sort?}/{order?}', 'AttributesController@index');
			Route::get('attribute_groups/{sort?}/{order?}', 'AttributesController@groups');
			Route::get('categories/{sort?}/{order?}', 'CategoriesController@index');
			Route::get('filters/{sort?}/{order?}', 'FiltersController@index');
			Route::get('contacts','SettingsController@contacts');
			Route::get('pages/{sort?}/{order?}','PagesController@index');
			Route::get('page_categories/{sort?}/{order?}','PageCategoryController@index');
			Route::get('regions/{sort?}/{order?}','RegionsController@index');
			Route::get('layouts/{sort?}/{order?}','LayoutsController@index');
			Route::get('extensions/{type?}','ExtensionsController@index');
			Route::get('elements/{sort?}/{order?}','ElementsController@index');
			Route::get('reviews/{sort?}/{order?}','ReviewsController@index');
			Route::get('get_element/{code}/{id?}','ElementsController@getElement');
			Route::get('orders/{sort?}/{order?}','OrdersController@index');
			Route::get('customer_groups/{sort?}/{order?}','CustomerGroupsController@index');
			Route::get('customers/{sort?}/{order?}','CustomersController@index');
			Route::get('currencies/{sort?}/{order?}', 'CurrenciesController@index');
			Route::get('statuses/{sort?}/{order?}', 'StatusController@index');
			Route::get('returns/{sort?}/{order?}', 'ReturnsController@index');
			Route::get('manufacturers/{sort?}/{order?}', 'ManufacturersController@index');
			Route::get('options/{sort?}/{order?}', 'OptionsController@index');
			Route::get('coupons/{sort?}/{order?}', 'CouponController@index');
		});
		
		Route::group(['middleware' => ['role:look|edit|create|delete|content_edit']], function() {
			Route::get('attribute_autocomplete', 'AttributesController@attribute_autocomplete');
			Route::get('category_autocomplete', 'CategoriesController@category_autocomplete');
			Route::get('product_autocomplete', 'ProductsController@product_autocomplete');
			Route::get('filter_autocomplete', 'FiltersController@filter_autocomplete');
			Route::get('page_category_autocomplete', 'PageCategoryController@category_autocomplete');
			Route::get('customer_autocomplete', 'CustomersController@customer_autocomplete');
			Route::get('manufacturer_autocomplete', 'ManufacturersController@manufacturer_autocomplete');
		});
		
		Route::group(['middleware' => ['role:edit|create|content_edit']], function() {
			Route::group(['prefix' => 'filemanager'], function () {
				\UniSharp\LaravelFilemanager\Lfm::routes();
			});
			
			Route::get('product_add', 'ProductsController@add');
			Route::post('product_copy', 'ProductsController@copy');
			Route::get('product/{id}', 'ProductsController@edit');
			Route::post('product_save', 'ProductsController@save');
			Route::post('product_add_image', 'ProductsController@addImage');
			
			Route::get('attribute_add', 'AttributesController@add');
			Route::get('attribute/{id}', 'AttributesController@edit');
			Route::post('attribute_save', 'AttributesController@save');
			
			Route::get('attribute_group_add', 'AttributesController@add_group');
			Route::get('attribute_group/{id}', 'AttributesController@edit_group');
			Route::post('attribute_group_save', 'AttributesController@save_group');
			
			Route::get('category_add', 'CategoriesController@add');
			Route::get('category/{id}', 'CategoriesController@edit');
			Route::post('category_save', 'CategoriesController@save');
			
			Route::get('filter_add', 'FiltersController@add');
			Route::get('filter/{id}', 'FiltersController@edit');
			Route::post('filter_save', 'FiltersController@save');
			Route::post('filters_add', 'FiltersController@filters_add');
			Route::get('filter_copy', 'FiltersController@copy');
			
			Route::get('page_category_add', 'PageCategoryController@add');
			Route::get('page_category/{id}', 'PageCategoryController@edit');
			Route::post('page_category_save', 'PageCategoryController@save');
			
			Route::get('page_add', 'PagesController@add');
			Route::get('page/{id}', 'PagesController@edit');
			Route::post('page_save', 'PagesController@save');
			
			Route::get('layout_add', 'LayoutsController@add');
			Route::get('layout/{id}', 'LayoutsController@edit');
			Route::post('layout_save', 'LayoutsController@save');
			
			Route::get('extension/{type?}/{code}/add', 'ExtensionsController@add');
			Route::get('extension/copy/{type?}/{id?}', 'ExtensionsController@copy');
			Route::get('extension/{type?}/{code}/edit/{id?}', 'ExtensionsController@edit');
			Route::get('extension/{type?}/{code}/save/{id?}', 'ExtensionsController@save');
			Route::post('extension/{type?}/{code}/save/{id?}', 'ExtensionsController@save');
			Route::get('extension/{code}/ajax', 'ExtensionsController@ajax');
			Route::post('extension/{code}/ajax', 'ExtensionsController@ajax');
			
			Route::get('element_add/{x?}', 'ElementsController@add');
			Route::get('element_edit/{id}/{x}', 'ElementsController@edit');
			Route::get('element/{id}', 'ElementsController@edit');
			Route::post('element_save', 'ElementsController@save');
			
			Route::get('region_add', 'RegionsController@add');
			Route::get('region/{id}', 'RegionsController@edit');
			Route::post('region_save', 'RegionsController@save');
			
			Route::get('review_add', 'ReviewsController@add');
			Route::get('review/{id}', 'ReviewsController@edit');
			Route::post('review_save', 'ReviewsController@save');
			
			Route::get('order_add', 'OrdersController@add');
			Route::get('order/{id}', 'OrdersController@edit');
			Route::post('order_save', 'OrdersController@save');
			
			Route::get('customer_group_add', 'CustomerGroupsController@add');
			Route::get('customer_group/{id}', 'CustomerGroupsController@edit');
			Route::post('customer_group_save', 'CustomerGroupsController@save');
			
			Route::get('customer_add', 'CustomersController@add');
			Route::get('customer/{id}', 'CustomersController@edit');
			Route::post('customer_save', 'CustomersController@save');
			
			Route::get('status_add', 'StatusController@add');
			Route::get('status_edit/{id}', 'StatusController@edit');
			Route::post('status_save', 'StatusController@save');
			
			Route::get('return_add', 'ReturnsController@add');
			Route::get('return/{id}', 'ReturnsController@edit');
			Route::post('return_save', 'ReturnsController@save');
			
			Route::get('manufacturer_add', 'ManufacturersController@add');
			Route::get('manufacturer/{id}', 'ManufacturersController@edit');
			Route::post('manufacturer_save', 'ManufacturersController@save');
			
			Route::get('option_add', 'OptionsController@add');
			Route::get('option/{id}', 'OptionsController@edit');
			Route::post('option_save', 'OptionsController@save');
			
			Route::get('coupon_add', 'CouponController@add');
			Route::get('coupon/{id}', 'CouponController@edit');
			Route::post('coupon_save', 'CouponController@save');
			
			Route::get('emails','EmailController@index');
		});
		
		Route::group(['middleware' => ['role:edit|create']], function() {
			Route::post('settings_add_image', 'SettingsController@addImage');
			Route::post('settings_save', 'SettingsController@save');
			
			Route::post('add_image', 'DashboardController@addImage');
			Route::get('customer_login/{id}', 'CustomersController@customer_login');
			
			Route::get('language_add', 'LanguagesController@add');
			Route::get('language/{id}', 'LanguagesController@edit');
			Route::post('language_save', 'LanguagesController@save');
			Route::post('language_add_image', 'LanguagesController@addImage');
			Route::post('page_add_image', 'PagesController@addImage');
			
			Route::get('currency/{id}', 'CurrenciesController@edit');
			Route::post('currency_save', 'CurrenciesController@save');
			
			Route::get('export_import', 'PriceController@index');
			Route::post('export', 'PriceController@export');
			Route::post('import', 'PriceController@import');
		});
		
		Route::group(['middleware' => ['role:create']], function() {
			Route::get('language_add', 'LanguagesController@add');
			Route::get('user_add', 'UsersController@add');
			Route::get('role_add', 'RoleController@add');
			Route::get('currency_add', 'CurrenciesController@add');
			
			Route::get('users/{sort?}/{order?}','UsersController@index');
			Route::get('roles/{sort?}/{order?}', 'RoleController@index');
			
			Route::post('user_delete', 'UsersController@delete');
			Route::post('role_delete', 'RoleController@delete');
			
			Route::get('user/{id}', 'UsersController@edit');
			Route::post('user_save', 'UsersController@save');
			
			Route::get('role/{id}', 'RoleController@edit');
			Route::post('role_save', 'RoleController@save');
			
			Route::get('backup', 'ApiController@backup');
			Route::get('cache_clear', 'ApiController@cache_clear');
		});
		
		Route::group(['middleware' => ['role:delete']], function() {
			Route::post('language_delete', 'LanguagesController@delete');
			Route::post('product_delete', 'ProductsController@delete');
			Route::post('attribute_delete', 'AttributesController@delete');
			Route::post('attribute_group_delete', 'AttributesController@delete_group');
			Route::post('category_delete', 'CategoriesController@delete');
			Route::post('filter_delete', 'FiltersController@delete');
			Route::post('page_delete', 'PagesController@delete');
			Route::post('page_category_delete', 'PageCategoryController@delete');
			Route::post('layout_delete', 'LayoutsController@delete');
			Route::get('extension/{type?}/{code}/delete/{id}', 'ExtensionsController@delete');
			Route::get('extension/{type?}/{code}/delete', 'ExtensionsController@delete');
			Route::post('elements_delete', 'ElementsController@delete');
			Route::post('region_delete', 'RegionsController@delete');
			Route::post('review_delete', 'ReviewsController@delete');
			Route::post('order_delete', 'OrdersController@delete');
			Route::post('customer_group_delete', 'CustomerGroupsController@delete');
			Route::post('customer_delete', 'CustomersController@delete');
			Route::post('currency_delete', 'CurrenciesController@delete');
			Route::post('status_delete', 'StatusController@delete');
			Route::post('return_delete', 'ReturnsController@delete');
			Route::post('manufacturer_delete', 'ManufacturersController@delete');
			Route::post('option_delete', 'OptionsController@delete');
			Route::post('coupon_delete', 'CouponController@delete');
		});
	});
	
	Route::post('catalog/filter', 'CategoriesController@filter')->name('catalog_filters');
	Route::post('search/filter', 'SearchController@filter')->name('search_filters');
	Route::post('new/filter', 'NewController@filter')->name('new_filters');
	Route::post('last/filter', 'LastController@filter')->name('last_filters');
	Route::post('bestseller/filter', 'BestsellerController@filter')->name('bestseller_filters');
	Route::post('cart_add', 'CartController@add')->name('cart_add');
	Route::post('oneclick', 'CartController@oneclick')->name('oneclick');
	Route::post('cart_delete', 'CartController@remove')->name('cart_delete');
	Route::post('total_set', 'CheckoutController@total_set')->name('total_set');
	Route::post('checkout_save', 'CheckoutController@save')->name('checkout_save');
	Route::post('getWishlist', 'WishlistController@getWishlistIds')->name('get_wishlist');
	Route::post('wishlist_add', 'WishlistController@add')->name('wishlist_add');
	Route::post('wishlist_delete', 'WishlistController@remove')->name('wishlist_delete');
	Route::post('cart_update', 'CartController@edit')->name('cart_update');
	Route::post('api_cart_add', 'ApiController@add')->name('api_cart_add');
	Route::post('api_shipping', 'ApiController@shipping')->name('api_shipping');
	Route::post('api_payment', 'ApiController@payment')->name('api_payment');
	Route::post('checkout_shipping', 'CheckoutController@shipping')->name('checkout_shipping');
	Route::post('checkout_payment', 'CheckoutController@payment')->name('checkout_payment');
	Route::post('api_methods', 'ApiController@update_methods')->name('api_methods');
	Route::post('api_coupon', 'ApiController@coupon')->name('api_coupon');
	Route::post('api_reward', 'ApiController@reward')->name('api_reward');
	Route::post('api_currency', 'ApiController@currency')->name('api_currency');
	Route::post('email_send', 'EmailController@send')->name('email_send');
	Route::post('post_search', 'SearchController@search')->name('post_search');
	Route::post('form_add_image', 'DashboardController@addImageSite')->name('form_add_image');
	Route::post('login', 'AuthController@login')->name('login');
	Route::post('register', 'AuthController@register')->name('register');
	
	Route::group(['middleware' => 'account_redirect'], function() {
		Route::post('account_save', 'Account\AccountController@save')->name('account_save');
		Route::post('newsletter_save', 'Account\NewsletterController@save')->name('newsletter_save');
		Route::post('review_delete', 'Account\ReviewsController@delete')->name('review_delete');
		Route::post('review_save', 'Account\ReviewsController@save')->name('review_save');
		Route::post('review_write', 'Account\ReviewsController@write')->name('review_write');
		Route::post('get_review/{sort?}/{page?}', 'Account\ReviewsController@getReviews')->name('get_reviews')->where('sort', '[\w\d\-]+(.*)')->where('page', '[0-9]');
		Route::post('newsletter_send', 'Account\NewsletterController@send_email')->name('newsletter_send');
		Route::get('yandex_login', 'Account\AccountController@yandex')->name('yandex_login');
	});
	
	Route::post('forgot', 'AuthController@forgot')->name('forgot');
	
	Route::post('send_code', 'AuthController@send_code')->name('send_code');
	
	foreach ($all_langs as $prefix) {
		if ($prefix == $language_default) $prefix = '';
		
		Route::group(['prefix' => $prefix, 'where' => [$prefix => implode('|', config('app.all_langs'))]], function() use ($prefix, $language_default, $regions, $routes) {
			if ($prefix == '') $prefix = $language_default;
			
			Route::group(['middleware' => 'seo_url'], function () use ($prefix, $regions, $routes) {
				Route::get('', 'HomeController@index')->name($prefix . '_home');
				Route::get('contacts', 'ContactsController@index')->name($prefix . '_contacts');
				Route::post('form-action', 'FormActionController@form_action')->name($prefix . '_form_action');
				Route::post('forms-action', 'Extensions\Module\FormsController@forms_action')->name($prefix . '_forms_action');
				Route::get('sitemap', ['uses' => 'SitemapController@index', 'as' => $prefix . '_sitemap']);
				Route::get('sitemap.xml', ['uses' => 'SitemapController@xml', 'as' => $prefix . '_sitemap_xml']);
				Route::get('cart', ['uses' => 'CartController@index', 'as' => $prefix . '_cart']);
				Route::get('checkout', ['uses' => 'CheckoutController@index', 'as' => $prefix . '_checkout']);
				Route::get('checkout_success', ['uses' => 'CheckoutController@success', 'as' => $prefix . '_checkout_success']);
				Route::get('search/{params?}/page/{page}', 'SearchController@index')->name($prefix . '_search_page')->where('params', '[\w\d\-]+(.*)')->where('page', '[0-9]');
				Route::get('search/{params?}', 'SearchController@index')->name($prefix . '_search')->where('params', '[\w\d\-]+(.*)');
				Route::get('new/{params?}/page/{page}', 'NewController@index')->name($prefix . '_new')->where('params', '[\w\d\-]+(.*)')->where('page', '[0-9]');
				Route::get('new/{params?}', 'NewController@index')->name($prefix . '_new')->where('params', '[\w\d\-]+(.*)');
				Route::get('last_viewed/{params?}/page/{page}', 'LastController@index')->name($prefix . '_last_viewed')->where('params', '[\w\d\-]+(.*)')->where('page', '[0-9]');
				Route::get('last_viewed/{params?}', 'LastController@index')->name($prefix . '_last_viewed')->where('params', '[\w\d\-]+(.*)');
				Route::get('bestseller/{params?}/page/{page}', 'BestsellerController@index')->name($prefix . '_bestseller')->where('params', '[\w\d\-]+(.*)')->where('page', '[0-9]');
				Route::get('bestseller/{params?}', 'BestsellerController@index')->name($prefix . '_bestseller')->where('params', '[\w\d\-]+(.*)');
				Route::get('wishlist', 'WishlistController@index')->name($prefix . '_wishlist');
				
				Route::group(['middleware' => 'account_redirect'], function() use($prefix) {
					Route::get('account', 'Account\AccountController@index')->name($prefix . '_account');
					Route::get('account_success', 'Account\AccountController@success')->name($prefix . '_account_success');
					Route::get('account_order', 'Account\OrderController@index')->name($prefix . '_account_order');
					Route::get('account_order/page/{page?}', 'Account\OrderController@index')->name($prefix . '_account_order_page')->where('page', '[0-9]');
					Route::get('account_order/{id?}', 'Account\OrderController@info')->name($prefix . '_account_order_info');
					Route::get('account_reviews', 'Account\ReviewsController@index')->name($prefix . '_account_reviews');
					Route::get('account_review/{id}', 'Account\ReviewsController@edit')->name($prefix . '_account_review_edit');
					Route::get('account_newsletter', 'Account\NewsletterController@index')->name($prefix . '_account_newsletter');
				});
				
				Route::get('logout', 'AuthController@logout')->name($prefix . '_logout');
				Route::post('account_forgot', 'AuthController@forgot')->name($prefix . '_account_post_forgot');
				Route::get('account_forgot/{token}', 'AuthController@forgot_get')->name($prefix . '_account_forgot');
				
				Route::get('/{slug}', function($query) use($routes, $prefix) {
					$controller = new \App\Http\Controllers\RouteController($routes);
					return $controller->index($query, $prefix);
				})->name($prefix . '_slug')->where('slug', '[\w\d\-]+(.*)');
				
				if (!empty($regions)) {
					foreach ($regions as $region) {
						Route::get($region['slug'] . '/', 'HomeController@index')->name($prefix . '_' . $region['slug'] . '_home');
						Route::get($region['slug'] . '/contacts', 'ContactsController@index')->name($prefix . '_' . $region['slug'] . '_contacts');
						Route::post($region['slug'] . '/form-action', 'FormActionController@form_action')->name($prefix . '_' . $region['slug'] . '_form_action');
						Route::post($region['slug'] . '/forms-action', 'Extensions\Module\FormsController@forms_action')->name($prefix . '_' . $region['slug'] . '_forms_action');
						Route::get($region['slug'] . '/sitemap', ['uses' => 'SitemapController@index', 'as' => $prefix . '_' . $region['slug'] . '_sitemap']);
						Route::get($region['slug'] . '/cart', ['uses' => 'CartController@index', 'as' => $prefix . '_' . $region['slug'] . '_cart']);
						Route::get($region['slug'] . '/checkout', ['uses' => 'CheckoutController@index', 'as' => $prefix . '_' . $region['slug'] . '_checkout']);
						Route::get($region['slug'] . '/checkout_success', ['uses' => 'CheckoutController@success', 'as' => $prefix . '_' . $region['slug'] . '_checkout_success']);
						Route::get($region['slug'] . '/search/{params?}', ['uses' => 'SearchController@index', 'as' => $prefix . '_' . $region['slug'] . '_search'])->where('params', '[\w\d\-]+(.*)');
						Route::get($region['slug'] . '/search/{params?}/page/{page}', 'SearchController@index')->name($prefix . '_' . $region['slug'] . '_search_page')->where('params', '[\w\d\-]+(.*)')->where('page', '[0-9]');
						Route::get($region['slug'] . '/new/{params?}/page/{page}', 'NewController@index')->name($prefix . '_' . $region['slug'] . '_new')->where('params', '[\w\d\-]+(.*)')->where('page', '[0-9]');
						Route::get($region['slug'] . '/new/{params?}', 'NewController@index')->name($prefix . '_' . $region['slug'] . '_new')->where('params', '[\w\d\-]+(.*)');
						Route::get($region['slug'] . '/last_viewed/{params?}/page/{page}', 'LastController@index')->name($prefix . '_' . $region['slug'] . '_last_viewed')->where('params', '[\w\d\-]+(.*)')->where('page', '[0-9]');
						Route::get($region['slug'] . '/last_viewed/{params?}', 'LastController@index')->name($prefix . '_' . $region['slug'] . '_last_viewed')->where('params', '[\w\d\-]+(.*)');
						Route::get($region['slug'] . '/bestseller/{params?}/page/{page}', 'BestsellerController@index')->name($prefix . '_' . $region['slug'] . '_bestseller')->where('params', '[\w\d\-]+(.*)')->where('page', '[0-9]');
						Route::get($region['slug'] . '/bestseller/{params?}', 'BestsellerController@index')->name($prefix . '_' . $region['slug'] . '_bestseller')->where('params', '[\w\d\-]+(.*)');
						Route::get($region['slug'] . '/wishlist', ['uses' => 'WishlistController@index', 'as' => $prefix . '_' . $region['slug'] . '_wishlist']);
						
						Route::group(['middleware' => 'account_redirect'], function() use($prefix, $region) {
							Route::get($region['slug'] . '/account', ['uses' => 'Account\AccountController@index', 'as' => $prefix . '_' . $region['slug'] . '_account']);
							Route::get($region['slug'] . '/account_success', ['uses' => 'Account\AccountController@success', 'as' => $prefix . '_' . $region['slug'] . '_account_success']);
							Route::get($region['slug'] . '/account_order', ['uses' => 'Account\OrderController@index', 'as' => $prefix . '_' . $region['slug'] . '_account_order']);
							Route::get($region['slug'] . '/account_order/page/{page?}', 'Account\OrderController@index')->name($prefix . '_account_order_page')->where('page', '[0-9]');
							Route::get($region['slug'] . '/account_order/{id?}', ['uses' => 'Account\OrderController@info', 'as' => $prefix . '_' . $region['slug'] . '_account_order_info']);
							Route::get($region['slug'] . '/account_reviews', ['uses' => 'Account\ReviewsController@success', 'as' => $prefix . '_' . $region['slug'] . '_account_reviews']);
							Route::get($region['slug'] . '/account_review/{id}', 'Account\ReviewsController@edit')->name( $prefix . '_' . $region['slug'] . '_account_review_edit');
							Route::get($region['slug'] . '/account_newsletter', ['uses' => 'Account\NewsletterController@success', 'as' => $prefix . '_' . $region['slug'] . '_account_newsletter']);
						});
						
						Route::get($region['slug'] . '/logout', ['uses' => 'AuthController@logout', 'as' => $prefix . '_' . $region['slug'] . '_logout']);
						Route::post($region['slug'] . '/account_forgot', ['uses' => 'AuthController@forgot', 'as' => $prefix . '_' . $region['slug'] . '_account_post_forgot']);
						Route::post($region['slug'] . '/account_forgot/{token}', ['uses' => 'AuthController@forgot_get', 'as' => $prefix . '_' . $region['slug'] . '_account_forgot']);
						
						Route::get($region['slug'] . '/{slug}', function($query) use($routes, $prefix) {
							$controller = new \App\Http\Controllers\RouteController($routes);
							return $controller->index($query, $prefix);
						})->name($prefix . '_' . $region['slug'] . '_slug')->where('slug', '[\w\d\-]+(.*)');
					}
				}
			});
		});
	}
	
	Route::get('/_debugbar/assets/stylesheets', [
		'as' => 'debugbar.assets.css',
		'uses' => '\Barryvdh\Debugbar\Controllers\AssetController@css'
	]);
	
	Route::get('/_debugbar/assets/javascript', [
		'as' => 'debugbar.assets.js',
		'uses' => '\Barryvdh\Debugbar\Controllers\AssetController@js'
	]);
	
	Route::get('/_debugbar/open', [
		'as' => 'debugbar.assets.open',
		'uses' => '\Barryvdh\Debugbar\Controllers\OpenController@handler'
	]);