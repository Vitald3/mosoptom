<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	
	class Products extends Model
	{
		protected $table = 'products';
		
		protected $fillable = [
			'slug'
		];
		
		public function getTableColumns() {
			return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
		}
		
		public function getSlug() {
			return app(\App\Helpers\PathRouteService::class)->getRoute('product_' . session('lang') . '_id=' . $this->id);
		}
		
		public function meta()
		{
			return $this->hasMany('App\Models\ProductDescription', 'product_id');
		}
		
		public function metaLang()
		{
			return $this->hasOne('App\Models\ProductDescription', 'product_id')->where('lang', config('app.locale'));
		}
		
		public function metaLang2()
		{
			return $this->belongsTo('App\Models\ProductDescription', 'product_id')->where('lang', config('app.locale'));
		}
		
		public function product_related()
		{
			return $this->hasMany(ProductRelated::class, 'product_id', 'id');
		}
		
		public function product_category()
		{
			return $this->hasMany('App\Models\ProductCategory', 'product_id');
		}
		
		public function category_id()
		{
			return $this->hasMany('App\Models\ProductCategory', 'product_id');
		}
		
		public function product_attribute()
		{
			return $this->hasMany('App\Models\ProductAttribute', 'product_id')->where('product_attribute.lang', config('app.locale'));
		}
		
		public function reviews()
		{
			return $this->hasMany(Reviews::class, 'product_id');
		}
		
		public function attributes()
		{
			return $this->belongsToMany(AttributeDescription::class, 'product_attribute', 'product_id', 'attribute_id', '', 'attribute_id');
		}
		
		public function filters()
		{
			return $this->belongsToMany(FilterDescription::class, 'product_filter', 'product_id', 'filter_id', '', 'filter_id');
		}
		
		public function filters_search()
		{
			return $this->belongsToMany(Filters::class, 'product_filter', 'product_id', 'filter_id')->where('product_filter.filter_id', '!=', '');
		}
		
		public function product_filter()
		{
			return $this->hasMany(FilterProduct::class, 'product_id')->where('product_filter.filter_id', '!=', '');
		}
		
		public function product_image()
		{
			return $this->hasMany('App\Models\ProductImage', 'product_id')->orderBy('created_at');
		}
		
		public function product_reward()
		{
			return $this->hasMany(ProductReward::class, 'product_id');
		}
		
		public function images()
		{
			return $this->hasMany('App\Models\ProductImage', 'product_id')->orderBy('created_at');
		}
		
		public function product_special()
		{
			return $this->hasMany(ProductSpecial::class, 'product_id')
				->distinct()
				->select('product_id', 'price', 'customer_group_id', 'date_start', 'date_end')
				->whereRaw("customer_group_id = '" . (int)session('customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < '" . now()->format('Y-m-d') . "') AND (date_end = '0000-00-00' OR date_end > '" . now()->format('Y-m-d') . "'))")
				->orderBy('created_at');
		}
		
		public function product_special_one()
		{
			return $this->hasOne(ProductSpecial::class, 'product_id')->select('product_id', 'price', 'customer_group_id', 'date_start', 'date_end')
				->whereRaw("customer_group_id = '" . (int)session('customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < '" . now()->format('Y-m-d') . "') AND (date_end = '0000-00-00' OR date_end > '" . now()->format('Y-m-d') . "'))")
				->orderBy('created_at');
		}
		
		public function product_discount()
		{
			return $this->hasMany(ProductDiscount::class, 'product_id')
				->distinct()
				->select('product_id', 'price', 'quantity', 'customer_group_id', 'date_start', 'date_end')
				->whereRaw("customer_group_id = '" . (int)session('customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < '" . now()->format('Y-m-d') . "') AND (date_end = '0000-00-00' OR date_end > '" . now()->format('Y-m-d') . "'))")
				->orderBy('quantity');
		}
		
		public function product_discount_cart()
		{
			return $this->hasMany(ProductDiscount::class, 'product_id')
				->distinct()
				->select('product_id', 'price', 'quantity', 'customer_group_id', 'date_start', 'date_end')
				->whereRaw("customer_group_id = '" . (int)session('customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < '" . now()->format('Y-m-d') . "') AND (date_end = '0000-00-00' OR date_end > '" . now()->format('Y-m-d') . "'))");
		}
		
		public function product_discount_one()
		{
			return $this->hasOne(ProductDiscount::class, 'product_id')->select('product_id', 'price', 'quantity', 'customer_group_id', 'date_start', 'date_end')
				->whereRaw("customer_group_id = '" . (int)session('customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < '" . now()->format('Y-m-d') . "') AND (date_end = '0000-00-00' OR date_end > '" . now()->format('Y-m-d') . "'))")
				->orderBy('quantity', 'desc');
		}
		
		public function categories()
		{
			return $this->belongsToMany(Categories::class, 'product_category', 'product_id', 'category_id');
		}
		
		public function category_name()
		{
			return $this->belongsToMany(Categories::class, 'product_category', 'product_id', 'category_id');
		}
		
		public function category()
		{
			return $this->hasOne(Categories::class, 'id', 'parent_id');
		}
		
		public function stock_status()
		{
			return $this->hasOne(Status::class, 'id', 'stock_status_id');
		}
		
		public function product_option()
		{
			return $this->hasMany(ProductOption::class, 'product_id')->select('product_option.product_id', 'product_option.option_id', 'product_option.id');
		}
		
		public function product_option_values()
		{
			return $this->hasMany(ProductOptionValues::class, 'id');
		}
		
		public function options()
		{
			return $this->belongsToMany(Options::class, ProductOption::class, 'product_id', 'option_id');
		}
	}
