<?php

namespace App\Http\Controllers\Extensions\Module;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Extensions;
use App\Models\LayoutExtension;
use App\Models\Languages;
use App\Models\Categories;
use App\Models\Pages;
use App\Models\PageCategories;

class MenuController extends Controller {
    public $title = 'Меню';
    public $slug = 'menu';
    public $type = 'module';
    private $styles;
    private $lang;

    public function __construct() {
		$this->settings = session('settings');
        $this->default_language = session('default_language');
        $this->lang = session('lang');
    }

    public function getModule($id) {
        $extension = Extensions::where('id', $id)->where('status', 1)->first();

        if (!empty($extension->setting)) {
            return $this->index($extension->setting);
        }
    }

    public function getItems($id) {
        $extension = Extensions::where('id', $id)->first();

        if (!empty($extension->setting)) {
            return $this->items($extension->setting);
        }
    }

    public function getHtmlStyle() {
        return $this->styles;
    }

    private function setHtmlStyle($style) {
        $this->styles = $style;
    }

    public function index($setting) {
        if (!empty($setting['class'])) {
            $data['class'] = $setting['class'];
        } else {
            $data['class'] = '';
        }

        if (!empty($setting['style']) && !empty($setting['class'])) {
            $this->setHtmlStyle('<style>' . $setting['style'] . '</style>');
        }

        if ($items = $this->items($setting)) {
            $data['items'] = $this->list($items);
            return view('pages.site.extensions.module.menu', $data);
        }
    }

    private function list($items) {
        $html = '';

        foreach($items as $item) {
            $html .= '<li><a href="' . $item['url'] . '">' . $item['name'] . '</a>';

            if($item['children']) {
                $html .= '<ul class="list-un-styled">' . t($item['children']) . '</ul>';
            }

            $html .= '</li>';
        }

        return $html;
    }

    private function items($setting) {
        if (empty($setting['hierarhy'])) return [];
		$PathRouteService = app(\App\Helpers\PathRouteService::class);

        $hierarhy = json_decode($setting['hierarhy'], true);
        $settings = $setting['data'];

        if (empty($settings) || empty($hierarhy)) return [];

        $items = [];

        foreach ($hierarhy as $level) {
            if (isset($settings[$level['id']])) {
                foreach ($settings[$level['id']] as $id => $s) {
                    if (!empty($s[$this->lang]['url']) && strpos($s[$this->lang]['url'], 'http://') === false &&
						strpos($s[$this->lang]['url'], 'https://') === false &&
						strpos($s[$this->lang]['url'], $this->lang . '_id=') !== false) {
                        $url = $PathRouteService->getRoute($s[$this->lang]['url']);
                    } else {
                        $url = $s[$this->lang]['url'];
                    }

                    $items[] = [
                        'id' => $id,
                        'name' => !empty($s[$this->lang]['text']) ? $s[$this->lang]['text'] : '',
                        'url' => $url,
                        'children' => isset($level['children']) ? $this->r($level['children'], $settings) : []
                    ];
                }
            }
        }

        return $items;
    }

    public function r($children, $settings) {
		$PathRouteService = app(\App\Helpers\PathRouteService::class);
        $items = [];

        foreach ($children as $level) {
            if (isset($settings[$level['id']])) {
                foreach ($settings[$level['id']] as $id => $s) {
                    if (!empty($s[$this->lang]['url']) && strpos($s[$this->lang]['url'], 'http://') === false && strpos($s[$this->lang]['url'], 'https://') === false) {
                        $url = $PathRouteService->getRoute($s[$this->lang]['url']);
                    } else {
                        $url = $s[$this->lang]['url'];
                    }

                    $items[] = [
                        'id' => $id,
                        'name' => !empty($s[$this->lang]['text']) ? $s[$this->lang]['text'] : '',
                        'url' => $url,
                        'children' => isset($level['children']) ? $this->r($level['children'], $settings) : []
                    ];
                }
            }
        }

        return $items;
    }

    private function getCategories($model)
    {
        $categories_name = [];

        $categories = $model::with('metaLang:category_id,name')->select('parent_id', 'id')->where('status', 1)->get()->keyBy('id');

        foreach ($categories as $id => $category) {
            $name = $this->getCategory($category, $categories);

            $categories_name[$id] = implode(' > ', $name);
        }

        return $categories_name;
    }

    private function getCategory($collection, $collections, array $name = [])
    {
        $n = !empty($collection->metaLang->name) ? $collection->metaLang->name : '';
        array_unshift($name, $n);

        if (!is_null($collection->parent_id) && isset($collections[$collection->parent_id])) {
            $name = $this->getCategory($collections[$collection->parent_id], $collections, $name);
        }

        return $name;
    }

    public function add() {
        $default_language = $this->default_language;
        $langs = Languages::orderBy('name')->get();
        $categories = $this->getCategories(new Categories);
        $page_categories = $this->getCategories(new PageCategories);
        $pages = Pages::select('pages.id', 'pd.name')->leftjoin('page_description as pd', 'pd.page_id', '=', 'pages.id')->where([['pages.status', 1],['pd.lang', config('app.locale')]])->orderBy('pd.name')->pluck('pd.name', 'pages.id');

        return ['default_language' => $default_language, 'pages' => $pages, 'page_categories' => $page_categories, 'categories' => $categories, 'langs' => $langs, 'setting' => (array)old('setting'), 'name' => old('name'), 'status' => old('status'), 'action' => asset('admin/extension/module/menu/save'), 'id' => ''];
    }

    public function edit(Request $request) {
        $default_language = $this->default_language;
        $extension = Extensions::where('id', $request->id)->first();

        if (!empty($extension)) {
            $langs = Languages::orderBy('name')->get();
            $categories = $this->getCategories(new Categories);
            $page_categories = $this->getCategories(new PageCategories);
            $pages = Pages::select('pages.id', 'pd.name')->leftjoin('page_description as pd', 'pd.page_id', '=', 'pages.id')->where([['pages.status', 1],['pd.lang', config('app.locale')]])->orderBy('pd.name')->pluck('pd.name', 'pages.id');

            return ['default_language' => $default_language, 'pages' => $pages, 'page_categories' => $page_categories, 'categories' => $categories, 'langs' => $langs, 'setting' => old('setting') ? (array)old('setting') : $extension->setting, 'name' => old('name') ? old('name') : $extension->name, 'status' => old('status') ? old('status') : $extension->status, 'action' => asset('admin/extension/module/menu/save/' . $request->id), 'id' => $request->id];
        } else {
            return redirect('admin/extensions')->with('error', 'Идентификатор не найден');
        }
    }

    public function ajax(Request $request)
    {
        $json = [];

        if (!is_null($request->id) && !is_null($request->type)) {
            if ($request->type == 1) {
                $model = new Categories;
                $url = 'category_';
            } else if ($request->type == 2) {
                $model = new PageCategories;
                $url = 'page_category_';
            } else if ($request->type == 3) {
                $model = new Pages;
                $url = 'page_';
            } else {
                $json['error'] = 'Информация не найдена';
            }

            if (!$json) {
                $langs = Languages::orderBy('name')->get();
                $query = $model::with('meta')->where([['status', 1],['id', $request->id]])->get();

                foreach ($query as $d) {
                    foreach ($langs as $l) {
                        $json[$l->code]['url'] = $url . $l->code . '_id=' . $d->id;
                        $json[$l->code]['image'] = asset($l->image);
                    }

                    foreach ($d->meta as $meta) {
                        $json[$meta->lang]['name'] = $meta->name;
                    }
                }
            }
        } else {
            $json['error'] = 'Информация не найдена';
        }

        return response()->json($json);
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
