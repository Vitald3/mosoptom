<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Status;
	use App\Models\Customers;
	use App\Models\Returns;
	use App\Models\ReturnHistory;
	use App\Models\Currencies;
	use App\Models\CustomerGroups;
	use Illuminate\Support\Facades\Hash;
	
	class ReturnsController extends Controller
	{
		private $breadcrumbs;
		
		public function __construct() {
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			
			$classes = array('breadcrumb', 'breadcrumb-item');
			$this->breadcrumbs->addCssClasses($classes);
			$this->breadcrumbs->setDivider('');
			
			$this->breadcrumbs->addCrumb(__('locale.home'), url('admin'));
			
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
			
			if (!is_null($request->status)) {
				$where[] = ['status', '=', $request->status];
				$status = $request->status;
			} else {
				$status = '';
			}
			
			if (!is_null($request->order_id)) {
				$where[] = ['order_id', '=', $request->order_id];
				$order_id = $request->order_id;
			} else {
				$order_id = '';
			}
			
			if (!is_null($request->model)) {
				$where[] = ['model', '=', $request->order_id];
				$model = $request->model;
			} else {
				$model = '';
			}
			
			if (!is_null($request->product)) {
				$where[] = ['product', '=', $request->product];
				$product = $request->product;
			} else {
				$product = '';
			}
			
			if (!is_null($request->customer)) {
				$where[] = ['customer', '=', $request->customer];
				$customer = $request->customer;
			} else {
				$customer = '';
			}
			
			if (!is_null($request->created_at)) {
				$where[] = ['created_at', '=', 'date(' . $request->created_at . ')'];
				$created_at = $request->created_at;
			} else {
				$created_at = '';
			}
			
			if ($request->sort) {
				$sort = $request->sort;
			} else {
				$sort = 'returns.updated_at';
			}
			
			if ($request->order) {
				$order = $request->order;
			} else {
				$order = 'desc';
			}
			
			$limit = session('settings.limit', 25);
			
			$sort_id = url('admin/returns', ['sort' => 'id', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_order_id = url('admin/returns', ['sort' => 'order_id', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_customer = url('admin/returns', ['sort' => 'customer', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_product = url('admin/returns', ['sort' => 'product', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_created_at = url('admin/returns', ['sort' => 'returns.updated_at', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_model = url('admin/returns', ['sort' => 'model', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_status = url('admin/returns', ['sort' => 'st.name', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			
			if (in_array($sort, ['id', 'order_id', 'customer', 'product', 'updated_at', 'model'])) {
				$returns = Returns::
				join('status_description as st', 'st.status_id', '=', 'returns.status')
					->selectRaw("returns.id, st.name as status, customer_id, order_id, product, concat(firstname, ' ', lastname) as customer, returns.updated_at, model")
					->where($where)
					->orderBy($sort, $order)
					->paginate($limit);
			} else {
				$returns = Returns::
				join('status_description as st', 'st.status_id', '=', 'returns.status')
					->selectRaw("returns.id, st.name as status, customer_id, order_id, product, concat(firstname, ' ', lastname) as customer, returns.updated_at, model")
					->where($where)
					->orderBy('returns.updated_at')
					->paginate($limit);
			}
			
			$this->breadcrumbs->addCrumb('Пользователи', url('admin/returns'));
			$breadcrumbs = $this->breadcrumbs->render();
			$statuses = Status::join('status_description as st', 'st.status_id', '=', 'status.id')->select('status.id', 'st.name')->where('st.lang', $this->lang)->where('status.type', 3)->get()->keyBy('id');
			$params = $this->params;
			$params_array = $this->params_array;
			
			return view('pages.returns', compact('params', 'params_array', 'sort_status', 'statuses', 'sort_id', 'created_at', 'sort_order_id', 'sort_customer', 'sort_product', 'sort_model', 'sort_created_at', 'customer', 'breadcrumbs', 'product', 'returns', 'order_id', 'model', 'status', 'sort', 'order'));
		}
		
		public function add() {
			$this->breadcrumbs->addCrumb('Возвраты', url('admin/returns') . $this->params);
			$this->breadcrumbs->addCrumb('Создать', url('admin/return_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			$statuses = Status::join('status_description as st', 'st.status_id', '=', 'status.id')->select('status.id', 'st.name')->where('st.lang', $this->lang)->where('status.type', 3)->get();
			
			return view('pages.return-edit', ['statuses' => $statuses, 'breadcrumbs' => $breadcrumbs, 'firstname' => old('firstname'), 'lastname' => old('lastname'), 'email' => old('email'), 'phone' => old('phone'), 'updated_at' => old('updated_at'), 'order_id' => old('order_id'), 'product_id' => old('product_id'), 'product' => old('product'), 'customer' => old('customer'), 'customer_id' => old('customer_id'), 'model' => old('model'), 'quantity' => old('quantity'), 'status' => old('status'), 'comment' => old('comment'), 'action' => asset('admin/return_save') . $this->params, 'id' => '']);
		}
		
		public function edit($id)
		{
			$data = Returns::with('getHistory')->where('id', $id)->first();
			
			if (!empty($data)) {
				$this->breadcrumbs->addCrumb('Возвраты', url('admin/returns') . $this->params);
				$this->breadcrumbs->addCrumb('Редактировать', url('admin/return/' . $id));
				$breadcrumbs = $this->breadcrumbs->render();
				$statuses = Status::join('status_description as st', 'st.status_id', '=', 'status.id')->select('status.id', 'st.name')->where('st.lang', $this->lang)->where('status.type', 3)->get()->keyBy('id');
				
				extract($data->toArray());
				$action = asset('admin/return_save') . $this->params;
				
				if (!empty($data->customer_id)) {
					$data = Customers::selectRaw("concat(firstname, ' ', lastname) as customer, id as customer_id, phone, email")->where('id', $data->customer_id)->first()->toArray();
				}
				
				if (empty($data)) {
					$data = ['firstname' => '', 'lastname' => '', 'email' => '', 'phone' => '', 'customer_id' => ''];
				}
				
				extract($data);
				
				return view('pages.return-edit', compact('get_history', 'statuses', 'customer', 'customer_id', 'order_id', 'firstname', 'lastname', 'email', 'breadcrumbs', 'firstname', 'phone', 'lastname', 'updated_at', 'product_id', 'product', 'model', 'status', 'quantity', 'comment', 'id', 'action'));
			} else {
				return redirect('admin/returns' . $this->params)->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete(Request $request) {
			if ($request->selected) {
				foreach ($request->selected as $s) {
					Returns::where('id', $s)->delete();
					ReturnHistory::where('return_id', $s)->delete();
				}
				
				$message = 'Операция успешна';
				$type = 'success';
			} else {
				$message = 'Выделите пункты для удаления';
				$type = 'error';
			}
			
			return redirect('admin/returns' . $this->params)->with($type, $message);
		}
		
		public function save(Request $request) {
			$this->validate($request, [
				'order_id' => 'required',
				'firstname' => 'required|max:300',
				'lastname' => 'required|max:300',
				'email' => 'required',
				'phone' => 'required',
				'product' => 'required',
				'model' => 'required',
				'status' => 'required'
			]);
			
			if (!is_null($request->id)) {
				$return['product'] = $request->product;
				$return['product_id'] = $request->product_id;
				$return['customer_id'] = $request->customer_id;
				$return['order_id'] = $request->order_id;
				$return['firstname'] = $request->firstname;
				$return['lastname'] = $request->lastname;
				$return['email'] = $request->email;
				$return['phone'] = $request->phone;
				$return['model'] = $request->model;
				$return['status'] = $request->status;
				$return['quantity'] = $request->quantity ? $request->quantity : 1;
				$return['comment'] = $request->comment ? $request->comment : '';
				$return['updated_at'] = $request->updated_at ? $request->updated_at : date('Y-m-d H:i:s');
				
				Returns::where('id', $request->id)->update($return);
				
				$rh['comment'] = $request->comment ? $request->comment : '';
				$rh['return_id'] = $request->id;
				$rh['status'] = $request->status;
				$rh['updated_at'] = date('Y-m-d H:i:s');
				$rh['created_at'] = date('Y-m-d H:i:s');
				
				ReturnHistory::where('id', $request->id)->insertorIgnore($rh);
			} else {
				$return = new Returns;
				$return->product = $request->product;
				$return->product_id = $request->product_id;
				$return->customer_id = $request->customer_id;
				$return->order_id = $request->order_id;
				$return->firstname = $request->firstname;
				$return->lastname = $request->lastname;
				$return->email = $request->email;
				$return->phone = $request->phone;
				$return->model = $request->model;
				$return->status = $request->status;
				$return->quantity = $request->quantity ? $request->quantity : 1;
				$return->comment = $request->comment ? $request->comment : '';
				$return->updated_at = $request->updated_at ? $request->updated_at : date('Y-m-d H:i:s');
				
				$return->save();
				
				$rh['comment'] = $request->comment ? $request->comment : '';
				$rh['return_id'] = $return->id;
				$rh['status'] = $request->status;
				$rh['updated_at'] = date('Y-m-d H:i:s');
				$rh['created_at'] = date('Y-m-d H:i:s');
				
				ReturnHistory::where('id', $request->id)->insertorIgnore($rh);
			}
			
			return redirect('admin/returns' . $this->params)->with('success', 'Операция успешна');
		}
	}