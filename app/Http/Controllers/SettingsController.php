<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Settings;
	use App\Models\Status;
	use App\Models\CustomerGroups;
	use App\Models\Languages;
	use App\Models\Currencies;
	use App\Models\Layouts;
	use App\Models\Pages;
	
	class SettingsController extends Controller {
		public function __construct() {
			$this->settings = session('settings');
			$this->lang = session('lang');
			
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			
			$classes = array('breadcrumb', 'breadcrumb-item');
			$this->breadcrumbs->addCssClasses($classes);
			$this->breadcrumbs->setDivider('');
			
			$this->breadcrumbs->addCrumb(__('locale.home'), url('admin'));
		}
		
		public function index() {
			$settings = $this->settings;
			$layouts = Layouts::orderBy('name', 'asc')->get();
			$langs = Languages::orderBy('name', 'asc')->get();
			$action2 = asset('admin/settings_add_image');
			$action = asset('admin/settings_save');
			$this->breadcrumbs->addCrumb('Настройки', url('admin/settings'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			$pages = Pages::select('id')->get();
			$currencies = Currencies::select('code', 'title')->get();
			$status = Status::join('status_description as st', 'st.status_id', '=', 'status.id')->select('status.id', 'st.name')->where('st.lang', $this->lang)->where('status.type', 1)->get();
			$customer_groups = CustomerGroups::join('customer_group_description as cgd', 'cgd.customer_group_id', '=', 'customer_groups.id')->select('customer_groups.id', 'cgd.name')->where('customer_groups.status', 1)->orderBy('cgd.name')->get();
			
			return view('pages.settings-edit', compact('customer_groups', 'breadcrumbs', 'status', 'currencies', 'layouts', 'pages', 'langs', 'action2', 'action', 'settings'));
		}
		
		public function contacts() {
			$contacts = Settings::where('code', 'contacts')->value('value');
			
			$langs = Languages::orderBy('name', 'asc')->get();
			$this->breadcrumbs->addCrumb('Контакты', url('admin/settings'));
			$breadcrumbs = $this->breadcrumbs->render();
			$action = asset('admin/settings_save');
			
			return view('pages.contacts-edit', compact('breadcrumbs', 'langs', 'action', 'contacts'));
		}
		
		public function save(Request $request) {
			$validate = [];
			
			if ($request->code == 'settings') {
				$validate = [
					$request->code . '.meta_title.*' => 'required|max:200',
					$request->code . '.meta_description.*' => 'required|max:500',
					$request->code . '.open.*' => 'required',
					$request->code . '.name.*' => 'required',
					$request->code . '.email' => 'required|email',
					$request->code . '.phone' => 'required',
					'code' => 'required'
				];
			} else if ($request->code == 'contacts') {
				$validate = [
					$request->code . '.meta_title.*' => 'required|max:200',
					$request->code . '.meta_description.*' => 'required|max:500',
					$request->code . '.open.*' => 'required',
					$request->code . '.address.*' => 'required',
					'code' => 'required'
				];
			}
			
			if ($validate) $this->validate($request, $validate);
			
			$settings = new Settings;
			$settings->code = $request->code;
			$settings->value = $request->{$request->code};
			
			Settings::where('code', $request->code)->delete();
			$settings->save();
			
			if ($request->code == 'settings') {
				$request->session()->forget('settings');
				$request->session()->put(['settings' => $request->{$request->code}]);
			}
			
			return redirect('admin/' . $request->code)->with('success', 'Операция успешна');
		}
		
		public function addImage(Request $request) {
			if ($request->hasFile('file')) {
				$files = $request->file('file');
				
				$images = [];
				
				if (!is_array($files)) {
					$name = $files->getClientOriginalName();
					$files->move('assets/site/img/settings', $name);
					$images[] = 'assets/site/img/settings/' . $name;
				} else {
					foreach ($files as $file) {
						$name = $file->getClientOriginalName();
						$file->move('assets/site/img/settings', $name);
						$images[] = 'assets/site/img/settings/' . $name;
					}
				}
				
				return response()->json($images);
			}
		}
	}
