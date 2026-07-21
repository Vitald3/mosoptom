<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Manufacturers;
	use App\Models\Products;
	
	class ManufacturersController extends Controller
	{
		private $breadcrumbs;
		
		public function __construct() {
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			
			$classes = array('breadcrumb', 'breadcrumb-item');
			$this->breadcrumbs->addCssClasses($classes);
			$this->breadcrumbs->setDivider('');
			
			$this->breadcrumbs->addCrumb(__('locale.home'), url('admin'));
		}
		
		public function manufacturer_autocomplete(Request $request) {
			$json = [];
			
			if ($request->term) {
				$where[] = ['name', 'like', '%' . $request->term . '%'];
				$where[] = ['status', '=', 1];
				
				if ($request->id) {
					$where[] = ['id', '!=', $request->id];
				}
				
				$json = Manufacturers::select('name', 'id')->limit(5)->where($where)->get();
			}
			
			return response()->json($json);
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
			
			$query = parse_url($request->fullUrl());
			
			if (isset($query['query'])) {
				$params = '?' . $query['query'];
			} else {
				$params = '';
			}
			
			$sort_name = url('admin/manufacturers', ['sort' => 'name', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $params;
			$sort_order = url('admin/manufacturers', ['sort' => 'sort_order', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $params;
			$sort_status = url('admin/manufacturers', ['sort' => 'status', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $params;
			
			if (in_array($sort, ['name', 'sort_order', 'status'])) {
				$manufacturers = Manufacturers::select('name', 'sort_order', 'id', 'status')
					->orderBy($sort, $order)
					->paginate($limit);
			} else {
				$manufacturers = Manufacturers::select('name', 'sort_order', 'id', 'status')
					->orderBy('name')
					->paginate($limit);
			}
			
			$this->breadcrumbs->addCrumb('Производители', url('admin/manufacturers'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.manufacturers', compact('sort_status', 'sort_order', 'breadcrumbs', 'sort_name', 'manufacturers', 'sort', 'order'));
		}
		
		public function add() {
			$this->breadcrumbs->addCrumb('Производители', url('admin/manufacturers'));
			$this->breadcrumbs->addCrumb('Создать', url('admin/manufacturer_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.manufacturer-edit', ['breadcrumbs' => $breadcrumbs, 'name' => old('name'), 'image' => old('image'), 'sort_order' => old('sort_order'), 'status' => old('status'), 'action' => asset('admin/manufacturer_save'), 'action2' => asset('admin/add_image'), 'id' => '']);
		}
		
		public function edit($id)
		{
			$data = Manufacturers::where('id', $id)->first();
			
			if (!empty($data)) {
				$this->breadcrumbs->addCrumb('Производители', url('admin/manufacturers'));
				$this->breadcrumbs->addCrumb('Редактировать', url('admin/manufacturer/' . $id));
				$breadcrumbs = $this->breadcrumbs->render();
				
				extract($data->toArray());
				$action = asset('admin/manufacturer_save');
				$action2 = asset('admin/add_image');
				
				return view('pages.manufacturer-edit', compact('name', 'image', 'sort_order', 'breadcrumbs', 'status', 'action', 'action2', 'id'));
			} else {
				return redirect('admin/manufacturers')->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete(Request $request) {
			if ($request->selected) {
				foreach ($request->selected as $s) {
					Manufacturers::where('id', $s)->delete();
					Products::where('manufacturer_id', $s)->update(['manufacturer_id' => 0]);
				}
				
				$message = 'Операция успешна';
				$type = 'success';
			} else {
				$message = 'Выделите пункты для удаления';
				$type = 'error';
			}
			
			return redirect('admin/manufacturers')->with($type, $message);
		}
		
		public function save(Request $request) {
			$this->validate($request, [
				'name' => 'required|max:64'
			]);
			
			if (!is_null($request->id)) {
				$manufacturer['sort_order'] = $request->sort_order ? $request->sort_order : 0;
				$manufacturer['name'] = $request->name;
				$manufacturer['image'] = $request->image ? $request->image : '';
				$manufacturer['status'] = $request->status ? $request->status : 0;
				
				Manufacturers::where('id', $request->id)->update($manufacturer);
			} else {
				$manufacturer = new Manufacturers;
				$manufacturer->sort_order = $request->sort_order ? $request->sort_order : 0;
				$manufacturer->image = $request->image ? $request->image : '';
				$manufacturer->name = $request->name;
				$manufacturer->status = $request->status ? $request->status : 0;
				
				$manufacturer->save();
			}
			
			return redirect('admin/manufacturers')->with('success', 'Операция успешна');
		}
	}