<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\Elements;
use App\Models\Extensions;
use App\Models\LayoutExtension;
use Illuminate\Support\Facades\Route;

class GetContentController extends Controller {
    private $layout_id;
    private $styles = [];
    private $links = [];
    private $scripts = [];
    private $position = [];
	private $media = [];
	private $modules = [];
    private $settings;

    public function __construct($layout_id = 0, $not = []) {
        $this->settings = session('settings');
        $this->layout_id = $layout_id;

        if (!$this->layout_id && !empty($this->settings['layout_id'])) {
            $this->layout_id = $this->settings['layout_id'];
        }
	
		$extension_module = [];

        $modules = LayoutExtension::with([
            'extensions' => function($query) {
                $query->select('id', 'setting')->where('status', 1)->where('id', '!=', null)->where('setting', '!=', '');
            }
        ])->select('extension_id', 'code', 'position')->where('layout_id', $layout_id)->where(function($query) use($not) {
        	if ($not) {
				$query->whereNotIn('code', $not);
			}
		})->orderBy('sort')->get();

        foreach ($modules as $sort => $module) {
			$module['code'] = str_replace('module.', '', $module['code']);
            $part = explode('.', $module['code']);

            if (is_null($module->extensions)) {
                if (isset($part[0]) && file_exists(app_path() . '/Http/Controllers/Extensions/Module/' . ucfirst($part[0]) . 'Controller.php')) {
                    if (!isset($extension_module[$part[0]])) {
						$extension_module[] = 'extension.module.' . $part[0];
						$extension_sort[] = ['position' => $module['position'], 'sort' => $sort];
                    }
                }
            }
            else {
                if (isset($part[1]) && file_exists(app_path() . '/Http/Controllers/Extensions/Module/' . ucfirst($part[0]) . 'Controller.php')) {
                    $extension = $module->extensions->setting;

                    if (!empty($extension)) {
                        $output = '\App\Http\Controllers\Extensions\Module\\' . ucfirst($part[0]) . 'Controller';
                        $output = new $output;

                        if ($view = $output->index($extension, $this->media)) {
							$this->modules[$module['code']] = $view;
							
                            if (method_exists($output, 'getHtmlStyle') && $extension_style = $output->getHtmlStyle()) {
                                $this->media = $extension_style;
                            }

                            if (method_exists($output, 'getLinkStyle') && $extension_links = $output->getLinkStyle()) {
                                $this->links = array_merge($this->links, $extension_links);
                            }

                            if (method_exists($output, 'getScript') && $scripts = $output->getScript()) {
                                $this->scripts = array_merge($this->scripts, $scripts);
                            }

                            $this->position[$module['position']]['modules'][$sort] = $view;
                        }
                    }
                }
            }
        }
	
		if (!empty($extension_module)) {
			$s = implode("','", $extension_module);
			
			$settings_module = Settings::selectRaw("replace(code, 'extension.module.', '') as code, value")
				->where('value->status', 1)
				->whereRaw("code in ('" . htmlspecialchars($s) . "') order by FIELD(code, '" . htmlspecialchars($s) . "')")
				->where(function($query) use($not) {
					if ($not) {
						$query->whereNotIn('code', $not);
					}
				})
				->get();
			
			$key = 0;
			
			foreach ($settings_module as $module) {
				$module_data = '\App\Http\Controllers\Extensions\Module\\' . ucfirst($module['code']) . 'Controller';
				$module_data = new $module_data;
				
				if ($view = $module_data->index($module['value'], $this->media)) {
					$this->modules[$module['code']] = $view;
					
					if (method_exists($module_data, 'getHtmlStyle') && $extension_style = $module_data->getHtmlStyle()) {
						$this->media = $extension_style;
					}
					
					if (method_exists($module_data, 'getLinkStyle') && $extension_links = $module_data->getLinkStyle()) {
						$this->links = array_merge($this->links, $extension_links);
					}
					
					if (method_exists($module_data, 'getScript') && $scripts = $module_data->getScript()) {
						$this->scripts = array_merge($this->scripts, $scripts);
					}
					
					$this->position[$extension_sort[$key]['position']]['modules'][$extension_sort[$key]['sort']] = $view;
				}
				
				$key++;
			}
		}
	
		if ($this->position) {
			foreach ($this->position as $position => $module) {
				ksort($this->position[$position]['modules']);
			}
		}
    }

    public function getHtmlStyle() {
        $styles = '';

        foreach (array_unique($this->media, SORT_REGULAR) as $key => $media) {
            if ($key != 'hover' && $key != '') {
                $styles .= $key . implode('', $media) . (!empty($key) ? '}' : '');
            } else {
                $styles .= implode('', $media);
            }
        }

        return $styles ? '<style>' . $styles . '</style>' : '';
    }

    public function getLinkStyle() {
        $styles = [];
        $styles_array = [];

        foreach ($this->links as $link) {
        	if (!in_array($link['href'], $styles_array)) {
				$styles_array[] = $link['href'];
				$styles[] = ['rel' => $link['rel'], 'href' => $link['href']];
			}
        }

        return $styles;
    }

    public function getScript() {
        $js = '';
		$js_array = [];

        foreach ($this->scripts as $script) {
            if (isset($script['src'])) {
            	if (!in_array( $script['src'], $js_array)) {
					$js_array[] = $script['src'];
					$js .= '<script src="' . $script['src'] . '"></script>';
				}
            } else {
                $js .= '<script>' . $script['text'] . '</script>';
            }
        }

        return $js;
    }
	
	public function getModuleById($part) {
		return isset($this->modules[$part]) ? $this->modules[$part] : false;
	}
	
	public function getModules() {
		return $this->modules;
	}

    public function getPosition($position) {
        if (isset($this->position[$position])){
            return view('pages.site.content_' . $position, $this->position[$position]);
        } else {
            return false;
        }
    }
}