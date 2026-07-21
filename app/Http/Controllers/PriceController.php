<?php

namespace App\Http\Controllers;

use App\Models\AttributeDescription;
use App\Models\Layouts;
use App\Models\PathRouteService;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\Filters;
use App\Models\Options;
use App\Models\OptionDescription;
use App\Models\OptionValues;
use App\Models\OptionValueDescription;
use App\Models\ProductReward;
use App\Models\ProductSpecial;
use App\Models\ProductOption;
use App\Models\ProductOptionValues;
use App\Models\FilterDescription;
use App\Models\FilterValues;
use App\Models\FilterValueDescription;
use App\Models\Categories;
use App\Models\CategoryDescription;
use App\Models\Products;
use App\Models\ProductDescription;
use App\Models\ProductImage;
use App\Models\FilterProduct;
use App\Models\ProductCategory;
use App\Models\ProductAttributeImage;
use App\Models\PageCategories;
use App\Models\PageCategoryDescription;
use App\Models\Pages;
use App\Models\PageDescription;
use App\Models\PageImage;
use App\Models\PageAttribute;
use App\Models\PageAttributeImage;
use App\Models\Languages;
use Illuminate\Support\Facades\Cache;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;
use League\Csv\CharsetConverter;
use League\Csv\Exception;
use Illuminate\Support\Facades\Validator;
use DB;
use Carbon\Carbon;

class PriceController extends Controller
{
    private $fields_category = [
        'parent_id' => 'ID родительской категории',
        'layout_id' => 'ID макета',
        'slug' => 'Seo Url',
        'image' => 'Изображение',
        'top' => 'Выводить в шапке',
        'sort' => 'Порядок сортировки',
        'status' => 'Статус'
    ];

    private $fields_category_description = [
        'name' => 'Наименование',
        'meta_title' => 'Meta Title',
        'meta_description' => 'Meta Description',
        'meta_keywords' => 'Meta Keywords'
    ];

    private $fields_product = [
        'parent_id' => 'ID главной категории',
        'category_id' => 'Категории',
        'layout_id' => 'ID макета',
        'slug' => 'Seo Url',
        'image' => 'Изображение',
        'images' => 'Дополнительные изображения',
        'price' => 'Цена',
        'quantity' => 'Количество',
        'model' => 'Модель',
        'popular' => 'Количество просмотров',
        'product_special' => 'Акционная цена',
        'sort' => 'Порядок сортировки',
        'reward' => 'Бонусные баллы',
        'product_reward' => 'Бонусные баллы по группам пользователей',
        'status' => 'Статус',
        'attributes' => 'Характеристики',
        'product_option' => 'Опции',
        'filters' => 'Фильтры'
    ];

    private $fields_product_description = [
        'name' => 'Наименование',
        'meta_title' => 'Meta Title',
        'meta_description' => 'Meta Description',
        'meta_keywords' => 'Meta Keywords'
    ];

    private $fields_category_page = [
        'parent_id' => 'ID родительской категории',
        'layout_id' => 'ID макета',
        'slug' => 'Seo Url',
        'image' => 'Изображение',
        'sort' => 'Порядок сортировки',
        'status' => 'Статус'
    ];

    private $fields_category_page_description = [
        'name' => 'Наименование',
        'meta_title' => 'Meta Title',
        'meta_description' => 'Meta Description',
        'meta_keywords' => 'Meta Keywords'
    ];

    private $fields_page = [
        'parent_id' => 'ID категории',
        'layout_id' => 'ID макета',
        'slug' => 'Seo Url',
        'image' => 'Изображение',
        'images' => 'Дополнительные изображения',
        'top' => 'Выводить в шапке',
        'bottom' => 'Выводить в подвале',
        'sort' => 'Порядок сортировки',
        'status' => 'Статус',
        'attributes' => 'Характеристики'
    ];

    private $fields_page_description = [
        'name' => 'Наименование',
        'meta_title' => 'Meta Title',
        'meta_description' => 'Meta Description',
        'meta_keywords' => 'Meta Keywords'
    ];

    public function __construct() {
        $this->settings = session('settings');

        $this->lang = session('lang');

        if (Cache::has('seo_url')) {
            $this->routes = Cache::get('seo_url');
        } else {
            $this->routes = [];
        }

        $this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;

        $classes = array('breadcrumb', 'breadcrumb-item');
        $this->breadcrumbs->addCssClasses($classes);
        $this->breadcrumbs->setDivider('');

        $this->breadcrumbs->addCrumb(__('locale.home'), url('admin'));
    }

    public function index() {
        $data['langs'] = Languages::orderBy('name', 'asc')->get();
        $data['layouts'] = Layouts::orderBy('name', 'asc')->get();

        $category = new Categories;
        $category_fields = $category->getTableColumns();
        $category_description = new CategoryDescription;
        $category_description_fields = $category_description->getTableColumns();

        $data['category_fields'] = [];

        foreach ($category_fields as $field) {
            if (isset($this->fields_category[$field])) {
                $data['category_fields'][] = [
                    'field' => $field,
                    'name' => $this->fields_category[$field]
                ];
            }
        }

        foreach ($category_description_fields as $field) {
            if (isset($this->fields_category_description[$field])) {
                $data['category_fields'][] = [
                    'field' => $field,
                    'name' => $this->fields_category_description[$field]
                ];
            }
        }

        $products = new Products;
        $product_fields = $products->getTableColumns();
        $product_description = new ProductDescription;
        $product_description_fields = $product_description->getTableColumns();

        $data['product_fields'] = [];

        foreach ($product_fields as $field) {
            if (isset($this->fields_product[$field])) {
                if ($field == 'parent_id') {
                    $data['product_fields'][] = [
                        'field' => 'category_id',
                        'name' => 'Категории'
                    ];
                }

                $data['product_fields'][] = [
                    'field' => $field,
                    'name' => $this->fields_product[$field]
                ];

                if ($field == 'reward') {
                    $data['product_fields'][] = [
                        'field' => 'product_reward',
                        'name' => 'Бонусные баллы по группам пользователей'
                    ];
                }

                if ($field == 'price') {
                    $data['product_fields'][] = [
                        'field' => 'product_special',
                        'name' => 'Акционная цена'
                    ];
                }
            }
        }

        $data['product_fields'][] = [
            'field' => 'attributes',
            'name' => 'Характеристики'
        ];

        $data['product_fields'][] = [
            'field' => 'product_option',
            'name' => 'Опции'
        ];

        $data['product_fields'][] = [
            'field' => 'filters',
            'name' => 'Фильтры'
        ];

        foreach ($product_description_fields as $field) {
            if (isset($this->fields_product_description[$field])) {
                $data['product_fields'][] = [
                    'field' => $field,
                    'name' => $this->fields_product_description[$field]
                ];
            }
        }

        $category = new PageCategories;
        $category_fields = $category->getTableColumns();
        $category_description = new PageCategoryDescription;
        $category_description_fields = $category_description->getTableColumns();

        $data['category_page_fields'] = [];

        foreach ($category_fields as $field) {
            if (isset($this->fields_category_page[$field])) {
                $data['category_page_fields'][] = [
                    'field' => $field,
                    'name' => $this->fields_category[$field]
                ];
            }
        }

        foreach ($category_description_fields as $field) {
            if (isset($this->fields_category_page_description[$field])) {
                $data['category_page_fields'][] = [
                    'field' => $field,
                    'name' => $this->fields_category_description[$field]
                ];
            }
        }

        $pages = new Pages;
        $page_fields = $pages->getTableColumns();
        $page_description = new PageDescription;
        $page_description_fields = $page_description->getTableColumns();

        $data['page_fields'] = [];

        foreach ($page_fields as $field) {
            if (isset($this->fields_page[$field])) {
                $data['page_fields'][] = [
                    'field' => $field,
                    'name' => $this->fields_page[$field]
                ];
            }
        }

        $data['page_fields'][] = [
            'field' => 'attributes',
            'name' => 'Характеристики'
        ];

        foreach ($page_description_fields as $field) {
            if (isset($this->fields_page_description[$field])) {
                $data['page_fields'][] = [
                    'field' => $field,
                    'name' => $this->fields_page_description[$field]
                ];
            }
        }

        $data['categories'] = [];

        $categories = Categories::with('metaLang')->where('status', 1)->get()->keyBy('id');

        foreach ($categories as $id => $category) {
            $name = (array)$this->getCategory($category, $categories);

            $data['categories'][$id] = implode(' > ', $name);
        }

        $data['page_categories'] = [];

        $categories = PageCategories::with('metaLang')->where('status', 1)->get()->keyBy('id');

        foreach ($categories as $id => $category) {
            $name = (array)$this->getCategory($category, $categories);

            $data['page_categories'][$id] = implode(' > ', $name);
        }

        $data['max_id'] = (int)Products::max('id');
        $data['max_category_id'] = (int)Categories::max('id');
        $data['max_page_category_id'] = (int)PageCategories::max('id');
        $data['max_page_id'] = (int)Pages::max('id');

        $this->breadcrumbs->addCrumb('Экспорт/Импорт', url('admin/export_import'));
        $data['breadcrumbs'] = $this->breadcrumbs->render();

        return view('pages.export', $data);
    }

    public function export(Request $request)
    {
        $export = $request->csv_export;

        if ($export && !empty($request->field)) {
            $header_column = [];
            $header = [];
            $select = [];
            $select_description = [];
            $lang = $this->lang;
            $unset = [];
            $export_category = isset($export['export_category']) ? $export['export_category'] : 0;
            $categories = isset($export['categories']) ? $export['categories'] : [];
            $delimiter = isset($export['delimiter']) ? $export['delimiter'] : '|';
            $start = isset($export['limit_from']) ? $export['limit_from'] : 0;
            $limit = isset($export['limit_to']) ? $export['limit_to'] : 0;

            if (!empty($export['lang'])) {
                $lang = $export['lang'];
            }

            try {
                if ($request->field == 'category') {
                    if (!empty($export['category_fields'])) {
                        $header_column = $export['category_fields'];
                    }

                    $table = new Categories;
                    $category_fields = $table->getTableColumns();
                    $select[] = 'id';

                    $category_description = new CategoryDescription;
                    $category_description_fields = $category_description->getTableColumns();

                    $header[] = '__ID__';

                    if (!$header_column) {
                        foreach ($category_fields as $field) {
                            if (isset($this->fields_category[$field])) {
                                $header[] = '__' . mb_strtoupper($field) . '__';
                                $select[] = $field;
                            }
                        }

                        $select_description[] = 'category_id';
                        $unset[] = 'category_id';

                        foreach ($category_description_fields as $field) {
                            if ($field != 'id' && isset($this->fields_category_description[$field])) {
                                $header[] = '__' . mb_strtoupper($field) . '__';
                                $select_description[] = $field;
                            }
                        }
                    } else {
                        $select_description[] = 'category_id';
                        $unset[] = 'category_id';

                        foreach ($header_column as $field) {
                            if (isset($this->fields_category[$field])) {
                                $header[] = '__' . mb_strtoupper($field) . '__';
                                $select[] = $field;
                            }

                            if ($field != 'id' && isset($this->fields_category_description[$field])) {
                                $header[] = '__' . mb_strtoupper($field) . '__';
                                $select_description[] = $field;
                            }
                        }
                    }

                    $file_path = $lang . '_category_' . date('y-m-d') . '.csv';
                }
                elseif ($request->field == 'category_page') {
                    if (!empty($export['category_page_fields'])) {
                        $header_column = $export['category_page_fields'];
                    }

                    $table = new PageCategories;
                    $category_fields = $table->getTableColumns();
                    $select[] = 'id';

                    $category_description = new PageCategoryDescription;
                    $category_description_fields = $category_description->getTableColumns();

                    $header[] = '__ID__';

                    if (!$header_column) {
                        foreach ($category_fields as $field) {
                            if (isset($this->fields_category[$field])) {
                                $header[] = '__' . mb_strtoupper($field) . '__';
                                $select[] = $field;
                            }
                        }

                        $select_description[] = 'category_id';
                        $unset[] = 'category_id';

                        foreach ($category_description_fields as $field) {
                            if ($field != 'id' && isset($this->fields_category_description[$field])) {
                                $header[] = '__' . mb_strtoupper($field) . '__';
                                $select_description[] = $field;
                            }
                        }
                    } else {
                        $select_description[] = 'category_id';
                        $unset[] = 'category_id';

                        foreach ($header_column as $field) {
                            if (isset($this->fields_category[$field])) {
                                $header[] = '__' . mb_strtoupper($field) . '__';
                                $select[] = $field;
                            }

                            if ($field != 'id' && isset($this->fields_category_description[$field])) {
                                $header[] = '__' . mb_strtoupper($field) . '__';
                                $select_description[] = $field;
                            }
                        }
                    }

                    $file_path = $lang . '_page_category_' . date('y-m-d') . '.csv';
                }
                elseif ($request->field == 'product') {
                    if (!empty($export['product_fields'])) {
                        $header_column = $export['product_fields'];
                    }

                    $table = new Products;
                    $product_fields = $table->getTableColumns();
                    $select[] = 'products.id';

                    $product_description = new ProductDescription;
                    $product_description_fields = $product_description->getTableColumns();

                    $header[] = '__ID__';

                    if (($header_column && in_array('category_id', $header_column)) || !$header_column) {
                        if ($export_category == 2) {
                            $header[] = '__CATEGORY_NAME__';
                        }
                    }

                    if ($export_category == 1 && !$header_column) {
                        $header[] = '__CATEGORY_ID__';
                    }

                    if (!$header_column) {
                        foreach ($product_fields as $field) {
                            if ($export_category == 0 && $field == 'parent_id') continue;

                            if (isset($this->fields_product[$field])) {
                                $header[] = '__' . mb_strtoupper($field) . '__';
                                $select[] = $field;

                                if ($field == 'image') {
                                    $header[] = '__IMAGES__';
                                }

                                if ($field == 'reward') {
                                    $header[] = '__PRODUCT_REWARD__';
                                }

                                if ($field == 'price') {
                                    $header[] = '__PRODUCT_SPECIAL__';
                                }
                            }
                        }

                        $select_description[] = 'product_id';
                        $unset[] = 'product_id';
                        $unset[] = 'attributes';
                        $unset[] = 'product_option';
                        $unset[] = 'product_reward';
                        $unset[] = 'product_special';
                        $unset[] = 'filters';
                        $unset[] = 'images';
                        $header[] = '__ATTRIBUTES__';
                        $header[] = '__OPTIONS__';
                        $header[] = '__FILTERS__';

                        foreach ($product_description_fields as $field) {
                            if ($field != 'id' && isset($this->fields_product_description[$field])) {
                                $header[] = '__' . mb_strtoupper($field) . '__';
                                $select_description[] = $field;
                            }
                        }
                    } else {
                        $select_description[] = 'product_id';
                        $unset[] = 'product_id';
                        $unset[] = 'attributes';
                        $unset[] = 'product_option';
                        $unset[] = 'product_reward';
                        $unset[] = 'filters';
                        $unset[] = 'product_special';

                        foreach ($header_column as $field) {
                            if ($export_category == 0 && $field == 'parent_id') continue;

                            if (isset($this->fields_product[$field])) {
                                if ($export_category == 2 && $field == 'category_id') continue;
                                if ($export_category == 2 && $field == 'category_id') {
                                    $field = 'category_name';
                                }

                                $header[] = '__' . mb_strtoupper($field) . '__';

                                if (!in_array($field, ['attributes', 'product_option', 'product_reward', 'product_special', 'filters', 'category_id', 'category_name'])) {
                                    $select[] = $field;
                                }
                            }

                            if ($field != 'id' && isset($this->fields_product_description[$field])) {
                                $header[] = '__' . mb_strtoupper($field) . '__';
                                $select_description[] = $field;
                            }
                        }
                    }

                    $file_path = $lang . '_product_' . date('y-m-d') . '.csv';
                }
                elseif ($request->field == 'page') {
                    if (!empty($export['page_fields'])) {
                        $header_column = $export['page_fields'];
                    }

                    $table = new Pages;
                    $page_fields = $table->getTableColumns();
                    $select[] = 'pages.id';

                    $page_description = new PageDescription;
                    $page_description_fields = $page_description->getTableColumns();

                    $header[] = '__ID__';

                    if (($header_column && in_array('parent_id', $header_column)) || !$header_column) {
                        if ($export_category == 2) {
                            $header[] = '__CATEGORY_NAME__';
                        }
                    }

                    if (!$header_column) {
                        foreach ($page_fields as $field) {
                            if ($export_category == 0 && $field == 'parent_id') continue;

                            if (isset($this->fields_page[$field])) {
                                $select[] = $field;

                                if ($export_category == 2 && $field == 'parent_id') continue;
                                if ($export_category == 1 && $field == 'parent_id') $field = 'category_id';

                                $header[] = '__' . mb_strtoupper($field) . '__';

                                if ($field == 'image') {
                                    $header[] = '__IMAGES__';
                                }
                            }
                        }

                        $select_description[] = 'page_id';
                        $unset[] = 'page_id';
                        $unset[] = 'attributes';
                        $unset[] = 'images';
                        $header[] = '__ATTRIBUTES__';

                        foreach ($page_description_fields as $field) {
                            if ($field != 'id' && isset($this->fields_page_description[$field])) {
                                $header[] = '__' . mb_strtoupper($field) . '__';
                                $select_description[] = $field;
                            }
                        }
                    } else {
                        $select_description[] = 'page_id';
                        $unset[] = 'page_id';
                        $unset[] = 'attributes';

                        foreach ($header_column as $field) {
                            if ($export_category == 0 && $field == 'parent_id') continue;

                            if (isset($this->fields_page[$field])) {
                                if (!in_array($field, ['attributes'])) {
                                    $select[] = $field;
                                }

                                if ($export_category == 2 && $field == 'parent_id') continue;

                                if ($field == 'parent_id') {
                                    if ($export_category == 2) {
                                        $field = 'category_name';
                                    } elseif ($export_category == 1) {
                                        $field = 'category_id';
                                    }
                                }

                                $header[] = '__' . mb_strtoupper($field) . '__';
                            }

                            if ($field != 'id' && isset($this->fields_page_description[$field])) {
                                $header[] = '__' . mb_strtoupper($field) . '__';
                                $select_description[] = $field;
                            }
                        }
                    }

                    $file_path = $lang . '_page_' . date('y-m-d') . '.csv';
                }

                $order = [];

                foreach ($header as $column) {
                    $column = str_replace('__', '', strtolower($column));
                    if ($column === 'options') $column = 'product_option';
                    $order[$column] = $column;
                }

                if (isset($table)) {
                    $results = $table::with([
                        'meta' => function ($query) use ($lang, $select_description) {
                            $query->select($select_description)->where('lang', $lang);
                        },
                    ])->select($select);

                    if ($request->field == 'product') {
                        if (in_array('__CATEGORY_ID__', $header) || in_array('__CATEGORY_NAME__', $header)) {
                            $locale = config('app.locale');

                            if ($export_category == 1) {
                                $results->with('category_id:product_id,category_id');
                            } elseif ($export_category == 2) {
                                $results->with([
                                    'category_name' => function ($query) use ($lang) {
                                        config(['app.locale' => $lang]);

                                        $query->with('category_name')
                                            ->distinct('cd.name')
                                            ->join('category_description as cd', 'cd.category_id', '=', 'categories.id')
                                            ->select('cd.name', 'categories.id', 'categories.parent_id')
                                            ->where('cd.lang', $lang);
                                    }
                                ]);
                            }

                            config(['app.locale' => $locale]);
                        }

                        if (in_array('__ATTRIBUTES__', $header)) {
                            $results->with([
                                'attributes' => function ($query) use ($lang) {
                                    $query->with('product_attribute_image:attribute_id,image')
                                        ->selectRaw("concat(attribute_description.name, '|', product_attribute.text) as attribute, attribute_description.attribute_id")
                                        ->where('attribute_description.lang', $lang)
                                        ->where('product_attribute.lang', $lang);
                                }
                            ]);
                        }

                        if (in_array('__OPTIONS__', $header)) {
                            $results->with([
                                'product_option' => function($query) use($lang) {
                                    $query->join('options as o', 'o.id', '=', 'product_option.option_id')
                                        ->join('option_description as od', 'od.option_id', '=', 'o.id')
                                        ->join('product_option_values as pov', 'pov.product_option_id', '=', 'product_option.id')
                                        ->join('option_value_description as ovd', 'ovd.option_value_id', '=', 'pov.option_value_id')
                                        ->select('o.type', 'pov.quantity', 'pov.weight', 'pov.image', 'pov.price', 'pov.reward', 'ovd.name as value', 'product_option.product_id', 'product_option.option_id', 'product_option.id', 'product_option.required', 'product_option.required', 'od.name')
                                        ->where('od.lang', $lang)
                                        ->where('o.status', 1);
                                }
                            ]);
                        }

                        if (in_array('__PRODUCT_REWARD__', $header)) {
                            $results->with('product_reward:product_id,customer_group_id,reward');
                        }

                        if (in_array('__PRODUCT_SPECIAL__', $header)) {
                            $results->with('product_special:product_id,customer_group_id,price,date_start,date_end');
                        }
	
						if (in_array('__FILTERS__', $header)) {
							$results->with([
								'filters' => function ($query) use ($lang) {
									$query
										->selectRaw("distinct concat(filter_description.name, '|', fvd.name) as filter")
										->join('filter_value_description as fvd', 'fvd.filter_value_id', '=', 'product_filter.filter_value_id')
										->where('filter_description.lang', $lang)
										->where('fvd.lang', $lang);
								}
							]);
						}

                        if (in_array('__IMAGES__', $header)) {
                            $results->with('images:product_id,image');
                        }
                    }
                    elseif ($request->field == 'page') {
                        if (in_array('__CATEGORY_NAME__', $header) && $export_category == 2) {
                            $locale = config('app.locale');

                            $results->with([
                                'category_name' => function($query) use($lang) {
                                    config(['app.locale' => $lang]);

                                    $query->with('category_name')->distinct('pcd.name')
                                        ->join('page_category_description as pcd', 'pcd.category_id', '=', 'page_categories.id')
                                        ->select('pcd.name', 'page_categories.id', 'page_categories.parent_id')
                                        ->where('pcd.lang', $lang);
                                }
                            ]);

                            config(['app.locale' => $locale]);
                        }

                        if (in_array('__ATTRIBUTES__', $header)) {
                            $results->with([
                                'attributes' => function ($query) use ($lang) {
                                    $query->with('page_attribute_image:attribute_id,image')
                                        ->selectRaw("concat(attribute_description.name, '|', page_attribute.text) as attribute, attribute_description.attribute_id")
                                        ->where('attribute_description.lang', $lang)
                                        ->where('page_attribute.lang', $lang);
                                }
                            ]);
                        }

                        if (in_array('__IMAGES__', $header)) {
                            $results->with('images:page_id,image');
                        }
                    }

                    $records = [];

                    if ($categories) {
                        if ($request->field == 'page') {
                            $results->whereIn('pages.parent_id', $categories);
                        } else {
                            $results->join('product_category as pc', 'pc.product_id', '=', 'products.id')->whereIn('pc.category_id', $categories);
                        }
                    }

                    if ($limit > $start) {
                        $results = $results->skip($start)->take($limit-$start)->get();
                    } else {
                        $results = $results->get();
                    }

                    if (!$results->isEmpty()) {
                        $results = $results->toArray();

                        foreach ($results as &$result) {
                            if (!empty($result['meta'][0])) {
                                foreach ($select_description as $meta) {
                                    if (strpos($meta, '_id') === false && isset($result['meta'][0][$meta])) {
                                        $result[$meta] = $result['meta'][0][$meta];
                                    }
                                }
                            }

                            if (isset($result['category_id'])) {
                                $categories = [];

                                foreach ($result['category_id'] as $product_category) {
                                    $categories[] = $product_category['category_id'];
                                }

                                $result['category_id'] = implode($delimiter, $categories);
                            }

                            if ($request->field == 'page' && isset($result['parent_id'])) {
                                if ($export_category == 1) {

                                    $result['category_id'] = $result['parent_id'];
                                }

                                unset($result['parent_id']);
                            }

                            if (isset($result['category_name'])) {
                                $categories = [];
                                $categories2 = [];

                                if (isset($result['category_name'][0])) {
                                    foreach ($result['category_name'] as $all_children2) {
                                        $this->children($all_children2, $categories, 'name');

                                        $categories2[] = implode($delimiter, array_reverse($categories));
                                        $categories = [];
                                    }
                                } elseif (!empty($result['category_name'])) {
                                    $this->children($result['category_name'], $categories, 'name');

                                    $categories2[] = implode($delimiter, array_reverse($categories));
                                }

                                $result['category_name'] = implode("\n", $categories2);
                            }

                            if (isset($result['images'])) {
                                $images = [];

                                foreach ($result['images'] as $image) {
                                    $images[] = $image['image'];
                                }

                                $result['images'] = implode($delimiter, $images);
                            }

                            if (isset($result['meta'])) unset($result['meta']);

                            if (isset($result['attributes'])) {
                                $attributes = [];

                                foreach ($result['attributes'] as $attribute) {
                                    $attribute_images = isset($attribute['product_attribute_image']) ? $attribute['product_attribute_image'] : (isset($attribute['page_attribute_image']) ? $attribute['page_attribute_image'] : []);

                                    if (!empty($attribute_images)) {
                                        $attribute_image_ = [];

                                        foreach ($attribute_images as $attribute_image) {
                                            $attribute_image_[] = $attribute_image['image'];
                                        }

                                        $attribute['attribute'] = $attribute['attribute'] . ($attribute_image_ ? $delimiter . implode(',', $attribute_image_) : '');
                                    }

                                    if ($attribute_images) unset($attribute_images);

                                    $attributes[] = $attribute['attribute'];
                                }

                                $result['attributes'] = implode("\n", $attributes);
                            }

                            if (isset($result['product_option'])) {
                                $options = [];

                                foreach ($result['product_option'] as $option) {
                                    $options[] = $option['name'] .
                                        $delimiter . $option['value'] .
                                        $delimiter . $option['required'] .
                                        $delimiter . $option['type'] .
                                        $delimiter . $option['price'] .
                                        $delimiter . $option['quantity'] .
                                        $delimiter . $option['weight'] .
                                        $delimiter . $option['reward'] .
                                        $delimiter . $option['image'];
                                }

                                $result['product_option'] = implode("\n", $options);
                            }

                            if (isset($result['product_reward'])) {
                                $product_reward = [];

                                foreach ($result['product_reward'] as $option) {
                                    $product_reward[] = $option['customer_group_id'] . $delimiter . $option['reward'];
                                }

                                $result['product_reward'] = implode("\n", $product_reward);
                            }

                            if (isset($result['product_special'])) {
                                $product_special = [];

                                foreach ($result['product_special'] as $special) {
                                    $product_special[] = $special['customer_group_id'] . $delimiter . $special['price'] . $delimiter . date('Y-m-d', \strtotime($special['date_start'])) . $delimiter . date('Y-m-d', \strtotime($special['date_end']));
                                }

                                $result['product_special'] = implode("\n", $product_special);
                            }

                            if (isset($result['filters'])) {
                                $filters = [];

                                foreach ($result['filters'] as $filter) {
                                    $filters[] = $filter['filter'];
                                }

                                $result['filters'] = implode("\n", $filters);
                            }

                            $records[] = array_map(function($i) use ($result) {
                                return $result[$i];
                            }, $order);
                        }

                        $csv = Writer::createFromString();

                        if (!empty($export['csv_delimiter'])) {
                            $csv = $csv->setDelimiter($export['csv_delimiter']);
                        }

                        if (!empty($export['file_encoding'])) {
                            $encoder = (new CharsetConverter())->inputEncoding('utf-8')->outputEncoding($export['file_encoding']);
                            $csv = $csv->addFormatter($encoder);
                        }

                        $csv->insertOne($header);
                        $csv->insertAll($records);

                        $csv->output($file_path);
                    } else {
                        return redirect('admin/export_import')->with('error', 'По выбранным параметрам ничего не найдено');
                    }
                } else {
                    return redirect('admin/export_import')->with('error', 'Неверный тип данных');
                }
            } catch (Exception | RuntimeException $e) {
                return redirect('admin/export_import')->with('error', $e->getMessage());
            }
        }
    }

