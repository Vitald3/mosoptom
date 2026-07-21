<?php

namespace App\Http\Controllers\Extensions\Module;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Extensions;
use App\Models\Elements;
use App\Models\LayoutExtension;
use App\Models\Languages;

class SliderController extends Controller {
    public $title = 'Слайдер';
    public $slug = 'slider';
    public $type = 'module';
    private $links = [];
    private $scripts = [];
    private $media = [];
    private $lang;

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

    public function index($setting, $media = []) {
        if ($media) {
            $this->media = $media;
        }

        static $module = 0;

        $data['sliders'] = [];
        $html_id = [];

        foreach ($setting['data'] as $slider) {
            if (!empty($slider['html_id'])) {
                if (!isset($html_id[$slider['html_id']])) {
                    $html = Extensions::where('id', $slider['html_id'])->value('setting');

                    if (!empty($html)) {
                        if (!empty($html['description'][$this->lang])) {
                            $description = $html['description'][$this->lang];

                            $this->setHtmlStyle($html);

                            $html_id[$slider['html_id']] = [
                                'html' => view('pages.site.extensions.html', ['html' => $description])
                            ];

                            $data['sliders'][] = $html_id[$slider['html_id']];
                        }
                    }
                } else {
                    $data['sliders'][] = $html_id[$slider['html_id']];
                }
            } else {
                if (!empty($slider['image']))
                $data['sliders'][] = [
                    'title' => !empty($slider[$this->lang]['title']) ? $slider[$this->lang]['title'] : '',
                    'text' => !empty($slider[$this->lang]['text']) ? $slider[$this->lang]['text'] : '',
                    'button' => !empty($slider[$this->lang]['button']) ? $slider[$this->lang]['button'] : '',
                    'button_href' => !empty($slider[$this->lang]['button_href']) ? $slider[$this->lang]['button_href'] : '',
                    'a' => !empty($slider[$this->lang]['a']) ? $slider[$this->lang]['a'] : '',
                    'a_href' => !empty($slider[$this->lang]['a_href']) ? $slider[$this->lang]['a_href'] : '',
                    'image' => asset($slider['image'])
                ];
            }
        }

        if ($data['sliders']) {
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
	
			$script = 'var slider_' . $module . ' = $(\'.slider-' . $module . ' .owl-carousel\');';
	
			$script .= 'slider_' . $module . '.owlCarousel({
                loop: ' . (isset($setting['loop']) ? 'true' : 'false') . ',
                items: ' . (isset($setting['items']) ? $setting['items'] : 1) . ',
                ' . (isset($setting['items']) && $setting['items'] == 1 ? 'singleItem: true,' : '') . '
                nav: ' . (isset($setting['nav']) ? 'true' : 'false') . ',
                ' . (isset($setting['nav']) ? 'navText: [\'<span style="display: inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none"><circle xmlns="http://www.w3.org/2000/svg" cx="20" cy="20" r="18" transform="rotate(-90 20 20)" fill="white" stroke-dashoffset="113.04" stroke-dasharray="113.04" stroke="#54B0AC" stroke-width="3"/><g clip-path="url(#clip0_432_54444)"><path d="M16.5815 20C16.5815 19.7849 16.6637 19.5699 16.8275 19.4059L21.9873 14.2462C22.3155 13.9179 22.8477 13.9179 23.1758 14.2462C23.5039 14.5743 23.5039 15.1063 23.1758 15.4346L18.6101 20L23.1756 24.5654C23.5037 24.8936 23.5037 25.4256 23.1756 25.7537C22.8476 26.0821 22.3154 26.0821 21.9872 25.7537L16.8274 20.594C16.6635 20.43 16.5815 20.215 16.5815 20Z" fill="#54B0AC"/></g><defs><clipPath id="clip0_432_54444"><rect width="12" height="12" fill="white" transform="translate(26 14) rotate(90)"/></clipPath></defs></svg></span>\', \'<span style="display: inline-block;transform: rotate(180deg)"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none"><circle xmlns="http://www.w3.org/2000/svg" cx="20" cy="20" r="18" transform="rotate(-90 20 20)" fill="white" stroke-dashoffset="113.04" stroke-dasharray="113.04" stroke="#54B0AC" stroke-width="3"><animate restart="always" xmlns="http://www.w3.org/2000/svg" attributeName="stroke-dashoffset" dur="5s" begin="0" repeatCount="indefinite" values="113.04;0"/><\'+\'/circle><g clip-path="url(#clip0_432_54444)"><path d="M16.5815 20C16.5815 19.7849 16.6637 19.5699 16.8275 19.4059L21.9873 14.2462C22.3155 13.9179 22.8477 13.9179 23.1758 14.2462C23.5039 14.5743 23.5039 15.1063 23.1758 15.4346L18.6101 20L23.1756 24.5654C23.5037 24.8936 23.5037 25.4256 23.1756 25.7537C22.8476 26.0821 22.3154 26.0821 21.9872 25.7537L16.8274 20.594C16.6635 20.43 16.5815 20.215 16.5815 20Z" fill="#54B0AC"/></g><defs><clipPath id="clip0_432_54444"><rect width="12" height="12" fill="white" transform="translate(26 14) rotate(90)"/></clipPath></defs></svg></span>\'],' : '') . '
                dots: ' . (isset($setting['dots']) ? 'true' : 'false') . ',
                autoplay:true,
                autoplayTimeout:5000
            });';
	
