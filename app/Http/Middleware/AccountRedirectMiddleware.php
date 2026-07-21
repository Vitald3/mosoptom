<?php
	
	namespace App\Http\Middleware;
	
	use Closure;
	
	class AccountRedirectMiddleware
	{
		public function handle($request, Closure $next)
		{
			if (!$request->session()->get('customer_id')) {
				return redirect('/' . '#modal_login', 301);
			}
			
			return $next($request);
		}
	}