    public function import(Request $request)
    {
        if (!$request->hasFile('file')) {
            return redirect('admin/export_import')->with('error', 'Выберите файл Csv');
        } else {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $extension = strtolower(utf8_substr($filename, utf8_strrpos($filename, '.')+1, strlen($filename)));

            if ($extension != 'csv') {
                return redirect('admin/export_import')->with('error', 'Выберите файл Csv');
            }
        }

        $export = $request->csv_import;

        if ($export) {
            $lang = $this->lang;
            $header = 0;
            $fields = [];
            $layout_id = isset($export['layout_id']) ? $export['layout_id'] : 0;
            $status = isset($export['status']) ? $export['status'] : 0;
            $mode = isset($export['mode']) ? $export['mode'] : 1;
            $key_field = isset($export['key_field']) ? $export['key_field'] : 'id';
            $delimiter = isset($export['delimiter']) ? $export['delimiter'] : '|';
            $success = '';
            $errors = [];
            $success_row = 0;
            $success_update_row = 0;

            try {
                if (!empty($export['lang'])) {
                    $lang = $export['lang'];
                }

                $csv = Reader::createFromPath($file, 'r');
                $csv->skipEmptyRecords();
                $csv->setHeaderOffset(0);
                $header_file = count(@explode(';', $csv->getHeader()[0]));

                if (!empty($export['csv_delimiter'])) {
                    $csv = $csv->setDelimiter($export['csv_delimiter']);
                }

                if (!empty($export['file_encoding'])) {
                    CharsetConverter::addTo($csv, $export['file_encoding'], 'utf-8');
                }

                $records = $csv->getRecords();

                foreach ($records as $key => $record) {
                    if ($mode != 2 && empty($record['__ID__'])) continue;

                    foreach ($record as $field_name => $field) {
                        $field_name = strtolower(str_replace('__', '', $field_name));
                        $fields[$key][$field_name] = $field;
                    }
                }

                if (!$fields) {
                    return redirect('admin/export_import')->with('error', 'В файле недопустимое название колонки');
                }

                if ($request->field == 'category') {
                    $fields_ = $this->fields_category;
                    $fields_description = $this->fields_category_description;
                } elseif ($request->field == 'product') {
                    $fields_ = $this->fields_product;
                    $fields_description = $this->fields_product_description;
                } elseif ($request->field == 'category_page') {
                    $fields_ = $this->fields_category_page;
                    $fields_description = $this->fields_category_page_description;
                } elseif ($request->field == 'page') {
                    $fields_ = $this->fields_page;
                    $fields_description = $this->fields_page_description;
                }
	
				foreach ($fields as $key => $record) {
					$meta = [];
		
					foreach ($record as $field_name => &$field) {
						if (isset($fields_description[$field_name])) {
							if (!$meta) $meta = ['lang' => $lang];
							$meta[$field_name] = $field;
							unset($fields[$key][$field_name]);
						}
					}
		
					if (!$meta) continue;
		
					$fields[$key]['meta'] = $meta;
				}

                foreach ($fields[array_key_first($fields)] as $field_name => $field) {
                    if ($field_name === 'options') $field_name = 'product_option';

                    if (isset($fields_[$field_name]) || $field_name == 'id' || $field_name == 'category_name' || $field_name == 'category_id') {
                        $header++;
                    }

                    if ($field_name == 'meta') {
                        foreach ($field as $field_name => $field2) {
                            if (isset($fields_description[$field_name])) {
                                $header++;
                            }
                        }
                    }
                }

                if ($header_file != 0 && $header_file == $header) {
                    if ($request->field == 'category') {
                        $import_id = !empty($export['import_id']) ? $export['import_id'] : 0;

                        if ($import_id) {
                            $max_id = (int)Categories::max('id');
                        }

                        foreach ($fields as $key => &$field) {
                            if ((isset($field['layout_id']) && $field['layout_id'] == 0) && $layout_id) {
                                $field['layout_id'] = $layout_id;
                            }

                            if ((isset($field['status']) && $field['status'] == 0) && $status) {
                                $field['status'] = $status;
                            }

                            $field['parent_id'] = isset($field['parent_id']) ? $field['parent_id'] : 0;
                            $id = 0;

                            if ($mode != 2) {
                                if ($key_field == 'id') {
                                    $id = $field['id'];
                                } elseif (!empty($field['name']) && $key_field == 'name') {
                                    $id = (int)CategoryDescription::select('category_id')
                                        ->where('name', $field['name'])
                                        ->where('lang', $lang)
                                        ->value('category_id');
                                }
                            } else {
                                if (isset($field['id']) && isset($max_id)) {
                                    $id = $field['id'];

                                    if ($id <= $max_id) {
                                        $import_id = false;
                                    }
                                }
                            }

                            if (!$id && $mode != 2) {
                                $errors[] = 'В строке №' . $key . ' ошибки:<br>&nbsp;&nbsp;&nbsp; - идентификатор категории не найден';
                                continue;
                            }
	
							$count_id = Categories::where('id', $id)->count();
	
							if (!$count_id && ($mode == 2 || $mode == 3)) {
								$messages = [
									'slug' => 'required|not_in:sort-name,sort-popular,sort-price,page,news,catalog|max:255|unique:categories,slug' . ($id ? ',' . $id . ',id' : '') . '|alpha_dash',
									'layout_id' => 'required',
									'meta.meta_title' => 'required',
									'meta.name' => 'required'
								];
							}

                            if (isset($messages)) {
                                $validator = Validator::make($field, $messages);

                                if ($validator->fails()) {
                                    $validate_error = [];

                                    foreach (json_decode($validator->errors(), true) as $error) {
                                        $validate_error[] = $error[0];
                                    }

                                    $errors[] = 'В строке №' . $key . ' ошибки:<br>&nbsp;&nbsp;&nbsp; - ' . implode('<br>&nbsp;&nbsp;&nbsp; - ', $validate_error);
                                    continue;
                                }
                            }

                            if ($mode == 1) {
                                $category = [];

                                if (!empty($field['slug'])) $category['slug'] = $field['slug'];
                                if (!empty($field['layout_id'])) $category['layout_id'] = $field['layout_id'];
                                if (!empty($field['top'])) $category['top'] = $field['top'];
                                if (isset($field['image'])) {
                                    if (filter_var($field['image'], FILTER_VALIDATE_URL)) {
                                        $field['image'] = $this->download(public_path() . '/../images/other/', $field['image'], 'images/other/');
                                    }

                                    $category['image'] = $field['image'];
                                }
                                if (isset($field['sort'])) $category['sort'] = $field['sort'];
                                if (isset($field['status'])) $category['status'] = $field['status'];
                                if (!empty($field['parent_id'])) $category['parent_id'] = $field['parent_id'];
                                if (isset($field['status'])) $category['status'] = $field['status'];

                                $query = Categories::where('id', $id)->update($category);

                                if ($query) {
                                    if (isset($field['meta'])) {
                                        $meta = $field['meta'];

                                        $cd['lang'] = $lang;
                                        $cd['category_id'] = $id;
                                        if (isset($meta['name'])) $cd['name'] = $meta['name'];
                                        if (isset($meta['meta_title'])) $cd['meta_title'] = $meta['meta_title'];
                                        if (isset($meta['meta_description'])) $cd['meta_description'] = $meta['meta_description'];
                                        if (isset($meta['meta_keywords'])) $cd['meta_keywords'] = $meta['meta_keywords'];
                                        if (isset($meta['description'])) $cd['description'] = $meta['description'];

                                        CategoryDescription::where('category_id', $id)->update($cd);
                                    }

                                    $success_row++;
                                }
                            }
                            elseif ($mode == 2) {
                                $category = new Categories;
                                $category->slug = $field['slug'];
                                $category->layout_id = $field['layout_id'];
                                $category->image = isset($field['image']) ? $field['image'] : '';
                                $category->top = isset($field['top']) ? $field['top'] : '';
                                $category->sort = isset($field['sort']) ? $field['sort'] : 0;
                                $category->parent_id = $field['parent_id'];
                                $category->status = isset($field['status']) ? $field['status'] : 0;

                                $category->save();

                                $meta = $field['meta'];

                                $cd = new CategoryDescription;
                                $cd->lang = $lang;
                                $cd->category_id = $category->id;
                                $cd->name = $meta['name'];
                                $cd->meta_title = $meta['meta_title'];
                                $cd->meta_description = !empty($meta['meta_description']) ? $meta['meta_description'] : '';
                                $cd->meta_keywords = !empty($meta['meta_keywords']) ? $meta['meta_keywords'] : '';
                                $cd->description = !empty($meta['description']) ? $meta['description'] : '';

                                $cd->save();
                                $success_row++;
                            }
                            elseif ($mode == 3) {
                                $category = [];
                                if (!empty($field['slug'])) $category['slug'] = $field['slug'];
                                if (!empty($field['layout_id'])) $category['layout_id'] = $field['layout_id'];
                                if (isset($field['image'])) $category['image'] = $field['image'];
                                if (isset($field['top'])) $category['top'] = $field['top'];
                                if (isset($field['sort'])) $category['sort'] = $field['sort'];
                                if (isset($field['status'])) $category['status'] = $field['status'];
                                if (!empty($field['parent_id'])) $category['parent_id'] = $field['parent_id'];
                                if (isset($field['status'])) $category['status'] = $field['status'];
								$category['id'] = $id;

                                $query = Categories::where('id', $id)->update($category);

                                if (!$query) {
                                    if (!empty($field['slug']) && !empty($field['meta']['name']) && !empty($field['meta']['meta_title'])) {
                                        if (!$import_id) {
                                            $category['id'] = Categories::insert($category)->id;
                                        } else {
                                            Categories::insert($category);
                                        }

                                        $success_row++;
                                    } else {
                                        $errors[] = 'В строке №' . $key . ' ошибки:<br>&nbsp;&nbsp;&nbsp; - Минимальный набор полей: ID категории, Seo Url';
                                        continue;
                                    }
                                } else {
                                    $success_update_row++;
                                }

                                if (isset($field['meta'])) {
                                    $meta = $field['meta'];

									$cd['lang'] = $lang;
									$cd['text'] = !empty($meta['text']) ? $meta['text'] : '';
									$cd['description'] = !empty($meta['description']) ? $meta['description'] : '';
									$cd['meta_keywords'] = !empty($meta['meta_keywords']) ? $meta['meta_keywords'] : '';
									$cd['meta_description'] = !empty($meta['meta_description']) ? $meta['meta_description'] : '';
									$cd['meta_title'] = !empty($meta['meta_title']) ? $meta['meta_title'] : '';
									$cd['name'] = !empty($meta['name']) ? $meta['name'] : '';

                                    $query = CategoryDescription::where('category_id', $id)->update($cd);

                                    if (!$query) {
                                        $cd['category_id'] = $category['id'];

                                        if (isset($cd['name']) && isset($cd['meta_title'])) {
                                            CategoryDescription::insert($cd);
                                        } else {
                                            $errors[] = 'В строке №' . $key . ' ошибки:<br>&nbsp;&nbsp;&nbsp; - Минимальный набор полей: Наименование категории, Meta Title';
                                            continue;
                                        }
                                    }
                                }
                            }
                            elseif ($mode == 4) {
                                $query = Categories::find($id)->delete();

                                if ($query) {
                                    CategoryDescription::where('category_id', $id)->delete();
                                    $success_row++;
                                }
                            }
                        }
                    }
                    elseif ($request->field == 'category_page') {
                        $import_id = !empty($export['import_id']) ? $export['import_id'] : 0;

                        if ($import_id) {
                            $max_id = (int)PageCategories::max('id');
                        }

                        foreach ($fields as $key => &$field) {
                            if ((isset($field['layout_id']) && $field['layout_id'] == 0) && $layout_id) {
                                $field['layout_id'] = $layout_id;
                            }

                            if ((isset($field['status']) && $field['status'] == 0) && $status) {
                                $field['status'] = $status;
                            }

                            $field['parent_id'] = 0;
                            $id = 0;

                            if ($mode != 2) {
                                if ($key_field == 'id') {
                                    $id = $field['id'];
                                    $field['parent_id'] = isset($field['parent_id']) ? $field['parent_id'] : 0;
                                } elseif (!empty($field['name']) && $key_field == 'name') {
                                    $id = (int)PageCategoryDescription::select('category_id')
                                        ->where('name', $field['name'])
                                        ->where('lang', $lang)
                                        ->value('category_id');
                                }
                            } else {
                                if (isset($field['id']) && isset($max_id)) {
                                    $id = $field['id'];

                                    if ($id <= $max_id) {
                                        $import_id = false;
                                    }
                                }
                            }

                            if (!$id && $mode != 2) {
                                $errors[] = 'В строке №' . $key . ' ошибки:<br>&nbsp;&nbsp;&nbsp; - идентификатор категории не найден';
                                continue;
                            }
	
							$count_id = PageCategories::where('id', $id)->count();
	
							if (!$count_id && ($mode == 2 || $mode == 3)) {
								$messages = [
									'slug' => 'required|not_in:sort-name,sort-popular,sort-price,page,news,catalog|max:255|unique:page_categories,slug' . ($id ? ',' . $id . ',id' : '') . '|alpha_dash',
									'layout_id' => 'required',
									'meta.meta_title' => 'required',
									'meta.name' => 'required'
								];
							}

                            if (isset($messages)) {
                                $validator = Validator::make($field, $messages);

                                if ($validator->fails()) {
                                    $validate_error = [];

                                    foreach (json_decode($validator->errors(), true) as $error) {
                                        $validate_error[] = $error[0];
                                    }

                                    $errors[] = 'В строке №' . $key . ' ошибки:<br>&nbsp;&nbsp;&nbsp; - ' . implode('<br>&nbsp;&nbsp;&nbsp; - ', $validate_error);
                                    continue;
                                }
                            }

                            if ($mode == 1) {
                                $category = [];

                                if (!empty($field['slug'])) $category['slug'] = $field['slug'];
                                if (!empty($field['layout_id'])) $category['layout_id'] = $field['layout_id'];
                                if (isset($field['image'])) {
                                    if (filter_var($field['image'], FILTER_VALIDATE_URL)) {
                                        $field['image'] = $this->download(public_path() . '/../images/other/', $field['image'], 'images/other/');
                                    }

                                    $category['image'] = $field['image'];
                                }
                                if (isset($field['sort'])) $category['sort'] = $field['sort'];
                                if (isset($field['status'])) $category['status'] = $field['status'];
                                if (!empty($field['parent_id'])) $category['parent_id'] = $field['parent_id'];
                                if (isset($field['status'])) $category['status'] = $field['status'];

                                $query = PageCategories::where('id', $id)->update($category);

                                if ($query) {
                                    if (isset($field['meta'])) {
                                        $meta = $field['meta'];

                                        $cd = new PageCategoryDescription;
                                        $cd->lang = $lang;
                                        $cd->category_id = $id;
                                        if (isset($meta['name'])) $cd->name = $meta['name'];
                                        if (isset($meta['meta_title'])) $cd->meta_title = $meta['meta_title'];
                                        if (isset($meta['meta_description'])) $cd->meta_description = $meta['meta_description'];
                                        if (isset($meta['meta_keywords'])) $cd->meta_keywords = $meta['meta_keywords'];
                                        if (isset($meta['description'])) $cd->description = $meta['description'];

                                        PageCategoryDescription::where('category_id', $id)->update($cd->toArray());
                                    }

                                    $success_row++;
                                }
                            }
                            elseif ($mode == 2) {
                                $category = new PageCategories;
                                $category->slug = $field['slug'];
                                $category->layout_id = $field['layout_id'];
                                $category->image = isset($field['image']) ? $field['image'] : '';
                                $category->sort = isset($field['sort']) ? $field['sort'] : 0;
                                $category->parent_id = $field['parent_id'];
                                $category->status = isset($field['status']) ? $field['status'] : 0;

                                $category->save();

                                $meta = $field['meta'];

                                $cd = new PageCategoryDescription;
                                $cd->lang = $lang;
                                $cd->category_id = $category->id;
                                $cd->name = $meta['name'];
                                $cd->meta_title = $meta['meta_title'];
                                $cd->meta_description = !empty($meta['meta_description']) ? $meta['meta_description'] : '';
                                $cd->meta_keywords = !empty($meta['meta_keywords']) ? $meta['meta_keywords'] : '';
                                $cd->description = !empty($meta['description']) ? $meta['description'] : '';

                                $cd->save();
                                $success_row++;
                            }
                            elseif ($mode == 3) {
                                $category = new PageCategories;
                                if (!empty($field['slug'])) $category['slug'] = $field['slug'];
                                if (!empty($field['layout_id'])) $category['layout_id'] = $field['layout_id'];
                                if (isset($field['image'])) $category['image'] = $field['image'];
                                if (isset($field['sort'])) $category['sort'] = $field['sort'];
                                if (isset($field['status'])) $category['status'] = $field['status'];
                                if (!empty($field['parent_id'])) $category['parent_id'] = $field['parent_id'];
                                if (isset($field['status'])) $category['status'] = $field['status'];

                                $query = PageCategories::where('id', $id)->update($category);

                                if (!$query) {
                                    if (!empty($field['slug']) && !empty($field['meta']['name']) && !empty($field['meta']['meta_title'])) {
                                        if (!$import_id) {
                                            $category['id'] = PageCategories::insert($category)->id;
                                        } else {
                                            $category['id'] = $id;
                                            PageCategories::insert($category);
                                        }

                                        $success_row++;
                                    } else {
                                        $errors[] = 'В строке №' . $key . ' ошибки:<br>&nbsp;&nbsp;&nbsp; - Минимальный набор полей: ID категории, Seo Url';
                                        continue;
                                    }
                                } else {
                                    $success_update_row++;
                                }

                                if (isset($field['meta'])) {
                                    $meta = $field['meta'];

                                    $cd['lang'] = $lang;
                                    if (!empty($meta['name'])) $cd['name'] = $meta['name'];
                                    if (!empty($meta['meta_title'])) $cd['meta_title'] = $meta['meta_title'];
                                    if (!empty($meta['meta_description'])) $cd['meta_description'] = $meta['meta_description'];
                                    if (!empty($meta['meta_keywords'])) $cd['meta_keywords'] = $meta['meta_keywords'];
                                    if (!empty($meta['description'])) $cd['description'] = $meta['description'];

                                    $query = PageCategoryDescription::where('category_id', $id)->update($cd);

                                    if (!$query) {
                                        $cd['category_id'] = $category['id'];

                                        if (isset($cd['name']) && isset($cd['meta_title'])) {
                                            PageCategoryDescription::insert($cd);
                                        } else {
                                            $errors[] = 'В строке №' . $key . ' ошибки:<br>&nbsp;&nbsp;&nbsp; - Минимальный набор полей: Наименование категории, Meta Title';
                                            continue;
                                        }
                                    }
                                }
                            }
                            elseif ($mode == 4) {
                                $query = PageCategories::find($id)->delete();

                                if ($query) {
                                    PageCategoryDescription::where('category_id', $id)->delete();
                                    $success_row++;
                                }
                            }
                        }
                    }
                    elseif ($request->field == 'product') {
                        $import_id = !empty($export['import_id']) ? $export['import_id'] : 0;

                        if ($import_id) {
                            $max_id = (int)Products::max('id');
                        }

                        foreach ($fields as $key => &$field) {
                            if ((isset($field['layout_id']) && $field['layout_id'] == 0) && $layout_id) {
                                $field['layout_id'] = $layout_id;
                            }

                            if ((isset($field['status']) && $field['status'] == 0) && $status) {
                                $field['status'] = $status;
                            }

                            $id = 0;

                            if ($mode != 2) {
                                if (!$import_id) {
                                    if ($key_field == 'id') {
                                        $id = $field['id'];
                                    } elseif (!empty($field['name']) && $key_field == 'name') {
                                        $id = (int)ProductDescription::select('product_id')->where('name', $field['name'])->where('lang', $lang)->value('product_id');
                                    } elseif (!empty($field['model']) && $key_field == 'model') {
                                        $id = (int)Products::select('product_id')->where('model', $field['model'])->value('product_id');
                                    }
                                } else {
                                    $id = $field['id'];

                                    if ($id <= $max_id) {
                                        $import_id = false;
                                    }
                                }
                            } else {
                                if (isset($field['id']) && isset($max_id)) {
                                    $id = $field['id'];

                                    if ($id <= $max_id) {
                                        $import_id = false;
                                    }
                                }
                            }

                            if (!$id && $mode != 2) {
                                $errors[] = 'В строке №' . $key . ' ошибки:<br>&nbsp;&nbsp;&nbsp; - идентификатор товара не найден';
                                continue;
                            }
	
							$count_id = Products::where('id', $id)->count();
	
							if (!$count_id && ($mode == 2 || $mode == 3)) {
								$messages = [
									'slug' => 'required|not_in:sort-name,sort-popular,sort-price,page,news,catalog|max:255|unique:products,slug' . ($id ? ',' . $id . ',id' : '') . '|alpha_dash',
									'layout_id' => 'required',
									'meta.meta_title' => 'required',
									'meta.name' => 'required'
								];
							}

                            if (isset($messages)) {
                                $validator = Validator::make($field, $messages);

                                if ($validator->fails()) {
                                    $validate_error = [];

                                    foreach(json_decode($validator->errors(), true) as $error) {
                                        $validate_error[] = $error[0];
                                    }

                                    $errors[] = 'В строке №' . $key . ' ошибки:<br>&nbsp;&nbsp;&nbsp; - ' . implode('<br>&nbsp;&nbsp;&nbsp; - ', $validate_error);
                                    continue;
                                }
                            }

                            $now = Carbon::now();

                            if ($mode == 1) {
                                $product = [];

                                if (!empty($field['slug'])) $product['slug'] = $field['slug'];
                                if (!empty($field['layout_id'])) $product['layout_id'] = $field['layout_id'];
                                if (isset($field['model'])) $product['model'] = $field['model'];
                                if (isset($field['reward'])) $product['reward'] = $field['reward'];
                                if (isset($field['popular'])) $product['popular'] = $field['popular'];
                                if (isset($field['price'])) {
                                    if ($export['calc_mode_1'] && !empty($export['calc_mode_1_text'])) {
                                        $price = $export['calc_mode_1_text'];
                                        $calc = $export['calc_mode_1'];

                                        if ($calc == '*') {
                                            $field['price'] *= $price;
                                        } elseif ($calc == '/') {
                                            $field['price'] /= $price;
                                        } elseif ($calc == '+') {
                                            $field['price'] += $price;
                                        } elseif ($calc == '-') {
                                            $field['price'] -= $price;
                                        }
                                    }

                                    $product['price'] = $field['price'];
                                }

                                if (isset($field['image'])) {
                                    if (filter_var($field['image'], FILTER_VALIDATE_URL)) {
                                        $field['image'] = $this->download(public_path() . '/../images/products/', $field['image'], 'images/products/');
                                    }

                                    $product['image'] = $field['image'];
                                }

                                if (isset($field['sort'])) $product['sort'] = $field['sort'];
                                if (isset($field['status'])) $product['status'] = $field['status'];
                                if (!empty($field['parent_id'])) $product['parent_id'] = $field['parent_id'];
                                if (isset($field['status'])) $product['status'] = $field['status'];

                                $query = Products::where('id', $id)->update($product);

                                if ($query) {
                                    if (isset($field['images'])) {
                                        $field['images'] = @explode($delimiter, $field['images']);
                                        ProductImage::where('product_id', $id)->delete();

                                        if (isset($field['images'][0])) {
                                            foreach ($field['images'] as $image) {
                                                if (filter_var($image, FILTER_VALIDATE_URL)) {
                                                    $image = $this->download(public_path() . '/../images/products/', $image, 'images/products/');
                                                }

                                                $images[] = ['product_id' => $id, 'image' => $image, 'updated_at' => $now, 'created_at' => $now];
                                            }
                                        } else {
                                            if (filter_var($field['images'], FILTER_VALIDATE_URL)) {
                                                $field['images'] = $this->download(public_path() . '/../images/products/', $field['images'], 'images/products/');
                                            }

                                            $images[] = ['product_id' => $id, 'image' => $field['images'], 'updated_at' => $now, 'created_at' => $now];
                                        }

                                        if ($images) ProductImage::insert($images);
                                    }

                                    if (isset($field['category_id'])) {
                                        $field['category_id'] = @explode($delimiter, $field['category_id']);
                                        ProductCategory::where('product_id', $id)->delete();
                                        $category_ids = [];

                                        if (isset($field['category_id'][0])) {
                                            foreach ($field['category_id'] as $category_id) {
                                                $category_ids[] = ['product_id' => $id, 'category_id' => $category_id, 'updated_at' => $now, 'created_at' => $now];
                                            }
                                        } else {
                                            $category_ids[] = ['product_id' => $id, 'category_id' => $field['category_id'], 'updated_at' => $now, 'created_at' => $now];
                                        }

                                        if (isset($category_ids)) {
                                            if (empty($field['parent_id'])) {
                                                Products::where('id', $id)->update(['parent_id', $category_ids[count($category_ids) - 1]['category_id']]);
                                            }

                                            ProductCategory::insert($category_ids);
                                        }
                                    }

                                    if (isset($field['category_name'])) {
                                        $category_names = @explode("\n", $field['category_name']);
                                        ProductCategory::where('product_id', $id)->delete();
                                        $category_ids = [];

                                        if (isset($category_names[1])) {
                                            foreach ($category_names as $category_name) {
                                                $parent_categories = @explode($delimiter, $category_name);

                                                if (isset($parent_categories[0])) {
                                                    $parents_id = [];

                                                    foreach ($parent_categories as $key => $parent_name) {
                                                        $cd = CategoryDescription::select('c.id')
                                                            ->join('categories as c', 'c.id', '=', 'category_description.category_id')
                                                            ->where('c.parent_id', (isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0))
                                                            ->where('category_description.name', trim($parent_name))
                                                            ->where('category_description.lang', $lang)
                                                            ->value('c.id');

                                                        $parents_id[$key] = (int)$cd;
                                                    }

                                                    if (count($parents_id) == count($parent_categories)) {
                                                        $category_ids[] = ['product_id' => $id, 'category_id' => $parents_id[count($parents_id) - 1], 'updated_at' => $now, 'created_at' => $now];
                                                    }
                                                } else {
                                                    $category_id = (int)CategoryDescription::select('c.id')
                                                        ->join('categories as c', 'c.id', '=', 'category_description.category_id')
                                                        ->where('category_description.name', trim($parent_categories))
                                                        ->where('category_description.lang', $lang)
                                                        ->value('c.id');

                                                    if ($category_id) $category_ids[] = ['product_id' => $id, 'category_id' => $category_id, 'updated_at' => $now, 'created_at' => $now];
                                                }
                                            }
                                        }
                                        else {
                                            $parent_categories = @explode($delimiter, $field['category_name']);

                                            if (isset($parent_categories[0])) {
                                                $parents_id = [];

                                                foreach ($parent_categories as $key => $parent_name) {
                                                    $cd = CategoryDescription::select('c.id')
                                                        ->join('categories as c', 'c.id', '=', 'category_description.category_id')
                                                        ->where('c.parent_id', (isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0))
                                                        ->where('category_description.name', trim($parent_name))
                                                        ->where('category_description.lang', $lang)
                                                        ->value('c.id');

                                                    $parents_id[$key] = (int)$cd;
                                                }

                                                if (count($parents_id) == count($parent_categories)) {
                                                    $category_ids[] = ['product_id' => $id, 'category_id' => $parents_id[count($parents_id) - 1], 'updated_at' => $now, 'created_at' => $now];
                                                }
                                            } else {
                                                $category_id = (int)CategoryDescription::select('c.id')
                                                    ->join('categories as c', 'c.id', '=', 'category_description.category_id')
                                                    ->where('category_description.name', trim($parent_categories))
                                                    ->where('category_description.lang', $lang)
                                                    ->value('c.id');

                                                if ($category_id) $category_ids[] = ['product_id' => $id, 'category_id' => $category_id, 'updated_at' => $now, 'created_at' => $now];
                                            }
                                        }

                                        if ($category_ids) {
                                            if (empty($field['parent_id'])) {
                                                Products::where('id', $id)->update(['parent_id', $category_ids[count($category_ids) - 1]['category_id']]);
                                            }

                                            ProductCategory::insert($category_ids);
                                        }
                                    }

                                    if (isset($field['meta'])) {
                                        $meta = $field['meta'];

                                        $pd['lang'] = $lang;
                                        $pd['product_id'] = $id;
                                        if (isset($meta['name'])) $pd['name'] = $meta['name'];
                                        if (isset($meta['meta_title'])) $pd['meta_title'] = $meta['meta_title'];
                                        if (isset($meta['meta_description'])) $pd['meta_description'] = $meta['meta_description'];
                                        if (isset($meta['meta_keywords'])) $pd['meta_keywords'] = $meta['meta_keywords'];
                                        if (isset($meta['description'])) $pd['description'] = $meta['description'];

                                        ProductDescription::where('product_id', $id)->where('lang', $lang)->update($pd);
                                    }

                                    if (!empty($field['attributes'])) {
                                        $attributes = @explode("\n", $field['attributes']);

                                        if (isset($attributes[1])) {
                                            foreach ($attributes as $attribute) {
                                                $attribute_text = @explode($delimiter, $attribute);

                                                if (isset($attribute_text[1])) {
                                                    $attribute_name = trim($attribute_text[0]);
                                                    $attribute_value = trim($attribute_text[1]);

                                                    $attribute_id = AttributeDescription::select('attribute_id')
                                                        ->where('name', $attribute_name)
                                                        ->where('lang', $lang)
                                                        ->value('attribute_id');

                                                    if (!is_null($attribute_id)) {
                                                        ProductAttribute::where('attribute_id', $attribute_id)
                                                            ->where('product_id', $id)
                                                            ->where('lang', $lang)
                                                            ->update(['text' => $attribute_value]);

                                                        if (!empty($attribute_text[2])) {
                                                            $attribute_images = @explode(',', $attribute_text[2]);

                                                            if (isset($attribute_images[0])) {
                                                                foreach ($attribute_images as $attribute_image) {
                                                                    ProductAttributeImage::where('attribute_id', $attribute_id)
                                                                        ->where('product_id', $id)
                                                                        ->update(['image' => trim($attribute_image)]);
                                                                }
                                                            } else {
                                                                ProductAttributeImage::where('attribute_id', $attribute_id)
                                                                    ->where('product_id', $id)
                                                                    ->update(['image' => trim($attribute_text[2])]);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            $attribute_text = @explode($delimiter, $field['attributes']);

                                            if (isset($attribute_text[1])) {
                                                $attribute_name = trim($attribute_text[0]);
                                                $attribute_value = trim($attribute_text[1]);

                                                $attribute_id = AttributeDescription::select('attribute_id')
                                                    ->where('name', $attribute_name)
                                                    ->where('lang', $lang)
                                                    ->value('attribute_id');

                                                if (!is_null($attribute_id)) {
                                                    ProductAttribute::where('attribute_id', $attribute_id)
                                                        ->where('product_id', $id)
                                                        ->where('lang', $lang)
                                                        ->update(['text' => $attribute_value]);

                                                    if (!empty($attribute_text[2])) {
                                                        $attribute_images = @explode(',', $attribute_text[2]);

                                                        if (isset($attribute_images[0])) {
                                                            foreach ($attribute_images as $attribute_image) {
                                                                ProductAttributeImage::where('attribute_id', $attribute_id)
                                                                    ->where('product_id', $id)
                                                                    ->update(['image' => trim($attribute_image)]);
                                                            }
                                                        } else {
                                                            ProductAttributeImage::where('attribute_id', $attribute_id)
                                                                ->where('product_id', $id)
                                                                ->update(['image' => trim($attribute_text[2])]);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    if (!empty($field['options'])) {
                                        ProductOption::where('product_id', $id)->delete();
                                        ProductOptionValues::where('product_id', $id)->delete();
                                        $options = @explode("\n", $field['options']);

                                        if (isset($options[1])) {
                                            foreach ($options as $option) {
                                                $option_text = @explode($delimiter, $option);

                                                if (isset($option_text[1])) {
                                                    $option_name = trim($option_text[0]);
                                                    $option_value_text = trim($option_text[1]);
                                                    $option_required = trim($option_text[2]);
                                                    $option_type = trim($option_text[3]);
                                                    $option_price = trim($option_text[4]);
                                                    $option_quantity = isset($option_text[5]) ? trim($option_text[5]) : 0;
                                                    $option_weight = isset($option_text[6]) ? trim($option_text[6]) : 0;
                                                    $option_reward = isset($option_text[7]) ? trim($option_text[7]) : 0;
                                                    $option_image = isset($option_text[8]) ? trim($option_text[8]) : '';

                                                    $option_id = new Options;
                                                    $option_id->type = $option_type;
                                                    $option_id->sort_order = 0;
                                                    $option_id->status = 1;

                                                    $option_id->save();

                                                    $option_description = new OptionDescription;
                                                    $option_description->name = $option_name;
                                                    $option_description->lang = $lang;
                                                    $option_description->option_id = $option_id->id;

                                                    $option_description->save();

                                                    $option_values = new OptionValues;
                                                    $option_values->sort_order = 0;
                                                    $option_values->image = '';
                                                    $option_values->option_id = $option_id->id;

                                                    $option_values->save();

                                                    $option_value_description = new OptionValueDescription;
                                                    $option_value_description->name = $option_value_text;
                                                    $option_value_description->lang = $lang;
                                                    $option_value_description->option_value_id = $option_values->id;

                                                    $option_value_description->save();

                                                    if (!in_array($option_type, ['radio', 'color', 'select', 'checkbox'])) {
                                                        $product_option = new ProductOption;
                                                        $product_option->required = $option_required;
                                                        $product_option->value = $option_value_text;
                                                        $product_option->option_id = $option_id->id;
                                                        $product_option->product_id = $id;

                                                        $product_option->save();
                                                    } else {
                                                        $product_option = new ProductOption;
                                                        $product_option->required = $option_required;
                                                        $product_option->value = $option_value_text;
                                                        $product_option->option_id = $option_id->id;
                                                        $product_option->product_id = $id;

                                                        $product_option->save();

                                                        $product_option_values = new ProductOptionValues;
                                                        $product_option_values->quantity = $option_quantity;
                                                        $product_option_values->price = $option_price;
                                                        $product_option_values->weight = $option_weight;
                                                        $product_option_values->reward = $option_reward;
                                                        $product_option_values->image = $option_image;
                                                        $product_option_values->product_id = $id;
                                                        $product_option_values->product_option_id = $product_option->id;
                                                        $product_option_values->option_id = $option_id->id;
                                                        $product_option_values->product_option_value_id = $option_values->id;

                                                        $product_option_values->save();
                                                    }
                                                }
                                            }
                                        }
                                        else {
                                            $option_text = @explode($delimiter, $field['options']);

                                            if (isset($option_text[1])) {
                                                $option_name = trim($option_text[0]);
                                                $option_value_text = trim($option_text[1]);
                                                $option_required = trim($option_text[2]);
                                                $option_type = trim($option_text[3]);
                                                $option_price = trim($option_text[4]);
                                                $option_quantity = isset($option_text[5]) ? trim($option_text[5]) : 0;
                                                $option_weight = isset($option_text[6]) ? trim($option_text[6]) : 0;
                                                $option_reward = isset($option_text[7]) ? trim($option_text[7]) : 0;
                                                $option_image = isset($option_text[8]) ? trim($option_text[8]) : '';

                                                $option_id = new Options;
                                                $option_id->type = $option_type;
                                                $option_id->sort_order = 0;
                                                $option_id->status = 1;

                                                $option_id->save();

                                                $option_description = new OptionDescription;
                                                $option_description->name = $option_name;
                                                $option_description->lang = $lang;
                                                $option_description->option_id = $option_id->id;

                                                $option_description->save();

                                                $option_values = new OptionValues;
                                                $option_values->sort_order = 0;
                                                $option_values->image = '';
                                                $option_values->option_id = $option_id->id;

                                                $option_values->save();

                                                $option_value_description = new OptionValueDescription;
                                                $option_value_description->name = $option_value_text;
                                                $option_value_description->lang = $lang;
                                                $option_value_description->option_value_id = $option_values->id;

                                                $option_value_description->save();

                                                if (!in_array($option_type, ['radio', 'color', 'select', 'checkbox'])) {
                                                    $product_option = new ProductOption;
                                                    $product_option->required = $option_required;
                                                    $product_option->value = $option_value_text;
                                                    $product_option->option_id = $option_id->id;
                                                    $product_option->product_id = $id;

                                                    $product_option->save();
                                                } else {
                                                    $product_option = new ProductOption;
                                                    $product_option->required = $option_required;
                                                    $product_option->value = $option_value_text;
                                                    $product_option->option_id = $option_id->id;
                                                    $product_option->product_id = $id;

                                                    $product_option->save();

                                                    $product_option_values = new ProductOptionValues;
                                                    $product_option_values->quantity = $option_quantity;
                                                    $product_option_values->price = $option_price;
                                                    $product_option_values->weight = $option_weight;
                                                    $product_option_values->reward = $option_reward;
                                                    $product_option_values->image = $option_image;
                                                    $product_option_values->product_id = $id;
                                                    $product_option_values->product_option_id = $product_option->id;
                                                    $product_option_values->option_id = $option_id->id;
                                                    $product_option_values->product_option_value_id = $option_values->id;

                                                    $product_option_values->save();
                                                }
                                            }
                                        }
                                    }

                                    if (!empty($field['product_reward'])) {
                                        ProductReward::where('product_id', $id)->delete();
                                        $product_rewards = @explode("\n", $field['product_reward']);

                                        if (isset($product_rewards[1])) {
                                            foreach ($product_rewards as $product_reward) {
                                                $reward_text = @explode($delimiter, $product_reward);

                                                if (isset($reward_text[1])) {
                                                    $customer_group_id = trim($reward_text[0]);
                                                    $reward = trim($reward_text[1]);

                                                    if (CustomerGroups::where('customer_group_id', $customer_group_id)->count()) {
                                                        ProductReward::insert(['customer_group_id' => $customer_group_id, 'product_id' => $id, 'reward' => $reward]);
                                                    }
                                                }
                                            }
                                        } else {
                                            $reward_text = @explode($delimiter, $field['product_reward']);

                                            if (isset($reward_text[1])) {
                                                $customer_group_id = trim($reward_text[0]);
                                                $reward = trim($reward_text[1]);

                                                if (CustomerGroups::where('customer_group_id', $customer_group_id)->count()) {
                                                    ProductReward::insert(['customer_group_id' => $customer_group_id, 'product_id' => $id, 'reward' => $reward]);
                                                }
                                            }
                                        }
                                    }

                                    if (!empty($field['product_special'])) {
                                        ProductSpecial::where('product_id', $id)->delete();
                                        $product_specials = @explode("\n", $field['product_special']);

                                        if (isset($product_specials[1])) {
                                            foreach ($product_specials as $product_special) {
                                                $special_text = @explode($delimiter, $product_special);

                                                if (isset($special_text[1])) {
                                                    $customer_group_id = trim($special_text[0]);
                                                    $price = trim($special_text[1]);
                                                    $date_start = date('Y-m-d H:i:s', \strtotime(trim($special_text[2])));
                                                    $date_end = date('Y-m-d H:i:s', \strtotime(trim($special_text[3])));

                                                    if ($export['calc_mode_2'] && !empty($export['calc_mode_2_text'])) {
                                                        $price = $export['calc_mode_2_text'];
                                                        $calc = $export['calc_mode_2'];

                                                        if ($calc == '*') {
                                                            $price *= $price;
                                                        } elseif ($calc == '/') {
                                                            $price /= $price;
                                                        } elseif ($calc == '+') {
                                                            $price += $price;
                                                        } elseif ($calc == '-') {
                                                            $price -= $price;
                                                        }
                                                    }

                                                    $product_special = new ProductSpecial;
                                                    $product_special->customer_group_id = $customer_group_id;
                                                    $product_special->product_id = $product['id'];
                                                    $product_special->price = $price;
                                                    $product_special->date_start = $date_start;
                                                    $product_special->date_end = $date_end;

                                                    $product_special->save();
                                                }
                                            }
                                        } else {
                                            $special_text = @explode($delimiter, $field['product_special']);

                                            if (isset($special_text[1])) {
                                                $customer_group_id = trim($special_text[0]);
                                                $price = trim($special_text[1]);
                                                $date_start = date('Y-m-d H:i:s', \strtotime(trim($special_text[2])));
                                                $date_end = date('Y-m-d H:i:s', \strtotime(trim($special_text[3])));

                                                if ($export['calc_mode_2'] && !empty($export['calc_mode_2_text'])) {
                                                    $price = $export['calc_mode_2_text'];
                                                    $calc = $export['calc_mode_2'];

                                                    if ($calc == '*') {
                                                        $price *= $price;
                                                    } elseif ($calc == '/') {
                                                        $price /= $price;
                                                    } elseif ($calc == '+') {
                                                        $price += $price;
                                                    } elseif ($calc == '-') {
                                                        $price -= $price;
                                                    }
                                                }

                                                $product_special = new ProductSpecial;
                                                $product_special->customer_group_id = $customer_group_id;
                                                $product_special->product_id = $product['id'];
                                                $product_special->price = $price;
                                                $product_special->date_start = $date_start;
                                                $product_special->date_end = $date_end;

                                                $product_special->save();
                                            }
                                        }
                                    }

                                    if (!empty($field['filters'])) {
                                        $filters = @explode("\n", $field['filters']);
                                        FilterProduct::where('product_id', $id)->delete();

                                        if (isset($filters[1])) {
                                            foreach ($filters as $filter) {
                                                $filter_text = @explode($delimiter, $filter);

                                                if (isset($filter_text[1])) {
                                                    $filter_name = trim($filter_text[0]);
                                                    $filter_value = trim($filter_text[1]);

                                                    $filter_id = FilterDescription::select('filter_id')
                                                        ->where('name', $filter_name)
                                                        ->where('lang', $lang)
                                                        ->value('filter_id');

                                                    if (!is_null($filter_id)) {
                                                        $filter_values_id = FilterValueDescription::select('filter_value_id')
                                                            ->where('name', $filter_value)
                                                            ->where('lang', $lang)
                                                            ->value('filter_value_id');

                                                        if (!is_null($filter_values_id)) {
                                                            FilterProduct::insert(['filter_value_id' => $filter_values_id, 'product_id' => $id, 'filter_id' => $filter_id, 'updated_at' => $now, 'created_at' => $now]);
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            $filter_text = @explode($delimiter, $filter);

                                            if (isset($filter_text[1])) {
                                                $filter_name = trim($filter_text[0]);
                                                $filter_value = trim($filter_text[1]);

                                                $filter_id = FilterDescription::select('filter_id')
                                                    ->where('name', $filter_name)
                                                    ->where('lang', $lang)
                                                    ->value('filter_id');

                                                if (!is_null($filter_id)) {
                                                    $filter_values_id = FilterValueDescription::select('filter_value_id')
                                                        ->where('name', $filter_value)
                                                        ->where('lang', $lang)
                                                        ->value('filter_value_id');

                                                    if (!is_null($filter_values_id)) {
                                                        FilterProduct::insert(['filter_value_id' => $filter_values_id, 'product_id' => $id, 'filter_id' => $filter_id, 'updated_at' => $now, 'created_at' => $now]);
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    $success_row++;
                                }
                            }
                            elseif ($mode == 2) {
                                $product = new Products;
                                $product->slug = $field['slug'];
                                $product->layout_id = $field['layout_id'];
                                $product->popular = isset($field['popular']) ? $field['popular'] : 0;
                                $product->model = isset($field['model']) ? $field['model'] : '';
                                $product->price = isset($field['price']) ? $field['price'] : 0;
                                $product->image = isset($field['image']) ? $field['image'] : '';
                                $product->sort = isset($field['sort']) ? $field['sort'] : 0;
                                $product->reward = isset($field['reward']) ? $field['reward'] : 0;
                                $product->weight = isset($field['weight']) ? $field['weight'] : 0;
                                $product->parent_id = isset($field['parent_id']) ? $field['parent_id'] : 0;
                                $product->status = isset($field['status']) ? $field['status'] : 0;

                                $product->save();

                                if (!$import_id || !$id) {
                                    $id = $product->id;
                                }

                                $meta = $field['meta'];

                                $pd = new ProductDescription;
                                $pd->lang = $lang;
                                $pd->product_id = $id;
                                $pd->name = $meta['name'];
                                $pd->meta_title = $meta['meta_title'];
                                $pd->meta_description = !empty($meta['meta_description']) ? $meta['meta_description'] : '';
                                $pd->meta_keywords = !empty($meta['meta_keywords']) ? $meta['meta_keywords'] : '';
                                $pd->description = !empty($meta['description']) ? $meta['description'] : '';

                                $pd->save();

                                if (isset($field['images'])) {
                                    $field['images'] = @explode($delimiter, $field['images']);
                                    $images = [];

                                    if (isset($field['images'][0])) {
                                        foreach ($field['images'] as $image) {
                                            if (filter_var($image, FILTER_VALIDATE_URL)) {
                                                $image = $this->download(public_path() . '/../images/products/', $image, 'images/products/');
                                            }

                                            $images[] = ['product_id' => $id, 'image' => $image, 'updated_at' => $now, 'created_at' => $now];
                                        }
                                    } else {
                                        if (filter_var($field['images'], FILTER_VALIDATE_URL)) {
                                            $field['images'] = $this->download(public_path() . '/../images/products/', $field['images'], 'images/products/');
                                        }

                                        $images[] = ['product_id' => $id, 'image' => $field['images'], 'updated_at' => $now, 'created_at' => $now];
                                    }

                                    if ($images) ProductImage::insert($images);
                                }

                                if (isset($field['category_id'])) {
                                    $field['category_id'] = @explode($delimiter, $field['category_id']);
                                    $category_ids = [];

                                    if (isset($field['category_id'][0])) {
                                        foreach ($field['category_id'] as $category_id) {
                                            $category_ids[] = ['product_id' => $id, 'category_id' => $category_id, 'updated_at' => $now, 'created_at' => $now];
                                        }
                                    } else {
                                        $category_ids[] = ['product_id' => $id, 'category_id' => $field['category_id'], 'updated_at' => $now, 'created_at' => $now];
                                    }

                                    if (isset($category_ids)) {
                                        if (empty($field['parent_id'])) {
                                            Products::where('id', $id)->update(['parent_id', $category_ids[count($category_ids) - 1]['category_id']]);
                                        }

                                        ProductCategory::insert($category_ids);
                                    }
                                }

                                if (isset($field['category_name'])) {
                                    $category_names = @explode("\n", $field['category_name']);
                                    $category_ids = [];

                                    if (isset($category_names[1])) {
                                        foreach ($category_names as $category_name) {
                                            $parent_categories = @explode($delimiter, $category_name);

                                            if (isset($parent_categories[0])) {
                                                $parents_id = [];

                                                foreach ($parent_categories as $key => $parent_name) {
                                                    $cd = CategoryDescription::select('c.id')
                                                        ->join('categories as c', 'c.id', '=', 'category_description.category_id')
                                                        ->where('c.parent_id', (isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0))
                                                        ->where('category_description.name', trim($parent_name))
                                                        ->where('category_description.lang', $lang)
                                                        ->value('c.id');

                                                    if (!is_null($cd)) {
                                                        $parents_id[$key] = (int)$cd;
                                                    } elseif (!empty($export['categories_add'])) {
                                                        $category = new Categories;
                                                        $category->slug = str_slug($parent_name);
                                                        $category->layout_id = isset($export['category_layout_id']) ? $export['category_layout_id'] : 8;
                                                        $category->image = '';
                                                        $category->top = 0;
                                                        $category->sort = 0;
                                                        $category->parent_id = isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0;
                                                        $category->status = 1;

                                                        $category->save();
                                                        $parents_id[$key] = $category->id;

                                                        $cd = new CategoryDescription;
                                                        $cd->lang = $lang;
                                                        $cd->category_id = $category->id;
                                                        $cd->name = $parent_name;
                                                        $cd->meta_title = $parent_name;
                                                        $cd->meta_description = '';
                                                        $cd->meta_keywords = '';
                                                        $cd->description = '';

                                                        $cd->save();
                                                    }
                                                }

                                                if (count($parents_id) == count($parent_categories)) {
                                                    $category_ids[] = ['product_id' => $id, 'category_id' => $parents_id[count($parents_id) - 1], 'updated_at' => $now, 'created_at' => $now];
                                                }
                                            } else {
                                                $category_id = (int)CategoryDescription::select('c.id')
                                                    ->join('categories as c', 'c.id', '=', 'category_description.category_id')
                                                    ->where('category_description.name', trim($parent_categories))
                                                    ->where('category_description.lang', $lang)
                                                    ->value('c.id');

                                                if (!$category_id && !empty($export['categories_add'])) {
                                                    $category = new Categories;
                                                    $category->slug = str_slug($parent_categories);
                                                    $category->layout_id = isset($export['category_layout_id']) ? $export['category_layout_id'] : 8;
                                                    $category->image = '';
                                                    $category->top = 0;
                                                    $category->sort = 0;
                                                    $category->parent_id = isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0;
                                                    $category->status = 1;

                                                    $category->save();
                                                    $category_id = $category->id;

                                                    $cd = new CategoryDescription;
                                                    $cd->lang = $lang;
                                                    $cd->category_id = $category->id;
                                                    $cd->name = $parent_categories;
                                                    $cd->meta_title = $parent_categories;
                                                    $cd->meta_description = '';
                                                    $cd->meta_keywords = '';
                                                    $cd->description = '';

                                                    $cd->save();
                                                }

                                                if ($category_id) {
                                                    $category_ids[] = ['product_id' => $id, 'category_id' => $category_id, 'updated_at' => $now, 'created_at' => $now];
                                                }
                                            }
                                        }
                                    } else {
                                        $parent_categories = @explode($delimiter, $field['category_name']);

                                        if (isset($parent_categories[0])) {
                                            $parents_id = [];

                                            foreach ($parent_categories as $key => $parent_name) {
                                                $cd = CategoryDescription::select('c.id')
                                                    ->join('categories as c', 'c.id', '=', 'category_description.category_id')
                                                    ->where('c.parent_id', (isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0))
                                                    ->where('category_description.name', trim($parent_name))
                                                    ->where('category_description.lang', $lang)
                                                    ->value('c.id');

                                                if (!is_null($cd)) {
                                                    $parents_id[$key] = (int)$cd;
                                                } elseif (!empty($export['categories_add'])) {
                                                    $category = new Categories;
                                                    $category->slug = str_slug($parent_name);
                                                    $category->layout_id = isset($export['category_layout_id']) ? $export['category_layout_id'] : 8;
                                                    $category->image = '';
                                                    $category->top = 0;
                                                    $category->sort = 0;
                                                    $category->parent_id = isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0;
                                                    $category->status = 1;

                                                    $category->save();
                                                    $parents_id[$key] = $category->id;

                                                    $cd = new CategoryDescription;
                                                    $cd->lang = $lang;
                                                    $cd->category_id = $category->id;
                                                    $cd->name = $parent_name;
                                                    $cd->meta_title = $parent_name;
                                                    $cd->meta_description = '';
                                                    $cd->meta_keywords = '';
                                                    $cd->description = '';

                                                    $cd->save();
                                                }
                                            }

                                            if (count($parents_id) == count($parent_categories)) {
                                                $category_ids[] = ['product_id' => $id, 'category_id' => $parents_id[count($parents_id) - 1], 'updated_at' => $now, 'created_at' => $now];
                                            }
                                        } else {
                                            $category_id = (int)CategoryDescription::select('c.id')
                                                ->join('categories as c', 'c.id', '=', 'category_description.category_id')
                                                ->where('category_description.name', trim($parent_categories))
                                                ->where('category_description.lang', $lang)
                                                ->value('c.id');

                                            if (!$category_id && !empty($export['categories_add'])) {
                                                $category = new Categories;
                                                $category->slug = str_slug($parent_categories);
                                                $category->layout_id = isset($export['category_layout_id']) ? $export['category_layout_id'] : 8;
                                                $category->image = '';
                                                $category->top = 0;
                                                $category->sort = 0;
                                                $category->parent_id = isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0;
                                                $category->status = 1;

                                                $category->save();
                                                $category_id = $category->id;

                                                $cd = new CategoryDescription;
                                                $cd->lang = $lang;
                                                $cd->category_id = $category->id;
                                                $cd->name = $parent_categories;
                                                $cd->meta_title = $parent_categories;
                                                $cd->meta_description = '';
                                                $cd->meta_keywords = '';
                                                $cd->description = '';

                                                $cd->save();
                                            }

                                            if ($category_id) {
                                                $category_ids[] = ['product_id' => $id, 'category_id' => $category_id, 'updated_at' => $now, 'created_at' => $now];
                                            }
                                        }
                                    }

                                    if ($category_ids) {
                                        if (empty($field['parent_id'])) {
                                            Products::where('id', $id)->update(['parent_id', $category_ids[count($category_ids) - 1]['category_id']]);
                                        }

                                        ProductCategory::insert($category_ids);
                                    }
                                }

                                if (!empty($field['attributes'])) {
                                    $attributes = @explode("\n", $field['attributes']);

                                    if (isset($attributes[1])) {
                                        foreach ($attributes as $attribute) {
                                            $attribute_text = @explode($delimiter, $attribute);

                                            if (isset($attribute_text[1])) {
                                                $attribute_name = trim($attribute_text[0]);
                                                $attribute_value = trim($attribute_text[1]);

                                                $attribute_id = AttributeDescription::select('attribute_id')
                                                    ->where('name', $attribute_name)
                                                    ->where('lang', $lang)
                                                    ->value('attribute_id');

                                                if (!is_null($attribute_id)) {
                                                    $pa = new ProductAttribute;
                                                    $pa->lang = $lang;
                                                    $pa->product_id = $id;
                                                    $pa->attribute_id = $attribute_id;
                                                    $pa->text = $attribute_value;

                                                    $pa->save();

                                                    if (!empty($attribute_text[2])) {
                                                        $attribute_images = @explode(',', $attribute_text[2]);

                                                        if (isset($attribute_images[0])) {
                                                            foreach ($attribute_images as $attribute_image) {
                                                                ProductAttributeImage::insert(['attribute_id' => $attribute_id, 'product_id' => $id, 'image' => trim($attribute_image), 'updated_at' => $now, 'created_at' => $now]);
                                                            }
                                                        } else {
                                                            ProductAttributeImage::insert(['attribute_id' => $attribute_id, 'product_id' => $id, 'image' => trim($attribute_text[2]), 'updated_at' => $now, 'created_at' => $now]);
                                                        }
                                                    }
                                                } else if (!empty($export['attributes_add'])) {
                                                    $attribute = new Attributes;
                                                    $attribute->image = '';
                                                    $attribute->sort = 0;
                                                    $attribute->position_left = 0;
                                                    $attribute->position_right = 0;
                                                    $attribute->status = 1;

                                                    $attribute->save();

                                                    if (!empty($attribute_text[2])) {
                                                        $attribute_images = @explode(',', $attribute_text[2]);

                                                        if (isset($attribute_images[0])) {
                                                            foreach ($attribute_images as $attribute_image) {
                                                                ProductAttributeImage::insert(['attribute_id' => $attribute->id, 'product_id' => $id, 'image' => trim($attribute_image), 'updated_at' => $now, 'created_at' => $now]);
                                                            }
                                                        } else {
                                                            ProductAttributeImage::insert(['attribute_id' => $attribute->id, 'product_id' => $id, 'image' => trim($attribute_text[2]), 'updated_at' => $now, 'created_at' => $now]);
                                                        }
                                                    }

                                                    $ad = new AttributeDescription;
                                                    $ad->lang = $lang;
                                                    $ad->attribute_id = $attribute->id;
                                                    $ad->name = $attribute_name;

                                                    $ad->save();

                                                    $pa = new ProductAttribute;
                                                    $pa->lang = $lang;
                                                    $pa->product_id = $id;
                                                    $pa->attribute_id = $attribute->id;
                                                    $pa->text = $attribute_value;

                                                    $pa->save();
                                                }
                                            }
                                        }
                                    } else {
                                        $attribute_text = @explode($delimiter, $field['attributes']);

                                        if (isset($attribute_text[1])) {
                                            $attribute_name = trim($attribute_text[0]);
                                            $attribute_value = trim($attribute_text[1]);

                                            $attribute_id = AttributeDescription::select('attribute_id')
                                                ->where('name', $attribute_name)
                                                ->where('lang', $lang)
                                                ->value('attribute_id');

                                            if (!is_null($attribute_id)) {
                                                $pa = new ProductAttribute;
                                                $pa->lang = $lang;
                                                $pa->product_id = $id;
                                                $pa->attribute_id = $attribute_id;
                                                $pa->text = $attribute_value;

                                                $pa->save();

                                                if (!empty($attribute_text[2])) {
                                                    $attribute_images = @explode(',', $attribute_text[2]);

                                                    if (isset($attribute_images[0])) {
                                                        foreach ($attribute_images as $attribute_image) {
                                                            ProductAttributeImage::insert(['attribute_id' => $attribute_id, 'product_id' => $id, 'image' => trim($attribute_image), 'updated_at' => $now, 'created_at' => $now]);
                                                        }
                                                    } else {
                                                        ProductAttributeImage::insert(['attribute_id' => $attribute_id, 'product_id' => $id, 'image' => trim($attribute_text[2]), 'updated_at' => $now, 'created_at' => $now]);
                                                    }
                                                }
                                            } else if (!empty($export['attributes_add'])) {
                                                $attribute = new Attributes;
                                                $attribute->image = '';
                                                $attribute->sort = 0;
                                                $attribute->position_left = 0;
                                                $attribute->position_right = 0;
                                                $attribute->status = 1;

                                                $attribute->save();

                                                if (!empty($attribute_text[2])) {
                                                    $attribute_images = @explode(',', $attribute_text[2]);

                                                    if (isset($attribute_images[0])) {
                                                        foreach ($attribute_images as $attribute_image) {
                                                            ProductAttributeImage::insert(['attribute_id' => $attribute->id, 'product_id' => $id, 'image' => trim($attribute_image), 'updated_at' => $now, 'created_at' => $now]);
                                                        }
                                                    } else {
                                                        ProductAttributeImage::insert(['attribute_id' => $attribute->id, 'product_id' => $id, 'image' => trim($attribute_text[2]), 'updated_at' => $now, 'created_at' => $now]);
                                                    }
                                                }

                                                $ad = new AttributeDescription;
                                                $ad->lang = $lang;
                                                $ad->attribute_id = $attribute->id;
                                                $ad->name = $attribute_name;

                                                $ad->save();

                                                $pa = new ProductAttribute;
                                                $pa->lang = $lang;
                                                $pa->product_id = $id;
                                                $pa->attribute_id = $attribute->id;
                                                $pa->text = $attribute_value;

                                                $pa->save();
                                            }
                                        }
                                    }
                                }

                                if (!empty($field['options'])) {
                                    $options = @explode("\n", $field['options']);

                                    if (isset($options[1])) {
                                        foreach ($options as $option) {
                                            $option_text = @explode($delimiter, $option);

                                            if (isset($option_text[1])) {
                                                $option_name = trim($option_text[0]);
                                                $option_value_text = trim($option_text[1]);
                                                $option_required = trim($option_text[2]);
                                                $option_type = trim($option_text[3]);
                                                $option_price = trim($option_text[4]);
                                                $option_quantity = isset($option_text[5]) ? trim($option_text[5]) : 0;
                                                $option_weight = isset($option_text[6]) ? trim($option_text[6]) : 0;
                                                $option_reward = isset($option_text[7]) ? trim($option_text[7]) : 0;
                                                $option_image = isset($option_text[8]) ? trim($option_text[8]) : '';

                                                $option_id = new Options;
                                                $option_id->type = $option_type;
                                                $option_id->sort_order = 0;
                                                $option_id->status = 1;

                                                $option_id->save();

                                                $option_description = new OptionDescription;
                                                $option_description->name = $option_name;
                                                $option_description->lang = $lang;
                                                $option_description->option_id = $option_id->id;

                                                $option_description->save();

                                                $option_values = new OptionValues;
                                                $option_values->sort_order = 0;
                                                $option_values->image = '';
                                                $option_values->option_id = $option_id->id;

                                                $option_values->save();

                                                $option_value_description = new OptionValueDescription;
                                                $option_value_description->name = $option_value_text;
                                                $option_value_description->lang = $lang;
                                                $option_value_description->option_value_id = $option_values->id;

                                                $option_value_description->save();

                                                if (!in_array($option_type, ['radio', 'color', 'select', 'checkbox'])) {
                                                    $product_option = new ProductOption;
                                                    $product_option->required = $option_required;
                                                    $product_option->value = $option_value_text;
                                                    $product_option->option_id = $option_id->id;
                                                    $product_option->product_id = $id;

                                                    $product_option->save();
                                                } else {
                                                    $product_option = new ProductOption;
                                                    $product_option->required = $option_required;
                                                    $product_option->value = $option_value_text;
                                                    $product_option->option_id = $option_id->id;
                                                    $product_option->product_id = $id;

                                                    $product_option->save();

                                                    $product_option_values = new ProductOptionValues;
                                                    $product_option_values->quantity = $option_quantity;
                                                    $product_option_values->price = $option_price;
                                                    $product_option_values->weight = $option_weight;
                                                    $product_option_values->reward = $option_reward;
                                                    $product_option_values->image = $option_image;
                                                    $product_option_values->product_id = $id;
                                                    $product_option_values->product_option_id = $product_option->id;
                                                    $product_option_values->option_id = $option_id->id;
                                                    $product_option_values->product_option_value_id = $option_values->id;

                                                    $product_option_values->save();
                                                }
                                            }
                                        }
                                    } else {
                                        $option_text = @explode($delimiter, $field['options']);

                                        if (isset($option_text[1])) {
                                            $option_name = trim($option_text[0]);
                                            $option_value_text = trim($option_text[1]);
                                            $option_required = trim($option_text[2]);
                                            $option_type = trim($option_text[3]);
                                            $option_price = trim($option_text[4]);
                                            $option_quantity = isset($option_text[5]) ? trim($option_text[5]) : 0;
                                            $option_weight = isset($option_text[6]) ? trim($option_text[6]) : 0;
                                            $option_reward = isset($option_text[7]) ? trim($option_text[7]) : 0;
                                            $option_image = isset($option_text[8]) ? trim($option_text[8]) : '';

                                            $option_id = new Options;
                                            $option_id->type = $option_type;
                                            $option_id->sort_order = 0;
                                            $option_id->status = 1;

                                            $option_id->save();

                                            $option_description = new OptionDescription;
                                            $option_description->name = $option_name;
                                            $option_description->lang = $lang;
                                            $option_description->option_id = $option_id->id;

                                            $option_description->save();

                                            $option_values = new OptionValues;
                                            $option_values->sort_order = 0;
                                            $option_values->image = '';
                                            $option_values->option_id = $option_id->id;

                                            $option_values->save();

                                            $option_value_description = new OptionValueDescription;
                                            $option_value_description->name = $option_value_text;
                                            $option_value_description->lang = $lang;
                                            $option_value_description->option_value_id = $option_values->id;

                                            $option_value_description->save();

                                            if (!in_array($option_type, ['radio', 'color', 'select', 'checkbox'])) {
                                                $product_option = new ProductOption;
                                                $product_option->required = $option_required;
                                                $product_option->value = $option_value_text;
                                                $product_option->option_id = $option_id->id;
                                                $product_option->product_id = $id;

                                                $product_option->save();
                                            } else {
                                                $product_option = new ProductOption;
                                                $product_option->required = $option_required;
                                                $product_option->value = $option_value_text;
                                                $product_option->option_id = $option_id->id;
                                                $product_option->product_id = $id;

                                                $product_option->save();

                                                $product_option_values = new ProductOptionValues;
                                                $product_option_values->quantity = $option_quantity;
                                                $product_option_values->price = $option_price;
                                                $product_option_values->weight = $option_weight;
                                                $product_option_values->reward = $option_reward;
                                                $product_option_values->image = $option_image;
                                                $product_option_values->product_id = $id;
                                                $product_option_values->product_option_id = $product_option->id;
                                                $product_option_values->option_id = $option_id->id;
                                                $product_option_values->product_option_value_id = $option_values->id;

                                                $product_option_values->save();
                                            }
                                        }
                                    }
                                }

                                if (!empty($field['product_reward'])) {
                                    $product_rewards = @explode("\n", $field['product_reward']);

                                    if (isset($product_rewards[1])) {
                                        foreach ($product_rewards as $product_reward) {
                                            $reward_text = @explode($delimiter, $product_reward);

                                            if (isset($reward_text[1])) {
                                                $customer_group_id = trim($reward_text[0]);
                                                $reward = trim($reward_text[1]);

                                                if (CustomerGroups::where('customer_group_id', $customer_group_id)->count()) {
                                                    ProductReward::insert(['customer_group_id' => $customer_group_id, 'product_id' => $id, 'reward' => $reward]);
                                                }
                                            }
                                        }
                                    } else {
                                        $reward_text = @explode($delimiter, $field['product_reward']);

                                        if (isset($reward_text[1])) {
                                            $customer_group_id = trim($reward_text[0]);
                                            $reward = trim($reward_text[1]);

                                            if (CustomerGroups::where('customer_group_id', $customer_group_id)->count()) {
                                                ProductReward::insert(['customer_group_id' => $customer_group_id, 'product_id' => $id, 'reward' => $reward]);
                                            }
                                        }
                                    }
                                }

                                if (!empty($field['product_special'])) {
                                    $product_specials = @explode("\n", $field['product_special']);

                                    if (isset($product_specials[1])) {
                                        foreach ($product_specials as $product_special) {
                                            $special_text = @explode($delimiter, $product_special);

                                            if (isset($special_text[1])) {
                                                $customer_group_id = trim($special_text[0]);
                                                $price = trim($special_text[1]);
                                                $date_start = date('Y-m-d H:i:s', \strtotime(trim($special_text[2])));
                                                $date_end = date('Y-m-d H:i:s', \strtotime(trim($special_text[3])));

                                                if ($export['calc_mode_2'] && !empty($export['calc_mode_2_text'])) {
                                                    $price = $export['calc_mode_2_text'];
                                                    $calc = $export['calc_mode_2'];

                                                    if ($calc == '*') {
                                                        $price *= $price;
                                                    } elseif ($calc == '/') {
                                                        $price /= $price;
                                                    } elseif ($calc == '+') {
                                                        $price += $price;
                                                    } elseif ($calc == '-') {
                                                        $price -= $price;
                                                    }
                                                }

                                                $product_special = new ProductSpecial;
                                                $product_special->customer_group_id = $customer_group_id;
                                                $product_special->product_id = $id;
                                                $product_special->price = $price;
                                                $product_special->date_start = $date_start;
                                                $product_special->date_end = $date_end;

                                                $product_special->save();
                                            }
                                        }
                                    } else {
                                        $special_text = @explode($delimiter, $field['product_special']);

                                        if (isset($special_text[1])) {
                                            $customer_group_id = trim($special_text[0]);
                                            $price = trim($special_text[1]);
                                            $date_start = date('Y-m-d H:i:s', \strtotime(trim($special_text[2])));
                                            $date_end = date('Y-m-d H:i:s', \strtotime(trim($special_text[3])));

                                            if ($export['calc_mode_2'] && !empty($export['calc_mode_2_text'])) {
                                                $price = $export['calc_mode_2_text'];
                                                $calc = $export['calc_mode_2'];

                                                if ($calc == '*') {
                                                    $price *= $price;
                                                } elseif ($calc == '/') {
                                                    $price /= $price;
                                                } elseif ($calc == '+') {
                                                    $price += $price;
                                                } elseif ($calc == '-') {
                                                    $price -= $price;
                                                }
                                            }

                                            $product_special = new ProductSpecial;
                                            $product_special->customer_group_id = $customer_group_id;
                                            $product_special->product_id = $id;
                                            $product_special->price = $price;
                                            $product_special->date_start = $date_start;
                                            $product_special->date_end = $date_end;

                                            $product_special->save();
                                        }
                                    }
                                }

                                if (!empty($field['filters'])) {
                                    $filters = @explode("\n", $field['filters']);

                                    if (isset($filters[1])) {
                                        foreach ($filters as $filter) {
                                            $filter_text = @explode($delimiter, $filter);

                                            if (isset($filter_text[1])) {
                                                $filter_name = trim($filter_text[0]);
                                                $filter_value = trim($filter_text[1]);
                                                $filter_type = isset($export['filters_type']) ? $export['filters_type'] : 'checkbox';

                                                $filter_id = FilterDescription::select('filter_id')
                                                    ->where('name', $filter_name)
                                                    ->where('lang', $lang)
                                                    ->value('filter_id');

                                                if (!is_null($filter_id)) {
                                                    $filter_values_id = FilterValueDescription::select('filter_value_id')
                                                        ->where('name', $filter_value)
                                                        ->where('lang', $lang)
                                                        ->value('filter_value_id');

                                                    if (!is_null($filter_values_id)) {
                                                        $pf = new FilterProduct;
                                                        $pf->filter_value_id = $filter_values_id;
                                                        $pf->product_id = $id;
                                                        $pf->filter_id = $filter_id;

                                                        $pf->save();
                                                    } elseif (!empty($export['filters_add'])) {
                                                        $fv = new FilterValues;
                                                        $fv->filter_id = $filter_id;
                                                        $fv->slug = str_slug($filter_value);
                                                        $fv->sort = 0;

                                                        $fv->save();

                                                        $fvd = new FilterValueDescription;
                                                        $fvd->lang = $lang;
                                                        $fvd->filter_value_id = $fv->id;
                                                        $fvd->name = $filter_value;

                                                        $fvd->save();

                                                        $pf = new FilterProduct;
                                                        $pf->filter_value_id = $fv->id;
                                                        $pf->product_id = $id;
                                                        $pf->filter_id = $filter_id;

                                                        $pf->save();
                                                    }
                                                }
                                                else if (!empty($export['filters_add'])) {
                                                    $filter = new Filters;
                                                    $filter->type = $filter_type;
                                                    $filter->slug = str_slug($filter_name);
                                                    $filter->sort = 0;
                                                    $filter->status = 1;

                                                    $filter->save();

                                                    $fd = new FilterDescription;
                                                    $fd->lang = $lang;
                                                    $fd->filter_id = $filter->id;
                                                    $fd->name = $filter_name;
                                                    $fd->description = '';

                                                    $fd->save();

                                                    foreach (Categories::select('id')->where('status', 1)->get() as $category) {
                                                        $fc = new FilterCategory;
                                                        $fc->filter_id = $filter->id;
                                                        $fc->category_id = $category->id;

                                                        $fc->save();
                                                    }

                                                    $fv = new FilterValues;
                                                    $fv->filter_id = $filter->id;
                                                    $fv->slug = str_slug($filter_value);
                                                    $fv->sort = 0;

                                                    $fv->save();

                                                    $fvd = new FilterValueDescription;
                                                    $fvd->lang = $lang;
                                                    $fvd->filter_value_id = $fv->id;
                                                    $fvd->name = $filter_value;

                                                    $fvd->save();

                                                    $pf = new FilterProduct;
                                                    $pf->filter_value_id = $fv->id;
                                                    $pf->product_id = $id;
                                                    $pf->filter_id = $filter->id;

                                                    $pf->save();
                                                }
                                            }
                                        }
                                    } else {
                                        $filter_text = @explode($delimiter, $field['filters']);

                                        if (isset($filter_text[1])) {
                                            $filter_name = trim($filter_text[0]);
                                            $filter_value = trim($filter_text[1]);
                                            $filter_type = isset($export['filters_type']) ? $export['filters_type'] : 'checkbox';

                                            $filter_id = FilterDescription::select('filter_id')
                                                ->where('name', $filter_name)
                                                ->where('lang', $lang)
                                                ->value('filter_id');

                                            if (!is_null($filter_id)) {
                                                $filter_values_id = FilterValueDescription::select('filter_value_id')
                                                    ->where('name', $filter_value)
                                                    ->where('lang', $lang)
                                                    ->value('filter_value_id');

                                                if (!is_null($filter_values_id)) {
                                                    $pf = new FilterProduct;
                                                    $pf->filter_value_id = $filter_values_id;
                                                    $pf->product_id = $id;
                                                    $pf->filter_id = $filter_id->filter_id;

                                                    $pf->save();
                                                } else {
                                                    $fv = new FilterValues;
                                                    $fv->filter_id = $filter_id->filter_id;
                                                    $fv->slug = str_slug($filter_value);
                                                    $fv->sort = 0;

                                                    $fv->save();

                                                    $fvd = new FilterValueDescription;
                                                    $fvd->lang = $lang;
                                                    $fvd->filter_value_id = $fv->id;
                                                    $fvd->name = $filter_value;

                                                    $fvd->save();

                                                    $pf = new FilterProduct;
                                                    $pf->filter_value_id = $fv->id;
                                                    $pf->product_id = $id;
                                                    $pf->filter_id = $filter_id->filter_id;

                                                    $pf->save();
                                                }
                                            }
                                            else if (!empty($export['filters_add'])) {
                                                $filter = new Filters;
                                                $filter->type = $filter_type;
                                                $filter->slug = str_slug($filter_name);
                                                $filter->sort = 0;
                                                $filter->status = 1;

                                                $filter->save();

                                                $fd = new FilterDescription;
                                                $fd->lang = $lang;
                                                $fd->filter_id = $filter->id;
                                                $fd->name = $filter_name;
                                                $fd->description = '';

                                                $fd->save();

                                                foreach (Categories::select('id')->where('status', 1)->get() as $category) {
                                                    $fc = new FilterCategory;
                                                    $fc->filter_id = $filter->id;
                                                    $fc->category_id = $category->id;

                                                    $fc->save();
                                                }

                                                $fv = new FilterValues;
                                                $fv->filter_id = $filter->id;
                                                $fv->slug = str_slug($filter_value);
                                                $fv->sort = 0;

                                                $fv->save();

                                                $fvd = new FilterValueDescription;
                                                $fvd->lang = $lang;
                                                $fvd->filter_value_id = $fv->id;
                                                $fvd->name = $filter_value;

                                                $fvd->save();

                                                $pf = new FilterProduct;
                                                $pf->filter_value_id = $fv->id;
                                                $pf->product_id = $id;
                                                $pf->filter_id = $filter->id;

                                                $pf->save();
                                            }
                                        }
                                    }
                                }

                                $success_row++;
                            }
                            elseif ($mode == 3) {
                                $product = [];

                                if (!empty($field['slug'])) $product['slug'] = $field['slug'];
                                if (!empty($field['layout_id'])) $product['layout_id'] = $field['layout_id'];
                                if (isset($field['model'])) $product['model'] = $field['model'];
                                if (isset($field['popular'])) $product['popular'] = $field['popular'];

                                if (isset($field['price'])) {
                                    if ($export['calc_mode_1'] && !empty($export['calc_mode_1_text'])) {
                                        $price = $export['calc_mode_1_text'];
                                        $calc = $export['calc_mode_1'];

                                        if ($calc == '*') {
                                            $field['price'] *= $price;
                                        } elseif ($calc == '/') {
                                            $field['price'] /= $price;
                                        } elseif ($calc == '+') {
                                            $field['price'] += $price;
                                        } elseif ($calc == '-') {
                                            $field['price'] -= $price;
                                        }
                                    }

                                    $product['price'] = $field['price'];
                                }

                                if (isset($field['image'])) {
                                    if (filter_var($field['image'], FILTER_VALIDATE_URL)) {
                                        $field['image'] = $this->download(public_path() . '/../images/products/', $field['image'], 'images/products/');
                                    }

                                    $product['image'] = $field['image'];
                                }

                                if (isset($field['sort'])) $product['sort'] = $field['sort'];
                                if (isset($field['reward'])) $product['reward'] = $field['reward'];
                                if (isset($field['status'])) $product['status'] = $field['status'];
                                if (!empty($field['parent_id'])) $product['parent_id'] = $field['parent_id'];
                                if (isset($field['status'])) $product['status'] = $field['status'];

                                $query = Products::where('id', $id)->update($product);

                                if (!$query) {
                                    if (count($product) >= 2 && isset($field['meta']['name']) && isset($field['meta']['meta_title'])) {
                                        if (!$import_id) {
                                            $product['id'] = Products::insert($product)->id;
                                        } else {
                                            $product['id'] = $id;
                                            Products::insert($product);
                                        }

                                        $success_row++;
                                    } else {
                                        $errors[] = 'В строке №' . $key . ' ошибки:<br>&nbsp;&nbsp;&nbsp; - Минимальный набор полей при создании товара: ID, Seo Url, Name, Meta Title';
                                        continue;
                                    }
                                } else {
                                    $product['id'] = $id;
                                    $success_update_row++;
                                }

                                if (isset($field['images'])) {
                                    if ($query) {
                                        ProductImage::where('product_id', $product['id'])->delete();
                                    }

                                    $field['images'] = @explode($delimiter, $field['images']);
                                    $images = [];

                                    if (isset($field['images'][0])) {
                                        foreach ($field['images'] as $image) {
                                            if (filter_var($image, FILTER_VALIDATE_URL)) {
                                                $image = $this->download(public_path() . '/../images/products/', $image, 'images/products/');
                                            }

                                            $images[] = ['product_id' => $product['id'], 'image' => $image, 'updated_at' => $now, 'created_at' => $now];
                                        }
                                    } else {
                                        if (filter_var($field['images'], FILTER_VALIDATE_URL)) {
                                            $field['images'] = $this->download(public_path() . '/../images/products/', $field['images'], 'images/products/');
                                        }

                                        $images[] = ['product_id' => $product['id'], 'image' => $field['images'], 'updated_at' => $now, 'created_at' => $now];
                                    }

                                    if ($images) ProductImage::insert($images);
                                }

                                if (isset($field['category_id'])) {
                                    if ($query) {
                                        ProductCategory::where('product_id', $product['id'])->delete();
                                    }

                                    $field['category_id'] = @explode($delimiter, $field['category_id']);
                                    $category_ids = [];

                                    if (isset($field['category_id'][0])) {
                                        foreach ($field['category_id'] as $category_id) {
                                            $category_ids[] = ['product_id' => $product['id'], 'category_id' => $category_id, 'updated_at' => $now, 'created_at' => $now];
                                        }
                                    } else {
                                        $category_ids[] = ['product_id' => $product['id'], 'category_id' => $field['category_id'], 'updated_at' => $now, 'created_at' => $now];
                                    }

                                    if ($category_ids) {
                                        if (empty($field['parent_id'])) {
                                            Products::where('id', $product['id'])->update(['parent_id', $category_ids[count($category_ids) - 1]['category_id']]);
                                        }

                                        ProductCategory::insert($category_ids);
                                    }
                                }

                                if (isset($field['category_name'])) {
                                    if ($query) {
                                        ProductCategory::where('product_id', $product['id'])->delete();
                                    }

                                    $category_names = @explode("\n", $field['category_name']);
                                    $category_ids = [];

                                    if (isset($category_names[1])) {
                                        foreach ($category_names as $category_name) {
                                            $parent_categories = @explode($delimiter, $category_name);

                                            if (isset($parent_categories[0])) {
                                                $parents_id = [];

                                                foreach ($parent_categories as $key => $parent_name) {
                                                    $cd = CategoryDescription::select('c.id')
                                                        ->join('categories as c', 'c.id', '=', 'category_description.category_id')
                                                        ->where('c.parent_id', (isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0))
                                                        ->where('category_description.name', trim($parent_name))
                                                        ->where('category_description.lang', $lang)
                                                        ->value('c.id');

                                                    if (!is_null($cd)) {
                                                        $parents_id[$key] = (int)$cd;
                                                    } elseif (!empty($export['categories_add'])) {
                                                        $category = new Categories;
                                                        $category->slug = str_slug($parent_name);
                                                        $category->layout_id = isset($export['category_layout_id']) ? $export['category_layout_id'] : 8;
                                                        $category->image = '';
                                                        $category->top = 0;
                                                        $category->sort = 0;
                                                        $category->parent_id = isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0;
                                                        $category->status = 1;

                                                        $category->save();
                                                        $parents_id[$key] = $category->id;

                                                        $cd = new CategoryDescription;
                                                        $cd->lang = $lang;
                                                        $cd->category_id = $category->id;
                                                        $cd->name = $parent_name;
                                                        $cd->meta_title = $parent_name;
                                                        $cd->meta_description = '';
                                                        $cd->meta_keywords = '';
                                                        $cd->description = '';

                                                        $cd->save();
                                                    }
                                                }

                                                if (count($parents_id) == count($parent_categories)) {
                                                    $category_ids[] = ['product_id' => $product['id'], 'category_id' => $category_id, 'updated_at' => $now, 'created_at' => $now];
                                                }
                                            } else {
                                                $category_id = (int)CategoryDescription::select('c.id')
                                                    ->join('categories as c', 'c.id', '=', 'category_description.category_id')
                                                    ->where('category_description.name', trim($parent_categories))
                                                    ->where('category_description.lang', $lang)
                                                    ->value('c.id');

                                                if (!$category_id && !empty($export['categories_add'])) {
                                                    $category = new Categories;
                                                    $category->slug = str_slug($parent_categories);
                                                    $category->layout_id = isset($export['category_layout_id']) ? $export['category_layout_id'] : 8;
                                                    $category->image = '';
                                                    $category->top = 0;
                                                    $category->sort = 0;
                                                    $category->parent_id = isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0;
                                                    $category->status = 1;

                                                    $category->save();
                                                    $category_id = $category->id;

                                                    $cd = new CategoryDescription;
                                                    $cd->lang = $lang;
                                                    $cd->category_id = $category->id;
                                                    $cd->name = $parent_categories;
                                                    $cd->meta_title = $parent_categories;
                                                    $cd->meta_description = '';
                                                    $cd->meta_keywords = '';
                                                    $cd->description = '';

                                                    $cd->save();
                                                }

                                                if ($category_id) {
                                                    $category_ids[] = ['product_id' => $product['id'], 'category_id' => $category_id, 'updated_at' => $now, 'created_at' => $now];
                                                }
                                            }
                                        }
                                    } else {
                                        $parent_categories = @explode($delimiter, $field['category_name']);

                                        if (isset($parent_categories[0])) {
                                            $parents_id = [];

                                            foreach ($parent_categories as $key => $parent_name) {
                                                $cd = CategoryDescription::select('c.id')
                                                    ->join('categories as c', 'c.id', '=', 'category_description.category_id')
                                                    ->where('c.parent_id', (isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0))
                                                    ->where('category_description.name', trim($parent_name))
                                                    ->where('category_description.lang', $lang)
                                                    ->value('c.id');

                                                if (!is_null($cd)) {
                                                    $parents_id[$key] = (int)$cd;
                                                } elseif (!empty($export['categories_add'])) {
                                                    $category = new Categories;
                                                    $category->slug = str_slug($parent_name);
                                                    $category->layout_id = isset($export['category_layout_id']) ? $export['category_layout_id'] : 8;
                                                    $category->image = '';
                                                    $category->top = 0;
                                                    $category->sort = 0;
                                                    $category->parent_id = isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0;
                                                    $category->status = 1;

                                                    $category->save();
                                                    $parents_id[$key] = $category->id;

                                                    $cd = new CategoryDescription;
                                                    $cd->lang = $lang;
                                                    $cd->category_id = $category->id;
                                                    $cd->name = $parent_name;
                                                    $cd->meta_title = $parent_name;
                                                    $cd->meta_description = '';
                                                    $cd->meta_keywords = '';
                                                    $cd->description = '';

                                                    $cd->save();
                                                }
                                            }

                                            if (count($parents_id) == count($parent_categories)) {
                                                $category_ids[] = ['product_id' => $product['id'], 'category_id' => $parents_id[count($parents_id) - 1], 'updated_at' => $now, 'created_at' => $now];
                                            }
                                        } else {
                                            $category_id = (int)CategoryDescription::select('c.id')
                                                ->join('categories as c', 'c.id', '=', 'category_description.category_id')
                                                ->where('category_description.name', trim($parent_categories))
                                                ->where('category_description.lang', $lang)
                                                ->value('c.id');

                                            if (!$category_id && !empty($export['categories_add'])) {
                                                $category = new Categories;
                                                $category->slug = str_slug($parent_categories);
                                                $category->layout_id = isset($export['category_layout_id']) ? $export['category_layout_id'] : 8;
                                                $category->image = '';
                                                $category->top = 0;
                                                $category->sort = 0;
                                                $category->parent_id = isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0;
                                                $category->status = 1;

                                                $category->save();
                                                $category_id = $category->id;

                                                $cd = new CategoryDescription;
                                                $cd->lang = $lang;
                                                $cd->category_id = $category->id;
                                                $cd->name = $parent_categories;
                                                $cd->meta_title = $parent_categories;
                                                $cd->meta_description = '';
                                                $cd->meta_keywords = '';
                                                $cd->description = '';

                                                $cd->save();
                                            }

                                            if ($category_id) {
                                                $category_ids[] = ['product_id' => $product['id'], 'category_id' => $category_id, 'updated_at' => $now, 'created_at' => $now];
                                            }
                                        }
                                    }

                                    if ($category_ids) {
                                        if (empty($field['parent_id'])) {
                                            Products::where('id', $product['id'])->update(['parent_id', $category_ids[count($category_ids) - 1]['category_id']]);
                                        }

                                        ProductCategory::insert($category_ids);
                                    }
                                }

                                if (isset($field['meta'])) {
                                    $meta = $field['meta'];

                                    $pd['lang'] = $lang;
                                    if (!empty($meta['name'])) $pd['name'] = $meta['name'];
                                    if (!empty($meta['meta_title'])) $pd['meta_title'] = $meta['meta_title'];
                                    if (!empty($meta['meta_description'])) $pd['meta_description'] = $meta['meta_description'];
                                    if (!empty($meta['meta_keywords'])) $pd['meta_keywords'] = $meta['meta_keywords'];
                                    if (!empty($meta['description'])) $pd['description'] = $meta['description'];

                                    $query = ProductDescription::where('product_id', $id)->where('lang', $lang)->update($pd);

                                    if (!$query) {
                                        $pd->product_id = $product['id'];

                                        if (isset($pd['name']) && isset($pd['meta_title'])) {
                                            ProductDescription::insert($pd);
                                        } else {
                                            $errors[] = 'В строке №' . $key . ' ошибки:<br>&nbsp;&nbsp;&nbsp; - Минимальный набор полей: Наименование, Meta Title';
                                            continue;
                                        }
                                    }
                                }

                                if (!empty($field['attributes'])) {
                                    $attributes = @explode("\n", $field['attributes']);

                                    if (isset($attributes[1])) {
                                        foreach ($attributes as $attribute) {
                                            $attribute_text = @explode($delimiter, $attribute);

                                            if (isset($attribute_text[1])) {
                                                $attribute_name = trim($attribute_text[0]);
                                                $attribute_value = trim($attribute_text[1]);

                                                $attribute_id = AttributeDescription::select('attribute_id')
                                                    ->where('name', $attribute_name)
                                                    ->where('lang', $lang)
                                                    ->value('attribute_id');

                                                if (!is_null($attribute_id)) {
                                                    ProductAttributeImage::where('product_id', $product['id'])->delete();
	
													$pa = ProductAttribute::where('product_id', $product['id'])->where('lang', $lang)->where('attribute_id', $attribute_id)->update(['text' => $attribute_value]);
	
													if (!$pa) {
														$pa = new ProductAttribute;
														$pa->lang = $lang;
														$pa->product_id = $product['id'];
														$pa->attribute_id = $attribute_id;
														$pa->text = $attribute_value;
		
														$pa->save();
													}

                                                    if (!empty($attribute_text[2])) {
                                                        $attribute_images = @explode(',', $attribute_text[2]);

                                                        if (isset($attribute_images[0])) {
                                                            foreach ($attribute_images as $attribute_image) {
                                                                ProductAttributeImage::insert(['attribute_id' => $attribute_id, 'product_id' => $id, 'image' => trim($attribute_image), 'updated_at' => $now, 'created_at' => $now]);
                                                            }
                                                        } else {
                                                            ProductAttributeImage::insert(['attribute_id' => $attribute_id, 'product_id' => $id, 'image' => trim($attribute_text[2]), 'updated_at' => $now, 'created_at' => $now]);
                                                        }
                                                    }
                                                } else if (!empty($export['attributes_add'])) {
                                                    $attribute = new Attributes;
                                                    $attribute->image = '';
                                                    $attribute->sort = 0;
                                                    $attribute->position_left = 0;
                                                    $attribute->position_right = 0;
                                                    $attribute->status = 1;

                                                    $attribute->save();

                                                    $ad = new AttributeDescription;
                                                    $ad->lang = $lang;
                                                    $ad->attribute_id = $attribute->id;
                                                    $ad->name = $attribute_name;

                                                    $ad->save();

                                                    $pa = new ProductAttribute;
                                                    $pa->lang = $lang;
                                                    $pa->product_id = $product['id'];
                                                    $pa->attribute_id = $attribute->id;
                                                    $pa->text = $attribute_value;

                                                    $pa->save();

                                                    if (!empty($attribute_text[2])) {
                                                        $attribute_images = @explode(',', $attribute_text[2]);

                                                        if (isset($attribute_images[0])) {
                                                            foreach ($attribute_images as $attribute_image) {
                                                                ProductAttributeImage::insert(['attribute_id' => $attribute->id, 'product_id' => $id, 'image' => trim($attribute_image), 'updated_at' => $now, 'created_at' => $now]);
                                                            }
                                                        } else {
                                                            ProductAttributeImage::insert(['attribute_id' => $attribute->id, 'product_id' => $id, 'image' => trim($attribute_text[2]), 'updated_at' => $now, 'created_at' => $now]);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        $attribute_text = @explode($delimiter, $field['attributes']);

                                        if (isset($attribute_text[1])) {
                                            $attribute_name = trim($attribute_text[0]);
                                            $attribute_value = trim($attribute_text[1]);

                                            $attribute_id = AttributeDescription::select('attribute_id')
                                                ->where('name', $attribute_name)
                                                ->where('lang', $lang)
                                                ->value('attribute_id');

                                            if (!is_null($attribute_id)) {
                                                ProductAttributeImage::where('product_id', $product['id'])->delete();
	
												$pa = ProductAttribute::where('product_id', $product['id'])->where('lang', $lang)->where('attribute_id', $attribute_id)->update(['text' => $attribute_value]);
	
												if (!$pa) {
													$pa = new ProductAttribute;
													$pa->lang = $lang;
													$pa->product_id = $product['id'];
													$pa->attribute_id = $attribute_id;
													$pa->text = $attribute_value;
		
													$pa->save();
												}

                                                if (!empty($attribute_text[2])) {
                                                    $attribute_images = @explode(',', $attribute_text[2]);

                                                    if (isset($attribute_images[0])) {
                                                        foreach ($attribute_images as $attribute_image) {
                                                            ProductAttributeImage::insert(['attribute_id' => $attribute_id, 'product_id' => $id, 'image' => trim($attribute_image), 'updated_at' => $now, 'created_at' => $now]);
                                                        }
                                                    } else {
                                                        ProductAttributeImage::insert(['attribute_id' => $attribute_id, 'product_id' => $id, 'image' => trim($attribute_text[2]), 'updated_at' => $now, 'created_at' => $now]);
                                                    }
                                                }
                                            } else if (!empty($export['attributes_add'])) {
                                                $attribute = new Attributes;
                                                $attribute->image = '';
                                                $attribute->sort = 0;
                                                $attribute->position_left = 0;
                                                $attribute->position_right = 0;
                                                $attribute->status = 1;

                                                $attribute->save();

                                                $ad = new AttributeDescription;
                                                $ad->lang = $lang;
                                                $ad->attribute_id = $attribute->id;
                                                $ad->name = $attribute_name;

                                                $ad->save();

                                                $pa = new ProductAttribute;
                                                $pa->lang = $lang;
                                                $pa->product_id = $product['id'];
                                                $pa->attribute_id = $attribute->id;
                                                $pa->text = $attribute_value;

                                                $pa->save();

                                                if (!empty($attribute_text[2])) {
                                                    $attribute_images = @explode(',', $attribute_text[2]);

                                                    if (isset($attribute_images[0])) {
                                                        foreach ($attribute_images as $attribute_image) {
                                                            ProductAttributeImage::insert(['attribute_id' => $attribute->id, 'product_id' => $id, 'image' => trim($attribute_image), 'updated_at' => $now, 'created_at' => $now]);
                                                        }
                                                    } else {
                                                        ProductAttributeImage::insert(['attribute_id' => $attribute->id, 'product_id' => $id, 'image' => trim($attribute_text[2]), 'updated_at' => $now, 'created_at' => $now]);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                                if (!empty($field['options'])) {
                                    $options = @explode("\n", $field['options']);

                                    if (isset($options[1])) {
                                        foreach ($options as $option) {
                                            $option_text = @explode($delimiter, $option);

                                            if (isset($option_text[1])) {
                                                $option_name = trim($option_text[0]);
                                                $option_value_text = trim($option_text[1]);
                                                $option_required = trim($option_text[2]);
                                                $option_type = trim($option_text[3]);
                                                $option_price = trim($option_text[4]);
                                                $option_quantity = isset($option_text[5]) ? trim($option_text[5]) : 0;
                                                $option_weight = isset($option_text[6]) ? trim($option_text[6]) : 0;
                                                $option_reward = isset($option_text[7]) ? trim($option_text[7]) : 0;
                                                $option_image = isset($option_text[8]) ? trim($option_text[8]) : '';

                                                $option_id = OptionDescription::join('options as o', 'o.id', '=', 'option_description.option_id')
                                                    ->select('o.id')
                                                    ->where('option_description.name', $option_name)
                                                    ->where('option_description.lang', $lang)
                                                    ->where('o.type', $option_type)
                                                    ->value('o.id');

                                                if (!is_null($option_id)) {
                                                    $option_value = OptionValueDescription::join('product_option_values as pov', 'pov.option_value_id', '=', 'option_value_description.option_value_id')
                                                        ->join('option_values as ov', 'ov.id', '=', 'option_value_description.option_value_id')
                                                        ->select('pov.id', 'pov.product_option_id')
                                                        ->where('option_value_description.name', $option_value_text)
                                                        ->where('option_value_description.lang', $lang)
                                                        ->where('ov.option_id', $option_id)
                                                        ->where('pov.product_id', $id)
                                                        ->first();

                                                    if (!is_null($option_value)) {
                                                        if (!in_array($option_type, ['radio', 'color', 'select', 'checkbox'])) {
                                                            ProductOption::where('option_id', $option_id)
                                                                ->where('product_id', $id)
                                                                ->where('id', $option_value->product_option_id)
                                                                ->update(['required' => $option_required, 'value' => $option_value_text]);
                                                        } else {
                                                            ProductOption::where('option_id', $option_id)
                                                                ->where('product_id', $id)
                                                                ->where('id', $option_value->product_option_id)
                                                                ->update(['required' => $option_required, 'value' => '']);

                                                            ProductOptionValues::where('id', $option_value->id)
                                                                ->where('product_id', $id)
                                                                ->update(['quantity' => $option_quantity, 'price' => $option_price, 'image' => $option_image, 'weight' => $option_weight, 'reward' => $option_reward]);
                                                        }
                                                    } else {
                                                        $option_values = new OptionValues;
                                                        $option_values->sort_order = 0;
                                                        $option_values->image = '';
                                                        $option_values->option_id = $option_id->id;

                                                        $option_values->save();

                                                        $option_value_description = new OptionValueDescription;
                                                        $option_value_description->name = $option_value_text;
                                                        $option_value_description->lang = $lang;
                                                        $option_value_description->option_value_id = $option_values->id;

                                                        $option_value_description->save();

                                                        if (!in_array($option_type, ['radio', 'color', 'select', 'checkbox'])) {
                                                            $product_option = new ProductOption;
                                                            $product_option->required = $option_required;
                                                            $product_option->value = $option_value_text;
                                                            $product_option->option_id = $option_id->id;
                                                            $product_option->product_id = $id;

                                                            $product_option->save();
                                                        } else {
                                                            $product_option = new ProductOption;
                                                            $product_option->required = $option_required;
                                                            $product_option->value = $option_value_text;
                                                            $product_option->option_id = $option_id->id;
                                                            $product_option->product_id = $id;

                                                            $product_option->save();

                                                            $product_option_values = new ProductOptionValues;
                                                            $product_option_values->quantity = $option_quantity;
                                                            $product_option_values->price = $option_price;
                                                            $product_option_values->weight = $option_weight;
                                                            $product_option_values->reward = $option_reward;
                                                            $product_option_values->image = $option_image;
                                                            $product_option_values->product_id = $id;
                                                            $product_option_values->product_option_id = $product_option->id;
                                                            $product_option_values->option_id = $option_id->id;
                                                            $product_option_values->product_option_value_id = $option_values->id;

                                                            $product_option_values->save();
                                                        }
                                                    }
                                                }
                                                else {
                                                    $option_id = new Options;
                                                    $option_id->type = $option_type;
                                                    $option_id->sort_order = 0;
                                                    $option_id->status = 1;

                                                    $option_id->save();

                                                    $option_description = new OptionDescription;
                                                    $option_description->name = $option_name;
                                                    $option_description->lang = $lang;
                                                    $option_description->option_id = $option_id->id;

                                                    $option_description->save();

                                                    $option_values = new OptionValues;
                                                    $option_values->sort_order = 0;
                                                    $option_values->image = '';
                                                    $option_values->option_id = $option_id->id;

                                                    $option_values->save();

                                                    $option_value_description = new OptionValueDescription;
                                                    $option_value_description->name = $option_value_text;
                                                    $option_value_description->lang = $lang;
                                                    $option_value_description->option_value_id = $option_values->id;

                                                    $option_value_description->save();

                                                    if (!in_array($option_type, ['radio', 'color', 'select', 'checkbox'])) {
                                                        $product_option = new ProductOption;
                                                        $product_option->required = $option_required;
                                                        $product_option->value = $option_value_text;
                                                        $product_option->option_id = $option_id->id;
                                                        $product_option->product_id = $id;

                                                        $product_option->save();
                                                    } else {
                                                        $product_option = new ProductOption;
                                                        $product_option->required = $option_required;
                                                        $product_option->value = $option_value_text;
                                                        $product_option->option_id = $option_id->id;
                                                        $product_option->product_id = $id;

                                                        $product_option->save();

                                                        $product_option_values = new ProductOptionValues;
                                                        $product_option_values->quantity = $option_quantity;
                                                        $product_option_values->price = $option_price;
                                                        $product_option_values->weight = $option_weight;
                                                        $product_option_values->reward = $option_reward;
                                                        $product_option_values->image = $option_image;
                                                        $product_option_values->product_id = $id;
                                                        $product_option_values->product_option_id = $product_option->id;
                                                        $product_option_values->option_id = $option_id->id;
                                                        $product_option_values->product_option_value_id = $option_values->id;

                                                        $product_option_values->save();
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        $option_text = @explode($delimiter, $field['options']);

                                        if (isset($option_text[1])) {
                                            $option_name = trim($option_text[0]);
                                            $option_value_text = trim($option_text[1]);
                                            $option_required = trim($option_text[2]);
                                            $option_type = trim($option_text[3]);
                                            $option_price = trim($option_text[4]);
                                            $option_quantity = isset($option_text[5]) ? trim($option_text[5]) : 0;
                                            $option_weight = isset($option_text[6]) ? trim($option_text[6]) : 0;
                                            $option_reward = isset($option_text[7]) ? trim($option_text[7]) : 0;
                                            $option_image = isset($option_text[8]) ? trim($option_text[8]) : '';

                                            $option_id = OptionDescription::join('options as o', 'o.id', '=', 'option_description.option_id')
                                                ->select('o.id')
                                                ->where('option_description.name', $option_name)
                                                ->where('option_description.lang', $lang)
                                                ->where('o.type', $option_type)
                                                ->value('o.id');

                                            if (!is_null($option_id)) {
                                                $option_value = OptionValueDescription::join('product_option_values as pov', 'pov.option_value_id', '=', 'option_value_description.option_value_id')
                                                    ->join('option_values as ov', 'ov.id', '=', 'option_value_description.option_value_id')
                                                    ->select('pov.id', 'pov.product_option_id')
                                                    ->where('option_value_description.name', $option_value_text)
                                                    ->where('option_value_description.lang', $lang)
                                                    ->where('ov.option_id', $option_id)
                                                    ->where('pov.product_id', $id)
                                                    ->first();

                                                if (!is_null($option_value)) {
                                                    if (!in_array($option_type, ['radio', 'color', 'select', 'checkbox'])) {
                                                        ProductOption::where('option_id', $option_id)
                                                            ->where('product_id', $id)
                                                            ->where('id', $option_value->product_option_id)
                                                            ->update(['required' => $option_required, 'value' => $option_value_text]);
                                                    } else {
                                                        ProductOption::where('option_id', $option_id)
                                                            ->where('product_id', $id)
                                                            ->where('id', $option_value->product_option_id)
                                                            ->update(['required' => $option_required]);

                                                        ProductOptionValues::where('id', $option_value->id)
                                                            ->where('product_id', $id)
                                                            ->update(['quantity' => $option_quantity, 'price' => $option_price, 'image' => $option_image, 'weight' => $option_weight, 'reward' => $option_reward]);
                                                    }
                                                } else {
                                                    $option_values = new OptionValues;
                                                    $option_values->sort_order = 0;
                                                    $option_values->image = '';
                                                    $option_values->option_id = $option_id->id;

                                                    $option_values->save();

                                                    $option_value_description = new OptionValueDescription;
                                                    $option_value_description->name = $option_value_text;
                                                    $option_value_description->lang = $lang;
                                                    $option_value_description->option_value_id = $option_values->id;

                                                    $option_value_description->save();

                                                    if (!in_array($option_type, ['radio', 'color', 'select', 'checkbox'])) {
                                                        $product_option = new ProductOption;
                                                        $product_option->required = $option_required;
                                                        $product_option->value = $option_value_text;
                                                        $product_option->option_id = $option_id->id;
                                                        $product_option->product_id = $id;

                                                        $product_option->save();
                                                    } else {
                                                        $product_option = new ProductOption;
                                                        $product_option->required = $option_required;
                                                        $product_option->value = $option_value_text;
                                                        $product_option->option_id = $option_id->id;
                                                        $product_option->product_id = $id;

                                                        $product_option->save();

                                                        $product_option_values = new ProductOptionValues;
                                                        $product_option_values->quantity = $option_quantity;
                                                        $product_option_values->price = $option_price;
                                                        $product_option_values->weight = $option_weight;
                                                        $product_option_values->reward = $option_reward;
                                                        $product_option_values->image = $option_image;
                                                        $product_option_values->product_id = $id;
                                                        $product_option_values->product_option_id = $product_option->id;
                                                        $product_option_values->option_id = $option_id->id;
                                                        $product_option_values->product_option_value_id = $option_values->id;

                                                        $product_option_values->save();
                                                    }
                                                }
                                            }
                                            else {
                                                $option_id = new Options;
                                                $option_id->type = $option_type;
                                                $option_id->sort_order = 0;
                                                $option_id->status = 1;

                                                $option_id->save();

                                                $option_description = new OptionDescription;
                                                $option_description->name = $option_name;
                                                $option_description->lang = $lang;
                                                $option_description->option_id = $option_id->id;

                                                $option_description->save();

                                                $option_values = new OptionValues;
                                                $option_values->sort_order = 0;
                                                $option_values->image = '';
                                                $option_values->option_id = $option_id->id;

                                                $option_values->save();

                                                $option_value_description = new OptionValueDescription;
                                                $option_value_description->name = $option_value_text;
                                                $option_value_description->lang = $lang;
                                                $option_value_description->option_value_id = $option_values->id;

                                                $option_value_description->save();

                                                if (!in_array($option_type, ['radio', 'color', 'select', 'checkbox'])) {
                                                    $product_option = new ProductOption;
                                                    $product_option->required = $option_required;
                                                    $product_option->value = $option_value_text;
                                                    $product_option->option_id = $option_id->id;
                                                    $product_option->product_id = $id;

                                                    $product_option->save();
                                                } else {
                                                    $product_option = new ProductOption;
                                                    $product_option->required = $option_required;
                                                    $product_option->value = $option_value_text;
                                                    $product_option->option_id = $option_id->id;
                                                    $product_option->product_id = $id;

                                                    $product_option->save();

                                                    $product_option_values = new ProductOptionValues;
                                                    $product_option_values->quantity = $option_quantity;
                                                    $product_option_values->price = $option_price;
                                                    $product_option_values->weight = $option_weight;
                                                    $product_option_values->reward = $option_reward;
                                                    $product_option_values->image = $option_image;
                                                    $product_option_values->product_id = $id;
                                                    $product_option_values->product_option_id = $product_option->id;
                                                    $product_option_values->option_id = $option_id->id;
                                                    $product_option_values->product_option_value_id = $option_values->id;

                                                    $product_option_values->save();
                                                }
                                            }
                                        }
                                    }
                                }

                                if (!empty($field['product_reward'])) {
                                    if ($query) {
                                        ProductReward::where('product_id', $product['id'])->delete();
                                    }

                                    $product_rewards = @explode("\n", $field['product_reward']);

                                    if (isset($product_rewards[1])) {
                                        foreach ($product_rewards as $product_reward) {
                                            $reward_text = @explode($delimiter, $product_reward);

                                            if (isset($reward_text[1])) {
                                                $customer_group_id = trim($reward_text[0]);
                                                $reward = trim($reward_text[1]);

                                                if (CustomerGroups::where('customer_group_id', $customer_group_id)->count()) {
                                                    ProductReward::insert(['customer_group_id' => $customer_group_id, 'product_id' => $product['id'], 'reward' => $reward]);
                                                }
                                            }
                                        }
                                    } else {
                                        $reward_text = @explode($delimiter, $field['attributes']);

                                        if (isset($reward_text[1])) {
                                            $customer_group_id = trim($reward_text[0]);
                                            $reward = trim($reward_text[1]);

                                            if (CustomerGroups::where('customer_group_id', $customer_group_id)->count()) {
                                                ProductReward::insert(['customer_group_id' => $customer_group_id, 'product_id' => $product['id'], 'reward' => $reward]);
                                            }
                                        }
                                    }
                                }

                                if (!empty($field['product_special'])) {
                                    if ($query) {
                                        ProductSpecial::where('product_id', $product['id'])->delete();
                                    }

                                    $product_specials = @explode("\n", $field['product_special']);

                                    if (isset($product_specials[1])) {
                                        foreach ($product_specials as $product_special) {
                                            $special_text = @explode($delimiter, $product_special);

                                            if (isset($special_text[1])) {
                                                $customer_group_id = trim($special_text[0]);
                                                $price = trim($special_text[1]);
                                                $date_start = date('Y-m-d H:i:s', \strtotime(trim($special_text[2])));
                                                $date_end = date('Y-m-d H:i:s', \strtotime(trim($special_text[3])));

                                                if ($export['calc_mode_2'] && !empty($export['calc_mode_2_text'])) {
                                                    $price = $export['calc_mode_2_text'];
                                                    $calc = $export['calc_mode_2'];

                                                    if ($calc == '*') {
                                                        $price *= $price;
                                                    } elseif ($calc == '/') {
                                                        $price /= $price;
                                                    } elseif ($calc == '+') {
                                                        $price += $price;
                                                    } elseif ($calc == '-') {
                                                        $price -= $price;
                                                    }
                                                }

                                                $product_special = new ProductSpecial;
                                                $product_special->customer_group_id = $customer_group_id;
                                                $product_special->product_id = $product['id'];
                                                $product_special->price = $price;
                                                $product_special->date_start = $date_start;
                                                $product_special->date_end = $date_end;

                                                $product_special->save();
                                            }
                                        }
                                    } else {
                                        $special_text = @explode($delimiter, $field['product_special']);

                                        if (isset($special_text[1])) {
                                            $customer_group_id = trim($special_text[0]);
                                            $price = trim($special_text[1]);
                                            $date_start = date('Y-m-d H:i:s', \strtotime(trim($special_text[2])));
                                            $date_end = date('Y-m-d H:i:s', \strtotime(trim($special_text[3])));

                                            if ($export['calc_mode_2'] && !empty($export['calc_mode_2_text'])) {
                                                $price = $export['calc_mode_2_text'];
                                                $calc = $export['calc_mode_2'];

                                                if ($calc == '*') {
                                                    $price *= $price;
                                                } elseif ($calc == '/') {
                                                    $price /= $price;
                                                } elseif ($calc == '+') {
                                                    $price += $price;
                                                } elseif ($calc == '-') {
                                                    $price -= $price;
                                                }
                                            }

                                            $product_special = new ProductSpecial;
                                            $product_special->customer_group_id = $customer_group_id;
                                            $product_special->product_id = $product['id'];
                                            $product_special->price = $price;
                                            $product_special->date_start = $date_start;
                                            $product_special->date_end = $date_end;

                                            $product_special->save();
                                        }
                                    }
                                }

                                if (!empty($field['filters'])) {
                                    $filters = @explode("\n", $field['filters']);
                                    FilterProduct::where('product_id', $product['id'])->delete();

                                    if (isset($filters[1])) {
                                        foreach ($filters as $filter) {
                                            $filter_text = @explode($delimiter, $filter);

                                            if (isset($filter_text[1])) {
                                                $filter_name = trim($filter_text[0]);
                                                $filter_value = trim($filter_text[1]);
                                                $filter_type = !empty($filter_text[2]) ? trim($filter_text[2]) : (isset($export['filters_type']) ? $export['filters_type'] : 'checkbox');

                                                $filter_id = FilterDescription::select('filter_id')
                                                    ->where('name', $filter_name)
                                                    ->where('lang', $lang)
                                                    ->value('filter_id');

                                                if (!is_null($filter_id)) {
                                                    $filter_values_id = FilterValueDescription::select('filter_value_id')
                                                        ->where('name', $filter_value)
                                                        ->where('lang', $lang)
                                                        ->value('filter_value_id');

                                                    if (!is_null($filter_values_id)) {
                                                        FilterProduct::insert(['filter_value_id' => $filter_values_id, 'product_id' => $product['id'], 'filter_id' => $filter_id, 'updated_at' => $now, 'created_at' => $now]);
                                                    } elseif (!empty($export['filters_add'])) {
                                                        $fv = new FilterValues;
                                                        $fv->filter_id = $filter_id->filter_id;
                                                        $fv->slug = str_slug($filter_value);
                                                        $fv->sort = 0;

                                                        $fv->save();

                                                        $fvd = new FilterValueDescription;
                                                        $fvd->lang = $lang;
                                                        $fvd->filter_value_id = $fv->id;
                                                        $fvd->name = $filter_value;

                                                        $fvd->save();

                                                        $pf = new FilterProduct;
                                                        $pf->filter_value_id = $fv->id;
                                                        $pf->product_id = $product['id'];
                                                        $pf->filter_id = $filter_id->filter_id;

                                                        $pf->save();
                                                    }
                                                }
                                                else if (!empty($export['filters_add'])) {
                                                    $filter = new Filters;
                                                    $filter->type = $filter_type;
                                                    $filter->slug = str_slug($filter_name);
                                                    $filter->sort = 0;
                                                    $filter->status = 1;

                                                    $filter->save();

                                                    $fd = new FilterDescription;
                                                    $fd->lang = $lang;
                                                    $fd->filter_id = $filter->id;
                                                    $fd->name = $filter_name;
                                                    $fd->description = '';

                                                    $fd->save();

                                                    foreach (Categories::select('id')->where('status', 1)->get() as $category) {
                                                        $fc = new FilterCategory;
                                                        $fc->filter_id = $filter->id;
                                                        $fc->category_id = $category->id;

                                                        $fc->save();
                                                    }

                                                    $fv = new FilterValues;
                                                    $fv->filter_id = $filter->id;
                                                    $fv->slug = str_slug($filter_value);
                                                    $fv->sort = 0;

                                                    $fv->save();

                                                    $fvd = new FilterValueDescription;
                                                    $fvd->lang = $lang;
                                                    $fvd->filter_value_id = $fv->id;
                                                    $fvd->name = $filter_value;

                                                    $fvd->save();

                                                    $pf = new FilterProduct;
                                                    $pf->filter_value_id = $fv->id;
                                                    $pf->product_id = $product['id'];
                                                    $pf->filter_id = $filter->id;

                                                    $pf->save();
                                                }
                                            }
                                        }
                                    } else {
                                        $filter_text = @explode($delimiter, $filter);

                                        if (isset($filter_text[1])) {
                                            $filter_name = trim($filter_text[0]);
                                            $filter_value = trim($filter_text[1]);
                                            $filter_type = isset($export['filters_type']) ? $export['filters_type'] : 'checkbox';

                                            $filter_id = FilterDescription::select('filter_id')
                                                ->where('name', $filter_name)
                                                ->where('lang', $lang)
                                                ->value('filter_id');

                                            if (!is_null($filter_id)) {
                                                $filter_values_id = FilterValueDescription::select('filter_value_id')
                                                    ->where('name', $filter_value)
                                                    ->where('lang', $lang)
                                                    ->value('filter_value_id');

                                                if (!is_null($filter_values_id)) {
                                                    FilterProduct::insert(['filter_value_id' => $filter_values_id, 'product_id' => $product['id'], 'filter_id' => $filter_id, 'updated_at' => $now, 'created_at' => $now]);
                                                } else {
                                                    $fv = new FilterValues;
                                                    $fv->filter_id = $filter_id->filter_id;
                                                    $fv->slug = str_slug($filter_value);
                                                    $fv->sort = 0;

                                                    $fv->save();

                                                    $fvd = new FilterValueDescription;
                                                    $fvd->lang = $lang;
                                                    $fvd->filter_value_id = $fv->id;
                                                    $fvd->name = $filter_value;

                                                    $fvd->save();

                                                    $pf = new FilterProduct;
                                                    $pf->filter_value_id = $fv->id;
                                                    $pf->product_id = $product['id'];
                                                    $pf->filter_id = $filter_id->filter_id;

                                                    $pf->save();
                                                }
                                            }
                                            else if (!empty($export['filters_add'])) {
                                                $filter = new Filters;
                                                $filter->type = $filter_type;
                                                $filter->slug = str_slug($filter_name);
                                                $filter->sort = 0;
                                                $filter->status = 1;

                                                $filter->save();

                                                $fd = new FilterDescription;
                                                $fd->lang = $lang;
                                                $fd->filter_id = $filter->id;
                                                $fd->name = $filter_name;
                                                $fd->description = '';

                                                $fd->save();

                                                foreach (Categories::select('id')->where('status', 1)->get() as $category) {
                                                    $fc = new FilterCategory;
                                                    $fc->filter_id = $filter->id;
                                                    $fc->category_id = $category->id;

                                                    $fc->save();
                                                }

                                                $fv = new FilterValues;
                                                $fv->filter_id = $filter->id;
                                                $fv->slug = str_slug($filter_value);
                                                $fv->sort = 0;

                                                $fv->save();

                                                $fvd = new FilterValueDescription;
                                                $fvd->lang = $lang;
                                                $fvd->filter_value_id = $fv->id;
                                                $fvd->name = $filter_value;

                                                $fvd->save();

                                                $pf = new FilterProduct;
                                                $pf->filter_value_id = $fv->id;
                                                $pf->product_id = $product['id'];
                                                $pf->filter_id = $filter->id;

                                                $pf->save();
                                            }
                                        }
                                    }
                                }
                            }
                            elseif ($mode == 4) {
                                $query = Products::find($id)->delete();

                                if ($query) {
                                    ProductDescription::where('product_id', $id)->delete();
                                    FilterProduct::where('product_id', $id)->delete();
                                    ProductAttribute::where('product_id', $id)->delete();
                                    ProductImage::where('product_id', $id)->delete();
                                    ProductOption::where('product_id', $id)->delete();
                                    ProductOptionValues::where('product_id', $id)->delete();
                                    ProductSpecial::where('product_id', $id)->delete();
                                    ProductReward::where('product_id', $id)->delete();
                                    ProductCategory::where('product_id', $id)->delete();
                                    ProductAttributeImage::where('product_id', $id)->delete();
                                    $success_row++;
                                }
                            }
                        }
                    }
                    elseif ($request->field == 'page') {
                        $import_id = !empty($export['import_id']) ? $export['import_id'] : 0;

                        if ($import_id) {
                            $max_id = (int)Pages::max('id');
                        }

                        foreach ($fields as $key => &$field) {
                            if ((isset($field['layout_id']) && $field['layout_id'] == 0) && $layout_id) {
                                $field['layout_id'] = $layout_id;
                            }

                            if ((isset($field['status']) && $field['status'] == 0) && $status) {
                                $field['status'] = $status;
                            }

                            $id = 0;

                            if ($mode != 2) {
                                if (!$import_id) {
                                    if ($key_field == 'id') {
                                        $id = $field['id'];
                                    } elseif (!empty($field['name']) && $key_field == 'name') {
                                        $id = (int)PageDescription::select('page_id')->where('name', $field['name'])->where('lang', $lang)->value('page_id');
                                    }
                                } else {
                                    $id = $field['id'];

                                    if ($id <= $max_id) {
                                        $import_id = false;
                                    }
                                }
                            } else {
                                if (isset($field['id']) && isset($max_id)) {
                                    $id = $field['id'];

                                    if ($id <= $max_id) {
                                        $import_id = false;
                                    }
                                }
                            }

                            if (!$id && $mode != 2) {
                                $errors[] = 'В строке №' . $key . ' ошибки:<br>&nbsp;&nbsp;&nbsp; - идентификатор статьи не найден';
                                continue;
                            }
	
							$count_id = Pages::where('id', $id)->count();
	
							if (!$count_id && ($mode == 2 || $mode == 3)) {
								$messages = [
									'slug' => 'required|not_in:sort-name,sort-popular,sort-price,page,news,catalog|max:255|unique:pages,slug' . ($id ? ',' . $id . ',id' : '') . '|alpha_dash',
									'layout_id' => 'required',
									'meta.meta_title' => 'required',
									'meta.name' => 'required'
								];
							}

                            if (isset($messages)) {
                                $validator = Validator::make($field, $messages);

                                if ($validator->fails()) {
                                    $validate_error = [];

                                    foreach(json_decode($validator->errors(), true) as $error) {
                                        $validate_error[] = $error[0];
                                    }

                                    $errors[] = 'В строке №' . $key . ' ошибки:<br>&nbsp;&nbsp;&nbsp; - ' . implode('<br>&nbsp;&nbsp;&nbsp; - ', $validate_error);
                                    continue;
                                }
                            }

                            $now = Carbon::now();

                            if ($mode == 1) {
                                $page = [];

                                if (!empty($field['slug'])) $page['slug'] = $field['slug'];
                                if (!empty($field['layout_id'])) $page['layout_id'] = $field['layout_id'];
                                if (isset($field['top'])) $page['top'] = $field['top'];
                                if (isset($field['bottom'])) $page['bottom'] = $field['bottom'];
                                if (isset($field['image'])) {
                                    if (filter_var($field['image'], FILTER_VALIDATE_URL)) {
                                        $field['image'] = $this->download(public_path() . '/../images/pages/', $field['image'], 'images/pages/');
                                    }

                                    $page['image'] = $field['image'];
                                }

                                if (isset($field['sort'])) $page['sort'] = $field['sort'];
                                if (isset($field['status'])) $page['status'] = $field['status'];
                                if (!empty($field['parent_id'])) $page['parent_id'] = $field['parent_id'];
                                if (isset($field['status'])) $page['status'] = $field['status'];

                                $query = Pages::where('id', $id)->update($page);

                                if ($query) {
                                    if (isset($field['images'])) {
                                        $field['images'] = @explode($delimiter, $field['images']);
                                        PageImage::where('page_id', $id)->delete();

                                        if (isset($field['images'][0])) {
                                            foreach ($field['images'] as $image) {
                                                if (filter_var($image, FILTER_VALIDATE_URL)) {
                                                    $image = $this->download(public_path() . '/../images/pages/', $image, 'images/pages/');
                                                }

                                                $images[] = ['page_id' => $id, 'image' => $image, 'updated_at' => $now, 'created_at' => $now];
                                            }
                                        } else {
                                            if (filter_var($field['images'], FILTER_VALIDATE_URL)) {
                                                $field['images'] = $this->download(public_path() . '/../images/pages/', $field['images'], 'images/pages/');
                                            }

                                            $images[] = ['page_id' => $id, 'image' => $field['images'], 'updated_at' => $now, 'created_at' => $now];
                                        }

                                        if ($images) PageImage::insert($images);
                                    }

                                    if (isset($field['category_id'])) {
                                        $field['parent_id'] = $field['category_id'];
                                    }

                                    if (!empty($field['category_name']) && empty($field['parent_id'])) {
                                        $category_ids = [];

                                        $parent_categories = @explode($delimiter, $field['category_name']);

                                        if (isset($parent_categories[0])) {
                                            $parents_id = [];

                                            foreach ($parent_categories as $key => $parent_name) {
                                                $cd = PageCategoryDescription::select('pc.id')
                                                    ->join('page_categories as pc', 'pc.id', '=', 'page_category_description.category_id')
                                                    ->where('pc.parent_id', (isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0))
                                                    ->where('page_category_description.name', trim($parent_name))
                                                    ->where('page_category_description.lang', $lang)
                                                    ->value('pc.id');

                                                $parents_id[$key] = (int)$cd;
                                            }

                                            if (count($parents_id) == count($parent_categories)) {
                                                $category_ids[] = $parents_id[count($parents_id) - 1];
                                            }
                                        } else {
                                            $category_id = (int)PageCategoryDescription::select('pc.id')
                                                ->join('page_categories as pc', 'pc.id', '=', 'page_category_description.category_id')
                                                ->where('page_category_description.name', trim($parent_categories))
                                                ->where('page_category_description.lang', $lang)
                                                ->value('pc.id');

                                            if ($category_id) $category_ids[] = $category_id;
                                        }

                                        if ($category_ids) {
                                            Pages::where('id', $id)->update(['parent_id' => $category_ids[count($category_ids) - 1]]);
                                        }
                                    }

                                    if (isset($field['meta'])) {
                                        $meta = $field['meta'];

                                        $pd['lang'] = $lang;
                                        $pd['page_id'] = $id;
                                        if (isset($meta['name'])) $pd['name'] = $meta['name'];
                                        if (isset($meta['meta_title'])) $pd['meta_title'] = $meta['meta_title'];
                                        if (isset($meta['meta_description'])) $pd['meta_description'] = $meta['meta_description'];
                                        if (isset($meta['meta_keywords'])) $pd['meta_keywords'] = $meta['meta_keywords'];
                                        if (isset($meta['description'])) $pd['description'] = $meta['description'];

                                        PageDescription::where('page_id', $id)->where('lang', $lang)->update($pd);
                                    }

                                    if (!empty($field['attributes'])) {
                                        $attributes = @explode("\n", $field['attributes']);

                                        if (isset($attributes[1])) {
                                            foreach ($attributes as $attribute) {
                                                $attribute_text = @explode($delimiter, $attribute);

                                                if (isset($attribute_text[1])) {
                                                    $attribute_name = trim($attribute_text[0]);
                                                    $attribute_value = trim($attribute_text[1]);

                                                    $attribute_id = AttributeDescription::select('attribute_id')
                                                        ->where('name', $attribute_name)
                                                        ->where('lang', $lang)
                                                        ->value('attribute_id');

                                                    if (!is_null($attribute_id)) {
                                                        PageAttribute::where('attribute_id', $attribute_id)
                                                            ->where('page_id', $id)
                                                            ->where('lang', $lang)
                                                            ->update(['text' => $attribute_value]);

                                                        if (!empty($attribute_text[2])) {
                                                            $attribute_images = @explode(',', $attribute_text[2]);

                                                            if (isset($attribute_images[0])) {
                                                                foreach ($attribute_images as $attribute_image) {
                                                                    PageAttributeImage::where('attribute_id', $attribute_id)
                                                                        ->where('page_id', $id)
                                                                        ->update(['image' => trim($attribute_image)]);
                                                                }
                                                            } else {
                                                                PageAttributeImage::where('attribute_id', $attribute_id)
                                                                    ->where('page_id', $id)
                                                                    ->update(['image' => trim($attribute_text[2])]);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            $attribute_text = @explode($delimiter, $field['attributes']);

                                            if (isset($attribute_text[1])) {
                                                $attribute_name = trim($attribute_text[0]);
                                                $attribute_value = trim($attribute_text[1]);

                                                $attribute_id = AttributeDescription::select('attribute_id')
                                                    ->where('name', $attribute_name)
                                                    ->where('lang', $lang)
                                                    ->value('attribute_id');

                                                if (!is_null($attribute_id)) {
                                                    PageAttribute::where('attribute_id', $attribute_id)
                                                        ->where('page_id', $id)
                                                        ->where('lang', $lang)
                                                        ->update(['text' => $attribute_value]);

                                                    if (!empty($attribute_text[2])) {
                                                        $attribute_images = @explode(',', $attribute_text[2]);

                                                        if (isset($attribute_images[0])) {
                                                            foreach ($attribute_images as $attribute_image) {
                                                                PageAttributeImage::where('attribute_id', $attribute_id)
                                                                    ->where('page_id', $id)
                                                                    ->update(['image' => trim($attribute_image)]);
                                                            }
                                                        } else {
                                                            PageAttributeImage::where('attribute_id', $attribute_id)
                                                                ->where('page_id', $id)
                                                                ->update(['image' => trim($attribute_text[2])]);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    $success_row++;
                                }
                            }
                            elseif ($mode == 2) {
                                $page = new Pages;
                                $page->slug = $field['slug'];
                                $page->layout_id = $field['layout_id'];
                                $page->top = isset($field['top']) ? $field['top'] : 0;
                                $page->bottom = isset($field['bottom']) ? $field['bottom'] : 0;
                                $page->image = isset($field['image']) ? $field['image'] : '';
                                $page->sort = isset($field['sort']) ? $field['sort'] : 0;
                                $page->parent_id = isset($field['parent_id']) ? $field['parent_id'] : 0;
                                $page->status = isset($field['status']) ? $field['status'] : 0;

                                $page->save();

                                if (!$import_id || !$id) {
                                    $id = $page->id;
                                }

                                $meta = $field['meta'];

                                $pd = new PageDescription;
                                $pd->lang = $lang;
                                $pd->page_id = $id;
                                $pd->name = $meta['name'];
                                $pd->meta_title = $meta['meta_title'];
                                $pd->meta_description = !empty($meta['meta_description']) ? $meta['meta_description'] : '';
                                $pd->meta_keywords = !empty($meta['meta_keywords']) ? $meta['meta_keywords'] : '';
                                $pd->description = !empty($meta['description']) ? $meta['description'] : '';

                                $pd->save();

                                if (isset($field['images'])) {
                                    $field['images'] = @explode($delimiter, $field['images']);
                                    $images = [];

                                    if (isset($field['images'][0])) {
                                        foreach ($field['images'] as $image) {
                                            if (filter_var($image, FILTER_VALIDATE_URL)) {
                                                $image = $this->download(public_path() . '/../images/pages/', $image, 'images/pages/');
                                            }

                                            $images[] = ['page_id' => $id, 'image' => $image, 'updated_at' => $now, 'created_at' => $now];
                                        }
                                    } else {
                                        if (filter_var($field['images'], FILTER_VALIDATE_URL)) {
                                            $field['images'] = $this->download(public_path() . '/../images/pages/', $field['images'], 'images/pages/');
                                        }

                                        $images[] = ['page_id' => $id, 'image' => $field['images'], 'updated_at' => $now, 'created_at' => $now];
                                    }

                                    if ($images) PageImage::insert($images);
                                }

                                if (isset($field['category_id'])) {
                                    $field['parent_id'] = @explode($delimiter, $field['category_id']);
                                }

                                if (!empty($field['category_name'])) {
                                    $category_ids = [];

                                    $parent_categories = @explode($delimiter, $field['category_name']);

                                    if (isset($parent_categories[0])) {
                                        $parents_id = [];

                                        foreach ($parent_categories as $key => $parent_name) {
                                            $cd = PageCategoryDescription::select('pc.id')
                                                ->join('page_categories as pc', 'pc.id', '=', 'page_category_description.category_id')
                                                ->where('pc.parent_id', (isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0))
                                                ->where('page_category_description.name', trim($parent_name))
                                                ->where('page_category_description.lang', $lang)
                                                ->value('pc.id');

                                            if (!is_null($cd)) {
                                                $parents_id[$key] = (int)$cd;
                                            } elseif (!empty($export['categories_add'])) {
                                                $category = new PageCategories;
                                                $category->slug = str_slug($parent_name);
                                                $category->layout_id = isset($export['category_layout_id']) ? $export['category_layout_id'] : 8;
                                                $category->image = '';
                                                $category->sort = 0;
                                                $category->parent_id = isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0;
                                                $category->status = 1;

                                                $category->save();
                                                $parents_id[$key] = $category->id;

                                                $cd = new PageCategoryDescription;
                                                $cd->lang = $lang;
                                                $cd->category_id = $category->id;
                                                $cd->name = $parent_name;
                                                $cd->meta_title = $parent_name;
                                                $cd->meta_description = '';
                                                $cd->meta_keywords = '';
                                                $cd->description = '';

                                                $cd->save();
                                            }
                                        }

                                        if (count($parents_id) == count($parent_categories)) {
                                            $category_ids[] = $parents_id[count($parents_id) - 1];
                                        }
                                    } else {
                                        $category_id = (int)PageCategoryDescription::select('pc.id')
                                            ->join('page_categories as pc', 'pc.id', '=', 'page_category_description.category_id')
                                            ->where('page_category_description.name', trim($parent_categories))
                                            ->where('page_category_description.lang', $lang)
                                            ->value('pc.id');

                                        if (!$category_id && !empty($export['categories_add'])) {
                                            $category = new PageCategories;
                                            $category->slug = str_slug($parent_categories);
                                            $category->layout_id = isset($export['category_layout_id']) ? $export['category_layout_id'] : 8;
                                            $category->image = '';
                                            $category->sort = 0;
                                            $category->parent_id = isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0;
                                            $category->status = 1;

                                            $category->save();
                                            $category_id = $category->id;

                                            $cd = new PageCategoryDescription;
                                            $cd->lang = $lang;
                                            $cd->category_id = $category->id;
                                            $cd->name = $parent_categories;
                                            $cd->meta_title = $parent_categories;
                                            $cd->meta_description = '';
                                            $cd->meta_keywords = '';
                                            $cd->description = '';

                                            $cd->save();
                                        }

                                        if ($category_id) {
                                            $category_ids[] = $category_id;
                                        }
                                    }

                                    if ($category_ids && empty($field['parent_id'])) {
                                        Pages::where('id', $id)->update(['parent_id' => $category_ids[count($category_ids) - 1]]);
                                    }
                                }

                                if (!empty($field['attributes'])) {
                                    $attributes = @explode("\n", $field['attributes']);

                                    if (isset($attributes[1])) {
                                        foreach ($attributes as $attribute) {
                                            $attribute_text = @explode($delimiter, $attribute);

                                            if (isset($attribute_text[1])) {
                                                $attribute_name = trim($attribute_text[0]);
                                                $attribute_value = trim($attribute_text[1]);

                                                $attribute_id = AttributeDescription::select('attribute_id')
                                                    ->where('name', $attribute_name)
                                                    ->where('lang', $lang)
                                                    ->value('attribute_id');

                                                if (!is_null($attribute_id)) {
                                                    $pa = new PageAttribute;
                                                    $pa->lang = $lang;
                                                    $pa->page_id = $id;
                                                    $pa->attribute_id = $attribute_id;
                                                    $pa->text = $attribute_value;

                                                    $pa->save();

                                                    if (!empty($attribute_text[2])) {
                                                        $attribute_images = @explode(',', $attribute_text[2]);

                                                        if (isset($attribute_images[0])) {
                                                            foreach ($attribute_images as $attribute_image) {
                                                                PageAttributeImage::insert(['attribute_id' => $attribute_id, 'page_id' => $id, 'image' => trim($attribute_image), 'updated_at' => $now, 'created_at' => $now]);
                                                            }
                                                        } else {
                                                            PageAttributeImage::insert(['attribute_id' => $attribute_id, 'page_id' => $id, 'image' => trim($attribute_text[2]), 'updated_at' => $now, 'created_at' => $now]);
                                                        }
                                                    }
                                                } else if (!empty($export['attributes_add'])) {
                                                    $attribute = new Attributes;
                                                    $attribute->image = '';
                                                    $attribute->sort = 0;
                                                    $attribute->position_left = 0;
                                                    $attribute->position_right = 0;
                                                    $attribute->status = 1;

                                                    $attribute->save();

                                                    if (!empty($attribute_text[2])) {
                                                        $attribute_images = @explode(',', $attribute_text[2]);

                                                        if (isset($attribute_images[0])) {
                                                            foreach ($attribute_images as $attribute_image) {
                                                                PageAttributeImage::insert(['attribute_id' => $attribute->id, 'page_id' => $id, 'image' => trim($attribute_image), 'updated_at' => $now, 'created_at' => $now]);
                                                            }
                                                        } else {
                                                            PageAttributeImage::insert(['attribute_id' => $attribute->id, 'page_id' => $id, 'image' => trim($attribute_text[2]), 'updated_at' => $now, 'created_at' => $now]);
                                                        }
                                                    }

                                                    $ad = new AttributeDescription;
                                                    $ad->lang = $lang;
                                                    $ad->attribute_id = $attribute->id;
                                                    $ad->name = $attribute_name;

                                                    $ad->save();

                                                    $pa = new PageAttribute;
                                                    $pa->lang = $lang;
                                                    $pa->page_id = $id;
                                                    $pa->attribute_id = $attribute->id;
                                                    $pa->text = $attribute_value;

                                                    $pa->save();
                                                }
                                            }
                                        }
                                    } else {
                                        $attribute_text = @explode($delimiter, $field['attributes']);

                                        if (isset($attribute_text[1])) {
                                            $attribute_name = trim($attribute_text[0]);
                                            $attribute_value = trim($attribute_text[1]);

                                            $attribute_id = AttributeDescription::select('attribute_id')
                                                ->where('name', $attribute_name)
                                                ->where('lang', $lang)
                                                ->value('attribute_id');

                                            if (!is_null($attribute_id)) {
                                                $pa = new PageAttribute;
                                                $pa->lang = $lang;
                                                $pa->page_id = $id;
                                                $pa->attribute_id = $attribute_id;
                                                $pa->text = $attribute_value;

                                                $pa->save();

                                                if (!empty($attribute_text[2])) {
                                                    $attribute_images = @explode(',', $attribute_text[2]);

                                                    if (isset($attribute_images[0])) {
                                                        foreach ($attribute_images as $attribute_image) {
                                                            PageAttributeImage::insert(['attribute_id' => $attribute_id, 'page_id' => $id, 'image' => trim($attribute_image), 'updated_at' => $now, 'created_at' => $now]);
                                                        }
                                                    } else {
                                                        PageAttributeImage::insert(['attribute_id' => $attribute_id, 'page_id' => $id, 'image' => trim($attribute_text[2]), 'updated_at' => $now, 'created_at' => $now]);
                                                    }
                                                }
                                            } else if (!empty($export['attributes_add'])) {
                                                $attribute = new Attributes;
                                                $attribute->image = '';
                                                $attribute->sort = 0;
                                                $attribute->position_left = 0;
                                                $attribute->position_right = 0;
                                                $attribute->status = 1;

                                                $attribute->save();

                                                if (!empty($attribute_text[2])) {
                                                    $attribute_images = @explode(',', $attribute_text[2]);

                                                    if (isset($attribute_images[0])) {
                                                        foreach ($attribute_images as $attribute_image) {
                                                            PageAttributeImage::insert(['attribute_id' => $attribute->id, 'page_id' => $id, 'image' => trim($attribute_image), 'updated_at' => $now, 'created_at' => $now]);
                                                        }
                                                    } else {
                                                        PageAttributeImage::insert(['attribute_id' => $attribute->id, 'page_id' => $id, 'image' => trim($attribute_text[2]), 'updated_at' => $now, 'created_at' => $now]);
                                                    }
                                                }

                                                $ad = new AttributeDescription;
                                                $ad->lang = $lang;
                                                $ad->attribute_id = $attribute->id;
                                                $ad->name = $attribute_name;

                                                $ad->save();

                                                $pa = new PageAttribute;
                                                $pa->lang = $lang;
                                                $pa->page_id = $id;
                                                $pa->attribute_id = $attribute->id;
                                                $pa->text = $attribute_value;

                                                $pa->save();
                                            }
                                        }
                                    }
                                }

                                $success_row++;
                            }
                            elseif ($mode == 3) {
                                $page = [];

                                if (!empty($field['slug'])) $page['slug'] = $field['slug'];
                                if (!empty($field['layout_id'])) $page['layout_id'] = $field['layout_id'];
                                if (isset($field['top'])) $page['top'] = $field['top'];
                                if (isset($field['bottom'])) $page['bottom'] = $field['bottom'];
                                if (isset($field['image'])) {
                                    if (filter_var($field['image'], FILTER_VALIDATE_URL)) {
                                        $field['image'] = $this->download(public_path() . '/../images/pages/', $field['image'], 'images/pages/');
                                    }

                                    $page['image'] = $field['image'];
                                }

                                if (isset($field['sort'])) $page['sort'] = $field['sort'];
                                if (isset($field['status'])) $page['status'] = $field['status'];
                                if (!empty($field['parent_id'])) $page['parent_id'] = $field['parent_id'];
                                if (isset($field['status'])) $page['status'] = $field['status'];

                                $query = Pages::where('id', $id)->update($page);

                                if (!$query) {
                                    if (count($page) >= 2 && isset($field['meta']['name']) && isset($field['meta']['meta_title'])) {
                                        if (!$import_id) {
                                            $page['id'] = Pages::insert($page)->id;
                                        } else {
                                            $page['id'] = $id;
                                            Pages::insert($page);
                                        }

                                        $success_row++;
                                    } else {
                                        $errors[] = 'В строке №' . $key . ' ошибки:<br>&nbsp;&nbsp;&nbsp; - Минимальный набор полей при создании товара: ID, Seo Url, Name, Meta Title';
                                        continue;
                                    }
                                } else {
                                    $success_update_row++;
                                }

                                if (isset($field['images'])) {
                                    if ($query) {
                                        PageImage::where('page_id', $page['id'])->delete();
                                    }

                                    $field['images'] = @explode($delimiter, $field['images']);
                                    $images = [];

                                    if (isset($field['images'][0])) {
                                        foreach ($field['images'] as $image) {
                                            if (filter_var($image, FILTER_VALIDATE_URL)) {
                                                $image = $this->download(public_path() . '/../images/pages/', $image, 'images/pages/');
                                            }

                                            $images[] = ['page_id' => $page['id'], 'image' => $image, 'updated_at' => $now, 'created_at' => $now];
                                        }
                                    } else {
                                        if (filter_var($field['images'], FILTER_VALIDATE_URL)) {
                                            $field['images'] = $this->download(public_path() . '/../images/pages/', $field['images'], 'images/pages/');
                                        }

                                        $images[] = ['page_id' => $page['id'], 'image' => $field['images'], 'updated_at' => $now, 'created_at' => $now];
                                    }

                                    if ($images) PageImage::insert($images);
                                }

                                if (isset($field['category_id'])) {
                                    $field['parent_id'] = $field['category_id'];
                                }

                                if (!empty($field['category_name'])) {
                                    $category_ids = [];

                                    $parent_categories = @explode($delimiter, $field['category_name']);

                                    if (isset($parent_categories[0])) {
                                        $parents_id = [];

                                        foreach ($parent_categories as $key => $parent_name) {
                                            $cd = PageCategoryDescription::select('pc.id')
                                                ->join('page_categories as pc', 'pc.id', '=', 'page_category_description.category_id')
                                                ->where('pc.parent_id', (isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0))
                                                ->where('page_category_description.name', trim($parent_name))
                                                ->where('page_category_description.lang', $lang)
                                                ->value('pc.id');

                                            if (!is_null($cd)) {
                                                $parents_id[$key] = (int)$cd;
                                            } elseif (!empty($export['categories_add'])) {
                                                $category = new PageCategories;
                                                $category->slug = str_slug($parent_name);
                                                $category->layout_id = isset($export['category_layout_id']) ? $export['category_layout_id'] : 8;
                                                $category->image = '';
                                                $category->sort = 0;
                                                $category->parent_id = isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0;
                                                $category->status = 1;

                                                $category->save();
                                                $parents_id[$key] = $category->id;

                                                $cd = new PageCategoryDescription;
                                                $cd->lang = $lang;
                                                $cd->category_id = $category->id;
                                                $cd->name = $parent_name;
                                                $cd->meta_title = $parent_name;
                                                $cd->meta_description = '';
                                                $cd->meta_keywords = '';
                                                $cd->description = '';

                                                $cd->save();
                                            }
                                        }

                                        if (count($parents_id) == count($parent_categories)) {
                                            $category_ids[] = $parents_id[count($parents_id) - 1];
                                        }
                                    } else {
                                        $category_id = (int)PageCategoryDescription::select('pc.id')
                                            ->join('page_categories as pc', 'pc.id', '=', 'page_category_description.category_id')
                                            ->where('page_category_description.name', trim($parent_categories))
                                            ->where('page_category_description.lang', $lang)
                                            ->value('pc.id');

                                        if (!$category_id && !empty($export['categories_add'])) {
                                            $category = new PageCategories;
                                            $category->slug = str_slug($parent_categories);
                                            $category->layout_id = isset($export['category_layout_id']) ? $export['category_layout_id'] : 8;
                                            $category->image = '';
                                            $category->sort = 0;
                                            $category->parent_id = isset($parents_id[$key - 1]) ? $parents_id[$key - 1] : 0;
                                            $category->status = 1;

                                            $category->save();
                                            $category_id = $category->id;

                                            $cd = new PageCategoryDescription;
                                            $cd->lang = $lang;
                                            $cd->category_id = $category->id;
                                            $cd->name = $parent_categories;
                                            $cd->meta_title = $parent_categories;
                                            $cd->meta_description = '';
                                            $cd->meta_keywords = '';
                                            $cd->description = '';

                                            $cd->save();
                                        }

                                        if ($category_id) {
                                            $category_ids[] = $category_id;
                                        }
                                    }

                                    if ($category_ids && empty($field['parent_id'])) {
                                        Pages::where('id', $id)->update(['parent_id' => $category_ids[count($category_ids) - 1]]);
                                    }
                                }

                                if (isset($field['meta'])) {
                                    $meta = $field['meta'];

                                    $pd['lang'] = $lang;
                                    if (!empty($meta['name'])) $pd['name'] = $meta['name'];
                                    if (!empty($meta['meta_title'])) $pd['meta_title'] = $meta['meta_title'];
                                    if (!empty($meta['meta_description'])) $pd['meta_description'] = $meta['meta_description'];
                                    if (!empty($meta['meta_keywords'])) $pd['meta_keywords'] = $meta['meta_keywords'];
                                    if (!empty($meta['description'])) $pd['description'] = $meta['description'];

                                    $query = PageDescription::where('page_id', $id)->where('lang', $lang)->update($pd);

                                    if (!$query) {
                                        $pd->page_id = $page['id'];

                                        if (isset($pd['name']) && isset($pd['meta_title'])) {
                                            PageDescription::insert($pd);
                                        } else {
                                            $errors[] = 'В строке №' . $key . ' ошибки:<br>&nbsp;&nbsp;&nbsp; - Минимальный набор полей: Наименование, Meta Title';
                                            continue;
                                        }
                                    }
                                }

                                if (!empty($field['attributes'])) {
                                    $attributes = @explode("\n", $field['attributes']);

                                    if (isset($attributes[1])) {
                                        foreach ($attributes as $attribute) {
                                            $attribute_text = @explode($delimiter, $attribute);

                                            if (isset($attribute_text[1])) {
                                                $attribute_name = trim($attribute_text[0]);
                                                $attribute_value = trim($attribute_text[1]);

                                                $attribute_id = AttributeDescription::select('attribute_id')
                                                    ->where('name', $attribute_name)
                                                    ->where('lang', $lang)
                                                    ->value('attribute_id');

                                                if (!is_null($attribute_id)) {
                                                    PageAttributeImage::where('page_id', $page['id'])->delete();

                                                    PageAttribute::where('attribute_id', $attribute_id)
                                                        ->where('page_id', $page['id'])
                                                        ->where('lang', $lang)
                                                        ->update(['text' => $attribute_value]);

                                                    if (!empty($attribute_text[2])) {
                                                        $attribute_images = @explode(',', $attribute_text[2]);

                                                        if (isset($attribute_images[0])) {
                                                            foreach ($attribute_images as $attribute_image) {
                                                                PageAttributeImage::insert(['attribute_id' => $attribute_id, 'page_id' => $id, 'image' => trim($attribute_image), 'updated_at' => $now, 'created_at' => $now]);
                                                            }
                                                        } else {
                                                            PageAttributeImage::insert(['attribute_id' => $attribute_id, 'page_id' => $id, 'image' => trim($attribute_text[2]), 'updated_at' => $now, 'created_at' => $now]);
                                                        }
                                                    }
                                                } else if (!empty($export['attributes_add'])) {
                                                    $attribute = new Attributes;
                                                    $attribute->image = '';
                                                    $attribute->sort = 0;
                                                    $attribute->position_left = 0;
                                                    $attribute->position_right = 0;
                                                    $attribute->status = 1;

                                                    $attribute->save();

                                                    $ad = new AttributeDescription;
                                                    $ad->lang = $lang;
                                                    $ad->attribute_id = $attribute->id;
                                                    $ad->name = $attribute_name;

                                                    $ad->save();

                                                    $pa = new PageAttribute;
                                                    $pa->lang = $lang;
                                                    $pa->page_id = $page['id'];
                                                    $pa->attribute_id = $attribute->id;
                                                    $pa->text = $attribute_value;

                                                    $pa->save();

                                                    if (!empty($attribute_text[2])) {
                                                        $attribute_images = @explode(',', $attribute_text[2]);

                                                        if (isset($attribute_images[0])) {
                                                            foreach ($attribute_images as $attribute_image) {
                                                                PageAttributeImage::insert(['attribute_id' => $attribute->id, 'page_id' => $id, 'image' => trim($attribute_image), 'updated_at' => $now, 'created_at' => $now]);
                                                            }
                                                        } else {
                                                            PageAttributeImage::insert(['attribute_id' => $attribute->id, 'page_id' => $id, 'image' => trim($attribute_text[2]), 'updated_at' => $now, 'created_at' => $now]);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        $attribute_text = @explode($delimiter, $field['attributes']);

                                        if (isset($attribute_text[1])) {
                                            $attribute_name = trim($attribute_text[0]);
                                            $attribute_value = trim($attribute_text[1]);

                                            $attribute_id = AttributeDescription::select('attribute_id')
                                                ->where('name', $attribute_name)
                                                ->where('lang', $lang)
                                                ->value('attribute_id');

                                            if (!is_null($attribute_id)) {
                                                PageAttributeImage::where('page_id', $page['id'])->delete();

                                                PageAttribute::where('attribute_id', $attribute_id)
                                                    ->where('page_id', $page['id'])
                                                    ->where('lang', $lang)
                                                    ->update(['text' => $attribute_value]);

                                                if (!empty($attribute_text[2])) {
                                                    $attribute_images = @explode(',', $attribute_text[2]);

                                                    if (isset($attribute_images[0])) {
                                                        foreach ($attribute_images as $attribute_image) {
                                                            PageAttributeImage::insert(['attribute_id' => $attribute_id, 'page_id' => $id, 'image' => trim($attribute_image), 'updated_at' => $now, 'created_at' => $now]);
                                                        }
                                                    } else {
                                                        PageAttributeImage::insert(['attribute_id' => $attribute_id, 'page_id' => $id, 'image' => trim($attribute_text[2]), 'updated_at' => $now, 'created_at' => $now]);
                                                    }
                                                }
                                            } else if (!empty($export['attributes_add'])) {
                                                $attribute = new Attributes;
                                                $attribute->image = '';
                                                $attribute->sort = 0;
                                                $attribute->position_left = 0;
                                                $attribute->position_right = 0;
                                                $attribute->status = 1;

                                                $attribute->save();

                                                $ad = new AttributeDescription;
                                                $ad->lang = $lang;
                                                $ad->attribute_id = $attribute->id;
                                                $ad->name = $attribute_name;

                                                $ad->save();

                                                $pa = new PageAttribute;
                                                $pa->lang = $lang;
                                                $pa->page_id = $page['id'];
                                                $pa->attribute_id = $attribute->id;
                                                $pa->text = $attribute_value;

                                                $pa->save();

                                                if (!empty($attribute_text[2])) {
                                                    $attribute_images = @explode(',', $attribute_text[2]);

                                                    if (isset($attribute_images[0])) {
                                                        foreach ($attribute_images as $attribute_image) {
                                                            PageAttributeImage::insert(['attribute_id' => $attribute->id, 'page_id' => $id, 'image' => trim($attribute_image), 'updated_at' => $now, 'created_at' => $now]);
                                                        }
                                                    } else {
                                                        PageAttributeImage::insert(['attribute_id' => $attribute->id, 'page_id' => $id, 'image' => trim($attribute_text[2]), 'updated_at' => $now, 'created_at' => $now]);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            elseif ($mode == 4) {
                                $query = Pages::find($id)->delete();

                                if ($query) {
                                    PageDescription::where('page_id', $id)->delete();
                                    PageAttribute::where('page_id', $id)->delete();
                                    PageImage::where('page_id', $id)->delete();
                                    PageAttributeImage::where('page_id', $id)->delete();
                                    $success_row++;
                                }
                            }
                        }
                    }
                    else {
                        $errors[] = 'Неверный тип данных';
                    }
                } else {
                    return redirect('admin/export_import')->with('error', 'В файле недопустимое название колонки');
                }

                if ($request->field == 'category' || $request->field == 'category_page') {
                    $row = num_decline($success_row, ['категория', 'категории', 'категорий']);

                    if ($mode == 1) {
                        $success = num_decline($success_row, ['Обновлена', 'Обновлено'], false) . ' ' . $row;
                    } elseif ($mode == 2) {
                        $success = num_decline($success_row, ['Добавлена', 'Добавлено'], false) . ' ' . $row;
                    } elseif ($mode == 3) {
                        $success = num_decline($success_row, ['Обновлена', 'Обновлено'], false) . ' ' . num_decline($success_update_row, ['категория', 'категории', 'категорий']) . ', ' . num_decline($success_row, ['Добавлена', 'Добавлено'], false) . $row;
                    } elseif ($mode == 4) {
                        $success = num_decline($success_row, ['Удалена', 'Удалено'], false) . ' ' . $row;
                    }
                }
                elseif ($request->field == 'product') {
                    $row = num_decline($success_row, ['товар', 'товара', 'товаров']);

                    if ($mode == 1) {
                        $success = num_decline($success_row, ['Обновлен', 'Обновлено'], false) . ' ' . $row;
                    } elseif ($mode == 2) {
                        $success = num_decline($success_row, ['Добавлен', 'Добавлено'], false) . ' ' . $row;
                    } elseif ($mode == 3) {
                        $success = num_decline($success_row, ['Обновлен', 'Обновлено'], false) . ' '  . num_decline($success_update_row, ['товар', 'товара', 'товаров']) . ', ' . num_decline($success_row, ['Добавлен', 'Добавлено'], false) . $row;
                    } elseif ($mode == 4) {
                        $success = num_decline($success_row, ['Удален', 'Удалено'], false) . ' ' . $row;
                    }
                }
                elseif ($request->field == 'page') {
                    $row = num_decline($success_row, ['статья', 'статьи', 'статей']);

                    if ($mode == 1) {
                        $success = num_decline($success_row, ['Обновлена', 'Обновлено'], false) . ' ' . $row;
                    } elseif ($mode == 2) {
                        $success = num_decline($success_row, ['Добавлена', 'Добавлено'], false) . ' ' . $row;
                    } elseif ($mode == 3) {
                        $success = num_decline($success_row, ['Обновлена', 'Обновлено'], false) . ' '  . num_decline($success_update_row, ['статья', 'статьи', 'статей']) . ', ' . num_decline($success_row, ['Добавлена', 'Добавлено'], false) . $row;
                    } elseif ($mode == 4) {
                        $success = num_decline($success_row, ['Удалена', 'Удалено'], false) . ' ' . $row;
                    }
                }

                $routes = new PathRouteService();
                Cache::put('seo_url', $routes->getRoutes());

                if ($errors) {
                    return redirect('admin/export_import')->withErrors(implode('<br>', $errors));
                } else {
                    return redirect('admin/export_import')->with('success', $success);
                }
            } catch (Exception | RuntimeException $e) {
                return redirect('admin/export_import')->with('error', $e->getMessage());
            }
        } else {
            return redirect('admin/export_import')->with('error', 'Неверный тип данных');
        }
    }

    public function download($href, $path, $path2){
        $name = str_replace(' ', '_', basename($href));
        $p = $path . '/' . str_replace(' ', '_', $name);

        if (!file_exists($p)) {
            $curl = curl_init($href);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_BINARYTRANSFER,1);
            $content = curl_exec($curl);
            $resultCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($resultCode === 200) {
                $fp = fopen($p,'x');
                fwrite($fp, $content);
                fclose($fp);
                return $path2 . $name;
            } else {
                return '';
            }
        } else {
            return $path2 . $name;
        }
    }

    private function getCategory($collection, $collections, array $name = [])
    {
        $n = !is_null($collection->metaLang) ? $collection->metaLang->name : '';
        array_unshift($name, $n);

        if (!is_null($collection->parent_id) && isset($collections[$collection->parent_id])) {
            $name = $this->getCategory($collections[$collection->parent_id], $collections, $name);
        }

        return $name;
    }

    protected function children($children, &$categories, $key) {
        $categories[] = $children[$key];

        if (!empty($children['category_name']) && $key == 'name') {
            $this->children($children['category_name'], $categories, $key);
        } elseif (!empty($children['category_name']) && $key == 'id') {
            $this->children($children['category_name'], $categories, $key);
        }
    }
}