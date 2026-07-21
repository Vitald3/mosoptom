<?php
	
	namespace App\Http\Controllers;
	
	use App\Models\Permission;
	use App\Models\Settings;
	use Illuminate\Http\Request;
	
	class PermissionController extends Controller {
		private $breadcrumbs;
		
		public function __construct() {
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			
			$classes = array('breadcrumb', 'breadcrumb-item');
			$this->breadcrumbs->addCssClasses($classes);
			$this->breadcrumbs->setDivider('');
			
			$this->breadcrumbs->addCrumb(__('locale.home'), url('admin'));
		}
		
		public function index() {
			$limit = session('settings.limit', 25);
			
			$permissions = Permission::orderBy('name', 'asc')->paginate($limit);
			$this->breadcrumbs->addCrumb('Права пользователей', url('admin/permissions'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.permissions', compact('permissions', 'breadcrumbs'));
		}
		
		public function add() {
			$this->breadcrumbs->addCrumb('Права пользователей', url('admin/permissions'));
			$this->breadcrumbs->addCrumb('Создать', url('admin/permission_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.permission-edit', ['breadcrumbs' => $breadcrumbs, 'name' => old('name'), 'slug' => old('slug'), 'action' => asset('admin/permission_save'), 'id' => '']);
		}
		
		public function edit($id)
		{
			$permission = Permission::where('id', $id)->first();
			
			if (!empty($permission)) {
				extract($permission->toArray());
				$this->breadcrumbs->addCrumb('Права пользователей', url('admin/permissions'));
				$this->breadcrumbs->addCrumb('Редактировать', url('admin/permission/' . $id));
				$breadcrumbs = $this->breadcrumbs->render();
				$action = asset('admin/permission_save');
				
				return view('pages.permission-edit', compact('breadcrumbs', 'name', 'slug', 'action', 'id'));
			} else {
				return redirect('admin/permissions')->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete(Request $request) {
			if ($request->selected) {
				foreach ($request->selected as $s) {
					Permission::where('id', $s)->delete();
				}
				
				$message = 'Операция успешна';
				$type = 'success';
			} else {
				$message = 'Выделите пункты для удаления';
				$type = 'error';
			}
			
			return redirect('admin/permissions')->with($type, $message);
		}
		
		public function save(Request $request) {
			$this->validate($request, [
				'name' => 'required',
				'slug' => 'required'
			]);
			
			if (!is_null($request->id)) {
				$permissions['name'] = $request->name;
				$permissions['slug'] = $request->code;
				
				Permission::where('permission_id', $request->id)->update($permissions);
			} else {
				$permissions = new Permission();
				$permissions->slug = $request->slug;
				$permissions->name = $request->name;
				$permissions->save();
			}
			
			return redirect('admin/permissions')->with('success', 'Операция успешна');
		}
	}