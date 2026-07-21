<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if ((isset($_GET['code']) && $_GET['code'] != '*fe_5gsgKn83m0Xjhd') || !isset($_GET['code'])) {
            abort(404);
        }

        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $admin = Role::where('name', 'Admin')->value('id');

        if (!$admin) {
            $role = Role::create(['name' => 'Admin']);

            $permissions = Permission::pluck('id', 'id')->all();

            $role->syncPermissions($permissions);

            $admin = $role->id;
        }

        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->roles = [$admin];
        $user->password = Hash::make($data['password']);
        $user->save();

        $user->assignRole([$admin]);

        return $user;
    }

    // Register
    public function showRegistrationForm(){
      $pageConfigs = ['bodyCustomClass' => 'bg-full-screen-image'];  

     return view('/auth/register', [
         'pageConfigs' => $pageConfigs,
         'code' => isset($_GET['code']) ? $_GET['code'] : ''
     ]);
   }
}
