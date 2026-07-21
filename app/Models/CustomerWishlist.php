<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Facades\Cookie;
	
	class CustomerWishlist extends Model
	{
		protected $table = 'customer_wishlist';
		
		public static function remove($request) {
			if ($request->session()->has('customer_id')) {
				self::where('customer_id', $request->session()->get('customer_id'))->where('product_id', $request->product_id)->delete();
			}
			
			setcookie('wishlist[' . $request->product_id . ']', null, -1, '/');
		}
		
		public static function add($request) {
			if ($request->session()->has('customer_id') && $request->product_id) {
				self::where('customer_id', $request->session()->get('customer_id'))->where('product_id', $request->product_id)->delete();
				
				self::insert(['customer_id' => $request->session()->get('customer_id'), 'product_id' => $request->product_id, 'updated_at' => now(), 'created_at' => now()]);
			} else {
				setcookie('wishlist[' . $request->product_id . ']', $request->product_id, time()+3600*24, '/');
			}
		}
		
		public static function getWishlist() {
			$results = [];
			
			if (session('customer_id')) {
				$wishlist = self::select('product_id')->where('customer_id', session('customer_id'))->orderBy('created_at')->pluck('product_id');
				
				foreach ($wishlist as $product_id) {
					$results[] = ['product_id' => $product_id];
				}
			}
	
			if (isset($_COOKIE['wishlist'])) {
				foreach ((array)$_COOKIE['wishlist'] as $wishlist) {
					$results[] = ['product_id' => $wishlist];
				}
			}
			
			return $results;
		}
		
		public static function getTotalWishlist() {
			$count = 0;
			
			if (session('customer_id')) {
				$count += self::where('customer_id', session('customer_id'))->orderBy('created_at')->count('product_id');
			}
			
			if (isset($_COOKIE['wishlist'])) {
				$count += count((array)$_COOKIE['wishlist']);
			}
			
			return $count ? $count : '';
		}
	}