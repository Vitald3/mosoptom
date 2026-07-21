<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\ProductAttribute;
	use App\Models\ProductAttributeImage;
	use App\Models\Attributes;
	use App\Models\AttributeDescription;
	use App\Models\AttributeGroups;
	use App\Models\AttributeGroupDescription;
	use App\Models\Settings;
	use App\Models\Languages;
	
	class AttributesController extends Controller
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
			$where = [];
			
			$language_default = session('default_language');
			
			if (!is_null($request->status)) {
				$where[] = ['attributes.status', '=', $request->status];
				$status = $request->status;
			} else {
				$status = '';
			}
			
			if (!is_null($request->name)) {
				$where[] = ['ad.name', 'like', '%' . $request->name . '%'];
				$name = $request->name;
			} else {
				$name = '';
			}
			
			$where[] = ['ad.lang', '=', $language_default];
			$where[] = ['agd.lang', '=', $language_default];
			$where[] = ['ag.status', '=', 1];
			
			if ($request->sort) {
				$sort = $request->sort;
			} else {
				$sort = 'ad.name';
			}
			
			if ($request->order) {
				$order = $request->order;
			} else {
				$order = 'asc';
			}
			
			$limit = session('settings.limit', 25);
			
			$sort_name = url('admin/attributes', ['sort' => 'ad.name', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_name_group = url('admin/attributes', ['sort' => 'agd.name', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_sort = url('admin/attributes', ['sort' => 'attributes.sort', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_status = url('admin/attributes', ['sort' => 'attributes.status', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			
			if (in_array($sort, ['ad.name', 'attributes.sort', 'attributes.status'])) {
				$attributes = Attributes::select('attributes.sort', 'attributes.id', 'attributes.status', 'ad.name', 'agd.name as group')->join('attribute_groups as ag', 'ag.id', '=', 'attributes.attribute_group_id')->join('attribute_group_description as agd', 'agd.attribute_group_id', '=', 'ag.id')->join('attribute_description as ad', 'ad.attribute_id', '=', 'attributes.id')->where($where)->orderBy($sort, $order)->paginate($limit);
			} else {
				$attributes = Attributes::select('attributes.sort', 'attributes.id', 'attributes.status', 'ad.name', 'agd.name as group')->join('attribute_groups as ag', 'ag.id', '=', 'attributes.attribute_group_id')->join('attribute_group_description as agd', 'agd.attribute_group_id', '=', 'ag.id')->join('attribute_description as ad', 'ad.attribute_id', '=', 'attributes.id')->where($where)->orderBy('ad.name')->paginate($limit);
			}
			
			$this->breadcrumbs->addCrumb('Характеристики', url('admin/attributes') . $this->params);
			$breadcrumbs = $this->breadcrumbs->render();
			$params = $this->params;
			$params_array = $this->params_array;
			
			return view('pages.attributes', compact('params', 'params_array', 'breadcrumbs', 'sort_name', 'sort_sort', 'sort_name_group', 'sort_status', 'attributes', 'name', 'status', 'sort', 'order'));
		}
		
		public function groups(Request $request){
			$language_default = session('default_language');
			
			$where[] = ['agd.lang', '=', $language_default];
			
			if ($request->sort) {
				$sort = $request->sort;
			} else {
				$sort = 'agd.name';
			}
			
			if ($request->order) {
				$order = $request->order;
			} else {
				$order = 'asc';
			}
			
			$limit = session('settings.limit', 25);
			
			$sort_name = url('admin/attribute_groups', ['sort' => 'agd.name', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_sort = url('admin/attribute_groups', ['sort' => 'attribute_groups.sort', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_status = url('admin/attribute_groups', ['sort' => 'attribute_groups.status', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			
			if (in_array($sort, ['agd.name', 'attribute_groups.sort', 'attribute_groups.status'])) {
				$attribute_groups = AttributeGroups::select('attribute_groups.sort', 'attribute_groups.id', 'attribute_groups.status', 'agd.name')->join('attribute_group_description as agd', 'agd.attribute_group_id', '=', 'attribute_groups.id')->where($where)->orderBy($sort, $order)->paginate($limit);
			} else {
				$attribute_groups = AttributeGroups::select('attribute_groups.sort', 'attribute_groups.id', 'attribute_groups.status', 'agd.name')->join('attribute_group_description as agd', 'agd.attribute_group_id', '=', 'attribute_groups.id')->where($where)->orderBy('agd.name')->paginate($limit);
			}
			
			$this->breadcrumbs->addCrumb('Группы характеристик', url('admin/attribute_groups') . $this->params);
			$breadcrumbs = $this->breadcrumbs->render();
			$params = $this->params;
			$params_array = $this->params_array;
			
			return view('pages.attribute_groups', compact('params', 'params_array', 'breadcrumbs', 'sort_name', 'sort_sort', 'sort_status', 'attribute_groups', 'sort', 'order'));
		}
		
		public function attribute_autocomplete(Request $request) {
			$json = [];
			
			if ($request->term) {
				$language_default = session('default_language');
				
				$where[] = ['ad.name', 'like', '%' . $request->term . '%'];
				$where[] = ['ad.lang', '=', $language_default];
				$where[] = ['attributes.status', '=', 1];
				
				if ($request->id) {
					$where[] = ['attributes.id', '!=', $request->id];
				}
				
				foreach (Attributes::join('attribute_description as ad', 'ad.attribute_id', '=', 'attributes.id')->limit(5)->where($where)->pluck('ad.name', 'attributes.id') as $key => $c) {
					$json[] = ['id' => $key, 'value' => $c];
				}
			}
			
			return response()->json($json);
		}
		
		public function add() {
			$langs = Languages::orderBy('name', 'asc')->get();
			$attribute_groups = AttributeGroups::join('attribute_group_description as agd', 'agd.attribute_group_id', '=', 'attribute_groups.id')->select('attribute_groups.id', 'agd.name')->where('attribute_groups.status', 1)->orderBy('agd.name', 'asc')->get();
			$this->breadcrumbs->addCrumb('Характеристики', url('admin/attributes') . $this->params);
			$this->breadcrumbs->addCrumb('Создать', url('admin/attribute_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.attribute-edit', ['langs' => $langs, 'attribute_groups' => $attribute_groups, 'breadcrumbs' => $breadcrumbs, 'meta' => (array)old('meta'), 'image' => old('image'), 'attribute_group_id' => old('attribute_group_id'), 'sort' => old('sort'), 'status' => old('status'), 'action' => asset('admin/attribute_save') . $this->params, 'action2' => asset('admin/add_image'), 'id' => '']);
		}
		
		public function edit($id)
		{
			$attribute = Attributes::with('meta')->where('id', $id)->first();
			
			if (!empty($attribute)) {
				extract($attribute->toArray());
				$langs = Languages::orderBy('name', 'asc')->get();
				$attribute_groups = AttributeGroups::join('attribute_group_description as agd', 'agd.attribute_group_id', '=', 'attribute_groups.id')->select('attribute_groups.id', 'agd.name')->where('attribute_groups.status', 1)->orderBy('agd.name', 'asc')->get();
				
				$meta = [];
				
				foreach ($attribute->meta as $description) {
					$meta[$description['lang']] = $description;
				}
				
				$this->breadcrumbs->addCrumb('Характеристики', url('admin/attributes') . $this->params);
				$this->breadcrumbs->addCrumb('Редактировать', url('admin/attribute/' . $id));
				$breadcrumbs = $this->breadcrumbs->render();
				
				$action = asset('admin/attribute_save' . $this->params);
				$action2 = asset('admin/add_image');
				
				return view('pages.attribute-edit', compact('attribute_groups', 'attribute_group_id', 'breadcrumbs', 'langs', 'meta', 'image', 'status', 'sort', 'id', 'action', 'action2'));
			} else {
				return redirect('admin/attributes' . $this->params)->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete(Request $request) {
			if ($request->selected) {
				foreach ($request->selected as $s) {
					Attributes::where('id', $s)->delete();
					AttributeDescription::where('attribute_id', $s)->delete();
					ProductAttribute::where('attribute_id', $s)->delete();
					ProductAttributeImage::where('attribute_id', $s)->delete();
				}
				
				$message = 'Операция успешна';
				$type = 'success';
			} else {
				$message = 'Выделите пункты для удаления';
				$type = 'error';
			}
			
			return redirect('admin/attributes' . $this->params)->with($type, $message);
		}
		
		public function add_group() {
			$langs = Languages::orderBy('name', 'asc')->get();
			$this->breadcrumbs->addCrumb('Группа характеристики', url('admin/attribute_groups') . $this->params);
			$this->breadcrumbs->addCrumb('Создать', url('admin/attribute_group_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.attribute-group-edit', ['langs' => $langs, 'breadcrumbs' => $breadcrumbs, 'meta' => (array)old('meta'), 'sort' => old('sort'), 'status' => old('status'), 'action' => asset('admin/attribute_group_save') . $this->params, 'id' => '']);
		}
		
		public function edit_group($id)
		{
			$attribute_group = AttributeGroups::with('meta')->where('id', $id)->first();
			
			if (!empty($attribute_group)) {
				extract($attribute_group->toArray());
				$langs = Languages::orderBy('name', 'asc')->get();
				
				$meta = [];
				
				foreach ($attribute_group->meta as $description) {
					$meta[$description['lang']] = $description;
				}
				
				$this->breadcrumbs->addCrumb('Группа характеристик', url('admin/attributes') . $this->params);
				$this->breadcrumbs->addCrumb('Редактировать', url('admin/attribute_group/' . $id));
				$breadcrumbs = $this->breadcrumbs->render();
				
				$action = asset('admin/attribute_group_save' . $this->params);
				
				return view('pages.attribute-group-edit', compact('breadcrumbs', 'langs', 'meta', 'status', 'sort', 'id', 'action'));
			} else {
				return redirect('admin/attribute_groups' . $this->params)->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete_group(Request $request) {
			if ($request->selected) {
				foreach ($request->selected as $s) {
					AttributeGroups::where('id', $s)->delete();
					AttributeGroupDescription::where('attribute_group_id', $s)->delete();
				}
				
				$message = 'Операция успешна';
				$type = 'success';
			} else {
				$message = 'Выделите пункты для удаления';
				$type = 'error';
			}
			
			return redirect('admin/attribute_groups' . $this->params)->with($type, $message);
		}
		
		public function save(Request $request) {
			$this->validate($request, [
				'meta.*.name' => 'required',
				'attribute_group_id' => 'required'
			]);
			
			if (!is_null($request->id)) {
				$attribute['attribute_group_id'] = $request->attribute_group_id;
				$attribute['image'] = $request->image ? $request->image : '';
				$attribute['sort'] = $request->sort ? $request->sort : 0;
				$attribute['status'] = $request->status ? $request->status : 0;
				
				Attributes::where('id', $request->id)->update($attribute);
				
				AttributeDescription::where('attribute_id', $request->id)->delete();
				
				foreach ($request->meta as $lang => $meta) {
					$ad = new AttributeDescription;
					$ad->lang = $lang;
					$ad->attribute_id = $request->id;
					$ad->name = $meta['name'];
					
					$ad->save();
				}
			} else {
				$attribute = new Attributes;
				$attribute->attribute_group_id = $request->attribute_group_id;
				$attribute->image = $request->image ? $request->image : '';
				$attribute->sort = $request->sort ? $request->sort : 0;
				$attribute->status = $request->status ? $request->status : 0;
				
				$attribute->save();
				
				foreach ($request->meta as $lang => $meta) {
					$ad = new AttributeDescription;
					$ad->lang = $lang;
					$ad->attribute_id = $attribute->id;
					$ad->name = $meta['name'];
					
					$ad->save();
				}
			}
			
			return redirect('admin/attributes' . $this->params)->with('success', 'Операция успешна');
		}
		
		public function save_group(Request $request) {
			$this->validate($request, [
				'meta.*.name' => 'required'
			]);
			
			if (!is_null($request->id)) {
				$attribute['sort'] = $request->sort ? $request->sort : 0;
				$attribute['status'] = $request->status ? $request->status : 0;
				
				AttributeGroups::where('id', $request->id)->update($attribute);
				
				AttributeGroupDescription::where('attribute_group_id', $request->id)->delete();
				
				foreach ($request->meta as $lang => $meta) {
					$ad = new AttributeGroupDescription;
					$ad->lang = $lang;
					$ad->attribute_group_id = $request->id;
					$ad->name = $meta['name'];
					
					$ad->save();
				}
			} else {
				$attribute = new AttributeGroups;
				$attribute->sort = $request->sort ? $request->sort : 0;
				$attribute->status = $request->status ? $request->status : 0;
				
				$attribute->save();
				
				foreach ($request->meta as $lang => $meta) {
					$ad = new AttributeGroupDescription;
					$ad->lang = $lang;
					$ad->attribute_group_id = $attribute->id;
					$ad->name = $meta['name'];
					
					$ad->save();
				}
			}
			
			return redirect('admin/attribute_groups' . $this->params)->with('success', 'Операция успешна');
		}
	}
