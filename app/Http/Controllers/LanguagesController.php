<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Languages;
	
	class LanguagesController extends Controller {
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
		
		public function index(Request $request) {
			$where = [];
			
			if (!is_null($request->status)) {
				$where[] = ['status', '=', $request->status];
				$status = $request->status;
			} else {
				$status = '';
			}
			
			if (!is_null($request->name)) {
				$where[] = ['name', 'like', '%' . $request->name . '%'];
				$name = $request->name;
			} else {
				$name = '';
			}
			
			$limit = session('settings.limit', 25);
			
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
			
			$sort_name = url('admin/languages', ['sort' => 'name', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_sort = url('admin/languages', ['sort' => 'sort', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_status = url('admin/languages', ['sort' => 'status', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			
			if (in_array($sort, ['name', 'sort', 'status'])) {
				$languages = Languages::where($where)->orderBy($sort, $order)->paginate($limit);
			} else {
				$languages = Languages::where($where)->orderBy('name')->paginate($limit);
			}
			
			$this->breadcrumbs->addCrumb('Языки', url('admin/languages') . $this->params);
			$breadcrumbs = $this->breadcrumbs->render();
			$params = $this->params;
			$params_array = $this->params_array;
			
			return view('pages.languages', compact('sort', 'order', 'params', 'params_array',  'sort_name', 'sort_sort', 'sort_status', 'breadcrumbs', 'languages', 'status', 'name'));
		}
		
		public function add() {
			$dir = scandir(resource_path('lang'));
			
			$codes = [];
			
			foreach($dir as $d) {
				if ($d != '.' && $d != '..') {
					$codes[] = $d;
				}
			}
			
			$this->breadcrumbs->addCrumb('Языки', url('admin/languages') . $this->params);
			$this->breadcrumbs->addCrumb('Создать', url('admin/language_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.language-edit', ['breadcrumbs' => $breadcrumbs, 'name' => old('name'), 'hreflang' => old('hreflang'), 'code' => old('code'), 'codes' => $codes, 'mask' => old('mask'), 'sort' => old('sort'), 'status' => old('status'), 'image' => old('image'), 'action' => asset('admin/language_save') . $this->params, 'id' => '', 'action2' => asset('admin/language_add_image')]);
		}
		
		public function edit($id)
		{
			$language = Languages::where('language_id', $id)->first();
			
			if (!empty($language)) {
				extract($language->toArray());
				$dir = scandir(resource_path('lang'));
				
				$codes = [];
				
				foreach ($dir as $d) {
					if ($d != '.' && $d != '..') {
						$codes[] = $d;
					}
				}
				
				$this->breadcrumbs->addCrumb('Языки', url('admin/languages') . $this->params);
				$this->breadcrumbs->addCrumb('Редактировать', url('admin/language/' . $id));
				$breadcrumbs = $this->breadcrumbs->render();
				$action = asset('admin/language_save') . $this->params;
				$action2 = asset('admin/language_add_image');
				
				return view('pages.language-edit', compact('breadcrumbs', 'name', 'hreflang', 'codes', 'code', 'mask', 'sort', 'status', 'image', 'action', 'id', 'action2'));
			} else {
				return redirect('admin/languages' . $this->params)->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete(Request $request) {
			$language_default = session('default_language');
			
			if ($request->selected) {
				$message = 'Операция успешна';
				$type = 'success';
				
				foreach ($request->selected as $s) {
					if ($s == $language_default) {
						$message = 'Запрещено удалять язык по умолчанию';
						$type = 'error';
					} else {
						Languages::where('language_id', $s)->delete();
					}
				}
			} else {
				$message = 'Выделите пункты для удаления';
				$type = 'error';
			}
			
			return redirect('admin/languages' . $this->params)->with($type, $message);
		}
		
		public function save(Request $request) {
			$this->validate($request, [
				'name' => 'required',
				'code' => 'required',
				'hreflang' => 'required',
				'mask' => 'required',
				'image' => 'required'
			]);
			
			if (!is_null($request->id)) {
				$languages['name'] = $request->name;
				$languages['code'] = $request->code;
				$languages['hreflang'] = $request->hreflang;
				$languages['mask'] = $request->mask;
				$languages['image'] = $request->image;
				$languages['sort'] = $request->sort ? $request->sort : 0;
				$languages['status'] = $request->status;
				
				Languages::where('language_id', $request->id)->update($languages);
			} else {
				$languages = new languages;
				$languages->name = $request->name;
				$languages->code = $request->code;
				$languages->hreflang = $request->hreflang;
				$languages->mask = $request->mask;
				$languages->image = $request->image;
				$languages->sort = $request->sort ? $request->sort : 0;
				$languages->status = $request->status;
				
				$languages->save();
			}
			
			return redirect('admin/languages' . $this->params)->with('success', 'Операция успешна');
		}
		
		public function addImage(Request $request) {
			if ($request->hasFile('file')) {
				$files = $request->file('file');
				
				$name = $files->getClientOriginalName();
				$files->move('assets/site/img/languages', $name);
				$images[] = 'assets/site/img/languages/' . $name;
				
				return response()->json($images);
			}
		}
	}
