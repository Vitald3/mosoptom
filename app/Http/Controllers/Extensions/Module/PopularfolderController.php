<?php
	
	namespace App\Http\Controllers\Extensions\Module;
	use App\Http\Controllers\Controller;
	
	use Illuminate\Http\Request;
	use App\Models\Settings;
	use App\Models\LayoutExtension;
	use App\Models\Languages;
	use Str;
	
	class PopularfolderController extends Controller {
		public $title = 'Популярные разделы';
		public $slug = 'popularfolder';
		public $type = 'setting';
		private $links = [];
		private $scripts = [];
		
		public function __construct() {
			$this->lang = session('lang');
		}
		
		public function getLinkStyle() {
			return $this->links;
		}
		
		private function setLinkStyle($link) {
			$this->links[] = $link;
		}
		
		public function getScript() {
			return $this->scripts;
		}
		
		private function setScript($script) {
			$this->scripts[] = $script;
		}
		
		public function index($setting) {
			static $module = 0;
			$PathRouteService = app(\App\Helpers\PathRouteService::class);
			
			$data = [];
			
			if (!empty($setting['elements'])) {
				foreach ($setting['elements'] as $element) {
					$data['elements'][] = [
						'name' => $element['name'],
						'text' => $element['text'],
						'url' => $PathRouteService->getRoute('category_' . $this->lang . '_id=' . $element['category_id']),
						'image' => $element['image']
					];
				}
			}
			
			if (!empty($setting['elements2'])) {
				foreach ($setting['elements2'] as $element) {
					$data['elements2'][] = [
						'name' => $element['name'],
						'text' => $element['text'],
						'url' => $PathRouteService->getRoute('product_' . $this->lang . '_id=' . $element['product_id']),
						'image' => $element['image']
					];
				}
			}
			
			if ($data) {
				$data['title'] = !empty($setting['title'][$this->lang]) ? $setting['title'][$this->lang] : '';
				
				$module++;
				
				$data['module'] = $module;
				
				$this->setLinkStyle(
					[
						'href' => asset('assets/site/css/owl.carousel.min.css'),
						'rel' => 'stylesheet'
					]
				);
				
				$this->setScript(
					[
						'src' => asset('assets/site/js/owl.carousel.min.js')
					]
				);
				
				$this->setLinkStyle(
					[
						'href' => asset('assets/site/css/popularfolder.css'),
						'rel' => 'stylesheet'
					]
				);
				
				$script = 'var element_' . $module . ' = $(\'.popular_folder' . $module . ' .elements\');';
				
				$script .= 'element_' . $module . '.owlCarousel({
                    loop: true,
                    items: 5,
                    margin: 40,
                    nav: true,
                    navText: [\'<span style="display: inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none"><circle xmlns="http://www.w3.org/2000/svg" cx="20" cy="20" r="18" transform="rotate(-90 20 20)" fill="white" stroke-dashoffset="113.04" stroke-dasharray="113.04" stroke="#54B0AC" stroke-width="3"/><g clip-path="url(#clip0_432_54444)"><path d="M16.5815 20C16.5815 19.7849 16.6637 19.5699 16.8275 19.4059L21.9873 14.2462C22.3155 13.9179 22.8477 13.9179 23.1758 14.2462C23.5039 14.5743 23.5039 15.1063 23.1758 15.4346L18.6101 20L23.1756 24.5654C23.5037 24.8936 23.5037 25.4256 23.1756 25.7537C22.8476 26.0821 22.3154 26.0821 21.9872 25.7537L16.8274 20.594C16.6635 20.43 16.5815 20.215 16.5815 20Z" fill="#54B0AC"/></g><defs><clipPath id="clip0_432_54444"><rect width="12" height="12" fill="white" transform="translate(26 14) rotate(90)"/></clipPath></defs></svg></span>\', \'<span style="display: inline-block;transform: rotate(180deg)"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none"><circle xmlns="http://www.w3.org/2000/svg" cx="20" cy="20" r="18" transform="rotate(-90 20 20)" fill="white" stroke-dashoffset="113.04" stroke-dasharray="113.04" stroke="#54B0AC" stroke-width="3"><animate restart="always" xmlns="http://www.w3.org/2000/svg" attributeName="stroke-dashoffset" dur="5s" begin="0" repeatCount="indefinite" values="113.04;0"/><\'+\'/circle><g clip-path="url(#clip0_432_54444)"><path d="M16.5815 20C16.5815 19.7849 16.6637 19.5699 16.8275 19.4059L21.9873 14.2462C22.3155 13.9179 22.8477 13.9179 23.1758 14.2462C23.5039 14.5743 23.5039 15.1063 23.1758 15.4346L18.6101 20L23.1756 24.5654C23.5037 24.8936 23.5037 25.4256 23.1756 25.7537C22.8476 26.0821 22.3154 26.0821 21.9872 25.7537L16.8274 20.594C16.6635 20.43 16.5815 20.215 16.5815 20Z" fill="#54B0AC"/></g><defs><clipPath id="clip0_432_54444"><rect width="12" height="12" fill="white" transform="translate(26 14) rotate(90)"/></clipPath></defs></svg></span>\'],
                    dots: false,
                    autoplay:true,
                    autoplayTimeout:5000,
                    responsiveClass:true,
                    responsive:{
                    1600:{
                        margin: 40,
                        autoWidth: true
                    },
                    960:{
                        margin: 30,
                        autoWidth: true
                    },
                    640:{
                        margin: 30,
                        items: 3,
                        autoWidth: true
                    },
                    320:{
                        margin: 30,
                        items: 2,
                        autoWidth: true
                    }
                    }
                });';
				
				$script .= 'var element2_' . $module . ' = $(\'.popular_folder' . $module . ' .elements2\');
				
				element2_' . $module . '.owlCarousel({
                    loop: true,
                    items: 2,
                    margin: 40,
                    nav: true,
                    navText: [\'<span style="display: inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none"><circle xmlns="http://www.w3.org/2000/svg" cx="20" cy="20" r="18" transform="rotate(-90 20 20)" fill="white" stroke-dashoffset="113.04" stroke-dasharray="113.04" stroke="#54B0AC" stroke-width="3"/><g clip-path="url(#clip0_432_54444)"><path d="M16.5815 20C16.5815 19.7849 16.6637 19.5699 16.8275 19.4059L21.9873 14.2462C22.3155 13.9179 22.8477 13.9179 23.1758 14.2462C23.5039 14.5743 23.5039 15.1063 23.1758 15.4346L18.6101 20L23.1756 24.5654C23.5037 24.8936 23.5037 25.4256 23.1756 25.7537C22.8476 26.0821 22.3154 26.0821 21.9872 25.7537L16.8274 20.594C16.6635 20.43 16.5815 20.215 16.5815 20Z" fill="#54B0AC"/></g><defs><clipPath id="clip0_432_54444"><rect width="12" height="12" fill="white" transform="translate(26 14) rotate(90)"/></clipPath></defs></svg></span>\', \'<span style="display: inline-block;transform: rotate(180deg)"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none"><circle xmlns="http://www.w3.org/2000/svg" cx="20" cy="20" r="18" transform="rotate(-90 20 20)" fill="white" stroke-dashoffset="113.04" stroke-dasharray="113.04" stroke="#54B0AC" stroke-width="3"><animate restart="always" xmlns="http://www.w3.org/2000/svg" attributeName="stroke-dashoffset" dur="5s" begin="0" repeatCount="indefinite" values="113.04;0"/><\'+\'/circle><g clip-path="url(#clip0_432_54444)"><path d="M16.5815 20C16.5815 19.7849 16.6637 19.5699 16.8275 19.4059L21.9873 14.2462C22.3155 13.9179 22.8477 13.9179 23.1758 14.2462C23.5039 14.5743 23.5039 15.1063 23.1758 15.4346L18.6101 20L23.1756 24.5654C23.5037 24.8936 23.5037 25.4256 23.1756 25.7537C22.8476 26.0821 22.3154 26.0821 21.9872 25.7537L16.8274 20.594C16.6635 20.43 16.5815 20.215 16.5815 20Z" fill="#54B0AC"/></g><defs><clipPath id="clip0_432_54444"><rect width="12" height="12" fill="white" transform="translate(26 14) rotate(90)"/></clipPath></defs></svg></span>\'],
                    dots: false,
                    autoplay:true,
                    autoplayTimeout:5000,
                    responsiveClass:true,
                    responsive:{
                    1600:{
                        margin: 40,
                    },
                    960:{
                        margin: 30,
                    },
                    640:{
                        margin: 0,
                        items: 1
                    },
                    320:{
                        margin: 30,
                        items: 1,
                        autoWidth: true
                    }
                    }
                });';
				
				$this->setScript([
					'text' => $script
				]);
				
				return view('pages.site.extensions.module.popularfolder', $data);
			}
		}
		
		public function edit() {
			$extension = Settings::where('code', 'extension.module.' . $this->slug)->value('value');
			$langs = Languages::orderBy('name')->get();
			return ['setting' => old('setting', $extension), 'langs' => $langs, 'action' => asset('admin/extension/module/' . $this->slug . '/save')];
		}
		
		public function delete(Request $request) {
			if ($request->code) {
				Settings::where('code', 'extension.module.' . $request->code)->delete();
				LayoutExtension::where('code', $request->code)->delete();
				return 'Модуль ' . $this->title . ' успешно удален';
			} else {
				return 'Произошла ошибка';
			}
		}
		
		public function save(Request $request) {
			$this->validate($request, [
				'setting.name' => 'required'
			]);
			
			$this->validate($request, [
				'setting.title.*' => 'required',
				'setting.elements.*.name' => 'required',
				'setting.elements.*.category_id' => 'required',
				'setting.elements2.*.name' => 'required',
				'setting.elements2.*.product_id' => 'required',
			]);
			
			$setting = [];
			
			if (!is_null($request->setting)) {
				foreach ($request->setting as $key => $s) {
					if (!is_null($s)) $setting[$key] = !is_array($s) ? $s : array_filter($s);
				}
			}
			
			Settings::where('code', 'extension.module.' . $this->slug)->delete();
			
			$settings = new Settings;
			$settings->code = 'extension.module.' . $this->slug;
			$settings->value = $setting;
			
			$settings->save();
			
			return 'Модуль ' . $this->title . ' успешно изменен';
		}
	}
