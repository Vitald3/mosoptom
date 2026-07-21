<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Role;
	use App\Models\User;
	use App\Models\Permission;
	use DB;
	
	class RoleController extends Controller {
		private $breadcrumbs;
		
		public function __construct() {
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			
			$classes = array('breadcrumb', 'breadcrumb-item');
			$this->breadcrumbs->addCssClasses($classes);
			$this->breadcrumbs->setDivider('');
			
			$this->breadcrumbs->addCrumb(__('locale.home'), url('admin'));
		}
		
		public function index(Request $request) {
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
			
			$sort_name = url('admin/roles', ['sort' => 'name', 'order' => $order == 'asc' ? 'desc' : 'asc']);
	
			if (in_array($sort, ['name'])) {
				$roles = Role::orderBy($sort, $order)->paginate($limit);
			} else {
				$roles = Role::paginate($limit);
			}
			
			$this->breadcrumbs->addCrumb('Роли', url('admin/roles'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.roles', compact('sort', 'order', 'sort_name', 'roles', 'breadcrumbs'));
		}
		
		public function add() {
			$permissions = Permission::orderBy('name', 'asc')->get();
			$this->breadcrumbs->addCrumb('Роли', url('admin/roles'));
			$this->breadcrumbs->addCrumb('Создать', url('admin/role_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.role-edit', ['breadcrumbs' => $breadcrumbs, 'permissions2' => $permissions, 'permissions' => (array)old('permissions'), 'name' => old('name'), 'description' => old('description'), 'action' => asset('admin/role_save'), 'id' => '']);
		}
		
		public function edit($id)
		{
			if (Role::where('id', $id)->where('slug', 'admin')->whereRaw('(select role_id from users where id = ' . (int)auth()->user()->id . ') != id')->count()) {
				return response()->view('errors.403', [], 403);
			}
			
			$role = Role::where('id', $id)->first();
			
			if (!empty($role)) {
				extract($role->toArray());
				$permissions2 = Permission::orderBy('name', 'asc')->get();
				
				$permissions = [];
				
				foreach (DB::table('roles_permissions')->where('role_id', $id)->pluck('permission_id') as $p) {
					$permissions[] = $p;
				}
				
				$this->breadcrumbs->addCrumb('Роли', url('admin/roles'));
				$this->breadcrumbs->addCrumb('Создать', url('admin/role_add'));
				$breadcrumbs = $this->breadcrumbs->render();
				$action = asset('admin/role_save');
				
				return view('pages.role-edit', compact('breadcrumbs', 'permissions2', 'permissions', 'name', 'description', 'action', 'id'));
			} else {
				return redirect('admin/roles')->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete(Request $request) {
			$bool = false;
			
			if ($request->selected) {
				foreach ($request->selected as $s) {
					if (!Role::where('id', $s)->where('slug', 'admin')->count()) {
						Role::where('id', $s)->delete();
						DB::table('roles_permissions')->where('role_id', $s)->delete();
					} else {
						$bool = true;
					}
				}
			}
			
			if ($bool) {
				return redirect('admin/roles')->with('error', 'Удаление роли администратора запрещено');
			} else {
				return redirect('admin/roles')->with('success', 'Операция успешна');
			}
		}
		
		public function save(Request $request) {
			$this->validate($request, [
				'name' => 'required',
				'permissions.*' => 'required'
			]);
			
			if (!is_null($request->id)) {
				$roles['name'] = $request->name;
				$roles['description'] = $request->description ? $request->description : '';
				
				Role::where('id', $request->id)->update($roles);
				
				$bool = User::where('role_id', $request->id)->where('id', auth()->user()->id)->count();
				$bool2 = Role::where('id', $request->id)->where('slug', 'admin')->count();
				
				if (!$bool && !$bool2) {
					DB::table('roles_permissions')->where('role_id', $request->id)->delete();
					
					foreach ($request->permissions as $permission) {
						DB::table('roles_permissions')->insert([
							'role_id' => $request->id,
							'permission_id' => $permission
						]);
					}
				} else {
					return redirect('/admin/roles')->with('error', 'Запрещено редактировать разрешения роли');
				}
			} else {
				$roles = new role;
				$roles->name = $request->name;
				$roles->description = $request->description ? $request->description : '';
				
				$roles->save();
				
				$id = $roles->id;
				$slug = 'slug_' . $id;
				$roles = [];
				$roles['slug'] = $slug;
				DB::table('roles')->where('id', $id)->update($roles);
				
				foreach ($request->permissions as $permission) {
					DB::table('roles_permissions')->insert([
						'role_id' => $id,
						'permission_id' => $permission
					]);
				}
			}
			
			return redirect('admin/roles')->with('success', 'Операция успешна');
		}
	}
