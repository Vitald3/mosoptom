<?php
	
	namespace App\Http\Controllers\Account;
	
	use Illuminate\Http\Request;
	use App\Http\Controllers\Controller;
	use App\Models\Reviews;
	use App\Http\Controllers\CartController;
	use App\Http\Controllers\HeaderController;
	use App\Http\Controllers\GetContentController;
	
	class ReviewsController extends Controller
	{
		public function __construct()
		{
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			$this->breadcrumbs->addCssClasses(array('breadcrumb', 'breadcrumb-item'));
			$this->breadcrumbs->setDivider('');
			$this->breadcrumbs->addCrumb(__('locale.home'), route(session('route_url') . '_home'));
			
			$this->settings = session('settings');
			$this->lang = session('lang');
			$this->region = session('region');
		}
		
		public function index(Request $request) {
			$header = new HeaderController;
			
			$meta = [
				'meta_title' => __('locale.text_account_reviews'),
				'meta_description' => '',
				'meta_keywords' => ''
			];
			
			$header->setMeta($meta);
			
			if ($request->page) {
				$page = $request->page;
			} else {
				$page = 1;
			}
			
			$this->breadcrumbs->addCrumb(__('locale.text_breadcrumbs_account'), route(session('route_url') . '_account'));
			$header->setBreadcrumbs($this->breadcrumbs->render());
			
			$content = new GetContentController(7);
			$data['content_top'] = $content->getPosition('top');
			$data['content_bottom'] = $content->getPosition('bottom');
			$header->setStyle($content->getHtmlStyle());
			$header->setLinkStyle($content->getLinkStyle());
			$header->setScript($content->getScript());
			$cart = new CartController;
			$data['cart'] = $cart->mini_cart($content->getModuleById('saleday'));
			$cart_count = $cart->getCount();
			$data['cart_count'] = $cart_count > 99 ? '99+' : $cart_count;
			$region_code = config('app.region_code');
			$this->region['code'] = $region_code ? $region_code . '/' : '';
			
			$data = array_merge($data, $header->data());
			$data['class'] = 'reviews';
			$data['canonical'] = '';
			$header->setRobots('noindex, nofollow');
			$data['title'] = __('locale.text_account_reviews');
			
			$data['reviews'] = Reviews::select('reviews.id', 'reviews.status', 'reviews.text', 'reviews.rating', 'p.id as product_id', 'p.image', 'pd.name')
				->join('products as p', 'reviews.product_id', '=', 'p.id')
				->join('product_description as pd', 'pd.product_id', '=', 'p.id')
				->where('pd.lang', $this->lang)
				->where('p.status', 1)
				->where('reviews.customer_id', session('customer_id'))
				->orderBy('reviews.created_at', 'desc')
				->paginate(session('settings.limit_sait', 25), ['*'], 'page', $page);
			
			return render_view(view('pages.site.account.reviews', $data), $this->region, false);
		}
		
		public function edit(Request $request) {
			$review = Reviews::select('reviews.id', 'reviews.status', 'reviews.rating', 'reviews.text', 'reviews.disadvantages', 'reviews.dignities', 'p.id as product_id', 'p.image', 'pd.name')
					->join('products as p', 'reviews.product_id', '=', 'p.id')
					->join('product_description as pd', 'pd.product_id', '=', 'p.id')
					->where('pd.lang', $this->lang)
					->where('p.status', 1)
					->where('reviews.customer_id', session('customer_id'))
				    ->where('reviews.id', $request->id)->firstOrFail();
			
			$header = new HeaderController;
			$title = sprintf(__('locale.text_account_review'), $review->name);
			
			$meta = [
				'meta_title' => $title,
				'meta_description' => '',
				'meta_keywords' => ''
			];
			
			$header->setMeta($meta);
			
			$this->breadcrumbs->addCrumb(__('locale.text_breadcrumbs_account'), route(session('route_url') . '_account'));
			$header->setBreadcrumbs($this->breadcrumbs->render());
			
			$content = new GetContentController(7);
			$data['content_top'] = $content->getPosition('top');
			$data['content_bottom'] = $content->getPosition('bottom');
			$header->setStyle($content->getHtmlStyle());
			$header->setLinkStyle($content->getLinkStyle());
			$header->setScript($content->getScript());
			$cart = new CartController;
			$data['cart'] = $cart->mini_cart($content->getModuleById('saleday'));
			$cart_count = $cart->getCount();
			$data['cart_count'] = $cart_count > 99 ? '99+' : $cart_count;
			$region_code = config('app.region_code');
			$this->region['code'] = $region_code ? $region_code . '/' : '';
			
			$data = array_merge($data, $header->data());
			$data['class'] = 'review_edit review_edit_' . $request->id;
			$data['canonical'] = '';
			$header->setRobots('noindex, nofollow');
			$data['title'] = __('locale.text_account_reviews');
			$data['review'] = $review;
			
			return render_view(view('pages.site.account.review_info', $data), $this->region, false);
		}
		
		public function delete(Request $request) {
			if ($request->id && session('customer_id')) {
				$delete = Reviews::where('customer_id', session('customer_id'))->where('id', $request->id)->delete();
				
				if ($delete) {
					return response()->json(['success' => 1]);
				} else {
					return response()->json(['error' => 'Error']);
				}
			}
		}
		
		public function save(Request $request) {
			$validate = \Validator::make($request->all(), [
				'text' => 'required|string|max:300',
				'rating' => 'required|integer'
			]);
			
			if ($validate->fails()) {
				return response()->json(['errors' => $validate->errors()->messages()]);
			} else {
				$review = Reviews::where('customer_id', session('customer_id'))->where('id', $request->id)->update([
					'text' => $request->text,
					'rating' => $request->rating,
					'disadvantages' => $request->disadvantages ? $request->disadvantages : '',
					'dignities' => $request->dignities ? $request->dignities : ''
				]);
				
				if ($review) {
					return response()->json(['success' => __('locale.text_account_review_3')]);
				} else {
					return response()->json(['error' => 'Error']);
				}
			}
		}
		
		public function getReviews(Request $request) {
			$json = [];
			
			if (in_array($request->sort, ['asc', 'desc'])) {
				$sort = $request->sort;
			} else {
				$sort = 'desc';
			}
			
			if ($request->page) {
				$page = $request->page;
			} else {
				$page = 1;
			}
			
			$limit = ($page - 1) * session('settings.limit', 25);
			
			$data['reviews'] = Reviews::with('social:customer_id,social,text')->where('customer_id', session('customer_id'))->orderBy('created_at', $sort)->skip($limit)->take(session('settings.limit', 25))->get();
			
			if ($data['reviews']) {
				$json['html'] = view('pages.site.product_reviews', $data)->render();
			}
			
			return response()->json($json);
		}
		
		public function write(Request $request) {
			$validate = \Validator::make($request->all(), [
				'text' => 'required|string|max:300',
				'rating' => 'required|integer',
				'id' => 'required|integer'
			]);
			
			if ($validate->fails()) {
				return response()->json(['errors' => $validate->errors()->messages()]);
			} else {
				$review = new Reviews;
				$review->text = $request->text;
				$review->customer_id = session('customer_id');
				$review->product_id = $request->id;
				$review->rating = $request->rating;
				$review->status = null;
				$review->author = session('customer.firstname') . (session('customer.lastname') ? ' ' . session('customer.lastname') : '');
				$review->disadvantages = $request->disadvantages ? $request->disadvantages : '';
				$review->dignities = $request->dignities ? $request->dignities : '';
				
				$review->save();
				
				if ($review) {
					return response()->json(['success' => __('locale.text_product_30'), 'title' => __('locale.text_product_31')]);
				} else {
					return response()->json(['error' => 'Error']);
				}
			}
		}
	}