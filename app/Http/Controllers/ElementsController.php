<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Elements;
	
	class ElementsController extends Controller {
		private $elements = [];
		
		public function __construct() {
			$this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
			
			$classes = array('breadcrumb', 'breadcrumb-item');
			$this->breadcrumbs->addCssClasses($classes);
			$this->breadcrumbs->setDivider('');
			
			$this->breadcrumbs->addCrumb(__('locale.home'), url('admin'));
			
			
			$this->params_array = request()->query();
			$params = [];
			
			if (!empty($this->params_array)) {
				foreach ($this->params_array as $key => $param) {
					$params[] = $key . '=' . $param;
				}
			}
			
			$this->params = !empty($this->params) ? '?' . implode('&', $params) : '';
			
			$float = [
				'placeholder' => 'Обтекание',
				'select' => [
					0 => [
						'value' => 'left',
						'text' => 'По левому краю'
					],
					1 => [
						'value' => 'right',
						'text' => 'По правому краю'
					]
				]
			];
			
			$overflow = [
				'placeholder' => 'Отображением содержания блочного элемента',
				'select' => [
					0 => [
						'value' => 'visible',
						'text' => 'Отображается все содержание элемента'
					],
					1 => [
						'value' => 'hidden',
						'text' => 'Отображается только область внутри элемента'
					],
					2 => [
						'value' => 'scroll',
						'text' => 'Всегда добавляются полосы прокрутки'
					],
					3 => [
						'value' => 'auto',
						'text' => 'Полосы прокрутки добавляются только при необходимости'
					]
				]
			];
			
			$font = [
				'cursor' => [
					'placeholder' => 'Курсор',
					'select' => [
						0 => [
							'value' => 'pointer',
							'text' => 'pointer'
						],
						1 => [
							'value' => 'default',
							'text' => 'default'
						],
						2 => [
							'value' => 'help',
							'text' => 'help'
						],
						3 => [
							'value' => 'move',
							'text' => 'move'
						],
						4 => [
							'value' => 'text',
							'text' => 'text'
						],
						5 => [
							'value' => 'wait',
							'text' => 'wait'
						]
					]
				],
				'font-size' => [
					'placeholder' => 'Размер шрифта (px,%)'
				],
				'font-weight' => [
					'placeholder' => 'Жирность шрифта (Варианты: 100, 300, 400, 600, 700, bold)'
				],
				'letter-spacing' => [
					'placeholder' => 'Расстояние между буквами (px,%)'
				],
				'line-height' => [
					'placeholder' => 'Линейная высота (px,%)'
				],
				'text-decoration' => [
					'placeholder' => 'Декоративные линии текста',
					'select' => [
						0 => [
							'value' => 'blink',
							'text' => 'Мигающий текст'
						],
						1 => [
							'value' => 'line-through',
							'text' => 'Перечеркнутый текст'
						],
						2 => [
							'value' => 'overline',
							'text' => 'Линия сверху'
						],
						3 => [
							'value' => 'underline',
							'text' => 'Подчеркнутый текст'
						],
						4 => [
							'value' => 'none',
							'text' => 'Без форматирования'
						]
					]
				],
				'text-align' => [
					'placeholder' => 'Выравнивание текста по горизонтали',
					'select' => [
						0 => [
							'value' => 'left',
							'text' => 'Слева'
						],
						1 => [
							'value' => 'right',
							'text' => 'Справа'
						],
						2 => [
							'value' => 'center',
							'text' => 'По центру'
						],
						3 => [
							'value' => 'justify',
							'text' => 'По ширине'
						]
					]
				],
				'vertical-align' => [
					'placeholder' => 'Выравнивание текста по вертикали',
					'select' => [
						0 => [
							'value' => 'baseline',
							'text' => 'Поверху'
						],
						1 => [
							'value' => 'bottom',
							'text' => 'Понизу'
						],
						2 => [
							'value' => 'middle',
							'text' => 'Посередине'
						]
					]
				],
				'word-break' => [
					'placeholder' => 'Перевод на новую строку',
					'select' => [
						0 => [
							'value' => 'break-all',
							'text' => 'break-all;'
						],
						1 => [
							'value' => 'break-word',
							'text' => 'break-word'
						]
					]
				],
				'color' => [
					'placeholder' => 'Цвет текста'
				]
			];
			
			$margin = [
				'placeholder' => 'Внешний отступ (px,%,auto)',
				'text' => [
					'top',
					'bottom',
					'left',
					'right',
					'auto'
				]
			];
			
			$padding = [
				'placeholder' => 'Внутренный отступ (px,%)',
				'text' => [
					'top',
					'bottom',
					'left',
					'right'
				]
			];
			
			$positionX = [
				'top' => [
					'placeholder' => 'Позиция элемента сверху (px,%)',
				],
				'bottom' => [
					'placeholder' => 'Позиция элемента снизу (px,%)',
				],
				'left' => [
					'placeholder' => 'Позиция элемента слева (px,%)',
				],
				'right' => [
					'placeholder' => 'Позиция элемента справа (px,%)',
				]
			];
			
			$flex_align = [
				'placeholder' => 'Выравнивание блоков по вертикали (для FLEX)',
				'select' => [
					0 => [
						'value' => 'baseline',
						'text' => 'Поверху'
					],
					1 => [
						'value' => 'center',
						'text' => 'Посередине'
					],
					2 => [
						'value' => 'end',
						'text' => 'Понизу'
					],
					3 => [
						'value' => 'flex-start',
						'text' => 'В начале'
					]
				]
			];
			
			$flex_justify = [
				'placeholder' => 'Выравнивание блоков по горизонтали (для FLEX)',
				'select' => [
					0 => [
						'value' => 'flex-start',
						'text' => 'Слева'
					],
					1 => [
						'value' => 'center',
						'text' => 'Посередине'
					],
					2 => [
						'value' => 'flex-end',
						'text' => 'Справа'
					],
					3 => [
						'value' => 'space-between',
						'text' => 'По ширине'
					],
					4 => [
						'value' => 'space-around',
						'text' => 'По ширине с оступами по краям'
					]
				]
			];
			
			$flex_wrap = [
				'placeholder' => 'Перенос блоков (для FLEX)',
				'select' => [
					0 => [
						'value' => 'wrap',
						'text' => 'Перенос на новую строку'
					],
					1 => [
						'value' => 'wrap-reverse',
						'text' => 'Реверсивно'
					],
					2 => [
						'value' => 'nowrap',
						'text' => 'Без переноса'
					]
				]
			];
			
			$flex_direction = [
				'placeholder' => 'Расположение блоков по оси (для FLEX)',
				'select' => [
					0 => [
						'value' => 'row',
						'text' => 'В 1 строку'
					],
					1 => [
						'value' => 'row-reverse',
						'text' => 'Реверсивно в 1 строку'
					],
					2 => [
						'value' => 'column-reverse',
						'text' => 'Вертикально'
					],
					3 => [
						'value' => 'column',
						'text' => 'Вертикально-реверс'
					]
				]
			];
			
			$position = [
				'placeholder' => 'Позиция',
				'select' => [
					0 => [
						'value' => 'absolute',
						'text' => 'Абсолютная'
					],
					1 => [
						'value' => 'relative',
						'text' => 'Относительная'
					],
					2 => [
						'value' => 'fixed',
						'text' => 'Фиксированная'
					],
					3 => [
						'value' => 'static',
						'text' => 'Статичная'
					]
				]
			];
			
			$width = [
				'placeholder' => 'Ширина (px,%,auto)'
			];
			
			$height = [
				'placeholder' => 'Высота (px,%,auto)'
			];
			
			$opacity = [
				'placeholder' => 'Прозрачность (0.1, 0.2, 0.3 ... 1)'
			];
			
			$transition = [
				'placeholder' => 'Анимация элемента (0.1s, 0.2s, 0.3s ... 3s)'
			];
			
			$fit = [
				'placeholder' => 'Картинка как фон',
				'select' => [
					1 => [
						'value' => 'cover',
						'text' => 'Масштаб с сохранением пропорций'
					],
					2 => [
						'value' => 'contain',
						'text' => 'Масштаб по ширине блока'
					]
				]
			];
			
			$align = [
				'placeholder' => 'Выравнивание',
				'select' => [
					0 => [
						'value' => 'baseline',
						'text' => 'Поверху'
					],
					1 => [
						'value' => 'bottom',
						'text' => 'Понизу'
					],
					2 => [
						'value' => 'middle',
						'text' => 'Посередине'
					]
				]
			];
			
			$display = [
				'placeholder' => 'Вариант отображения',
				'select' => [
					0 => [
						'value' => 'block',
						'text' => 'Блочный'
					],
					1 => [
						'value' => 'inline-block',
						'text' => 'Блочно-строчный'
					],
					2 => [
						'value' => 'flex',
						'text' => 'Флексы'
					],
					3 => [
						'value' => 'none',
						'text' => 'Скрытый'
					]
				]
			];
			
			$list = [
				'placeholder' => 'Стили маркера',
				'select' => [
					0 => [
						'value' => 'square',
						'text' => 'Квадрат'
					],
					1 => [
						'value' => 'disc',
						'text' => 'Круг'
					],
					2 => [
						'value' => 'none',
						'text' => 'Без маркеров'
					]
				]
			];
			
			$border = [
				'placeholder' => 'Бордер (px,%)',
				'text' => [
					'radius',
					'width',
					'color'
				]
			];
			
			$box_shadow = [
				'placeholder' => 'Тень ("inset" "сдвиг по вертикали" "сдвиг по горизонтали" "радиус размытия" "растяжение" "цвет") или (none - если нужно убрать)'
			];
			
			$background = [
				'placeholder' => 'Выберите фоновый цвет'
			];
			
			$background_image = [
				'placeholder' => 'Выберите фоновую картинку'
			];
			
			$background_position = [
				'placeholder' => 'Положение фоновой картинки (left center, 20px 50%, right top)'
			];
			
			$background_size = [
				'placeholder' => 'Масштаб фонового изображения (cover, contain, revert, 100%, 100px)'
			];
			
			$background_repeat = [
				'placeholder' => 'Повторение фоновой картинки',
				'select' => [
					0 => [
						'value' => 'no-repeat',
						'text' => 'Не повторяется'
					],
					1 => [
						'value' => 'repeat',
						'text' => 'Повторяется'
					],
					2 => [
						'value' => 'repeat-x',
						'text' => 'Повторяется по горизонтали'
					],
					3 => [
						'value' => 'repeat-y',
						'text' => 'Повторяется по вертикали'
					],
					4 => [
						'value' => 'space',
						'text' => 'Повторяется (заполнение элемента)'
					],
					5 => [
						'value' => 'round',
						'text' => 'Повторяется (заполняется целым числом картинок)'
					]
				]
			];
			
			$h1 = [
				'name' => 'Заголовок H1',
				'params' => [
					'font' => $font,
					'margin' => $margin,
					'padding' => $padding
				]
			];
			
			$this->elements = [
				'h1' => $h1,
				'h2' => [
					'name' => 'Заголовок H2',
					'params' => $h1['params'],
				],
				'h3' => [
					'name' => 'Заголовок H3',
					'params' => $h1['params'],
				],
				'h4' => [
					'name' => 'Заголовок H4',
					'params' => $h1['params'],
				],
				'h5' => [
					'name' => 'Заголовок H5',
					'params' => $h1['params'],
				],
				'h6' => [
					'name' => 'Заголовок H6',
					'params' => $h1['params'],
				],
				'img' => [
					'name' => 'Картинка',
					'params' => [
						'width' => $width,
						'height' => $height,
						'position' => $position,
						$positionX,
						'margin' => $margin,
						'float' => $float,
						'fit' => $fit,
						'vertical-align' => $align,
						'display' => $display,
						'overflow' => $overflow,
						'opacity' => $opacity,
						'transition' => $transition,
						'box-shadow' => $box_shadow
					]
				],
				'p' => [
					'name' => 'Абзац',
					'params' => [
						'font' => $font,
						'margin' => $margin,
						'padding' => $padding,
						'float' => $float,
					]
				],
				'b' => [
					'name' => 'Выделенный текст',
					'params' => [
						'font' => $font,
					]
				],
				'span' => [
					'name' => 'Простой текст',
					'params' => [
						'width' => $width,
						'height' => $height,
						'font' => $font,
						'margin' => $margin,
						'padding' => $padding,
						'position' => $position,
						$positionX,
						'float' => $float,
						'display' => $display,
						'overflow' => $overflow,
						'border' => $border,
						'align-items' => $flex_align,
						'justify-content' => $flex_justify,
						'flex-wrap' => $flex_wrap,
						'flex-direction' => $flex_direction,
						'background' => $background,
						'background-image' => $background_image,
						'background-position' => $background_position,
						'background-size' => $background_size,
						'background-repeat' => $background_repeat,
						'opacity' => $opacity,
						'transition' => $transition,
						'box-shadow' => $box_shadow
					]
				],
				'ul' => [
					'name' => 'Маркированный список',
					'params' => [
						'width' => $width,
						'height' => $height,
						'margin' => $margin,
						'padding' => $padding,
						'list-style' => $list,
						'display' => $display,
						'overflow' => $overflow,
						'flex-wrap' => $flex_wrap,
						'flex-direction' => $flex_direction,
						'align-items' => $flex_align,
						'justify-content' => $flex_justify,
						'box-shadow' => $box_shadow,
					]
				],
				'ol' => [
					'name' => 'Нумированный список',
					'params' => [
						'width' => $width,
						'height' => $height,
						'margin' => $margin,
						'padding' => $padding,
						'display' => $display,
						'overflow' => $overflow,
						'flex-wrap' => $flex_wrap,
						'flex-direction' => $flex_direction,
						'align-items' => $flex_align,
						'justify-content' => $flex_justify,
						'box-shadow' => $box_shadow
					]
				],
				'li' => [
					'name' => 'Тег списка',
					'params' => [
						'font' => $font,
						'border' => $border,
						'margin' => $margin,
						'padding' => $padding,
						'position' => $position,
						$positionX,
						'float' => $float,
						'display' => $display,
						'flex-wrap' => $flex_wrap,
						'flex-direction' => $flex_direction,
						'align-items' => $flex_align,
						'justify-content' => $flex_justify,
						'overflow' => $overflow,
						'opacity' => $opacity,
						'transition' => $transition,
						'box-shadow' => $box_shadow
					]
				],
				'quote' => [
					'name' => 'Цитата',
					'params' => [
						'width' => $width,
						'height' => $height,
						'font' => $font,
						'background' => $background,
						'background-image' => $background_image,
						'background-position' => $background_position,
						'background-size' => $background_size,
						'background-repeat' => $background_repeat,
						'border' => $border,
						'margin' => $margin,
						'padding' => $padding,
						'position' => $position,
						$positionX,
						'float' => $float,
						'display' => $display,
						'flex-wrap' => $flex_wrap,
						'flex-direction' => $flex_direction,
						'align-items' => $flex_align,
						'justify-content' => $flex_justify,
						'overflow' => $overflow,
						'opacity' => $opacity,
						'transition' => $transition,
						'box-shadow' => $box_shadow
					]
				],
				'a' => [
					'name' => 'Ссылка',
					'params' => [
						'font' => $font,
						'margin' => $margin,
						'padding' => $padding,
						'float' => $float,
						'display' => $display,
						'overflow' => $overflow,
						'background' => $background,
						'background-image' => $background_image,
						'background-position' => $background_position,
						'background-size' => $background_size,
						'background-repeat' => $background_repeat,
						'border' => $border,
						'opacity' => $opacity,
						'transition' => $transition,
						'box-shadow' => $box_shadow,
					]
				],
				'div' => [
					'name' => 'Блок',
					'params' => [
						'width' => $width,
						'height' => $height,
						'font' => $font,
						'margin' => $margin,
						'padding' => $padding,
						'position' => $position,
						$positionX,
						'float' => $float,
						'display' => $display,
						'overflow' => $overflow,
						'border' => $border,
						'align-items' => $flex_align,
						'justify-content' => $flex_justify,
						'flex-wrap' => $flex_wrap,
						'flex-direction' => $flex_direction,
						'background' => $background,
						'background-image' => $background_image,
						'background-position' => $background_position,
						'background-size' => $background_size,
						'background-repeat' => $background_repeat,
						'opacity' => $opacity,
						'transition' => $transition,
						'box-shadow' => $box_shadow
					]
				]
			];
		}
		
		public function index(Request $request) {
			$where = [];
			
			if (!is_null($request->name)) {
				$name = $request->name;
				$where[] = ['name', 'like', '%' . $name . '%'];
			} else {
				$name = '';
			}
			
			if ($request->sort) {
				$sort = $request->sort;
			} else {
				$sort = 'name';
			}
			
			if ($request->order) {
				$order = $request->order;
			} else {
				$order = 'asc';
			}
			
			$limit = session('settings.limit', 25);
			
			$sort_name = url('admin/elements', ['sort' => 'name', 'order' => $order == 'asc' ? 'desc' : 'asc']) . $this->params;
			
			if (in_array($sort, ['name'])) {
				$elements = Elements::select('id', 'name', 'setting')->where($where)->orderBy($sort, $order)->paginate($limit);
			} else {
				$elements = Elements::select('id', 'name', 'setting')->where($where)->orderBy('name')->paginate($limit);
			}
			
			$this->breadcrumbs->addCrumb('Элементы', url('admin/elements') . $this->params);
			$breadcrumbs = $this->breadcrumbs->render();
			$params = $this->params;
			$params_array = $this->params_array;
			
			return view('pages.elements', compact('params', 'params_array', 'breadcrumbs', 'sort_name', 'sort', 'elements', 'name', 'order'));
		}
		
		public function add(Request $request) {
			$class = Elements::select('id')->orderBy('created_at', 'desc')->limit(1)->value('id');
			
			if (!$class) {
				$class = 's_00001';
			} else {
				$class = 's_' . str_pad($class + 1, 5, '0', STR_PAD_LEFT);
			}
			
			if (!is_null($request->x)) {
				$x = true;
			} else {
				$x = false;
			}
			
			$this->breadcrumbs->addCrumb('Элементы', url('admin/elements') . $this->params);
			$this->breadcrumbs->addCrumb('Создать', url('admin/element_add'));
			$breadcrumbs = $this->breadcrumbs->render();
			
			return view('pages.element-edit', ['breadcrumbs' => $breadcrumbs, 'x' => $x, 'elements' => $this->elements, 'name' => old('name'), 'class' => old('class') ? old('class') : $class, 'code' => old('code'), 'setting' => (array)old('setting'), 'action' => asset('admin/element_save') . $this->params, 'id' => '']);
		}
		
		public function edit(Request $request)
		{
			$element = Elements::where('id', $request->id)->first();
			
			if (!empty($element)) {
				if (!is_null($request->x)) {
					$x = true;
				} else {
					$x = false;
				}
				
				$this->breadcrumbs->addCrumb('Элементы', url('admin/elements') . $this->params);
				$this->breadcrumbs->addCrumb('Создать', url('admin/element_add'));
				$breadcrumbs = $this->breadcrumbs->render();
				$action = asset('admin/element_save') . $this->params;
				
				return view('pages.element-edit', compact('breadcrumbs', 'x', 'elements', 'name', 'class', 'code', 'setting', 'action', 'id'));
			} else {
				return redirect('admin/elements' . $this->params)->with('error', 'Идентификатор не найден');
			}
		}
		
		public function delete(Request $request) {
			if ($request->selected) {
				foreach ($request->selected as $s) {
					Elements::where('id', $s)->delete();
				}
				
				$message = 'Операция успешна';
				$type = 'success';
			} else {
				$message = 'Выделите пункты для удаления';
				$type = 'error';
			}
			
			return redirect('admin/elements' . $this->params)->with($type, $message);
		}
		
		public function save(Request $request) {
			if (!is_null($request->id)) {
				$this->validate($request, [
					'name' => 'required',
					'code' => 'required'
				]);
			} else {
				$this->validate($request, [
					'name' => 'required',
					'code' => 'required',
					'class' => 'required|unique:elements,class'
				]);
			}
			
			if (!is_null($request->setting)) {
				$setting = [];
				
				foreach ($request->setting as $key => $s) {
					if (isset($s['children'])) {
						$s['children'] = array_filter($s['children']);
						if (!$s['children']) unset($s['children']);
					}
					
					if (!empty($s)){
						if ($s = array_filter($s)) $setting[$key] = $s;
					}
				}
			} else {
				$setting = [];
			}
			
			if (!is_null($request->id)) {
				$elements['name'] = $request->name;
				$elements['code'] = $request->code;
				$elements['class'] = $request->class;
				$elements['setting'] = $setting;
				
				Elements::where('id', $request->id)->update($elements);
			} else {
				$elements = new Elements;
				$elements->name = $request->name;
				$elements->class = $request->class;
				$elements->code = $request->code;
				$elements->setting = $setting;
				
				$elements->save();
			}
			
			return redirect('admin/elements' . $this->params)->with('success', 'Операция успешна');
		}
		
		public function getElement(Request $request) {
			if (!is_null($request->code) && !empty($this->elements[$request->code])) {
				if (!is_null($request->id)) {
					$setting = Elements::where('id', $request->id)->value('setting');
				} else {
					$setting = null;
				}
				
				$elements = $this->elements[$request->code];
				$params = isset($elements['params']) ? $elements['params'] : [];
				return view('pages.element-indent', ['name' => $elements['name'], 'code' => $request->code, 'settings' => !is_null($setting) ? $setting : [], 'params' => $params]);
			}
		}
	}
