<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerGroups;
use App\Models\CustomerGroupDescription;
use App\Models\Languages;
use App\Models\Settings;

class CustomerGroupsController extends Controller
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
        $where = [];

        if (!is_null($request->status)) {
            $where[] = ['customer_groups.status', '=', $request->status];
            $status = $request->status;
        } else {
            $status = '';
        }

        if (!is_null($request->name)) {
            $where[] = ['cgd.name', 'like', '%' . $request->name . '%'];
            $name = $request->name;
        } else {
            $name = '';
        }

        if ($request->sort) {
            $sort = $request->sort;
        } else {
            $sort = 'cgd.name';
        }

        if ($request->order) {
            $order = $request->order;
        } else {
            $order = 'desc';
        }
	
		$limit = session('settings.limit', 25);

        $sort_name = url('admin/customer_groups', ['sort' => 'cgd.name', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
        $sort_order = url('admin/customer_groups', ['sort' => 'customer_groups.sort_order', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
        $sort_status = url('admin/customer_groups', ['sort' => 'customer_groups.status', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;

        if (in_array($sort, ['cg.name', 'status', 'sort_order'])) {
            $customer_groups = CustomerGroups::join('customer_group_description as cgd', 'cgd.customer_group_id', '=', 'customer_groups.id')->select('customer_groups.id', 'customer_groups.status', 'customer_groups.sort_order', 'cgd.name')->where($where)->orderBy($sort, $order)->paginate($limit);
        } else {
            $customer_groups = CustomerGroups::join('customer_group_description as cgd', 'cgd.customer_group_id', '=', 'customer_groups.id')->select('customer_groups.id', 'customer_groups.status', 'customer_groups.sort_order', 'cgd.name')->where($where)->orderBy('cgd.name')->paginate($limit);
        }

        $this->breadcrumbs->addCrumb('Группы клиентов', url('admin/customer_groups') . $this->params);
        $breadcrumbs = $this->breadcrumbs->render();
		$params = $this->params;
		$params_array = $this->params_array;

        return view('pages.customer_groups', compact('params', 'params_array', 'breadcrumbs', 'sort_name', 'sort_status', 'sort_order', 'customer_groups', 'name', 'status', 'sort', 'order'));
    }

    public function add() {
        $langs = Languages::orderBy('name', 'asc')->get();
        $this->breadcrumbs->addCrumb('Группы клиентов', url('admin/customer_groups') . $this->params);
        $this->breadcrumbs->addCrumb('Создать', url('admin/customer_group_add'));
        $breadcrumbs = $this->breadcrumbs->render();

        return view('pages.customer_group-edit', ['breadcrumbs' => $breadcrumbs, 'langs' => $langs, 'name' => old('name'), 'description' => old('description'), 'sort_order' => old('sort_order'), 'approval' => old('approval'), 'status' => old('status'), 'action' => asset('admin/customer_group_save') . $this->params, 'id' => '']);
    }

    public function edit($id)
    {
        $langs = Languages::orderBy('name', 'asc')->get();
        $data = CustomerGroups::with('meta:customer_group_id,name,description,lang')->where('customer_groups.id', $id)->first();

        if (!empty($data)) {
            $this->breadcrumbs->addCrumb('Группы клиентов', url('admin/customer_groups') . $this->params);
            $this->breadcrumbs->addCrumb('Редактировать', url('admin/customer_group/' . $id));
            $breadcrumbs = $this->breadcrumbs->render();

            extract($data->toArray());
            $action = asset('admin/customer_group_save');

            $meta = [];

            foreach ($data->meta as $description) {
                $meta[$description['lang']] = $description;
            }

            return view('pages.customer_group-edit', compact('breadcrumbs', 'meta', 'langs', 'sort_order', 'status', 'approval', 'id', 'action'));
        } else {
            return redirect('admin/customer_groups' . $this->params)->with('error', 'Идентификатор не найден');
        }
    }

    public function delete(Request $request) {
        if ($request->selected) {
			$message = 'Операция успешна';
			$type = 'success';
			
			$customer_group_id = Settings::select('value->customer_group_id')->where('code', 'settings')->value('value->customer_group_id');
			
            foreach ($request->selected as $s) {
            	if ($s === $customer_group_id) {
					$message = 'Запрещено удалять группу клиентов по умолчанию';
					$type = 'error';
				} else {
					CustomerGroups::where('id', $s)->delete();
				}
            }
        } else {
			$message = 'Выделите пункты для удаления';
			$type = 'error';
		}

        return redirect('admin/customer_groups' . $this->params)->with($type, $message);
    }

    public function save(Request $request) {
        $this->validate($request, [
            'meta.*.name' => 'required|max:64'
        ]);

        if (!is_null($request->id)) {
            $customer_group['approval'] = $request->approval ? $request->approval : 0;
            $customer_group['sort_order'] = $request->sort_order ? $request->sort_order : 0;
            $customer_group['status'] = $request->status ? $request->status : 0;

            CustomerGroups::where('id', $request->id)->update($customer_group);

            CustomerGroupDescription::where('customer_group_id', $request->id)->delete();

            foreach ($request->meta as $lang => $meta) {
                $cgd = new CustomerGroupDescription;
                $cgd->lang = $lang;
                $cgd->customer_group_id = $request->id;
                $cgd->name = $meta['name'];
                $cgd->description = !empty($meta['description']) ? $meta['description'] : '';

                $cgd->save();
            }
        } else {
            $customer_group = new CustomerGroups;
            $customer_group->approval = $request->approval ? $request->approval : 0;
            $customer_group->sort_order = $request->sort_order ? $request->sort_order : 0;
            $customer_group->status = $request->status ? $request->status : 0;

            $customer_group->save();

            foreach ($request->meta as $lang => $meta) {
                $cgd = new CustomerGroupDescription;
                $cgd->lang = $lang;
                $cgd->customer_group_id = $customer_group->id;
                $cgd->name = $meta['name'];
                $cgd->description = !empty($meta['description']) ? $meta['description'] : '';

                $cgd->save();
            }
        }

        return redirect('admin/customer_groups' . $this->params)->with('success', 'Операция успешна');
    }
}
