<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Mail;
	use App\Mail\SendEmail;
	use Carbon\Carbon;
	
	class FormActionController extends Controller
	{
		public function form_action(Request $request)
		{
			$json = [];
			
			$setting = session('settings');
			$lang = session('lang');
			
			$data = $request->toArray();
			
			$data['logo'] = asset($setting['logo_mail']);
			
			$images = [];
			$html = '';
			$mail = env('MAIL_USERNAME');
			
			if ($request->type == 'write') {
				$subject = 'Вопрос с сайта';
				
				$validate = \Validator::make($request->all(), [
					'email' => 'required|email',
					'text' => 'required'
				]);
				
				if ($request->email) {
					$mail = $request->email;
					$html .= '<p>Email клиента: <b>' . $request->email . '</b></p>';
				}
				
				if ($request->text) {
					$html .= "<p>Сообщение: \r\n\r\n" . $request->text . '</b></p>';
				}
				
				if ($request->file) {
					foreach ($request->file as $file) {
						$images[] = $file;
					}
				}
			} elseif ($request->type == 'price_download') {
				$subject = 'Запрос на скачивание прайса';
				
				$validate = \Validator::make($request->all(), [
					'email' => 'required|email',
					'name' => 'required',
					'phone' => 'required'
				]);
				
				if ($request->email) {
					$mail = $request->email;
					$html .= '<p>Email: <b>' . $request->email . '</b></p>';
				}
				
				if ($request->name) {
					$html .= "<p>Имя: \r\n\r\n" . $request->name . '</b></p>';
				}
				
				if ($request->phone) {
					$html .= "<p>Телефон: \r\n\r\n" . $request->phone . '</b></p>';
				}
			} elseif ($request->type == 'callback') {
				$subject = 'Запрос обратного звонка';
				
				$validate = \Validator::make($request->all(), [
					'phone' => 'required'
				]);
				
				if ($request->phone) {
					$html .= "<p>Телефон: \r\n\r\n" . $request->phone . '</b></p>';
				}
			} elseif ($request->type == 'free') {
				$subject = 'Нашли дешевле!';
				
				$validate = \Validator::make($request->all(), [
					'email' => 'required|email',
					'name' => 'required',
					'phone' => 'required',
					'text' => 'required'
				]);
				
				if ($request->email) {
					$mail = $request->email;
					$html .= '<p>Email: <b>' . $request->email . '</b></p>';
				}
				
				if ($request->name) {
					$html .= "<p>Имя: \r\n\r\n" . $request->name . '</b></p>';
				}
				
				if ($request->phone) {
					$html .= "<p>Телефон: \r\n\r\n" . $request->phone . '</b></p>';
				}
				
				if ($request->text) {
					$html .= "<p>Где нашли: \r\n\r\n" . $request->text . '</b></p>';
				}
			}
			
			if (isset($validate) && $validate->fails()) {
				return response()->json(['errors' => $validate->errors()->messages()]);
			}
			
			if (isset($subject) && in_array($request->type, ['write', 'price_download', 'callback', 'free'])) {
				$setting = session('settings');
				
				$data = [
					'params' => [
						'logo' => $setting['logo_mail'],
						'name' => !empty($setting['name'][session('lang')]) ? $setting['name'][session('lang')] : '',
						'url' => url(''),
						'text' => $html
					],
					'subject' => __('locale.text_newsletter_9'),
					'template' => 'email.default',
					'images' => $images
				];
				
				Mail::later(Carbon::now()->addSeconds(5), new SendEmail($data));
				
				$type = 'error';
				
				if (Mail::failures()) {
					$message = Mail::failures();
				} else {
					$type = 'success';
					$message = __('locale.text_write_success');
				}
				
				return response()->json([$type => $message]);
			}
		}
	}