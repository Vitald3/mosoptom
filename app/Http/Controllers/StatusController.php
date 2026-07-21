<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Status;
	use App\Models\StatusDescription;
	use App\Models\Languages;
	
	class StatusController extends Controller
	{
		private $settings = [];
		private $breadcrumbs;
		
		public function __construct() {
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			
			$classes = array('breadcrumb', 'breadcrumb-item');
			$this->breadcrumbs->addCssClasses($classes);
			$this->breadcrumbs->setDivider('');
			
			$this->breadcrumbs->addCrumb(__('locale.home'), url('admin'));
			$this->settings = session('settings');
		}
		
		public function index(Request $request){
			if ($request->order) {
				$order = $request->order;
			} else {
				$order = 'asc';
			}
			
			$limit = session('settings.limit', 25);
			
			$sort_name = url('admin/statuses', ['sort' => 'st.name', 'order' => $order == 'asc' ? 'desc' : 'asc']);
			
			$status = Status::join('status_description as st', 'st.status_id', '=', 'status.id')->select('st.name', 'status.id', 'status.type', 'status.color')->orderBy('st.name', $order)->paginate($limit);
			
			$this->breadcrumbs->addCrumb('Статусы', url('admin/statuses'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.status', compact('breadcrumbs', 'status', 'sort_name', 'order'));
		}
		
		public function add() {
			$this->breadcrumbs->addCrumb('Статусы', url('admin/statuses'));
			$this->breadcrumbs->addCrumb('Создать', url('admin/status_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			$langs = Languages::orderBy('name', 'asc')->get();
			
			return view('pages.status-edit', ['langs' => $langs, 'breadcrumbs' => $breadcrumbs, 'type' => old('type'), 'color' => old('color'), 'meta' => (array)old('meta'), 'action' => asset('admin/status_save'), 'id' => '']);
		}
		
		public function edit($id)
		{
			$data = Status::with('meta:status_id,name,lang')->where('id', $id)->first();
			
			if (!empty($data)) {
				$langs = Languages::orderBy('name', 'asc')->get();
				$this->breadcrumbs->addCrumb('Статусы', url('admin/statuses'));
				$this->breadcrumbs->addCrumb('Редактировать', url('admin/status/' . $id));
				$breadcrumbs = $this->breadcrumbs->render();
				
				extract($data->toArray());
				
				$meta = [];
				
				foreach ($data->meta as $description) {
					$meta[$description['lang']] = $description;
				}
				
				$action = asset('admin/status_save');
				
				return view('pages.status-edit', compact('meta', 'langs', 'breadcrumbs', 'type', 'color', 'meta', 'id', 'action'));
			} else {
				return redirect('admin/statuses')->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete(Request $request) {
			if ($request->selected) {
				foreach ($request->selected as $s) {
					$count = Orders::where('order_status_id', $s)->count();
					
					if ($count) {
						$message[] = 'Статус ID ' . $s . ' используется в ' . num_decline($count, ['заказе', 'заказах']);
						$type = 'error';
					} else {
						Status::where('id', $s)->delete();
						StatusDescription::where('status_id', $s)->delete();
					}
				}
				
				$message = 'Операция успешна';
				$type = 'success';
			} else {
				$message = 'Выделите пункты для удаления';
				$type = 'error';
			}
			
			if (is_array($message)) $message = implode('<br>', $message);
			
			return redirect('admin/statuses')->with($type, $message);
		}
		
		public function save(Request $request) {
			$this->validate($request, [
				'meta.*.name' => 'required',
				'type' => 'required'
			]);
			
			if (!is_null($request->id)) {
				$status['type'] = $request->type;
				$status['color'] = $request->color;
				
				Status::where('id', $request->id)->update($status);
				
				StatusDescription::where('status_id', $request->id)->delete();
				
				foreach ($request->meta as $lang => $meta) {
					$sd = new StatusDescription;
					$sd->lang = $lang;
					$sd->status_id = $request->id;
					$sd->name = $meta['name'];
					
					$sd->save();
				}
			} else {
				$status = new status;
				$status->type = $request->type;
				$status->color = $request->color;
				
				$status->save();
				
				foreach ($request->meta as $lang => $meta) {
					$sd = new StatusDescription;
					$sd->lang = $lang;
					$sd->status_id = $status->id;
					$sd->name = $meta['name'];
					
					$sd->save();
				}
			}
			
			return redirect('admin/statuses')->with('success', 'Операция успешна');
		}
	}