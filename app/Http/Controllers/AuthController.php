<?php
	
	namespace App\Http\Controllers;
	use Illuminate\Http\Request;
	use Carbon\Carbon;
	use App\Models\Customers;
	use App\Models\PasswordResets;
	use App\Models\CustomerLegal;
	use App\Models\Address;
	use Validator;
	use App\Mail\SendEmail;
	use App\Http\Controllers\CSms4bBaseController;
	use Illuminate\Support\Str;
	use DB;
	use Illuminate\Support\Facades\Mail;
	
	class AuthController extends Controller
	{
		private $settings = [];
		
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
		
		public function register(Request $request)
		{
			if ($request->session()->get('customer_id')) {
				return response()->json(['redirect' => route(session('route_url') . '_account_success')]);
			}
			
			$validate = \Validator::make($request->all(), [
				'firstname' => 'required|string',
				'lastname' => 'required|string',
				'phone' => 'required|string|unique:customers,phone',
				'email' => 'required|string|email|unique:customers,email',
				'agree' => 'required|integer'
			]);
			
			if ($validate->fails()) {
				return response()->json(['errors' => $validate->errors()->messages()]);
			} else {
				$password = '';
				$chars = "qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
				$max = 6;
				$size = strlen($chars) - 1;
				while($max--) $password .= $chars[rand(0,$size)];
				
				$phone = preg_replace('![^0-9]+!', '', $request->phone);
				
				$address = new Address;
				$address->customer_id = 0;
				$address->firstname = $request->firstname;
				$address->lastname = $request->lastname;
				$address->company = '';
				$address->address = $request->address ? $request->address : '';
				$address->address2 = $request->address2 ? $request->address2 : '';
				$address->city = $request->city ? $request->city : (isset(session('region')['name']) ? session('region')['name'] : '');
				$address->postcode = $request->postcode ? $request->postcode : '';
				
				$address->save();
				
				$customer = new Customers;
				$customer->firstname = '';
				$customer->lastname = '';
				$customer->email = $request->email;
				$customer->phone = $phone;
				$customer->salt = $salt = Str::random(9);
				$customer->password = sha1($salt . sha1($salt . sha1($password)));
				$customer->customer_group_id = session('customer_group_id');
				$customer->ip = $request->ip();
				$customer->address_id = $address->id;
				$customer->approval = 1;
				$customer->newsletter = 1;
				$customer->type = 1;
				$customer->status = 1;
				
				$email = $request->email;
				$customer_session = $customer->toArray();
				$customer_session['password'] = $password;
				
				if ($customer->save()) {
					$customer_legal = new CustomerLegal;
					$customer_legal->firstname = $request->firstname;
					$customer_legal->lastname = $request->lastname;
					$customer_legal->ogrn = $request->ogrn ? $request->ogrn : '';
					$customer_legal->forma_sobstvennosti = $request->forma_sobstvennosti ? $request->forma_sobstvennosti : '';
					$customer_legal->kontragent = $request->kontragent ? $request->kontragent : '';
					$customer_legal->kpp = $request->kpp ? $request->kpp : '';
					$customer_legal->inn = $request->inn ? $request->inn : '';
					$customer_legal->address = $request->address ? $request->address : '';
					$customer_legal->address2 = $request->address2 ? $request->address2 : '';
					$customer_legal->company = $request->company ? $request->company : '';
					$customer_legal->customer_id = $customer->id;
					
					$customer_legal->save();
					
					$address->update(['customer_id' => $customer->id]);
					
					$request->session()->put(['customer_id' => $customer->id]);
					$request->session()->put(['customer_group_id' => $customer_session['customer_group_id']]);
					$request->session()->put(['customer' => $customer_session]);
					
					$setting = session('settings');
					
					$data = [
						'params' => [
							'logo' => $setting['logo_mail'],
							'name' => !empty($setting['name'][session('lang')]) ? $setting['name'][session('lang')] : '',
							'url' => url(''),
							'text' => view('email.register', $customer_session)->render()
						],
						'subject' => __('locale.text_success_register'),
						'email' => $email,
						'template' => 'email.default'
					];
					
					Mail::later(Carbon::now()->addSeconds(5), new SendEmail($data));
					
					return response()->json(['redirect' => route(session('route_url') . '_account_success')]);
				} else {
					return response()->json(['error' => __('locale.error_customer_register')]);
				}
			}
		}
		
		public function login(Request $request)
		{
			if ($request->session()->get('customer_id')) {
				return response()->json(['redirect' => route(session('route_url') . '_account_success')]);
			}
			
			if ($request->phone) {
				$validate = \Validator::make($request->all(), [
					'phone' => 'required|string'
				]);
				
				if (!$validate->fails()) {
					$phone = preg_replace('![^0-9]+!', '', $request->phone);
					
					if (!is_null($request->code)) {
						$code_field = implode('', $request->code);
					} else {
						$code_field = $request->code;
					}
					
					$customer = Customers::with([
						'address' => function($query) {
							$query->selectRaw("customer_id,id,address,address2");
						},
						'social' => function($query) {
							$query->selectRaw("customer_id,social,text");
						},
						'legal',
						'emails' => function($query) {
							$query->selectRaw("customer_id,email");
						},
						'phones' => function($query) {
							$query->selectRaw("customer_id,phone");
						}
					])->where('status', 1)->where('phone', $phone)->orderBy('created_at')->limit(1)->first();
					
					if (!empty($customer)) {
						if (!isset($request->session()->get('phone_code')[$phone])) {
							$code = '';
							$chars = "1234567890";
							$max = 4;
							$size = strlen($chars) - 1;
							while ($max--) $code .= $chars[rand(0, $size)];
							
							$request->session()->put(['phone_code' => [$phone => $code]]);
							
							$SMS4B = new CSms4bBaseController('Mosoptom', 'Mosoptom2020');
							$SMS4B->CSms4bBase('Mosoptom', 'Mosoptom2020');
							$SMS4B->GetSOAP("AccountParams", array("SessionID" => $SMS4B->GetSID()));
							$result = $SMS4B->SendSMS('Ваш код безопасности ' . $code, $phone);
							
							if ($result) {
								return response()->json(['success' => 1]);
							} else {
								return response()->json(['error' => __('locale.error_send_sms')]);
							}
						} else if (isset($request->session()->get('phone_code')[$phone]) && is_null($request->code)) {
							return response()->json(['success' => 1, 'sms' => 1]);
						} else if ($code_field && isset($request->session()->get('phone_code')[$phone]) && $request->session()->get('phone_code')[$phone] === $code_field) {
							$request->session()->forget('phone_code');
							$request->session()->forget('phone_code2');
							$request->session()->put(['customer_id' => $customer->id]);
							$request->session()->put(['customer_group_id' => $customer->customer_group_id]);
							$request->session()->put(['customer' => $customer]);
							
							Customers::where('id', $customer->id)->update(['ip' => $request->ip()]);
							
							return response()->json(['redirect' => route(session('route_url') . '_account_success')]);
						} else if ($code_field && isset($request->session()->get('phone_code')[$phone]) && $request->session()->get('phone_code')[$phone] !== $code_field) {
							return response()->json(['error' => __('locale.text_login_popup_v12')]);
						} else {
							return response()->json(['error' => __('locale.text_login_popup_v13')]);
						}
					} else {
						$password = '';
						$chars = "qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
						$max = 6;
						$size = strlen($chars) - 1;
						while($max--) $password .= $chars[rand(0,$size)];
						
						$customer = new Customers;
						$customer->firstname = '';
						$customer->lastname = '';
						$customer->email = '';
						$customer->phone = $phone;
						$customer->salt = $salt = Str::random(9);
						$customer->password = sha1($salt . sha1($salt . sha1($password)));
						$customer->customer_group_id = session('customer_group_id');
						$customer->ip = $request->ip();
						$customer->address_id = 0;
						$customer->approval = 1;
						$customer->newsletter = 1;
						$customer->type = 0;
						$customer->status = 1;
						
						if ($customer->save()) {
							$customer_session = $customer->toArray();
							$customer_session['password'] = $password;
							
							$request->session()->put(['customer_id' => $customer->id]);
							$request->session()->put(['customer_group_id' => $customer_session['customer_group_id']]);
							$request->session()->put(['customer' => $customer_session]);
							
							$setting = session('settings');
							
							$data = [
								'params' => [
									'logo' => $setting['logo_mail'],
									'name' => !empty($setting['name'][session('lang')]) ? $setting['name'][session('lang')] : '',
									'url' => url(''),
									'text' => view('email.register', $customer_session)->render()
								],
								'subject' => __('locale.text_success_register'),
								'email' => $request->email,
								'template' => 'email.default'
							];
							
							$code = '';
							$chars = "1234567890";
							$max = 4;
							$size = strlen($chars) - 1;
							while ($max--) $code .= $chars[rand(0, $size)];
							
							$request->session()->put(['phone_code' => [$phone => $code]]);
							
							$SMS4B = new CSms4bBaseController('Mosoptom', 'Mosoptom2020');
							$SMS4B->CSms4bBase('Mosoptom', 'Mosoptom2020');
							$SMS4B->GetSOAP("AccountParams", array("SessionID" => $SMS4B->GetSID()));
							$result = $SMS4B->SendSMS('Ваш код безопасности ' . $code, $phone);
							
							if ($result) {
								Mail::later(Carbon::now()->addMinutes(10), new SendEmail($data));
								return response()->json(['success' => 1]);
							} else {
								return response()->json(['error' => __('locale.error_send_sms')]);
							}
						} else {
							return response()->json(['error' => 'Error']);
						}
					}
				}
			} else {
				$validate = \Validator::make($request->all(), [
					'email' => 'required|string|email',
					'password' => 'required|string|min:4|max:10'
				]);
			}
			
			if (!$validate->fails()) {
				$customer = Customers::with([
					'address' => function($query) {
						$query->selectRaw("customer_id,id,address,address2");
					},
					'social' => function($query) {
						$query->selectRaw("customer_id,social,text");
					},
					'legal',
					'emails' => function($query) {
						$query->selectRaw("customer_id,email");
					},
					'phones' => function($query) {
						$query->selectRaw("customer_id,phone");
					}
				])->where('status', 1)
					->where('email', $request->email)
					->whereRaw("(`password` = SHA1(CONCAT(`salt`, SHA1(CONCAT(`salt`, SHA1(?))))) OR `password` = md5(?))", [$request->password, $request->password])
					->first();
				
				if (!empty($customer)) {
					if ($customer->type == 1 && !empty($customer->legal['firstname'])) {
						$customer->firstname = $customer->legal['firstname'];
					}
					
					$request->session()->put(['customer_id' => $customer->id]);
					$request->session()->put(['customer_group_id' => $customer->customer_group_id]);
					$request->session()->put(['customer' => $customer->toArray()]);
					
					Customers::where('id', $customer->id)->update(['ip' => $request->ip()]);
					
					return response()->json(['redirect' => route(session('route_url') . '_account_success')]);
				} else {
					return response()->json(['error' => __('locale.error_login_mail')]);
				}
			} else {
				return response()->json(['errors' => $validate->errors()->messages()]);
			}
		}
		
		public function forgot_get(Request $request)
		{
			$header = new HeaderController;
			
			$meta = [
				'meta_title' => __('locale.text_forgot_get'),
				'meta_description' => '',
				'meta_keywords' => ''
			];
			
			$data['email_form'] = PasswordResets::select('email')->where('token', $request->token)->value('email');
			
			if (!$data['email_form']) return response('/');
			
			$header->setMeta($meta);
			
			$this->breadcrumbs->addCrumb(__('locale.text_breadcrumbs_account'), route(session('route_url') . '_account'));
			$this->breadcrumbs->addCrumb(__('locale.text_forgot_get'), route(session('route_url') . '_account_forgot', ['token' => $request->token]));
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
			$data['class'] = 'account_forgot';
			$data['canonical'] = '';
			$header->setRobots('noindex, nofollow');
			$data['title'] = __('locale.text_forgot_get');
			$data['codes'] = $request->token;
			
			return render_view(view('pages.site.account.forgot', $data), $this->region, false);
		}
		
		public function forgot(Request $request)
		{
			if ($request->session()->get('customer_id')) {
				return response()->json(['redirect' => route(session('route_url') . '_account_success')]);
			}
			
			if ($request->email && !$request->token) {
				$validate = \Validator::make($request->all(), [
					'email' => 'required|string|email|unique:customers,email'
				]);
				
				if (!$validate->fails()) {
					$customer = Customers::with([
						'address' => function($query) {
							$query->selectRaw("customer_id,id,address,address2");
						},
						'social' => function($query) {
							$query->selectRaw("customer_id,social,text");
						},
						'legal',
						'emails' => function($query) {
							$query->selectRaw("customer_id,email");
						},
						'phones' => function($query) {
							$query->selectRaw("customer_id,phone");
						}
					])->where('status', 1)->where('email', $request->email)->orderBy('created_at')->limit(1)->first();
					
					if (!empty($customer)) {
						$setting = session('settings');
						
						$token = Str::random(12);
						
						$data = [
							'params' => [
								'logo' => $setting['logo_mail'],
								'name' => !empty($setting['name'][session('lang')]) ? $setting['name'][session('lang')] : '',
								'url' => url(''),
								'text' => view('email.forgot', ['token' => $token])->render()
							],
							'subject' => __('locale.text_forgot_email_5'),
							'email' => $request->email,
							'template' => 'email.default'
						];
						
						Mail::later(Carbon::now()->addSeconds(5), new SendEmail($data));
						
						if (Mail::failures()) {
							return response()->json(['error' => Mail::failures()]);
						} else {
							DB::table('password_resets')->insert([
								'email' => $request->email,
								'token' => $token,
								'created_at' => Carbon::now()
							]);
							
							return response()->json(['success' => __('locale.text_forgot_email_4')]);
						}
					} else {
						return response()->json(['error' => __('locale.error_forgot_email')]);
					}
				}
			} else {
				$validate = \Validator::make($request->all(), [
					'email' => 'required|string|email',
					'password' => 'required|string|min:4|max:10',
					'token' => 'required'
				]);
			}
			
			if (!$validate->fails()) {
				$customer = Customers::with([
					'address' => function($query) {
						$query->selectRaw("customer_id,id,address,address2");
					},
					'social' => function($query) {
						$query->selectRaw("customer_id,social,text");
					},
					'legal',
					'emails' => function($query) {
						$query->selectRaw("customer_id,email");
					},
					'phones' => function($query) {
						$query->selectRaw("customer_id,phone");
					}
				])
					->where('status', 1)
					->where('email', $request->email)->first();
				
				if (!empty($customer)) {
					$salt = Str::random(9);
					
					Customers::where('id', $customer->id)->update(['ip' => $request->ip(), 'password' => sha1($salt . sha1($salt . sha1($request->password)))]);
					PasswordResets::where('token', $request->token)->delete();
					
					return response()->json(['redirect' => route('login')]);
				} else {
					return response()->json(['error' => __('locale.error_login_mail')]);
				}
			} else {
				$warning = '';
				
				foreach ($validate->errors()->messages() as $v) {
					$warning = $v[count($v)-1];
				}
				
				return response()->json(['error' => $warning]);
			}
		}
		
		public function send_code(Request $request) {
			if ($request->session()->has('phone_code2')) {
				return response()->json(['success' => __('locale.text_send_sms')]);
			} else if ($request->phone && $request->session()->has('phone_code')) {
				$phone = preg_replace('![^0-9]+!', '', $request->phone);
				
				if (isset($request->session()->get('phone_code')[$phone])) {
					$SMS4B = new CSms4bBaseController('Mosoptom', 'Mosoptom2020');
					$SMS4B->CSms4bBase('Mosoptom', 'Mosoptom2020');
					$SMS4B->GetSOAP("AccountParams", array("SessionID" => $SMS4B->GetSID()));
					
					$code = '';
					$chars = "1234567890";
					$max = 4;
					$size = strlen($chars) - 1;
					while ($max--) $code .= $chars[rand(0, $size)];
					
					$request->session()->put(['phone_code' => [$phone => $code]]);
					$request->session()->put(['phone_code_2' => 1]);
					
					$result = $SMS4B->SendSMS('Ваш код безопасности ' . $code, $phone);
					
					if ($result) {
						return response()->json(['success' => __('locale.text_send_sms')]);
					} else {
						return response()->json(['error' => __('locale.error_send_sms')]);
					}
				}
			} else {
				return response()->json(['error' => 'Error']);
			}
		}

		public function logout(Request $request)
		{
			$request->session()->forget('customer_id');
			$request->session()->forget('customer');
			$request->session()->forget('customer_group_id');
			
			return redirect('/');
		}
	}

