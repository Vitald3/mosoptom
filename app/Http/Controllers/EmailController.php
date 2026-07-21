<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Settings;
	use App\Models\Customers;
	use App\Models\CustomerGroups;
	use Illuminate\Support\Facades\Mail;
	
	class EmailController extends Controller
	{
		public function __construct() {
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			
			$classes = array('breadcrumb', 'breadcrumb-item');
			$this->breadcrumbs->addCssClasses($classes);
			$this->breadcrumbs->setDivider('');
			
			$this->breadcrumbs->addCrumb(__('locale.home'), url('admin'));
		}
		
		public function index(){
			$this->breadcrumbs->addCrumb('Почта', url('admin/emails'));
			$breadcrumbs = $this->breadcrumbs->render();
			$customer_groups = CustomerGroups::join('customer_group_description as cgd', 'cgd.customer_group_id', '=', 'customer_groups.id')->select('customer_groups.id', 'cgd.name')->where('customer_groups.status', 1)->orderBy('cgd.name')->get();
			$action = route('email_send');
			$customers = Customers::selectRaw("id, concat(firstname, ' ', lastname) as customer")->where('status', 1)->get()->keyBy('id');
			
			return view('pages.emails', compact('breadcrumbs', 'customer_groups', 'customers', 'action'));
		}
		
		public function send(Request $request) {
			$this->validate($request, [
				'subject' => 'required',
				'to' => 'required',
				'text' => 'required'
			]);
			
			$setting = Settings::where('code', 'settings')->value('value');
			$lang = !empty($setting['default_language']) ? $setting['default_language'] : config('app.locale');
			
			if ($request->to === 'customer') {
				$email = Customers::whereIn('id', $request->customer)->pluck('email');
			} elseif ($request->to === 'customer_group') {
				$email = Customers::where('customer_group_id', $request->customer_group_id)->pluck('email');
			} elseif ($request->to === 'customer_all') {
				$email = Customers::where('status', 1)->pluck('email');
			} else {
				$email = Customers::where('newsletter', 1)->pluck('email');
			}
			
			if (!empty($email)) {
				$email = $email->toArray();
				
				$params = [
					'logo' => $setting['logo'],
					'name' => !empty($setting['name'][$lang]) ? $setting['name'][$lang] : '',
					'url' => url(''),
					'text' => $request->text
				];
				
				Mail::send('email.admin_emails', $params, function($message) use ($email, $request, $params) {
					$message->from(env('MAIL_USERNAME'), $params['name']);
					
					foreach ($email as $e) {
						$message->to($e);
					}
					
					$message->subject($request->subject);
				});
				
				$type = 'error';
				
				if (Mail::failures()) {
					$message = Mail::failures();
				} else {
					$type = 'success';
					$message = 'Отправлено ' . num_decline(count($email), ['письмо', 'письма', 'писем']);
				}
			} else {
				$type = 'error';
				$message = 'Пользователи не найдены';
			}
			
			return redirect('admin/emails')->with($type, $message);
		}
	}