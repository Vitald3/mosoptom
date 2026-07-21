<?php

use Illuminate\Database\Seeder;

use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $manageUser = new Permission;
        $manageUser->name = 'Просмотр';
        $manageUser->slug = 'look';
        $manageUser->save();

        $createTasks = new Permission;
        $createTasks->name = 'Создание';
        $createTasks->slug = 'create';
        $createTasks->save();

        $createTasks = new Permission;
        $createTasks->name = 'Редактирование';
        $createTasks->slug = 'edit';
        $createTasks->save();

        $createTasks = new Permission;
        $createTasks->name = 'Удаление';
        $createTasks->slug = 'delete';
        $createTasks->save();

        $createTasks = new Permission;
        $createTasks->name = 'Редактирование контента';
        $createTasks->slug = 'content_edit';
        $createTasks->save();
    }
}
