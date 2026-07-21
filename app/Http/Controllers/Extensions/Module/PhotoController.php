<?php

namespace App\Http\Controllers\Extensions\Module;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\LayoutExtension;
use App\Models\Languages;
use App\Models\Extensions;

class PhotoController extends Controller {
    public $title = 'Фотогалерея';
    public $slug = 'photo';
    public $type = 'module';
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
            $limit = 9;
        }

        if (!empty($setting['data']['images'])) {
            $images = $setting['data']['images'];
        } else {
            $images = [];
        }

        if (!empty($images)) {
            $images = array_slice($images, 0, (int)$limit);

            if (!empty($setting['data']['title'][$this->lang])) {
                $data['title'] = $setting['data']['title'][$this->lang];
            } else {
                $data['title'] = __('extension.photo.title');
            }

            $this->setLinkStyle(
                [
                    'href' => asset('css/site/owl.carousel.min.css'),
                    'rel' => 'stylesheet'
                ]
            );

            $this->setLinkStyle(
                [
                    'href' => asset('css/site/magnific.css'),
                    'rel' => 'stylesheet'
                ]
            );

            $this->setLinkStyle(
                [
                    'href' => asset('css/site/photo.css'),
                    'rel' => 'stylesheet'
                ]
            );

            $this->setScript(
                [
                    'src' => asset('js/site/owl.carousel.min.js')
                ]
            );

            $this->setScript(
                [
                    'src' => asset('js/site/magnific.js')
                ]
            );

            $module++;
            $data['module'] = $module;

            $this->setScript([
                'text' => 'var photo' . $module . ' = $(\'.photo-' . $module . ' .owl-carousel\'); photo' . $module . '.owlCarousel({
    loop:true,
    margin:30,
    dots: false,
    nav: false,
    autoWidth: true,
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
            items:4,
            nav:true,
            loop:true
        }
    }
});
        $(\'.popup-gallery\').magnificPopup({
          delegate: \'a\',
          type: \'image\',
          tLoading: \'Загрузка #%curr%...\',
          mainClass: \'mfp-img-mobile\',
          gallery: {
                enabled: true,
            navigateByImgClick: true,
            preload: [0,1] // Will preload 0 - before current, and 1 after the current image
          },
          image: {
                tError: \'<a href="%url%">The image #%curr%</a> could not be loaded.\',
            titleSrc: function(item) {
                    return item.el.attr(\'title\');
                }
          }
        });'
            ]);

            $data['images'] = [];

            foreach ($images as $x => $image) {
                $x++;
                $data['images'][] = ['alt' => $data['title'] . sprintf(__('locale.v25'), $x), 'image' => resize_image($image, 370, 370), 'popup' => $image];
            }

            return view('pages.site.extensions.module.photo', $data);
        }
    }

    public function add() {
        $langs = Languages::orderBy('name')->get();

        return ['langs' => $langs, 'setting' => (array)old('setting'), 'name' => old('name'), 'status' => old('status'), 'action' => asset('admin/extension/module/photo/save'), 'id' => ''];
    }

    public function edit(Request $request) {
        $extension = Extensions::where('id', $request->id)->first();

        if (!empty($extension)) {
            $langs = Languages::orderBy('name')->get();
            return ['langs' => $langs, 'setting' => old('setting') ? (array)old('setting') : $extension->setting, 'name' => old('name') ? old('name') : $extension->name, 'status' => old('status') ? old('status') : $extension->status, 'action' => asset('admin/extension/module/photo/save/' . $request->id), 'id' => $request->id];
        } else {
            return redirect('admin/extensions')->with('error', 'Идентификатор не найден');
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
