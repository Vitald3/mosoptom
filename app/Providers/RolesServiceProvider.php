<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class RolesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('role', function ($role){
            return "<?php if(auth()->check() && auth()->user()->getAllPermissionsByUser2({$role}, auth()->user()->id)): ?>";
        });

        Blade::directive('endrole', function ($role){
            return "<?php endif; ?>";
        });
    }
}
