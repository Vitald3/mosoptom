<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Regions;
	use App\Models\RegionDescription;
	use App\Models\Languages;
	use Illuminate\Support\Facades\Cache;
	
	class RegionsController extends Controller
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
				$where[] = ['regions.status', '=', $request->status];
				$status = $request->status;
			} else {
				$status = '';
			}
			
			if (!is_null($request->name)) {
				$where[] = ['rd.name', '=', $request->name];
				$name = $request->name;
			} else {
				$name = '';
			}
			
			if ($request->order) {
				$order = $request->order;
			} else {
				$order = 'asc';
			}
			
			$limit = session('settings.limit', 25);
			
			$default_language = session('default_language');
			$where[] = ['rd.lang', '=', $default_language];
			
			$sort_name = url('admin/regions', ['sort' => 'name', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_status = url('admin/regions', ['sort' => 'status', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			
			$regions = Regions::select('regions.id', 'regions.status', 'rd.name')->join('region_description as rd', 'rd.region_id', '=', 'regions.id')->where($where)->orderBy('rd.name')->paginate($limit);
			
			$this->breadcrumbs->addCrumb('Регионы', url('admin/regions') . $this->params);
			$breadcrumbs = $this->breadcrumbs->render();
			$params = $this->params;
			$params_array = $this->params_array;
			$sort = 'name';
			
			return view('pages.regions', compact('params', 'params_array', 'breadcrumbs', 'sort_name', 'name', 'sort_status', 'regions', 'status', 'sort', 'order'));
		}
		
		public function add() {
			$langs = Languages::orderBy('name', 'asc')->get();
			$this->breadcrumbs->addCrumb('Регионы', url('admin/regions') . $this->params);
			$this->breadcrumbs->addCrumb('Создать', url('admin/region_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.region-edit', ['breadcrumbs' => $breadcrumbs, 'langs' => $langs, 'slug' => old('slug'), 'meta' => old('meta'), 'status' => old('status'), 'action' => asset('admin/region_save') . $this->params, 'id' => '']);
		}
		
		public function edit($id)
		{
			$region = Regions::with('meta')->where('id', $id)->first();
			
			if (!empty($region)) {
				$langs = Languages::orderBy('name', 'asc')->get();
				
				$meta = [];
				
				foreach ($region->meta as $description) {
					$meta[$description->lang] = $description;
				}
				
				$this->breadcrumbs->addCrumb('Регионы', url('admin/regions') . $this->params);
				$this->breadcrumbs->addCrumb('Создать', url('admin/region_add'));
				$breadcrumbs = $this->breadcrumbs->render();
				$action = asset('admin/region_save') . $this->params;
				
				return view('pages.region-edit', compact('breadcrumbs', 'langs', 'meta', 'status', 'slug', 'action', 'id'));
			} else {
				return redirect('admin/regions' . $this->params)->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete(Request $request) {
			if ($request->selected) {
				foreach ($request->selected as $s) {
					Regions::where('id', $s)->delete();
					RegionDescription::where('region_id', $s)->delete();
				}
				
				$message = 'Операция успешна';
				$type = 'success';
			} else {
				$message = 'Выделите пункты для удаления';
				$type = 'error';
			}
			
			return redirect('admin/regions' . $this->params)->with($type, $message);
		}
		
		public function save(Request $request) {
			$this->validate($request, [
				'slug' => 'required|not_in:sort-name,sort-popular,sort-price,page,price,news,catalog|max:255|unique:regions,slug' . (!is_null($request->id) ? ',' . $request->id . ',id' : '') .'|alpha_dash',
				'meta.*.name' => 'required',
				'meta.*.format1' => 'required',
				'meta.*.format2' => 'required',
				'meta.*.meta_title' => 'required'
			]);
			
			if (!is_null($request->id)) {
				$region['slug'] = $request->slug;
				$region['status'] = $request->status ? $request->status : 0;
				
				Regions::where('id', $request->id)->update($region);
				
				RegionDescription::where('region_id', $request->id)->delete();
				
				foreach ($request->meta as $lang => $meta) {
					$rd = new RegionDescription;
					$rd->lang = $lang;
					$rd->region_id = $request->id;
					$rd->name = $meta['name'];
					$rd->format1 = $meta['format1'];
					$rd->format2 = $meta['format2'];
					$rd->format3 = !empty($meta['format3']) ? $meta['format3'] : '';
					$rd->meta_title = $meta['meta_title'];
					$rd->meta_description = !empty($meta['meta_description']) ? $meta['meta_description'] : '';
					$rd->meta_keywords = !empty($meta['meta_keywords']) ? $meta['meta_keywords'] : '';
					
					$rd->save();
				}
			} else {
				$region = new Regions;
				$region->slug = $request->slug;
				$region->status = $request->status ? $request->status : 0;
				
				$region->save();
				
				foreach ($request->meta as $lang => $meta) {
					$rd = new RegionDescription;
					$rd->lang = $lang;
					$rd->region_id = $region->id;
					$rd->name = $meta['name'];
					$rd->format1 = $meta['format1'];
					$rd->format2 = $meta['format2'];
					$rd->format3 = !empty($meta['format3']) ? $meta['format3'] : '';
					$rd->meta_title = $meta['meta_title'];
					$rd->meta_description = !empty($meta['meta_description']) ? $meta['meta_description'] : '';
					$rd->meta_keywords = !empty($meta['meta_keywords']) ? $meta['meta_keywords'] : '';
					
					$rd->save();
				}
			}
			
			$regions = Regions::with('meta')->select('id', 'slug')->where('status', 1)->get()->keyBy('slug')->toArray();
			
			foreach ($regions as &$region) {
				if (!empty($region['meta'])) {
					foreach ($region['meta'] as $key => $meta) {
						$region['meta'][$meta['lang']] = [
							'name' => $meta['name'],
							'format1' => $meta['format1'],
							'format2' => $meta['format1'],
							'format3' => $meta['format1'],
							'meta_title' => $meta['meta_title'],
							'meta_description' => $meta['meta_description'],
							'meta_keywords' => $meta['meta_keywords']
						];
						
						unset($region['meta'][$key]);
					}
				}
			}
			
			Cache::put('regions', $regions);
			
			return redirect('admin/regions' . $this->params)->with('success', 'Операция успешна');
		}
	}
