<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Reviews;
	
	class ReviewsController extends Controller
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
				$where[] = ['status', '=', $request->status];
				$status = $request->status;
			} else {
				$status = '';
			}
			
			if (!is_null($request->name)) {
				$where[] = ['author', 'like', '%' . $request->name . '%'];
				$name = $request->name;
			} else {
				$name = '';
			}
			
			if (!is_null($request->rating)) {
				$where[] = ['rating', '=', $request->rating];
				$rating = $request->rating;
			} else {
				$rating = '';
			}
			
			if ($request->sort) {
				$sort = $request->sort;
			} else {
				$sort = 'created_at';
			}
			
			if ($request->order) {
				$order = $request->order;
			} else {
				$order = 'desc';
			}
			
			$limit = session('settings.limit', 25);
			
			$sort_name = url('admin/reviews', ['sort' => 'author', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_rating = url('admin/reviews', ['sort' => 'rating', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			$sort_status = url('admin/reviews', ['sort' => 'status', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			
			if (in_array($sort, ['author', 'status', 'rating', 'created_at'])) {
				$reviews = Reviews::with('product')->select('id', 'status', 'author', 'text', 'rating', 'product_id')->where($where)->orderBy($sort, $order)->paginate($limit);
			} else {
				$reviews = Reviews::with('product')->select('id', 'status', 'author', 'text', 'rating', 'product_id')->where($where)->orderBy('author')->paginate($limit);
			}
			
			$this->breadcrumbs->addCrumb('Отзывы', url('admin/reviews') . $this->params);
			$breadcrumbs = $this->breadcrumbs->render();
			$params = $this->params;
			$params_array = $this->params_array;
			
			return view('pages.reviews', compact('params', 'params_array', 'breadcrumbs', 'sort_name', 'sort_status', 'sort_rating', 'reviews', 'name', 'rating', 'status', 'sort', 'order'));
		}
		
		public function add() {
			$this->breadcrumbs->addCrumb('Отзывы', url('admin/reviews') . $this->params);
			$this->breadcrumbs->addCrumb('Создать', url('admin/review_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.review-edit', ['breadcrumbs' => $breadcrumbs, 'author' => old('author'), 'rating' => old('rating'), 'text' => old('text'), 'product_id' => old('product_id'), 'product' => old('product'), 'status' => old('status'), 'action' => asset('admin/review_save') . $this->params, 'id' => '']);
		}
		
		public function edit($id)
		{
			$data = Reviews::with('product')->where('id', $id)->first()->toArray();
			
			if (!empty($data)) {
				extract($data);
				$product = $data['product']['name'];
				$action = asset('admin/review_save') . $this->params;
				
				$this->breadcrumbs->addCrumb('Отзывы', url('admin/reviews') . $this->params);
				$this->breadcrumbs->addCrumb('Редактировать', url('admin/review/' . $id));
				$breadcrumbs = $this->breadcrumbs->render();
				
				return view('pages.review-edit', compact('breadcrumbs', 'author', 'rating', 'text', 'product_id', 'status', 'product', 'id', 'action'));
			} else {
				return redirect('admin/reviews' . $this->params)->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete(Request $request) {
			if ($request->selected) {
				foreach ($request->selected as $s) {
					Reviews::where('id', $s)->delete();
				}
				
				$message = 'Операция успешна';
				$type = 'success';
			} else {
				$message = 'Выделите пункты для удаления';
				$type = 'error';
			}
			
			return redirect('admin/reviews' . $this->params)->with($type, $message);
		}
		
		public function save(Request $request) {
			$this->validate($request, [
				'author' => 'required|max:300',
				'text' => 'required|max:300',
				'product_id' => 'required|integer',
				'rating' => 'required|integer'
			]);
			
			if (!is_null($request->id)) {
				$review['author'] = $request->author;
				$review['text'] = $request->text;
				$review['product_id'] = $request->product_id;
				$review['rating'] = $request->rating;
				$review['status'] = $request->status ? $request->status : 0;
				
				Reviews::where('id', $request->id)->update($review);
			} else {
				$review = new Reviews;
				$review->author = $request->author;
				$review->text = $request->text;
				$review->rating = $request->rating;
				$review->product_id = $request->product_id;
				$review->status = $request->status ? $request->status : 0;
				
				$review->save();
			}
			
			return redirect('admin/reviews' . $this->params)->with('success', 'Операция успешна');
		}
	}
