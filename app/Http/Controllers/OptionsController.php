<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\ProductOption;
	use App\Models\ProductOptionValues;
	use App\Models\Options;
	use App\Models\OptionDescription;
	use App\Models\OptionValues;
	use App\Models\OptionValueDescription;
	use App\Models\Languages;
	
	class OptionsController extends Controller
	{
		private $breadcrumbs;
		
		public function __construct()
		{
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			
			$classes = array('breadcrumb', 'breadcrumb-item');
			$this->breadcrumbs->addCssClasses($classes);
			$this->breadcrumbs->setDivider('');
			
			$this->breadcrumbs->addCrumb(__('locale.home'), url('admin'));
			
			$this->lang = session('lang');
		}
		
		public function index(Request $request){
			if ($request->sort) {
				$sort = $request->sort;
			} else {
				$sort = 'od.name';
			}
			
			if ($request->order) {
				$order = $request->order;
			} else {
				$order = 'asc';
			}
			
			$limit = session('settings.limit', 25);
			
			$sort_name = url('admin/options', ['sort' => 'od.name', 'order' => $order == 'asc' ? 'desc' : 'asc']);
			$sort_order = url('admin/options', ['sort' => 'options.sort_order', 'order' => $order == 'asc' ? 'desc' : 'asc']);
			$sort_status = url('admin/options', ['sort' => 'options.status', 'order' => $order == 'asc' ? 'desc' : 'asc']);
			
			if (in_array($sort, ['od.name', 'options.sort_order', 'options.status'])) {
				$options = Options::join('option_description as od', 'od.option_id', '=', 'options.id')->select('options.sort_order', 'options.id', 'options.status', 'od.name')->orderBy($sort, $order)->paginate($limit);
			} else {
				$options = Options::join('option_description as od', 'od.option_id', '=', 'options.id')->select('options.sort_order', 'options.id', 'options.status', 'od.name')->orderBy('od.name')->paginate($limit);
			}
			
			$this->breadcrumbs->addCrumb('Опции', url('admin/options'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.options', compact('breadcrumbs', 'options', 'sort_name', 'sort_order', 'sort_status', 'sort', 'order'));
		}
		
		public function option_autocomplete(Request $request) {
			$json = [];
			
			if ($request->term) {
				$language_default = session('default_language');
				
				$where[] = ['od.name', 'like', '%' . $request->term . '%'];
				$where[] = ['od.lang', '=', $language_default];
				$where[] = ['options.status', '=', 1];
				
				if ($request->id) {
					$where[] = ['options.id', '!=', $request->id];
				}
				
				foreach (Options::join('option_description as od', 'od.option_id', '=', 'options.id')->limit(5)->where($where)->pluck('od.name', 'options.id') as $key => $c) {
					$json[] = ['id' => $key, 'value' => $c];
				}
			}
			
			return response()->json($json);
		}
		
		public function add() {
			$langs = Languages::orderBy('name', 'asc')->get();
			$this->breadcrumbs->addCrumb('Опции', url('admin/options'));
			$this->breadcrumbs->addCrumb('Создать', url('admin/option_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.option-edit', ['meta' => (array)old('meta'), 'option_values' => (array)old('option_values'), 'breadcrumbs' => $breadcrumbs, 'langs' => $langs, 'type' => old('type'), 'sort_order' => old('sort_order'), 'status' => old('status'), 'action' => asset('admin/option_save'), 'action2' => asset('admin/add_image'), 'id' => '']);
		}
		
		public function edit($id)
		{
			$data = Options::where('id', $id)->first();
			
			if (!empty($data)) {
				$option_value = OptionValues::with('option_value_description:option_value_id,name,lang')
					->select('id', 'option_id', 'image', 'sort_order')
					->where('option_id', $id)
					->get()
					->toArray();
				
				$option_values = [];
				
				foreach ($option_value as $value) {
					if (!empty($value['option_value_description'])) {
						$value['description'][$value['option_value_description']['lang']] = ['name' => $value['option_value_description']['name']];
					}
					
					$option_values[] = $value;
				}
				
				$langs = Languages::orderBy('name', 'asc')->get();
				extract($data->toArray());
				
				$meta = [];
				
				foreach ($data->meta as $description) {
					$meta[$description['lang']] = $description;
				}
				
				$this->breadcrumbs->addCrumb('Опции', url('admin/options'));
				$this->breadcrumbs->addCrumb('Редактировать', url('admin/option/' . $id));
				$breadcrumbs = $this->breadcrumbs->render();
				$action = asset('admin/option_save');
				$action2 = asset('admin/add_image');
				
				return view('pages.option-edit', compact('breadcrumbs', 'option_values', 'langs', 'meta', 'type', 'status', 'sort_order', 'action', 'action2', 'id'));
			} else {
				return redirect('admin/options')->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete(Request $request) {
			if ($request->selected) {
				foreach ($request->selected as $s) {
					Options::where('id', $s)->delete();
					OptionDescription::where('option_id', $s)->delete();
					ProductOption::where('option_id', $s)->delete();
					ProductOptionValues::where('option_id', $s)->delete();
					
					foreach (OptionValues::where('option_id', $s)->pluck('id') as $fv) {
						OptionValueDescription::where('option_value_id', $fv)->delete();
					}
					
					OptionValues::where('option_id', $s)->delete();
				}
				
				$message = 'Операция успешна';
				$type = 'success';
			} else {
				$message = 'Выделите пункты для удаления';
				$type = 'error';
			}
			
			return redirect('admin/options')->with($type, $message);
		}
		
		public function save(Request $request) {
			if ($request->type === 'select' || $request->type === 'radio' || $request->type === 'checkbox') {
				$this->validate($request, [
					'meta.*.name' => 'required',
					'type' => 'required',
					'option_values.*.option_value_description.*.name' => 'required',
				]);
			} else {
				$this->validate($request, [
					'meta.*.name' => 'required',
					'type' => 'required'
				]);
			}
			
			if (!is_null($request->id)) {
				$option['type'] = $request->type;
				$option['sort_order'] = $request->sort_order ? $request->sort_order : 0;
				$option['status'] = $request->status ? $request->status : 0;
				
				Options::where('id', $request->id)->update($option);
				
				OptionDescription::where('option_id', $request->id)->delete();
				
				foreach ($request->meta as $lang => $meta) {
					$fd = new OptionDescription;
					$fd->lang = $lang;
					$fd->option_id = $request->id;
					$fd->name = $meta['name'];
					
					$fd->save();
				}
				
				if (!is_null($request->option_values) && in_array($option['type'], ['radio', 'select', 'checkbox', 'color'])) {
					foreach ($request->option_values as $option_values) {
						if ($option_values['id']) {
							OptionValues::where('id', $option_values['id'])->update([
								'option_id' => $request->id,
								'sort_order' => isset($option_values['sort_order']) ? $option_values['sort_order'] : 0
							]);
							
							foreach ($option_values['option_value_description'] as $lang => $meta) {
								OptionValueDescription::where('option_value_id', $option_values['id'])->where('lang', $lang)->update([
									'name' => $meta['name']
								]);
							}
						} else {
							$ov = new OptionValues;
							$ov->option_id = $request->id;
							$ov->sort_order = isset($option_values['sort_order']) ? $option_values['sort_order'] : 0;
							
							$ov->save();
							
							foreach ($option_values['option_value_description'] as $lang => $meta) {
								$fvd = new OptionValueDescription;
								$fvd->lang = $lang;
								$fvd->option_value_id = $ov->id;
								$fvd->name = $meta['name'];
								
								$fvd->save();
							}
						}
					}
				}
			} else {
				$option = new Options;
				$option->type = $request->type;
				$option->sort_order = $request->sort_order ? $request->sort_order : 0;
				$option->status = $request->status ? $request->status : 0;
				
				$option->save();
				
				foreach ($request->meta as $lang => $meta) {
					$fd = new OptionDescription;
					$fd->lang = $lang;
					$fd->option_id = $option->id;
					$fd->name = $meta['name'];
					
					$fd->save();
				}
				
				if (!is_null($request->option_values) && in_array($option->type, ['radio', 'select', 'checkbox', 'color'])) {
					foreach ($request->option_values as $option_values) {
						$ov = new OptionValues;
						$ov->option_id = $option->id;
						$ov->sort_order = isset($option_values['sort_order']) ? $option_values['sort_order'] : 0;
						
						$ov->save();
						
						foreach ($option_values['option_value_description'] as $lang => $meta) {
							$fvd = new OptionValueDescription;
							$fvd->lang = $lang;
							$fvd->option_value_id = $ov->id;
							$fvd->name = $meta['name'];
							
							$fvd->save();
						}
					}
				}
			}
			
			return redirect('admin/options')->with('success', 'Операция успешна');
		}
	}