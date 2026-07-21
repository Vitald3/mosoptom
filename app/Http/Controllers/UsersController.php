<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\User;
	use App\Models\Role;
	use DB;
	use Illuminate\Support\Facades\Hash;
	
	class UsersController extends Controller
	{
		private $breadcrumbs;
		
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
			$where = [];
			
			if (!is_null($request->status)) {
				$where[] = ['users.status', '=', $request->status];
				$status = $request->status;
			} else {
				$status = '';
			}
			
			if (!is_null($request->name)) {
				$where[] = ['users.name', 'like', '%' . $request->name . '%'];
				$name = $request->name;
			} else {
				$name = '';
			}
			
			if (!is_null($request->email)) {
				$where[] = ['users.email', 'like', '%' . $request->email . '%'];
				$email = $request->email;
			} else {
				$email = '';
			}
			
			$limit = session('settings.limit', 25);
			
			if ($request->sort) {
				$sort = $request->sort;
			} else {
				$sort = 'users.name';
			}
			
			if ($request->order) {
				$order = $request->order;
			} else {
				$order = 'asc';
			}
			
			$sort_name = url('admin/users', ['sort' => 'users.name', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_email = url('admin/users', ['sort' => 'users.email', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_role = url('admin/users', ['sort' => 'roles.name', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_status = url('admin/users', ['sort' => 'users.status', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			
			if (in_array($sort, ['users.name', 'users.email', 'users.status', 'roles.name'])) {
				$users = User::select('users.*')->addSelect('roles.name as role')->leftjoin('roles as roles', 'roles.id', '=', 'users.role_id')->where($where)->orderBy($sort, $order)->paginate($limit);
			} else {
				$users = User::select('users.*')->addSelect('roles.name as role')->leftjoin('roles as roles', 'roles.id', '=', 'users.role_id')->where($where)->orderBy('users.name')->paginate($limit);
			}
			
			$this->breadcrumbs->addCrumb('Пользователи', url('admin/users') . $this->params);
			$breadcrumbs = $this->breadcrumbs->render();
			$params = $this->params;
			$params_array = $this->params_array;
			
			return view('pages.users', compact('sort', 'order', 'sort_name', 'sort_email', 'sort_role', 'sort_status', 'params', 'params_array', 'breadcrumbs', 'users', 'status', 'name', 'email'));
		}
		
		public function add() {
			$roles = Role::orderBy('name', 'asc')->pluck('name', 'id');
			$this->breadcrumbs->addCrumb('Пользователи', url('admin/users') . $this->params);
			$this->breadcrumbs->addCrumb('Создать', url('admin/user_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.user-edit', ['breadcrumbs' => $breadcrumbs, 'roles' => $roles, 'name' => '', 'role_id' => '', 'email' => '', 'password' => '', 'status' => '', 'action' => asset('admin/user_save') . $this->params, 'id' => '']);
		}
		
		public function edit($id)
		{
			$user = User::where('id', $id)->first();
			
			if (!empty($user)) {
				extract($user->toArray());
				$roles = Role::orderBy('name', 'asc')->pluck('name', 'id');
				$this->breadcrumbs->addCrumb('Пользователи', url('admin/users') . $this->params);
				$this->breadcrumbs->addCrumb('Редактировать', url('admin/user/' . $id));
				$breadcrumbs = $this->breadcrumbs->render();
				$action = asset('admin/user_save') . $this->params;
				$password = old('password');
				
				return view('pages.user-edit', compact('breadcrumbs', 'roles', 'role_id', 'name', 'status', 'email', 'password', 'action', 'id'));
			} else {
				return redirect('admin/users' . $this->params)->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete(Request $request) {
			$bool = false;
			
			if ($request->selected) {
				foreach ($request->selected as $s) {
					$user = User::leftjoin('roles as r', 'r.id', '=', 'users.role_id')->where('users.id', $s)->where('r.slug', 'admin')->count();
					
					if ($user) {
						$bool = true;
					} else {
						User::where('id', $s)->delete();
					}
				}
			}
			
			if ($bool) {
				return redirect('admin/users' . $this->params)->with('error', 'Удаление администратора запрещено');
			} else {
				return redirect('admin/users' . $this->params)->with('success', 'Операция успешна');
			}
		}
		
		public function save(Request $request) {
			if (!empty($request->password) || is_null($request->id)) {
				$this->validate($request, [
					'name' => 'required',
					'email' => 'required|email',
					'password' => 'required|min:8',
					'role_id' => 'required'
				]);
			} else {
				$this->validate($request, [
					'name' => 'required',
					'email' => 'required|email|unique:users',
					'role_id' => 'required'
				]);
			}
			
			if (!is_null($request->id)) {
				$user['name'] = $request->name;
				$user['email'] = $request->email;
				$user['status'] = $request->status ? $request->status : 0;
				
				if (!empty($request->password)) {
					$user['password'] = Hash::make($request->password);
				}
				
				User::where('id', $request->id)->update($user);
				
				$user = User::leftjoin('roles as r', 'r.id', '=', 'users.role_id')->where('users.id', $request->id)->where('r.slug', 'admin')->where('r.id', '!=', $request->role_id)->count();
				
				if ($user) {
					return redirect('admin/user/' . $request->id)->with('error', 'Изменение роли администратора запрещено');
				} else {
					DB::table('users')->where('id', $request->id)->update(['role_id' => $request->role_id]);
				}
			} else {
				$user = new User;
				$user->name = $request->name;
				$user->email = $request->email;
				$user->role_id = $request->role_id;
				$user->password = Hash::make($request->password);
				$user->status = $request->status ? $request->status : 0;
				
				$user->save();
			}
			
			return redirect('admin/users' . $this->params)->with('success', 'Операция успешна');
		}
	}
