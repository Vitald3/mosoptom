<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currencies;
use Illuminate\Support\Facades\Cache;

class CurrenciesController extends Controller
{
    public function __construct() {
        $this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;

        $classes = array('breadcrumb', 'breadcrumb-item');
        $this->breadcrumbs->addCssClasses($classes);
        $this->breadcrumbs->setDivider('');

        $this->breadcrumbs->addCrumb(__('locale.home'), url('admin'));
	
		$this->params_array = request()->query();
		$params = [];
	
		if (!empty($this->params_array)) {
			foreach ($this->params_array as $key => $param) {
				$params[] = $key . '=' . $param;
			}
		}
	
		$this->params = !empty($this->params) ? '?' . implode('&', $params) : '';
    }

    public function index(Request $request){
        if ($request->sort) {
            $sort = $request->sort;
        } else {
            $sort = 'updated_at';
        }

        if ($request->order) {
            $order = $request->order;
        } else {
            $order = 'asc';
        }
	
		$limit = session('settings.limit', 25);

        $sort_name = url('admin/currencies', ['sort' => 'title', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
        $sort_code = url('admin/currencies', ['sort' => 'code', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
        $sort_value = url('admin/currencies', ['sort' => 'value', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
        $sort_updated_at = url('admin/currencies', ['sort' => 'update_at', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
        $sort_status = url('admin/currencies', ['sort' => 'status', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;

        if (in_array($sort, ['title', 'code', 'value', 'status', 'status', 'update_at'])) {
            $currencies = Currencies::orderBy($sort, $order)->paginate($limit);
        } else {
            $currencies = Currencies::orderBy('title')->paginate($limit);
        }

        $this->breadcrumbs->addCrumb('Валюта', url('admin/currencies') . $this->params);
        $breadcrumbs = $this->breadcrumbs->render();
		$params = $this->params;
		$params_array = $this->params_array;

        return view('pages.currencies', compact('params', 'params_array', 'sort_updated_at', 'breadcrumbs', 'sort_name', 'sort_status', 'sort_code', 'currencies', 'sort_value', 'sort', 'order'));
    }

    public function add() {
        $this->breadcrumbs->addCrumb('Валюта', url('admin/currencies') . $this->params);
        $this->breadcrumbs->addCrumb('Создать', url('admin/currency_add'));
        $breadcrumbs = $this->breadcrumbs->render();

        return view('pages.currency-edit', ['breadcrumbs' => $breadcrumbs, 'title' => old('title'), 'code' => old('code'), 'decimal' => old('decimal'), 'position' => old('position'), 'symbol' => old('symbol'), 'value' => old('value'), 'status' => old('status'), 'action' => asset('admin/currency_save') . $this->params, 'id' => '']);
    }

    public function edit($id) {
        $data = Currencies::where('id', $id)->first()->toArray();

        if (!empty($data)) {
            extract($data);
            $action = asset('admin/currency_save') . $this->params;

            $this->breadcrumbs->addCrumb('Валюта', url('admin/currencies') . $this->params);
            $this->breadcrumbs->addCrumb('Редактировать', url('admin/currency/' . $id));
            $breadcrumbs = $this->breadcrumbs->render();

            return view('pages.currency-edit', compact('breadcrumbs', 'title', 'code', 'decimal', 'position', 'symbol', 'value', 'status', 'id', 'action'));
        } else {
            return redirect('admin/currencies' . $this->params)->with('error', 'Идентификатор не найден');
        }
    }

    public function delete(Request $request) {
        if ($request->selected) {
        	if (Currencies::count() === 1) {
				$message = 'Нельзя удалить единственную валюту';
				$type = 'error';
			} else {
				$message = 'Операция успешна';
				$type = 'success';
				
				foreach ($request->selected as $s) {
					Currencies::where('id', $s)->delete();
				}
			}
		} else {
			$message = 'Выделите пункты для удаления';
			$type = 'error';
		}
	
		return redirect('admin/currencies' . $this->params)->with($type, $message);
    }

    public function save(Request $request) {
        $this->validate($request, [
            'title' => 'required|max:64',
            'code' => 'required|max:6|unique:currencies,code' . (!is_null($request->id) ? ',' . $request->id . ',id' : ''),
            'symbol' => 'required|max:10',
            'value' => 'required'
        ]);

        if (!is_null($request->id)) {
            $currency['title'] = $request->title;
            $currency['code'] = $request->code;
            $currency['decimal'] = $request->decimal ? $request->decimal : 0;
            $currency['position'] = $request->position ? $request->position : 1;
            $currency['symbol'] = $request->symbol;
            $currency['value'] = $request->value;
            $currency['status'] = $request->status ? $request->status : 0;

            Currencies::where('id', $request->id)->update($currency);
        } else {
            $currency = new Currencies;
            $currency->title = $request->title;
            $currency->code = $request->code;
            $currency->decimal = $request->decimal ? $request->decimal : 0;
            $currency->position = $request->position ? $request->position : 1;
            $currency->symbol = $request->symbol;
            $currency->value = $request->value;
            $currency->status = $request->status ? $request->status : 0;

            $currency->save();
        }

        $currencies = Currencies::select('id', 'title', 'code', 'value', 'symbol', 'position', 'decimal')->where('status', 1)->get()->keyBy('code');
        Cache::set('currencies', $currencies);

        return redirect('admin/currencies' . $this->params)->with('success', 'Операция успешна');
    }
}