<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Layouts;
	use App\Models\Settings;
	use App\Models\LayoutExtension;
	use App\Models\Extensions;
	
	class LayoutsController extends Controller {
		private $breadcrumbs;
		
		public function __construct() {
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			
			$classes = array('breadcrumb', 'breadcrumb-item');
			$this->breadcrumbs->addCssClasses($classes);
			$this->breadcrumbs->setDivider('');
			
			$this->breadcrumbs->addCrumb(__('locale.home'), url('admin'));
		}
		
		private $positions = ['top' => 'Над контентом', 'bottom' => 'Под контентом'];
		
		public function index(Request $request) {
			if (!is_null($request->name)) {
				$name = $request->name;
			} else {
				$name = '';
			}
			
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
			
			$sort_name = url('admin/layouts', ['sort' => 'name', 'order' => $order == 'asc' ? 'desc' : 'asc']);
			
			if (in_array($sort, ['name'])) {
				$layouts = Layouts::select('id', 'name')->orderBy($sort, $order)->paginate($limit);
			} else {
				$layouts = Layouts::select('id', 'name')->orderBy('name')->paginate($limit);
			}
			
			$this->breadcrumbs->addCrumb('Схемы', url('admin/layouts'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.layouts', compact('breadcrumbs', 'sort_name', 'sort', 'layouts', 'name', 'order'));
		}
		
		public function add() {
			$extensions = [];
			
			Extensions::where('status', 1)->orderBy('created_at')->get()->map(function($item) use(&$extensions) {
				$extensions[$item->code][] = $item->toArray();
			});
			
			Settings::where([['code', 'like', '%extension.module.%'], ['value->status', 1]])->orderBy('created_at')->get()->map(function($item) use(&$extensions) {
				$code = str_replace('extension.module.', '', $item->code);
				$extensions[$code][] = $item->value;
			});
			
			$extensions2 = [];
			
			foreach ($extensions as $code => $extension) {
				$module_data = [];
				
				foreach ($extension as $module) {
					$module_data[] = array(
						'id' => isset($module['id']) ? $module['id'] : '',
						'name' => strip_tags($module['name']),
						'code' => $code . (isset($module['id']) ? '.' .  $module['id'] : '')
					);
				}
				
				$extensions2[] = array(
					'name'   => $code,
					'code'   => ucfirst($code),
					'module' => $module_data
				);
			}
			
			$routes = ['home' => 'Главная', 'pages' => 'Статья', 'default' => 'По умолчанию', 'page_category' => 'Категория статьи', 'products' => 'Товар', 'categories' => 'Категория товара', 'account' => 'Аккаунт'];
			
			$this->breadcrumbs->addCrumb('Схемы', url('admin/layouts'));
			$this->breadcrumbs->addCrumb('Создать', url('admin/layout_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.layout-edit', ['breadcrumbs' => $breadcrumbs, 'routes' => $routes, 'route' => old('route'), 'positions' => $this->positions, 'extensions' => $extensions2, 'name' => old('name'), 'layout_extensions' => (array)old('layout_extensions'), 'action' => asset('admin/layout_save'), 'id' => '']);
		}
		
		public function edit($id)
		{
			$layout = Layouts::where('id', $id)->first();
			
			if (!empty($layout)) {
				extract($layout->toArray());
				$layout_extensions = LayoutExtension::where('layout_id', $id)->orderBy('sort')->get();
				
				$extensions2 = [];
				
				Extensions::where('status', 1)->orderBy('created_at')->get()->map(function ($item) use (&$extensions2) {
					$extensions2[$item->code][] = $item->toArray();
				});
				
				Settings::where([['code', 'like', '%extension.module.%'], ['value->status', 1]])->orderBy('created_at')->get()->map(function ($item) use (&$extensions2) {
					$code = str_replace('extension.', '', $item->code);
					$extensions2[$code][] = $item->value;
				});
				
				$extensions = [];
				
				foreach ($extensions2 as $code => $extension) {
					$module_data = [];
					
					foreach ($extension as $module) {
						$module_data[] = array(
							'id' => isset($module['id']) ? $module['id'] : '',
							'name' => strip_tags($module['name']),
							'code' => $code . (isset($module['id']) ? '.' . $module['id'] : '')
						);
					}
					
					$extensions[] = array(
						'name' => $code,
						'code' => ucfirst($code),
						'module' => $module_data
					);
				}
				
				$routes = ['pages' => 'Статья', 'page_category' => 'Категория статьи', 'products' => 'Товар', 'categories' => 'Категория товара'];
				
				$this->breadcrumbs->addCrumb('Схемы', url('admin/layouts'));
				$this->breadcrumbs->addCrumb('Редактировать', url('admin/layout/' . $id));
				$breadcrumbs = $this->breadcrumbs->render();
				$action = asset('admin/layout_save');
				$positions = $this->positions;
				
				return view('pages.layout-edit', compact('breadcrumbs', 'routes', 'route', 'positions', 'extensions', 'name', 'layout_extensions', 'action', 'id'));
			} else {
				return redirect('admin/layouts')->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete(Request $request) {
			if ($request->selected) {
				foreach ($request->selected as $s) {
					Layouts::where('id', $s)->delete();
					LayoutExtension::where('layout_id', $s)->delete();
				}
				
				$message = 'Операция успешна';
				$type = 'success';
			} else {
				$message = 'Выделите пункты для удаления';
				$type = 'error';
			}
			
			return redirect('admin/layouts')->with($type, $message);
		}
		
		public function save(Request $request) {
			$this->validate($request, [
				'name' => 'required',
				'route' => 'required'
			]);
			
			if (!is_null($request->id)) {
				$layouts['name'] = $request->name;
				$layouts['route'] = $request->route;
				
				Layouts::where('id', $request->id)->update($layouts);
				
				LayoutExtension::where('layout_id', $request->id)->delete();
				
				if (!is_null($request->layout_extension)) {
					foreach ($request->layout_extension as $module) {
						$le = new LayoutExtension;
						$le->code = $module['code'];
						$le->extension_id = $module['extension_id'];
						$le->position = $module['position'];
						$le->sort = $module['sort'];
						$le->layout_id = $request->id;
						
						$le->save();
					}
				}
			} else {
				$layouts = new Layouts;
				$layouts->name = $request->name;
				$layouts->route = $request->route;
				
				$layouts->save();
				
				if (!is_null($request->layout_extension)) {
					foreach ($request->layout_extension as $module) {
						$le = new LayoutExtension;
						$le->code = $module['code'];
						$le->extension_id = $module['extension_id'];
						$le->position = $module['position'];
						$le->sort = $module['sort'];
						$le->layout_id = $layouts->id;
						
						$le->save();
					}
				}
			}
			
			return redirect('admin/layouts')->with('success', 'Операция успешна');
		}
	}