			$this->setScript([
				'text' => $script
			]);

            return view('pages.site.extensions.module.slider', $data);
        }
    }

    public function add() {
        $langs = Languages::orderBy('name')->get();
        $extensions = Extensions::select('id', 'name')->where('code', 'like', '%html%')->orderBy('created_at')->get()->keyBy('id');

        return ['extensions' => $extensions, 'langs' => $langs, 'setting' => (array)old('setting'), 'name' => old('name'), 'status' => old('status'), 'action' => asset('admin/extension/module/slider/save'), 'id' => ''];
    }

    public function edit(Request $request)
    {
        $extension = Extensions::where('id', $request->id)->first();

        if (!empty($extension)) {
            $extensions = Extensions::select('id', 'name')->where('code', 'like', '%html%')->orderBy('created_at')->get()->keyBy('id');
            $langs = Languages::orderBy('name')->get();
            return ['extensions' => $extensions, 'langs' => $langs, 'setting' => old('setting') ? (array)old('setting') : $extension->setting, 'name' => old('name') ? old('name') : $extension->name, 'status' => old('status') ? old('status') : $extension->status, 'action' => asset('admin/extension/module/slider/save/' . $request->id), 'id' => $request->id];
        } else {
            return redirect('admin/extensions')->with('error', 'Идентификатор не найден');
        }
    }

    public function getHtmlStyle() {
        return $this->media;
    }

    private function setHtmlStyle($extensions) {
        if (!empty($extensions)) {
            $ids = [];

            foreach ($extensions['data'] as $extension) {
                if (!empty($extension['element_id'])) {
                    $ids[] = $extension['element_id'];
                } elseif (!empty($extension['menu_id'])) {
                    $menu_extension = Extensions::where([['id', $extension['menu_id']], ['setting->description', '!=', '']])->value('setting');

                    if (!empty($menu_extension['css'])) {
                        $this->media[''][] = $menu_extension['css'];
                    }

                    foreach ($menu_extension['data'] as $menu) {
                        $ids[] = $menu['element_id'];
                    }
                }
            }

            if ($ids) {
                $ids = array_unique($ids, SORT_REGULAR);
            } else {
                return '';
            }

            if (!empty($extensions['css'])) {
                $this->media[''][] = $extensions['css'];
            }

            $media_text = [0 => '@media (max-width: 767px) {', 1 => '@media (min-width: 768px) and (max-width: 991px) {', 2 => '@media (min-width: 992px) and (max-width: 1199px) {', 3 => '@media (min-width: 1200px) {', 4 => '', 5 => 'hover'];

            $settings = Elements::select('setting', 'class')->where('setting', '!=', '')->whereIn('id', $ids)->get();

            if (!$settings->isEmpty()) {
                foreach ($settings as $id => $setting) {
                    foreach ($setting['setting'] as $key => $setting2) {
                        $style = [];

                        foreach ($setting2 as $property => $value) {
                            if (strpos($property, ':auto') !== false) {
                                $style[$setting['class']][] = $property;
                            } else {
                                $style[$setting['class']][] = $property . ':' . $value;
                            }
                        }

                        if ($style && isset($media_text[$key])) {
                            $this->media[$media_text[$key]][$setting['class']] = '.' . $setting['class'] . ($key == 5 ? ':hover' : '') . '{' . implode(';', $style[$setting['class']]) . '}';
                        }
                    }
                }
            }
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
			'setting.data.*.image' => 'required'
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
