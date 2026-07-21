<?php

use Illuminate\Database\Seeder;
use App\Models\Settings;
use App\Models\Languages;
use App\Models\Layouts;
use App\Models\RolesPermissions;
use App\Models\CustomerGroups;
use App\Models\CustomerGroupDescription;
use App\Models\Currencies;

class OtherTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$seeder = new CustomerGroups;
		$seeder->approval = 0;
		$seeder->sort_order = 1;
		$seeder->status = 1;
	
		$seeder->save();
	
		$cgd = new CustomerGroupDescription;
		$cgd->lang = 'ru';
		$cgd->customer_group_id = $seeder->id;
		$cgd->name = 'По умолчанию';
		$cgd->description = '';
	
		$cgd->save();
		
        $settings = [
            'meta_title' => [
                'ru' => 'Мой магазин'
            ],
            'meta_description' => [
                'ru' => 'Мой магазин'
            ],
            'meta_keywords' => [
                'ru' => 'Мой магазин'
            ],
            'format1' => [
                'ru' => 'В городе'
            ],
            'format2' => [
                'ru' => 'Города'
            ],
            'format3' => [
                'ru' => 'В город'
            ],
            'name' => [
                'ru' => 'Мой магазин'
            ],
            'open' => [
                'ru' => '09:00 - 18:00'
            ],
            'phone' => '+79999999999',
            'email' => 'local@mail.ru',
            'limit' => 25,
			'limit_sait' => 25,
			'customer_group_id' => 1,
            'default_language' => 'ru',
            'currency_code' => 'RUB',
            'layout_id' => 5,
            'main_layout_id' => 1
        ];

        $seeder = new Settings;
        $seeder->code = 'settings';
        $seeder->settings = $settings;
        $seeder->save();

        $seeder = new Languages;
        $seeder->code = 'ru';
        $seeder->hreflang = 'ru-ru';
        $seeder->mask = '+7 (999) 999-99-99';
        $seeder->name = 'Русский';
        $seeder->image = 'assets/site/img/languages/RU.svg';
        $seeder->sort = 1;
        $seeder->status = 1;
        $seeder->save();

        $seeder = new Layouts;
        $seeder->name = 'Статья';
        $seeder->route = 'pages';
        $seeder->save();

        $seeder = new Layouts;
        $seeder->name = 'Категория статьи';
        $seeder->route = 'page_category';
        $seeder->save();

        $seeder = new Layouts;
        $seeder->name = 'Товар';
        $seeder->route = 'products';
        $seeder->save();

        $seeder = new Layouts;
        $seeder->name = 'Категория товара';
        $seeder->route = 'categories';
        $seeder->save();

        $seeder = new Layouts;
        $seeder->name = 'Главная';
        $seeder->route = 'home';
        $seeder->save();
	
		$seeder = new Layouts;
		$seeder->name = 'По умолчанию';
		$seeder->route = 'default';
		$seeder->save();
	
		$seeder = new Layouts;
		$seeder->name = 'Аккаунт';
		$seeder->route = 'account';
		$seeder->save();

        $seeder = new RolesPermissions;
        $seeder->role_id = 1;
        $seeder->permission_id = 1;
        $seeder->save();

        $seeder = new RolesPermissions;
        $seeder->role_id = 1;
        $seeder->permission_id = 2;
        $seeder->save();

        $seeder = new RolesPermissions;
        $seeder->role_id = 1;
        $seeder->permission_id = 3;
        $seeder->save();

        $seeder = new RolesPermissions;
        $seeder->role_id = 1;
        $seeder->permission_id = 4;
        $seeder->save();

        $seeder = new RolesPermissions;
        $seeder->role_id = 2;
        $seeder->permission_id = 1;
        $seeder->save();

        $seeder = new CustomerGroups;
        $seeder->approval = 0;
        $seeder->sort_order = 1;
        $seeder->status = 1;

        $seeder->save();

        $seeder = new CustomerGroupDescription;
        $seeder->lang = 'ru';
        $seeder->customer_group_id = $seeder->id;
        $seeder->name = 'По умолчанию';
        $seeder->description = '';

        $seeder->save();

        $seeder = new Currencies;
        $seeder->title = 'Рубль';
        $seeder->code = 'RUB';
        $seeder->decimal = 0;
        $seeder->position = 1;
        $seeder->value = '1.00000000';
        $seeder->status = 1;

        $seeder->save();

        $seeder = new Status;
        $seeder->type = 1;

        $seeder->save();

        $seeder = new StatusDescription;
        $seeder->status_id = $seeder->id;
        $seeder->lang = 'ru';
        $seeder->name = 'Новый заказ';

        $seeder = new Status;
        $seeder->type = 1;

        $seeder->save();

        $seeder = new StatusDescription;
        $seeder->status_id = $seeder->id;
        $seeder->lang = 'ru';
        $seeder->name = 'Выполнен';

        $seeder = new Status;
        $seeder->type = 1;

        $seeder->save();

        $seeder = new StatusDescription;
        $seeder->status_id = $seeder->id;
        $seeder->lang = 'ru';
        $seeder->name = 'Ожидание';

        $seeder = new Status;
        $seeder->type = 1;

        $seeder->save();

        $seeder = new StatusDescription;
        $seeder->status_id = $seeder->id;
        $seeder->lang = 'ru';
        $seeder->name = 'В обработке';

        $seeder = new Status;
        $seeder->type = 1;

        $seeder->save();

        $seeder = new StatusDescription;
        $seeder->status_id = $seeder->id;
        $seeder->lang = 'ru';
        $seeder->name = 'Отмена';

        $seeder = new Status;
        $seeder->type = 2;

        $seeder->save();

        $seeder = new StatusDescription;
        $seeder->status_id = $seeder->id;
        $seeder->lang = 'ru';
        $seeder->name = 'В наличии';

        $seeder = new Status;
        $seeder->type = 2;

        $seeder->save();

        $seeder = new StatusDescription;
        $seeder->status_id = $seeder->id;
        $seeder->lang = 'ru';
        $seeder->name = 'Пол заказ';

        $seeder = new Status;
        $seeder->type = 3;

        $seeder->save();

        $seeder = new StatusDescription;
        $seeder->status_id = $seeder->id;
        $seeder->lang = 'ru';
        $seeder->name = 'Ожидание';

        $seeder->save();

        $seeder = new Status;
        $seeder->type = 3;

        $seeder->save();

        $seeder = new StatusDescription;
        $seeder->status_id = $seeder->id;
        $seeder->lang = 'ru';
        $seeder->name = 'В обработке';
    }
}