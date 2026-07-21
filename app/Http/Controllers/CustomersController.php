<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers;
use App\Models\CustomerAddress;
use App\Models\CustomerSocial;
use App\Models\Currencies;
use App\Models\CustomerGroups;
use App\Models\CustomerIp;
use App\Models\CustomerReward;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class CustomersController extends Controller
{
    private $currencies = [];
    private $settings = [];
    private $breadcrumbs;

    public function __construct() {
        $this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;

        $classes = array('breadcrumb', 'breadcrumb-item');
        $this->breadcrumbs->addCssClasses($classes);
        $this->breadcrumbs->setDivider('');

        $this->breadcrumbs->addCrumb(__('locale.home'), url('admin'));

        if (Cache::has('currencies')) {
            $this->currencies = Cache::get('currencies');
        }

        $this->settings = session('settings');
	
		$this->params_array = request()->query();
		$params = [];
	
		if (!empty($this->params_array)) {
			foreach ($this->params_array as $key => $param) {
				$params[] = $key . '=' . $param;
			}
		}
	
		$this->params = !empty($this->params) ? '?' . implode('&', $params) : '';
    }

    public function customer_autocomplete(Request $request) {
        $json = [];

        if ($request->term) {
            $where[] = ['customers.status', '=', 1];

            if ($request->id) {
                $where[] = ['customers.id', '!=', $request->id];
            }

            $json = Customers::selectRaw("customers.customer_group_id, concat(customers.firstname, ' ', customers.lastname) as customer, customers.firstname, customers.lastname, customers.email, customers.phone, customers.id")
                ->join('customer_groups as cg', 'cg.id', '=', 'customers.customer_group_id')
                ->where(function($query) use($request) {
                $query->where('customers.firstname', 'like', '%' . $request->term . '%')->Orwhere('customers.lastname', 'like', '%' . $request->term . '%');
            })->limit(5)->where($where)->get();
        }

        return response()->json($json);
    }

    public function index(Request $request){
        $where = [];

        if (!is_null($request->status)) {
            $where[] = ['customers.status', '=', $request->status];
            $status = $request->status;
        } else {
            $status = '';
        }

        if (!is_null($request->ip)) {
            $ip = $request->ip;
        } else {
            $ip = '';
        }

        if (!is_null($request->customer_group)) {
            $where[] = ['customers.customer_group_id', '=', $request->customer_group];
            $customer_group = $request->customer_group;
        } else {
            $customer_group = '';
        }

        if (!is_null($request->approved)) {
            $where[] = ['customers.approval', '=', $request->approved];
            $approved = $request->approved;
        } else {
            $approved = '';
        }

        if (!is_null($request->created_at)) {
            $where[] = ['customers.created_at', '=', 'date(' . $request->created_at . ')'];
            $created_at = $request->created_at;
        } else {
            $created_at = '';
        }

        if (!is_null($request->email)) {
            $where[] = ['customers.email', 'like', '%' . $request->email . '%'];
            $email = $request->email;
        } else {
            $email = '';
        }

        if (!is_null($request->name)) {
            $where[] = ['customers.firstname', 'like', '%' . $request->name . '%'];
            $name = $request->name;
        } else {
            $name = '';
        }

        if ($request->sort) {
            $sort = $request->sort;
        } else {
            $sort = 'cgd.name';
        }

        if ($request->order) {
            $order = $request->order;
        } else {
            $order = 'asc';
        }

        $limit = isset($this->settings['limit']) ? $this->settings['limit'] : 25;

        $customer_groups = CustomerGroups::join('customer_group_description as cgd', 'cgd.customer_group_id', '=', 'customer_groups.id')->select('customer_groups.id', 'cgd.name')->where('customer_groups.status', 1)->orderBy('cgd.name')->get();

        $sort_name = url('admin/customers', ['sort' => 'cgd.name', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
        $sort_email = url('admin/customers', ['sort' => 'customers.email', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
        $sort_group = url('admin/customers', ['sort' => 'customer_group', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
        $sort_ip = url('admin/customers', ['sort' => 'customers.ip', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
        $sort_created_at = url('admin/customers', ['sort' => 'customers.created_at', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
        $sort_status = url('admin/customers', ['sort' => 'customers.status', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;

        if (in_array($sort, ['cgd.name', 'customers.status', 'customers.email', 'customers.ip', 'customers.created_at'])) {
            $customers = Customers::join('customer_group_description as cgd', 'cgd.customer_group_id', '=', 'customers.customer_group_id')
                ->selectRaw("cgd.name, customers.id, customers.ip, customers.email, customers.customer_group_id, customers.status, concat(customers.firstname, ' ', customers.lastname) as name, customers.created_at")
                ->where($where)
                ->orderBy($sort, $order)
                ->paginate($limit);
        } else {
            $customers = Customers::join('customer_group_description as cgd', 'cgd.customer_group_id', '=', 'customers.customer_group_id')
                ->selectRaw("cgd.name, customers.id, customers.ip, customers.email, customers.customer_group_id, customers.status, concat(customers.firstname, ' ', customers.lastname) as name, customers.created_at")
                ->where($where)
                ->orderBy('name')
                ->paginate($limit);
        }

        $this->breadcrumbs->addCrumb('Клиенты', url('admin/customers') . $this->params);
        $breadcrumbs = $this->breadcrumbs->render();
		$params = $this->params;
		$params_array = $this->params_array;

        return view('pages.customers', compact('params', 'params_array', 'customer_group', 'approved', 'created_at', 'ip', 'email', 'sort_email', 'sort_group', 'sort_ip', 'sort_created_at', 'customer_groups', 'breadcrumbs', 'sort_name', 'sort_status', 'customers', 'name', 'status', 'sort', 'order'));
    }

    public function add() {
        $this->breadcrumbs->addCrumb('Клиенты', url('admin/customers') . $this->params);
        $this->breadcrumbs->addCrumb('Создать', url('admin/customer_add'));
        $breadcrumbs = $this->breadcrumbs->render();
        $customer_groups = CustomerGroups::join('customer_group_description as cgd', 'cgd.customer_group_id', '=', 'customer_groups.id')->select('customer_groups.id', 'cgd.name')->where('customer_groups.status', 1)->orderBy('cgd.name')->get();

        return view('pages.customer-edit', ['customer_groups' => $customer_groups, 'breadcrumbs' => $breadcrumbs, 'address' => (array)old('address'), 'address_id' => old('address_id'), 'firstname' => old('firstname'), 'approval' => old('approval'), 'ip' => old('ip'), 'lastname' => old('lastname'), 'password' => old('password'), 'customer_group_id' => old('customer_group_id'), 'email' => old('email'), 'phone' => old('phone'), 'approved' => old('approved'), 'status' => old('status'), 'action' => asset('admin/customer_save') . $this->params, 'id' => '']);
    }

    public function edit($id)
    {
        $data = Customers::with(['getIp:customer_id,ip,created_at', 'getOrders:customer_id,id,total,created_at', 'getRewards:customer_id,description,points,created_at', 'address'])->where('id', $id)->first();

        if (!empty($data)) {
            $this->breadcrumbs->addCrumb('Клиенты', url('admin/customers') . $this->params);
            $this->breadcrumbs->addCrumb('Редактировать', url('admin/customer/' . $id));
            $breadcrumbs = $this->breadcrumbs->render();
            $customer_groups = CustomerGroups::join('customer_group_description as cgd', 'cgd.customer_group_id', '=', 'customer_groups.id')->select('customer_groups.id', 'cgd.name')->where('customer_groups.status', 1)->orderBy('cgd.name')->get();

            extract($data->toArray());
            $action = asset('admin/customer_save') . $this->params;
            $currency = !empty($this->currencies[$this->settings['currency_code']]) ? $this->currencies[$this->settings['currency_code']]->toArray() : [];

            return view('pages.customer-edit', compact('address_id', 'address', 'currency', 'get_ip', 'get_orders', 'customer_groups', 'breadcrumbs', 'firstname', 'ip', 'lastname', 'password', 'customer_group_id', 'email', 'phone', 'status', 'approval', 'id', 'action'));
        } else {
            return redirect('admin/customers' . $this->params)->with('error', 'Идентификатор не найден');
        }
    }

    public function delete(Request $request) {
        if ($request->selected) {
            foreach ($request->selected as $s) {
                Customers::where('id', $s)->delete();
                CustomerIp::where('customer_id', $s)->delete();
                CustomerReward::where('customer_id', $s)->delete();
				CustomerAddress::where('customer_id', $s)->delete();
				CustomerSocial::where('customer_id', $s)->delete();
            }
	
			$message = 'Операция успешна';
			$type = 'success';
		} else {
			$message = 'Выделите пункты для удаления';
			$type = 'error';
		}
	
		return redirect('admin/customers' . $this->params)->with($type, $message);
    }

    public function save(Request $request) {
        $address = [];

        if (!empty($request->address)) {
            $address = [
                'address.*.firstname' => 'required|max:300',
                'address.*.lastname' => 'required|max:300',
                'address.*.city' => 'required|max:64',
                'address.*.postcode' => 'required|max:64'
            ];
        }

        if (!is_null($request->password) || is_null($request->id)) {
            $this->validate($request, [
                'customer_group_id' => 'required',
                'firstname' => 'required|max:300',
                'lastname' => 'required|max:300',
                'email' => 'required',
                'phone' => 'required',
                'password' => 'required|min:4|max:300',
                $address
            ]);
        } else {
            $this->validate($request, [
                'customer_group_id' => 'required',
                'firstname' => 'required|max:300',
                'lastname' => 'required|max:300',
                'email' => 'required',
                'phone' => 'required',
                $address
            ]);
        }

        if (!is_null($request->id)) {
            $customer['approval'] = $request->approval ? $request->approval : 0;
            $customer['customer_group_id'] = $request->customer_group_id;
            $customer['firstname'] = $request->firstname;
            $customer['lastname'] = $request->lastname;
            $customer['email'] = $request->email;
            $customer['phone'] = $request->phone;
            $customer['ip'] = $request->ip ? $request->ip : '';

            if (!is_null($request->password)) {
                $customer['password'] = DB::raw('SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1(?)))))', [$request->password]);
            }

            $customer['status'] = $request->status ? $request->status : 0;

            Customers::where('id', $request->id)->update($customer);

            CustomerAddress::where('customer_id', $request->id)->delete();

            if (!empty($request->address)) {
                foreach ($request->address as $address) {
                    $customer_address = new CustomerAddress;
                    $customer_address->customer_id = $request->id;
                    $customer_address->firstname = $address['firstname'];
                    $customer_address->lastname = $address['lastname'];
                    $customer_address->company = $address['company'] ? $address['company'] : '';
                    $customer_address->postcode = $address['postcode'] ? $address['postcode'] : '';
					$customer_address->address = $address['address'];
					$customer_address->address2 = $address['address'] ? $address['address'] : '';
                    $customer_address->city = $address['city'];

                    $customer_address->save();

                    if (!$address['default']) {
                        Customers::where('id', $request->id)->update(['address_id' => $customer_address->id]);
                    }
                }
            }
        } else {
            $customer = new Customers;
            $customer->approval = $request->approval ? $request->approval : 0;
            $customer->customer_group_id = $request->customer_group_id;
            $customer->firstname = $request->firstname;
            $customer->lastname = $request->lastname;
            $customer->email = $request->email;
            $customer->phone = $request->phone;
            $customer->salt = token_salt(9);
            $customer->password = DB::raw('SHA1(CONCAT(' . $customer->salt . ', SHA1(CONCAT(' . $customer->salt . ', SHA1(' . $request->password . ')))))');
            $customer->ip = $request->ip ? $request->ip : '';
            $customer->status = $request->status ? $request->status : 0;

            Customers::insert($customer->toArray());

            if (!empty($request->address)) {
                foreach ($request->address as $address) {
                    $customer_address = new CustomerAddress;
                    $customer_address->customer_id = $customer->id;
                    $customer_address->firstname = $address['firstname'];
                    $customer_address->lastname = $address['lastname'];
                    $customer_address->company = $address['company'] ? $address['company'] : '';
                    $customer_address->postcode = $address['postcode'] ? $address['postcode'] : '';
                    $customer_address->address = $address['address'];
                    $customer_address->city = $address['city'];

                    $customer_address->save();

                    if (!$address['default']) {
                        Customers::where('id', $customer->id)->update(['address_id' => $customer_address->id]);
                    }
                }
            }
        }

        return redirect('admin/customers' . $this->params)->with('success', 'Операция успешна');
    }
    
    public function customer_login(Request $request) {
    	if ($request->id) {
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
			])->where('status', 1)->where('id', $request->id)->firstOrFail();
			
			$request->session()->forget('customer_id');
			$request->session()->put(['customer_id' => $request->id]);
			$request->session()->put(['customer' => $customer->toArray()]);
		
			return redirect('account');
		} else {
			return redirect('admin/customers' . $this->params)->with('error', 'Пользователь не найден');
		}
	}
}