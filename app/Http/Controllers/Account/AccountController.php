<?php
	
	namespace App\Http\Controllers\Account;
	
	use Illuminate\Http\Request;
	use App\Http\Controllers\Controller;
	use App\Models\Customers;
	use App\Models\CustomerSocial;
	use App\Models\CustomerLegal;
	use App\Models\CustomerPhone;
	use App\Models\CustomerEmail;
	use App\Models\CustomerAddress;
	use App\Http\Controllers\CartController;
	use App\Http\Controllers\HeaderController;
	use App\Http\Controllers\GetContentController;
	
	class AccountController extends Controller
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
		
		public function index() {
			$header = new HeaderController;
			
			$meta = [
				'meta_title' => __('locale.text_account_menu_1'),
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
			$data['class'] = 'account';
			$data['canonical'] = '';
			$header->setRobots('noindex, nofollow');
			$data['title'] = __('locale.text_account_menu_1');
			$data['address_id'] = session('customer')['address_id'];
			
			if (!empty(session('customer')['social'])) {
				$data['socials'] = collect(session('customer')['social'])->keyBy('social')->toArray();
			} else {
				$data['socials'] = [];
			}
			
			$data['yandex_link'] = 'https://oauth.yandex.ru/authorize?response_type=code&client_id=32b0066fbbc84643a18b12bb9a97c81f&display=popup';
			
			return render_view(view('pages.site.account.account', $data), $this->region, false);
		}
		
		public function save(Request $request) {
			$fields = [];
			
			if ($request->type == 1) {
				$fields = [
					'legal.firstname' => 'required|max:300',
					'legal.lastname' => 'required|max:300',
					'legal.inn' => 'required',
					'legal.kpp' => 'required',
					'legal.ogrn' => 'required',
					'legal.company' => 'required',
					'legal.kontragent' => 'required',
					'legal.forma_sobstvennosti' => 'required',
					'legal.address' => 'required',
					'legal.address2' => 'required'
				];
			} else {
				$fields = [
					'firstname' => 'required|max:300',
					'lastname' => 'required|max:300',
					'phone' => 'required',
					'email' => 'required'
				];
			}
			
			if (!empty($request->address)) {
				$fields['address.*.address'] = 'required|max:300';
			}
			
			if (!is_null($request->password)) {
				$fields['password'] = 'required|min:4|max:300';
			}
			
			$validate = \Validator::make($request->all(), $fields);
			
			if ($validate->fails()) {
				return response()->json(['error' => $validate->errors()->messages()]);
			} else {
				CustomerLegal::where('customer_id', session('customer_id'))->delete();
				CustomerEmail::where('customer_id', session('customer_id'))->delete();
				CustomerPhone::where('customer_id', session('customer_id'))->delete();
				
				$customer = [];
				
				if ($request->type == 1) {
					$customer_legal = new CustomerLegal;
					$customer_legal->firstname = $request->legal['firstname'];
					$customer_legal->lastname = $request->legal['lastname'];
					$customer_legal->ogrn = $request->legal['ogrn'];
					$customer_legal->forma_sobstvennosti = $request->legal['forma_sobstvennosti'];
					$customer_legal->kontragent = $request->legal['kontragent'];
					$customer_legal->kpp = $request->legal['kpp'];
					$customer_legal->inn = $request->legal['inn'];
					$customer_legal->address = $request->legal['address'];
					$customer_legal->address2 = $request->legal['address2'];
					$customer_legal->company = $request->legal['company'];
					$customer_legal->customer_id = session('customer_id');
					
					$customer_legal->save();
					
					if (!empty($request->legal['phones'])) {
						foreach ($request->legal['phones'] as $phone) {
							if (!empty($phone['phone'])) {
								$cp = new CustomerPhone;
								$cp->customer_id = session('customer_id');
								$cp->phone = $phone['phone'];
								
								$cp->save();
							}
						}
						
						if (isset($request->legal['phone_default']) && !empty($request->legal['phones']) && isset($request->legal['phones'][$request->legal['phone_default']])) {
							$customer['phone'] = $request->legal['phones'][$request->legal['phone_default']]['phone'];
						}
					}
					
					if (!empty($request->legal['emails'])) {
						foreach ($request->legal['emails'] as $email) {
							if (!empty($email['email'])) {
								$ce = new CustomerEmail;
								$ce->customer_id = session('customer_id');
								$ce->email = $email['email'];
								
								$ce->save();
							}
						}
						
						if (isset($request->legal['email_default']) && !empty($request->legal['emails']) && isset($request->legal['emails'][$request->legal['email_default']])) {
							$customer['email'] = $request->legal['emails'][$request->legal['email_default']]['email'];
						}
					}
					
					$customer['type'] = 1;
				} else {
					$customer['type'] = 0;
					
					foreach ($fields as $key => $field) {
						if (isset($request->{$key})) {
							$customer[$key] = $request->{$key};
						}
					}
				}
				
				$customer['ip'] = $request->ip ? $request->ip : '';
				
				if (!is_null($request->password)) {
					$customer['password'] = DB::raw('SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1(?)))))', [$request->password]);
				}
				
				Customers::where('id', session('customer_id'))->update($customer);
				
				CustomerSocial::where('customer_id', session('customer_id'))->delete();
				
				if ($request->social) {
					foreach ($request->social as $key => $social) {
						if (!empty($social)) {
							$soc = new CustomerSocial;
							$soc->customer_id = session('customer_id');
							$soc->social = $key;
							$soc->text = $social;
							
							$soc->save();
						}
					}
				}
				
				if (!empty($request->address)) {
					foreach ($request->address as $key => $address) {
						if (!empty($address['id'])) {
							CustomerAddress::where('id', $address['id'])->update([
								'address' => $address['address'],
								'address2' => $address['address2'] ? $address['address2'] : ''
							]);
							
							$id = $address['id'];
						} else {
							$customer_address = new CustomerAddress;
							$customer_address->customer_id = session('customer_id');
							$customer_address->firstname = $request->firstname;
							$customer_address->lastname = $request->lastname;
							$customer_address->company = '';
							$customer_address->postcode = '';
							$customer_address->address = $address['address'];
							$customer_address->address2 = $address['address2'] ? $address['address2'] : '';
							$customer_address->city = '';
							
							$customer_address->save();
							$id = $customer_address->id;
						}
						
						if ($request->default == $key) {
							Customers::where('id', session('customer_id'))->update(['address_id' => $id]);
						}
					}
				}
				
				$customer = Customers::with([
					'address' => function ($query) {
						$query->selectRaw("customer_id,id,address,address2");
					},
					'social' => function ($query) {
						$query->selectRaw("customer_id,social,text");
					},
					'legal',
					'emails' => function ($query) {
						$query->selectRaw("customer_id,email");
					},
					'phones' => function ($query) {
						$query->selectRaw("customer_id,phone");
					}
				])->where('status', 1)->where('id', session('customer_id'))->first();
				
				if (!empty($customer)) {
					$request->session()->forget('customer');
					$request->session()->put(['customer' => $customer->toArray()]);
				}
				
				return response()->json(['success' => __('locale.text_account_12')]);
			}
		}
		
		public function yandex(Request $request) {
			$client_id = '32b0066fbbc84643a18b12bb9a97c81f';
			$client_secret = '0068d406ef2041b48045642b5c25462e';

			if ($request->code) {
				$query = array(
					'grant_type' => 'authorization_code',
					'code' => $request->code,
					'client_id' => $client_id,
					'client_secret' => $client_secret
				);
				
				$query = http_build_query($query);
				
				$header = "Content-type: application/x-www-form-urlencoded";
				
				$opts = array('http' =>
					array(
						'method'  => 'POST',
						'header'  => $header,
						'content' => $query
					)
				);
				$context = stream_context_create($opts);
				$result = file_get_contents('https://oauth.yandex.ru/token', false, $context);
				$result = json_decode($result);
				
				$params = array(
					'format'       => 'json',
					'oauth_token'  => $result->access_token
				);
				
				$userInfo = @file_get_contents('https://login.yandex.ru/info' . '?' . urldecode(http_build_query($params)));
	
				if ($userInfo) {
					echo '<script>window.opener.yandex_logged(\'' . $userInfo . '\'); window.close();</script>';
				}
			}
		}
		
		public function success() {
			$header = new HeaderController;
			$customer = session('customer');
			
			$meta = [
				'meta_title' => sprintf(__('locale.text_account_success_title'), $customer['firstname']),
				'meta_description' => '',
				'meta_keywords' => ''
			];
			
			$header->setMeta($meta);
			
			$this->breadcrumbs->addCrumb(__('locale.text_breadcrumbs_account'), route(session('route_url') . '_account'));
			$this->breadcrumbs->addCrumb(__('locale.text_breadcrumbs_account_success'), route(session('route_url') . '_account_success'));
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
			$data['class'] = 'account_success';
			$data['canonical'] = '';
			$header->setRobots('noindex, nofollow');
			$data['title'] = sprintf(__('locale.text_account_success_title'), $customer['firstname']);
			
			return render_view(view('pages.site.account.success', $data), $this->region);
		}
	}