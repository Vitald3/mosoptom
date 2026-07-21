<?php

namespace App\Http\Controllers\Extensions\Module;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\LayoutExtension;
use App\Models\Pages;
use Str;

class NewsController extends Controller {
    public $title = 'Последние статьи';
    public $slug = 'news';
    public $type = 'setting';
    private $links = [];
    private $scripts = [];
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

    public function index($setting) {
        static $module = 0;

        if (!empty($setting['limit'])) {
            $limit = $setting['limit'];
        } else {
            $limit = 3;
        }

        $pages = Pages::with('metaLang:page_id,name,description')->select('pages.*', 'pcd.name as category')->leftjoin('page_categories as pc', 'pc.id', '=', 'pages.parent_id')->leftjoin('page_category_description as pcd', 'pcd.category_id', '=', 'pc.id')->where([['pages.status', 1], ['pc.status', 1], ['pcd.lang', $this->lang]])->orderBy('pages.created_at', 'desc')->limit($limit)->get();

        if (!$pages->isEmpty()) {
            $module++;
            $data['module'] = $module;

            $this->setLinkStyle(
                [
                    'href' => asset('css/site/news.css'),
                    'rel' => 'stylesheet'
                ]
            );

            $this->setLinkStyle(
                [
                    'href' => asset('css/site/owl.carousel.min.css'),
                    'rel' => 'stylesheet'
                ]
            );

            $this->setScript(
                [
                    'src' => asset('js/site/owl.carousel.min.js')
                ]
            );

            $this->setScript([
                'text' => 'var news' . $module . ' = $(\'.news-' . $module . ' .owl-carousel\'); news' . $module . '.owlCarousel({
    loop:true,
    margin:30,
    dots: false,
    nav: false,
    responsiveClass:true,
    responsive:{
        0:{ 
            items:1,
            nav:true
        },
        600:{
            items:3,
            nav:true
        },
        1000:{
            items:3,
            nav:true,
            loop:true
        }
    },
    onInitialized: counternews,
    onTranslated: counternews 
});
function counternews(event) {
    var items = event.item.count;
    var item = event.item.index + 1;

    if (item > items - 1) {
        item = item - items;
    }
    
    $(\'.news-' . $module . ' .arrows span\').text(item + \'/\' + items);
}
$(document).on(\'click\', \'.news-' . $module . ' .next\', function() {
    news' . $module . '.trigger(\'next.owl.carousel\');
    return false;
})
$(document).on(\'click\', \'.news-' . $module . ' .prev\', function() {
    news' . $module . '.trigger(\'prev.owl.carousel\', [300]);
    return false;
})'
            ]);

            $data['news'] = [];

            foreach ($pages as $page) {
                if (!empty($page->image) && file_exists($page->image)) {
                    $image = resize_image($page->image, 370, 252);
                } else {
                    $image = resize_image('/images/no_image.png', 370, 252);
                }

                $data['news'][] = ['name' => $page->metaLang['name'], 'category' => $page->category, 'short' => Str::limit($page->metaLang['description'], 140), 'image' => $image, 'url' => $this->getUrl('page_' . $this->lang . '_id=' . $page->id)];
            }

            return view('pages.site.extensions.module.news', $data);
        }
    }
	
	public function edit() {
		$extension = Settings::where('code', 'extension.module.' . $this->slug)->value('value');
		return ['setting' => old('setting', $extension), 'action' => asset('admin/extension/module/' . $this->slug . '/save')];
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
