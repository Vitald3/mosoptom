<?php

namespace App\Http\Middleware;

use Closure;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      $availLocale = (array)config('app.all_langs');

      if(session()->has('locale') && array_key_exists(session()->get('locale'), $availLocale)){
          app()->setLocale(session()->get('locale'));
      }
      
      return $next($request);
    }
}
