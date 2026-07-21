<?php
	
	namespace App\Http\Middleware;
	
	use Closure;
	use Illuminate\Support\Facades\Cache;
	
	class UrlMiddleware
	{
		public function handle($request, Closure $next)
		{
			$host = $request->header('host');
			$uri = $request->getRequestUri();
			
			if (substr($host, 0, 4) == 'www.') {
				$isHttps = $request->server->get('HTTPS') && 'off' !== strtolower($request->server->get('HTTPS'));
				
				if (!$request->secure() && $isHttps) {
					$request->server->set('HTTPS', true);
				}
				
				$url = ($isHttps ? 'https://' : 'http://') . str_replace('www.', '', $host) . $uri;
				
				$request->headers->set('host', str_replace('www.', '', $host));
				return redirect($url, 301);
			} else {
				if (!$request->secure()) {
					$request->server->set('HTTPS', true);
					return redirect($request->path(), 301);
				}
			}
			
			if (!Cache::has('seo_url')) {
				$routes = app(\App\Helpers\PathRouteService::class);
				Cache::put('seo_url', $routes->getRoutes());
			}
			
			return $next($request);
		}
	}