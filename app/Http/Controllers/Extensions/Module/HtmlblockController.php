<?php
	
	namespace App\Http\Controllers\Extensions\Module;
	use App\Http\Controllers\Controller;
	
	use Illuminate\Http\Request;
	use App\Models\Extensions;
	use App\Models\LayoutExtension;
	use App\Models\Languages;
	use App\Models\Elements;
	
	class HtmlblockController extends Controller {
		public $title = 'HTML - Блок';
		public $slug = 'htmlblock';
		public $type = 'module';
		private $media = [];
		
		public function index($setting, $media = []) {
			static $module = 0;
			
			if ($media) {
				$this->media = $media;
			}
			
			if (!empty($setting['html'][session('lang')])) {
				$data['html'] = $setting['html'][session('lang')];
				$data['module'] = $module;
				$module++;
				
				$this->setHtmlStyle($setting);
				
				return view('pages.site.extensions.module.' . $this->slug, $data);
			}
		}
		
		public function getModule($id) {
			$extension = Extensions::where('id', $id)->where('status', 1)->first();
			
			if (!empty($extension->setting)) {
				return $this->index($extension->setting);
			}
		}
		
		public function add() {
			$langs = Languages::orderBy('name')->get();
			
			return ['default_language' => session('default_language'), 'langs' => $langs, 'setting' => (array)old('setting'), 'name' => old('name'), 'status' => old('status'), 'action' => asset('admin/extension/module/' . $this->slug . '/save'), 'id' => ''];
		}
		
		public function edit(Request $request)
		{
			$extension = Extensions::where('id', $request->id)->first();
			
			if (!empty($extension)) {
				$langs = Languages::orderBy('name')->get();
				
				return ['default_language' => session('default_language'), 'langs' => $langs, 'setting' => old('setting') ? (array)old('setting') : $extension->setting, 'name' => old('name') ? old('name') : $extension->name, 'status' => old('status') ? old('status') : $extension->status, 'action' => asset('admin/extension/module/' . $this->slug . '/save/' . $request->id), 'id' => $request->id];
			} else {
				return redirect('admin/extensions')->with('error', 'Идентификатор не найден');
			}
		}

		public function ajax(Request $request)
		{
			$json = ['error' => 'Информация не найдена'];
			
			if (!is_null($request->html)) {
				$langs = Languages::orderBy('name')->get();
				
				return view('pages.extensions.' . $this->type . '.' . $this->slug . '.preview', ['langs' => $langs, 'html' => $request->html, 'css' => $request->css]);
			}
			
			return response()->json($json);
		}
		
		public function getHtmlStyle() {
			return $this->media;
		}
		
		private function setHtmlStyle($extensions) {
			if (!empty($extensions['css'])) {
				$this->media[''][] = $extensions['css'];
			}
		}
		
		public function delete(Request $request) {
			if ($request->code && $request->id) {
				Extensions::where('code', $request->code)->where('id', $request->id)->delete();
				LayoutExtension::where('code', $request->code . '.' . $request->id)->delete();
				return 'Модуль ' . $this->title . ' успешно удален';
			} else {
				return 'Произошла ошибка';
			}
		}
		
		public function save(Request $request) {
			$this->validate($request, [
				'name' => 'required',
				'setting.html' => 'required'
			]);
			
			$setting = [];
			
			if (!is_null($request->setting)) {
				foreach ($request->setting as $key => $s) {
					if (!is_null($s)) $setting[$key] = !is_array($s) ? $s : array_filter($s);
				}
			}
			
			if (!empty($request->id)) {
				$extensions['name'] = $request->name;
				$extensions['code'] = $this->slug;
				$extensions['setting'] = $setting;
				$extensions['status'] = $request->status ? $request->status : 0;
				
				Extensions::where('id', $request->id)->update($extensions);
			} else {
				$extensions = new Extensions;
				$extensions->name = $request->name;
				$extensions->code = $this->slug;
				$extensions->setting = $setting;
				$extensions->status = $request->status ? $request->status : 0;
				
				$extensions->save();
			}
			
			return 'Модуль ' . $this->title . ' успешно изменен';
		}
	}