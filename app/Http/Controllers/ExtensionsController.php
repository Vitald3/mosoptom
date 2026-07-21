<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Extensions;
use App\Models\Settings;

class ExtensionsController extends Controller {
    private $extensions = [];
    private $settings;
    private $type = 'module';
    private $title = 'Модули';

    public function __construct(Request $request) {
        if (in_array($request->type, ['module', 'shipping', 'payment', 'total'])) {
            $this->type = $request->type;
        }

        if ($this->type == 'module') {
            $this->title = 'Модули';
        } elseif ($this->type == 'shipping') {
            $this->title = 'Доставка';
        } elseif ($this->type == 'shipping') {
            $this->title = 'Оплата';
        } elseif ($this->type == 'total') {
            $this->title = 'Учитывать в заказе';
        }

        $files = glob(app_path() . '/Http/Controllers/Extensions/' . ucfirst($this->type) . '/*.php', GLOB_BRACE);

        $this->settings = session('settings');

        foreach ($files as $file) {
            $extension = basename($file, '.php');
            $module = '\App\Http\Controllers\Extensions\\' . ucfirst($this->type) . '\\' . $extension;
            $module = new $module;
            $modules = Extensions::getModulesByCode($module->slug);

            $this->extensions[$module->slug] = array(
                'code' => $extension,
                'active' => $module->type == 'setting' ? $slug_setting = Settings::where([['code', 'extension.' . $this->type . '.' . $module->slug], ['value->status', 1]])->value('value') : 0,
                'type' => $module->type,
                'url'      => $module->type == 'setting' ? asset('admin/extension/' . $this->type . '/' . $module->slug . '/edit') : asset('admin/extension/' . $this->type . '/' . $module->slug . '/add'),
                'name'      => $module->title,
                'status'    => Extensions::where('code', $extension)->value('status'),
                'sort_order' => $module->type == 'setting' && isset($slug_setting['sort_order']) ? $slug_setting['sort_order'] : 0,
                'modules'    => $modules
            );
        }

        $this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;

        $classes = array('breadcrumb', 'breadcrumb-item');
        $this->breadcrumbs->addCssClasses($classes);
        $this->breadcrumbs->setDivider('');

        $this->breadcrumbs->addCrumb(__('locale.home'), url('admin'));
    }

    public function index() {
        $this->breadcrumbs->addCrumb($this->title, url('admin/extensions/' . $this->type));
        $breadcrumbs = $this->breadcrumbs->render();

        return view('pages.extensions', ['breadcrumbs' => $breadcrumbs, 'extensions' => $this->extensions, 'type' => $this->type]);
    }

    public function getTemplate($slug) {
        return !empty($this->extensions[$slug]['code']) ? $this->extensions[$slug]['code'] : false;
    }

    public function ajax(Request $request) {
        if (is_null($request->code) || !$this->getTemplate($request->code)) {
            return response()->json(['error' => 'Модуль не найден']);
        }

        $extension = '\App\Http\Controllers\Extensions\\' . ucfirst($this->type) . '\\' . $this->getTemplate($request->code);
        $extension = new $extension($this->settings);

        return $extension->ajax($request);
    }

    public function add(Request $request) {
        if (is_null($request->code) || !$this->getTemplate($request->code)) {
            abort(404);
        }

        $extension = '\App\Http\Controllers\Extensions\\' . ucfirst($this->type) . '\\' . $this->getTemplate($request->code);
        $extension = new $extension($this->settings);

        $this->breadcrumbs->addCrumb($this->title, url('admin/extensions/' . $this->type));
        $this->breadcrumbs->addCrumb($extension->title, url('admin/extensions/' . $this->type . '/' . $extension->slug . '/add'));
        $breadcrumbs = $this->breadcrumbs->render();

        return view('pages.extensions.' . $this->type . '.' . $extension->slug . '/' . $extension->slug, array_merge(['breadcrumbs' => $breadcrumbs], $extension->add()));
    }

    public function edit(Request $request) {
        if (is_null($request->code) || !$this->getTemplate($request->code)) {
            abort(404);
        }

        $extension = '\App\Http\Controllers\Extensions\\' . ucfirst($this->type) . '\\' . $this->getTemplate($request->code);
        $extension = new $extension($this->settings);

        $this->breadcrumbs->addCrumb($this->title, url('admin/extensions/' . $this->type));
        $this->breadcrumbs->addCrumb($extension->title, url('admin/extensions/' . $this->type . '/' . $extension->slug . '/add'));
        $breadcrumbs = $this->breadcrumbs->render();

        return view('pages.extensions.' . $this->type . '.' . $extension->slug . '.' . $extension->slug, array_merge(['breadcrumbs' => $breadcrumbs], $extension->edit($request)));
    }

    public function save(Request $request) {
        if (is_null($request->code) || !$this->getTemplate($request->code)) {
            abort(404);
        }

        $extension = '\App\Http\Controllers\Extensions\\' . ucfirst($this->type) . '\\' . $this->getTemplate($request->code);
        $extension = new $extension($this->settings);

        return redirect('admin/extensions/' . $this->type)->with('success', $extension->save($request));
    }

    public function copy(Request $request) {
        if ($request->id) {
            $extension = Extensions::find($request->id);
            $newExtension = $extension->replicate();
            $newExtension->name = $newExtension->name . ' - Копия';
            $newExtension->save();
            return redirect('admin/extensions/' . $this->type)->with('success', 'Операция успешна');
        } else {
            return redirect('admin/extensions/' . $this->type)->with('error', 'Выберите модуль для копирования');
        }
    }

    public function delete(Request $request) {
        if (is_null($request->code) || !$this->getTemplate($request->code)) {
            abort(404);
        }

        $extension = '\App\Http\Controllers\Extensions\\' . ucfirst($this->type) . '\\' . $this->getTemplate($request->code);
        $extension = new $extension($this->settings);

        return redirect('admin/extensions/' . $this->type)->with('success', $extension->delete($request));
    }
}
