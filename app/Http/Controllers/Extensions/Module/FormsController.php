<?php

namespace App\Http\Controllers\Extensions\Module;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Languages;
use App\Models\Extensions;
use App\Models\LayoutExtension;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class FormsController extends Controller {
    public $title = 'Конструктор форм';
    public $slug = 'forms';
    public $type = 'module';
    private $links = [];
    private $scripts = [];

    public function __construct() {
		$this->settings = session('settings');
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

        $fields = [];

        foreach ($setting['data']['fields'] as $field) {
            $values = [];

            if ($field['type'] === 'select') {
                foreach ($field['values'] as $value) {
                    if (!empty($value[session('lang')])) $values[] = $value[session('lang')];
                }
            }

            if ($field['type'] === 'select' && !$values) continue;

            $fields[] = [
                'type' => $field['type'],
                'title' => !empty($field['title'][session('lang')]) ? $field['title'][session('lang')] : '',
                'placeholder' => !empty($field['placeholder'][session('lang')]) ? $field['placeholder'][session('lang')] : '',
                'values' => $values,
                'required' => isset($field['required'])
            ];
        }

        $region_code = config('app.region_code');

        if ($fields && isset($setting['data']['id'])) {
            $data = [
                'languages' => session('languages'),
                'text_button' => !empty($setting['data']['text_button'][session('lang')]) ? $setting['data']['text_button'][session('lang')] : '',
				'title' => !empty($setting['data']['title'][session('lang')]) ? nl2br($setting['data']['title'][session('lang')]) : '',
				'text' => !empty($setting['data']['text'][session('lang')]) ? strip_tags($setting['data']['text'][session('lang')]) : '',
                'image' => !empty($setting['data']['image']) ? resize_image($setting['data']['image'], 394, 346) : false,
                'fields' => $fields,
                'lang' => session('lang'),
                'id' => $setting['data']['id'],
                'route_region' => $region_code ? '_' . $region_code : ''
            ];

            if (!empty($this->settings['policy'])) {
                $data['policy'] = sprintf(__('locale.text_write_policy'), app(\App\Helpers\PathRouteService::class)->getRoute('page_' . session('lang') . '_id=' . $this->settings['policy']));
            } else {
                $data['policy'] = false;
            }

            $module++;

            $data['module'] = $module;

            $this->setScript(
                [
                    'src' => asset('assets/site/js/forms.js')
                ]
            );

            $this->setLinkStyle(
                [
                    'href' => asset('assets/site/css/forms.css'),
                    'rel' => 'stylesheet'
                ]
            );

            return view('pages.site.extensions.module.forms', $data);
        }
    }

    public function add() {
        $langs = Languages::orderBy('name')->get();

        return ['langs' => $langs, 'setting' => (array)old('setting'), 'name' => old('name'), 'status' => old('status'), 'action' => asset('admin/extension/module/forms/save'), 'id' => ''];
    }

    public function edit(Request $request)
    {
        $extension = Extensions::where('id', $request->id)->first();

        if (!empty($extension)) {
            $langs = Languages::orderBy('name')->get();
            extract($extension->toArray());
            $action = asset('admin/extension/module/forms/save/' . $request->id);
            $id = $request->id;

            return compact('langs', 'setting', 'name', 'status', 'action', 'id');
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
            'name' => 'required',
			'setting.data.fields.*.type' => 'required',
			'setting.data.fields.*.title' => 'required',
            'setting.data.title.*' => 'required',
            'setting.data.subject' => 'required'
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
	
			$setting['data']['id'] = $extensions->id;
			Extensions::where('id', $extensions->id)->update(['setting' => $setting]);
        }

        return 'Модуль ' . $this->title . ' успешно изменен';
    }

    public function forms_action(Request $request)
    {
        $json = [];

        if ($request->id) {
            $setting = session('settings');
            $lang = session('lang');

            $data = $request->all();
		
            $data['logo'] = asset($setting['logo']);

            $extension = Extensions::where('id', $request->id)->value('setting');

            $data['fields'] = [];

            if (!empty($extension['data']['fields'])) {
                foreach ($extension['data']['fields'] as $key => $field) {
                    if (isset($field['title'][$lang]) && isset($data['field'][$key])) {
                        $data['fields'][] = ['name' => $field['title'][$lang], 'value' => $data['field'][$key]];
                    }
                }
            }

            if (!empty($extension['data']['subject'])) {
                $subject = $extension['data']['subject'];
            } else {
                $subject = 'Запрос с формы обратной связи';
            }

            if ($data['fields']) {
                $data = [
					'params' => [
						'logo' => $setting['logo_mail'],
						'name' => !empty($setting['name'][session('lang')]) ? $setting['name'][session('lang')] : '',
						'url' => url(''),
						'text' => view('email.forms', $data)->render()
					],
					'subject' => $subject,
					'template' => 'email.default'
				];
	
				Mail::later(Carbon::now()->addSeconds(5), new SendEmail($data));
	
				if (Mail::failures()) {
					$type = 'error';
					$message = Mail::failures();
				} else {
					$type = 'success';
					$message = __('locale.text_write_success');
				}
            }
        }

        return response()->json([$type => $message]);
    }
}
