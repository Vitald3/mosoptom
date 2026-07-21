<?php
	
	namespace App\Providers;
	
	use App\Helpers\PathRouteService;
	
	use Illuminate\Support\Facades\Blade;
	use Illuminate\Support\ServiceProvider;
	
	class RoutesSeviceProvider extends ServiceProvider
	{
		public function register()
		{
			$this->app->singleton(PathRouteService::class, function ($app) {
				return new PathRouteService();
			});
		}
	}
