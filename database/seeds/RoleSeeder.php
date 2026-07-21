<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = new Role;
        $role->name = 'Администратор';
        $role->slug = 'admin';
        $role->description = 'Имеет доступ ко всему';
        $role->save();

        $role = new Role;
        $role->name = 'Менеджер';
        $role->slug = 'manager';
        $role->description = 'Имеет доступ к созданию и редактированию контента';
        $role->save();
    }
}