<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;
        $user->name = 'Администратор';
        $user->email = 'spartak_27@bk.ru';
        $user->role_id = 1;
        $user->password = Hash::make('11111111');
        $user->status = 1;

        $user->save();
    }
}
