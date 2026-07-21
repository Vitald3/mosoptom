<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * @param $request
     * @param Closure $next
     * @param $role
     * @param null $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions)
    {
        $permissions = explode('|', $permissions);

        $route = Route::getFacadeRoot()->current()->uri();
        $user = auth()->user();

        if ($user && !$user->getAllPermissionsByUser($permissions, $user->id)) {
            if ($route == 'admin') {
                return redirect('admin/login');
            }

            return response()->view('errors.403', [], 403);
        } else if (!$user) {
            return redirect('admin/login');
        }

        return $next($request);
    }
}