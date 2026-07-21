<?php
	
	namespace App\Http\Controllers\Account;
	
	use Illuminate\Http\Request;
	use App\Http\Controllers\Controller;
	use App\Models\Customers;
	use App\Models\CustomerNewsletter;
	use App\Http\Controllers\CartController;
	use App\Http\Controllers\HeaderController;
	use App\Http\Controllers\GetContentController;
	use App\Mail\SendEmail;
	use Illuminate\Support\Facades\Mail;
	use Carbon\Carbon;
	
	class NewsletterController extends Controller
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
		
		public function index() {
			$header = new HeaderController;
			
			$meta = [
				'meta_title' => __('locale.text_newsletter'),
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
			$data['class'] = 'newsletter';
			$data['canonical'] = '';
			$header->setRobots('noindex, nofollow');
			$data['title'] = __('locale.text_newsletter');
			
			$data['newsletter'] = CustomerNewsletter::select('type')->where('customer_id', session('customer_id'))->pluck('type')->toArray();

			return render_view(view('pages.site.account.newsletter', $data), $this->region, false);
		}
		
		public function save(Request $request) {
			if ($request->type) {
				CustomerNewsletter::where('customer_id', session('customer_id'))->where('type', $request->type)->delete();
				
				$customer_newsletter = new CustomerNewsletter;
				$customer_newsletter->customer_id = session('customer_id');
				$customer_newsletter->type = $request->type;
				
				$customer_newsletter->save();
				
				return response()->json(['success' => 1]);
			}
		}
		
		public function send_email() {
			$newsletter = CustomerNewsletter::where('customer_id', session('customer_id'))->pluck('type');
			
			if (!empty($newsletter)) {
				$setting = session('settings');
				
				$data = [
					'params' => [
						'logo' => $setting['logo_mail'],
						'name' => !empty($setting['name'][session('lang')]) ? $setting['name'][session('lang')] : '',
						'url' => url(''),
						'text' => view('email.newsletter', ['newsletter' => $newsletter])->render()
					],
					'subject' => __('locale.text_newsletter_9'),
					'email' => session('customer')['email'],
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
				
				return response()->json([$type => $message]);
			}
		}
	}