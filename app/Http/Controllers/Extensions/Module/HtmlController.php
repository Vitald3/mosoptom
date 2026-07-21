<?php

namespace App\Http\Controllers\Extensions\Module;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Extensions;
use App\Models\LayoutExtension;
use App\Models\Languages;
use App\Models\Elements;

class HtmlController extends Controller {
    public $title = 'HTML - конструтор';
    public $slug = 'html';
    public $type = 'module';
    private $media = [];

    public function index($setting, $media = []) {
        if ($media) {
            $this->media = $media;
        }

        if (!empty($setting['description'][session('lang')])) {
            $data['html'] = $setting['description'][session('lang')];
        } else {
            $data['html'] = '';
        }

        $this->setHtmlStyle($setting);

        return view('pages.site.extensions.module.html', $data);
    }

    public function add() {
        $langs = Languages::orderBy('name')->get();
        $elements = Elements::select('id', 'name', 'code', 'setting')->orderBy('name')->get();
        $extension_last = Extensions::select('id')->orderBy('created_at', 'desc')->limit(1)->value('id');
        $extensions = Extensions::select('id', 'name')->orderBy('created_at')->get()->keyBy('id');

        return ['extensions' => $extensions, 'extension_last' => $extension_last+1, 'default_language' => session('default_language'), 'elements' => $elements, 'langs' => $langs, 'setting' => (array)old('setting'), 'name' => old('name'), 'status' => old('status'), 'action' => asset('admin/extension/module/html/save'), 'id' => ''];
    }

    public function edit(Request $request)
    {
        $extension = Extensions::where('id', $request->id)->first();

        if (!empty($extension)) {
            $langs = Languages::orderBy('name')->get();
            $elements = Elements::select('id', 'name', 'code', 'setting')->orderBy('name')->get()->keyBy('id');
            $extension_last = Extensions::select('id')->orderBy('created_at', 'desc')->limit(1)->value('id');
            $extensions = Extensions::select('id', 'name')->where([['id', '!=', $request->id], ['code', 'like', '%html%']])->orderBy('created_at')->get()->keyBy('id');

            return ['extensions' => $extensions, 'extension_last' => $extension_last + 1, 'default_language' => session('default_language'), 'elements' => $elements, 'langs' => $langs, 'setting' => old('setting') ? (array)old('setting') : $extension->setting, 'name' => old('name') ? old('name') : $extension->name, 'status' => old('status') ? old('status') : $extension->status, 'action' => asset('admin/extension/module/html/save/' . $request->id), 'id' => $request->id];
        } else {
            return redirect('admin/extensions')->with('error', 'Идентификатор не найден');
        }
    }

    public function r($children, $elements) {
        $elements2 = [];

        foreach ($children as $setting) {
            if (!empty($setting['menu_id'])) {
                $menu = Extensions::where([['id', $setting['menu_id']], ['setting->description', '!=', '']])->value('setting');

                if (!empty($menu) && !empty($menu['hierarchy'])) {
                    if (!empty($menu['css'])) {
                        $menu_css = $menu['css'];

                        if (strpos($menu_css, '{') === false) $menu_css = '';
                    } else {
                        $menu_css = '';
                    }

                    if ($hierarchy = $this->r($menu['hierarchy'], $elements)) {
                        $hierarchy = isset($hierarchy[0]) ? $hierarchy[0] : [];
                    } else {
                        $hierarchy = [];
                    }

                    $elements2[] = [
                        'menu' => $menu['description'],
                        'css' => $menu_css,
                        $hierarchy
                    ];
                }
            } else {
                $elements2[] = [
                    'element_id' => $setting['element_id'],
                    'class' => $elements[$setting['element_id']]['class'],
                    'code' => trim($setting['code']),
                    'text' => $setting['text'],
                    'link' => $setting['link'],
                    'img' => !empty($setting['img']) ? asset($setting['img']) : '',
                    'children' => !empty($setting['children']) ? $this->r($setting['children'], $elements) : []
                ];
            }
        }

        return $elements2;
    }

    public function ajax(Request $request)
    {
        $json = ['error' => 'Информация не найдена'];

        if (!is_null($request->preview)) {
            $elements2 = [];
            $hierarchy = json_decode(urldecode(base64_decode($request->preview)), true);

            if (!is_null($request->css)) {
                $css = urldecode(base64_decode($request->css));

                if (strpos($css, '{') === false) $css = '';
            } else {
                $css = '';
            }

            if (!is_null($request->parent_class)) {
                $parent_class = urldecode(base64_decode($request->parent_class));
                $parent_class = trim($parent_class, '.');
            }  else {
                $parent_class = '';
            }

            $elements = Elements::where('setting', '!=', '')->get()->keyBy('id');

            $langs = Languages::orderBy('name')->get();

            foreach ($hierarchy as $setting) {
                if (!empty($setting['menu_id'])) {
                    $menu = Extensions::where([['id', $setting['menu_id']], ['setting->description', '!=', '']])->value('setting');

                    if (!empty($menu) && !empty($menu['hierarchy'])) {
                        if (!empty($menu['css'])) {
                            $menu_css = $menu['css'];

                            if (strpos($menu_css, '{') === false) $menu_css = '';
                        } else {
                            $menu_css = '';
                        }

                        if ($hierarchy = $this->r($menu['hierarchy'], $elements)) {
                            $hierarchy = isset($hierarchy[0]) ? $hierarchy[0] : [];
                        } else {
                            $hierarchy = [];
                        }

                        $elements2[] = [
                            'menu' => $menu['description'],
                            'css' => $menu_css,
                            $hierarchy
                        ];
                    }
                } else {
                    $elements2[] = [
                        'element_id' => $setting['element_id'],
                        'class' => $elements[$setting['element_id']]['class'],
                        'code' => trim($setting['code']),
                        'text' => $setting['text'],
                        'link' => $setting['link'],
                        'img' => !empty($setting['img']) ? asset($setting['img']) : '',
                        'children' => !empty($setting['children']) ? $this->r($setting['children'], $elements) : []
                    ];
                }
            }

            return view('pages.extensions.html.preview', ['langs' => $langs, 'elements' => $elements2, 'settings' => $elements, 'css' => $css, 'parent_class' => $parent_class]);
        }

        return response()->json($json);
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
            'name' => 'required'
        ]);

        $setting = [];

        if (!is_null($request->setting)) {
            foreach ($request->setting as $key => $s) {
                if ($key == 'hierarchy') {
                    $s = json_decode($s, true);
                }

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
