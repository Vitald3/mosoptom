<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Coupon;
	use App\Models\CouponProduct;
	use App\Models\CouponCategory;
	use App\Models\CouponHistory;
	
	class CouponController extends Controller
	{
		public function __construct() {
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
		}
		
		public function index(Request $request){
			if ($request->sort) {
				$sort = $request->sort;
			} else {
				$sort = 'name';
			}
			
			if ($request->order) {
				$order = $request->order;
			} else {
				$order = 'asc';
			}
			
			$limit = session('settings.limit', 25);
			
			$sort_name = url('admin/coupons', ['sort' => 'name', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_code = url('admin/coupons', ['sort' => 'code', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_discount = url('admin/coupons', ['sort' => 'discount', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_start = url('admin/coupons', ['sort' => 'date_start', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_end = url('admin/coupons', ['sort' => 'date_end', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_status = url('admin/coupons', ['sort' => 'status', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			
			if (in_array($sort, ['name', 'code', 'discount', 'date_start', 'date_end', 'status'])) {
				$coupons = Coupon::select('id', 'status', 'name', 'code', 'discount', 'date_start', 'date_end')->orderBy($sort, $order)->paginate($limit);
			} else {
				$coupons = Coupon::select('id', 'status', 'name', 'code', 'discount', 'date_start', 'date_end')->orderBy('name')->paginate($limit);
			}
			
			$this->breadcrumbs->addCrumb('Купоны', url('admin/coupons') . $this->params);
			$breadcrumbs = $this->breadcrumbs->render();
			$params = $this->params;
			$params_array = $this->params_array;
			
			return view('pages.coupons', compact('params', 'params_array', 'sort_code', 'sort_discount', 'sort_start', 'sort_end', 'breadcrumbs', 'sort_name', 'sort_status', 'coupons', 'sort', 'order'));
		}
		
		public function add() {
			$this->breadcrumbs->addCrumb('Купоны', url('admin/coupons') . $this->params);
			$this->breadcrumbs->addCrumb('Создать', url('admin/coupon_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.coupon-edit', ['breadcrumbs' => $breadcrumbs, 'name' => old('name'), 'code' => old('code'), 'discount' => old('discount'), 'total' => old('total'), 'type' => old('type'), 'coupon_products' => (array)old('coupon_products'), 'coupon_categories' => (array)old('coupon_categories'), 'date_start' => old('date_start'), 'date_end' => old('date_end'), 'uses_total' => old('uses_total'), 'uses_customer' => old('uses_customer'), 'logged' => old('logged'), 'status' => old('status'), 'action' => asset('admin/coupon_save') . $this->params, 'id' => '']);
		}
		
		public function edit($id)
		{
			$data = Coupon::with([
				'history' => function($query) {
					$query->selectRaw("(select concat(firstname, ' ', lastname) as customer from customers c where c.id = coupon_history.customer_id) as customer, coupon_history.order_id, coupon_history.customer_id, coupon_history.amount, coupon_history.created_at");
				},
				'coupon_products' => function($query) {
					$query->join('product_description as pd', 'pd.product_id', '=', 'coupon_product.product_id')->select('coupon_product.coupon_id', 'coupon_product.product_id', 'pd.name');
				},
				'coupon_categories' => function($query) {
					$query->join('category_description as cd', 'cd.category_id', '=', 'coupon_category.category_id')->select('coupon_category.coupon_id', 'coupon_category.category_id', 'cd.name');
				}
			])->where('id', $id)->first()->toArray();
			
			if (!empty($data)) {
				extract($data);
				$action = asset('admin/coupon_save') . $this->params;
				
				$this->breadcrumbs->addCrumb('Купоны', url('admin/coupons') . $this->params);
				$this->breadcrumbs->addCrumb('Редактировать', url('admin/coupon/' . $id));
				$breadcrumbs = $this->breadcrumbs->render();
				
				return view('pages.coupon-edit', compact('breadcrumbs', 'history', 'name', 'code', 'discount', 'coupon_products', 'coupon_categories', 'total', 'status', 'type', 'id', 'action', 'date_start', 'date_end', 'uses_total', 'uses_customer', 'logged'));
			} else {
				return redirect('admin/coupons' . $this->params)->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete(Request $request) {
			if ($request->selected) {
				foreach ($request->selected as $s) {
					Coupon::where('id', $s)->delete();
					CouponCategory::where('coupon_id', $s)->delete();
					CouponProduct::where('coupon_id', $s)->delete();
					CouponHistory::where('coupon_id', $s)->delete();
				}
				
				$message = 'Операция успешна';
				$type = 'success';
			} else {
				$message = 'Выделите пункты для удаления';
				$type = 'error';
			}
			
			return redirect('admin/coupons' . $this->params)->with($type, $message);
		}
		
		public function save(Request $request) {
			$this->validate($request, [
				'name' => 'required|max:128',
				'code' => 'required|max:20',
				'discount' => 'required'
			]);
			
			if (!is_null($request->id)) {
				$coupon['name'] = $request->name;
				$coupon['code'] = $request->code;
				$coupon['type'] = $request->type ? $request->type : 'P';
				$coupon['total'] = $request->total ? $request->total : 0;
				$coupon['discount'] = $request->discount;
				$coupon['logged'] = $request->logged ? $request->logged : 0;
				$coupon['date_start'] = $request->date_start ? $request->date_start : '';
				$coupon['date_end'] = $request->date_end ? $request->date_end : '';
				$coupon['uses_total'] = $request->uses_total ? $request->uses_total : 0;
				$coupon['uses_customer'] = $request->uses_customer ? $request->uses_customer : 0;
				$coupon['status'] = $request->status ? $request->status : 0;
				
				Coupon::where('id', $request->id)->update($coupon);
				
				CouponProduct::where('coupon_id', $request->id)->delete();
				
				if (!is_null($request->coupon_product)) {
					foreach ($request->coupon_product as $product) {
						$cp = new CouponProduct;
						$cp->coupon_id = $request->id;
						$cp->product_id = $product;
						
						$cp->save();
					}
				}
				
				CouponCategory::where('coupon_id', $request->id)->delete();
				
				if (!is_null($request->coupon_category)) {
					foreach ($request->coupon_category as $category) {
						$cc = new CouponCategory;
						$cc->coupon_id = $request->id;
						$cc->category_id = $category;
						
						$cc->save();
					}
				}
			} else {
				$coupon = new Coupon;
				$coupon->name = $request->name;
				$coupon->code = $request->code;
				$coupon->type = $request->type ? $request->type : 'P';
				$coupon->total = $request->total ? $request->total : 0;
				$coupon->discount = $request->discount;
				$coupon->logged = $request->logged ? $request->logged : 0;
				$coupon->date_start = $request->date_start ? $request->date_start : '';
				$coupon->date_end = $request->date_end ? $request->date_end : '';
				$coupon->uses_total = $request->uses_total ? $request->uses_total : 0;
				$coupon->uses_customer = $request->uses_customer ? $request->uses_customer : 0;
				$coupon->status = $request->status ? $request->status : 0;
				
				$coupon->save();
				
				if (!is_null($request->coupon_product)) {
					foreach ($request->coupon_product as $product) {
						$cp = new CouponProduct;
						$cp->coupon_id = $coupon->id;
						$cp->product_id = $product;
						
						$cp->save();
					}
				}
				
				if (!is_null($request->coupon_category)) {
					foreach ($request->coupon_category as $category) {
						$cc = new CouponCategory;
						$cc->coupon_id = $coupon->id;
						$cc->category_id = $category;
						
						$cc->save();
					}
				}
			}
			
			return redirect('admin/coupons' . $this->params)->with('success', 'Операция успешна');
		}
	}