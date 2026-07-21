<?php
	
	use Intervention\Image\Facades\Image;
	use \Symfony\Component\HttpFoundation\File\File;
	
	function route_region($url, $slug) {
		$lang = config('app.locale_prefix');
		$region = config('app.region_code');
		
		if ($region) {
			$url = rtrim(str_replace([$region . '/', $region], '', $url), '/');
		}
		
		$route = parse_url($url);
		
		if (!empty($route['path'])) {
			$paths = explode('/', ltrim($route['path'], '/'));
			
			if ($paths) {
				foreach ($paths as $key => &$path) {
					if (in_array($lang, $paths)) {
						if ($path == $lang) {
							$path = $path . ($slug ? '/' . $slug : '');
						}
					} elseif (!empty($path) && $key == 0) {
						$path = ($slug ? $slug . '/' : '') . $path;
					}
				}
				
				$route = $route['scheme'] . '://' . (isset($route['host']) ? $route['host'] . '/' : '') . implode('/', $paths);
			} else {
				$route = $url . '/' . $slug;
			}
		} else {
			$route = $url . '/' . $slug;
		}
		
		return $route;
	}
	
	function format_price_value($number, $code, $value = '', $format = true) {
		if (session('currency_with_code.' . $code)) {
			$currency = session('currency_with_code.' . $code);
			
			$symbol = $currency['symbol'];
			$position = $currency['position'];
			$decimal = $currency['decimal'];
			
			if (!$value) {
				$value = $currency['value'];
			}
			
			$amount = $value ? (float)$number * $value : (float)$number;
			$amount = round($amount, (int)$decimal);
			
			if (!$format) {
				return $amount;
			}
			
			$string = number_format($amount, (int)$decimal, __('locale.decimal_point'), __('locale.thousand_point'));
			
			if ($position == 1) {
				$string = $symbol . ' ' . $string;
			} else {
				$string .= ' ' . $symbol;
			}
		} else {
			$string = $number;
		}
		
		return $string;
	}
	function format_price($number, $currency = false, $value = '', $format = true) {
		if (!$currency) $currency = session('currency');
		
		if ($currency) {
			$symbol = $currency['symbol'];
			$position = $currency['position'];
			$decimal = $currency['decimal'];
			
			if (!$value) {
				$value = $currency['value'];
			}
			
			$amount = $value ? (float)$number * $value : (float)$number;
			$amount = round($amount, (int)$decimal);
			
			if (!$format) {
				return $amount;
			}
			
			$string = number_format($amount, (int)$decimal, __('locale.decimal_point'), __('locale.thousand_point'));
			
			if ($position == 1) {
				$string = $symbol . ' ' . $string;
			} else {
				$string .= ' ' . $symbol;
			}
		} else {
			$string = $number;
		}
		
		return $string;
	}
	function render_view($output, $region = [], $optimise = true) {
		$output = preg_replace("/(\n)+/", "\n", $output);
		$output = preg_replace("/\r\n+/", "\n", $output);
		$output = preg_replace("/\n(\t)+/", "\n", $output);
		$output = preg_replace("/\n(\ )+/", "\n", $output);
		$output = preg_replace("/\>(\n)+</", '><', $output);
		$output = preg_replace("/\>\r\n</", '><', $output);
		if ($optimise) $output = webOptimise($output);
		
		$search = ['%7BREGION_CODE%7D', '{REGION_CODE}', '{REGION}', '{FORMAT1}', '{FORMAT2}', '{FORMAT3}'];
		$replace = [];
		$searchs = [];
		
		foreach (['code', 'code', 'name', 'format1', 'format2', 'format3'] as $key => $code) {
			$replace[] = isset($region[$code]) ? $region[$code] : '';
			$searchs[] = isset($search[$key]) ? $search[$key] : '';
		}
		
		if ($searchs) $output = str_replace($searchs, $replace, $output);
		
		return $output;
	}
	function resize_image($image, $width, $height) {
		if (empty($image) || !@file_exists($image)) {
			$image = asset('assets/site/img/no_image.png');
		}
		
		return $image;
		
		$new_image = new File($image);
		$image_name = $new_image->getBaseName();
		$image_name = utf8_substr($image_name, 0, utf8_strrpos($image_name, '.')) . '.webp';
		$extension = $new_image->getExtension();
		
		$root = base_path('assets/site/img/thumbnails/');
		
		if (file_exists($root . $width . 'x' . $height . '/' . $image_name)) {
			return asset('assets/site/img/thumbnails/' . $width . 'x' . $height . '/' . $image_name);
		}
		
		if (!is_dir($root . $width . 'x' . $height)) {
			mkdir($root . $width . 'x' . $height, 0777, true);
		}
		
		if ($new_image->getPathName() && in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])) {
			$destinationPath = $root . $width . 'x' . $height . '/';
			
			$image = Image::make($new_image->getPathName());
			
			$image->encode('webp', 75)->resize($width, $height)->save($destinationPath . '/' . $image_name);
			
			$image = asset('assets/site/img/thumbnails/' . $width . 'x' . $height . '/' . $image_name);
		}
		
		return $image;
	}
	function webOptimise($output) {
		if (!file_exists(base_path('assets/site/img/webp'))) {
			mkdir(base_path('assets/site/img/webp'), 0777);
		}
		
		$output2 = mb_convert_encoding($output, 'HTML-ENTITIES', "UTF-8");
		$document = new DOMDocument('1.0', 'UTF-8');
		@$document->loadHTML(utf8_decode($output2));
		
		if ($document) {
			$gd = gd_info();
			
			$title = $document->getElementsByTagName('title')->item(0)->nodeValue;
			
			foreach ($document->getElementsByTagName('img') as $x => $img) {
				if (empty($img->getAttribute('src'))) continue;
				
				if ($img->getAttribute('alt') == '' || !$img->getAttribute('alt')) {
					$img->setAttribute('alt', $title . ' - №' . $x);
				}
			}
			
			if ($gd['WebP Support']) {
				if (isset($_SERVER['HTTP_ACCEPT']) && isset($_SERVER['HTTP_USER_AGENT'])) {
					
					if (strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false) {
						foreach ($document->getElementsByTagName('img') as $x => $img) {
							if (empty($img->getAttribute('src'))) continue;
							
							/*$img->setAttribute('src', $i = addSrc($img->getAttribute('src')));
							
							if ($img->getAttribute('data-img') == '') {
								$img->setAttribute('class', 'v_lazy vlz' . ($img->getAttribute('class') ? ' ' . $img->getAttribute('class') : ''));
								$img->setAttribute('loading', 'lazy');
								$img->setAttribute('decoding', 'async');
								$img->setAttribute('data-img', $i);
								$img->setAttribute('src', addSrc(asset('assets/site/img/logo/Logo.png')));
							}*/
							
							$img->setAttribute('src', $i = $img->getAttribute('src'));
							
							if ($img->getAttribute('data-img') == '') {
								$img->setAttribute('class', 'v_lazy vlz' . ($img->getAttribute('class') ? ' ' . $img->getAttribute('class') : ''));
								$img->setAttribute('loading', 'lazy');
								$img->setAttribute('decoding', 'async');
								$img->setAttribute('data-img', $i);
								$img->setAttribute('src', asset('assets/site/img/load.gif'));
							}
						}
					}
				}
			}
			
			$output = html_entity_decode($document->saveHTML());
			
			$script = '<script>document.addEventListener("DOMContentLoaded", function() {  var lazyloadImages;if ("IntersectionObserver" in window) {    lazyloadImages = document.querySelectorAll(".v_lazy");    var imageObserver = new IntersectionObserver(function(entries, observer) {entries.forEach(function(entry) {  if (entry.isIntersecting) {    var image = entry.target;if (typeof image.dataset.img != "undefined") {image.src = image.dataset.img;    image.classList.remove("v_lazy");    imageObserver.unobserve(image);  }}});    });    lazyloadImages.forEach(function(image) {imageObserver.observe(image);    });  } else {var lazyloadThrottleTimeout;    lazyloadImages = document.querySelectorAll(".v_lazy");  function lazyload () {if(lazyloadThrottleTimeout) {  clearTimeout(lazyloadThrottleTimeout);}    lazyloadThrottleTimeout = setTimeout(function() {  var scrollTop = window.pageYOffset;  lazyloadImages.forEach(function(img) {if(img.offsetTop < (window.innerHeight + scrollTop)) {  img.src = img.dataset.img;  img.classList.remove(\'v_lazy\');}  });  if(lazyloadImages.length == 0) {     document.removeEventListener("scroll", lazyload);    window.removeEventListener("resize", lazyload);    window.removeEventListener("orientationChange", lazyload);  }}, 20);    }    document.addEventListener("scroll", lazyload);    window.addEventListener("resize", lazyload);    window.addEventListener("orientationChange", lazyload);  }});</script>';
			
			$output = str_replace('</head>', $script . '<style>.v_lazy{opacity:0;background: none !important;}.vlz{transition: 1000ms opacity ease;animation-play-state: running}</style></head>', $output);
		}
		
		return $output;
	}
	function utf8_strlen($string) {
		return mb_strlen($string);
	}
	function utf8_substr($string, $offset, $length = null) {
		if ($length === null) {
			return mb_substr($string, $offset, utf8_strlen($string));
		} else {
			return mb_substr($string, $offset, $length);
		}
	}
	function utf8_strrpos($string, $needle) {
		return iconv_strrpos($string, $needle, 'UTF-8');
	}
	function addSrc($image) {
		$image = str_replace(url('/') . '/', '', $image);
		$extension = strtolower(utf8_substr($image, utf8_strrpos($image, '.')+1, strlen($image)));
		
		if (isset($_SERVER['DOCUMENT_ROOT'])) {
			$root = $_SERVER['DOCUMENT_ROOT'] . '/';
		} else {
			$root = public_path() . '/../';
		}
		
		if (empty($image) || !file_exists($root . $image) || !in_array($extension, ['jpg', 'jpeg', 'png']) || $extension == 'webp') {
			return url('/') . '/' . $image;
		}
		
		$new_image = new File($root . $image);
		$filename = $new_image->getFileName();
		$filename = utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '.webp';
		$dirname = str_replace($root, '', $new_image->getPath());
		
		if (!file_exists($root . 'assets/site/img/webp/' . $dirname . '/' . $filename)) {
			$path = 'webp';
			
			$directories = explode('/', $dirname);
			
			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;
				
				if (!is_dir($root . 'assets/site/img/' . $path)) {
					@mkdir($root . 'assets/site/img/' . $path, 0777);
				}
			}
			
			$image_webp = $path . '/' . $filename;
			
			$image = Image::make($new_image->getPathName());
			$image->encode('webp', 75)->save($root . 'assets/site/img/' . $image_webp);
			$image = asset('assets/site/img/' . $image_webp);
		} else {
			$image = url('/') . '/assets/site/img/webp/' . $dirname . '/' . $filename;
		}
		
		return $image;
	}
	function num_decline($number, $titles, $show_number = 1){
		
		if( is_string( $titles ) ) $titles = preg_split( '/, */', $titles );
		
		if( empty( $titles[2] ) )
			$titles[2] = $titles[1];
		
		$cases = [ 2, 0, 1, 1, 1, 2 ];
		
		$intnum = abs( (int) strip_tags( $number ) );
		
		$title_index = ( $intnum % 100 > 4 && $intnum % 100 < 20 )
			? 2
			: $cases[ min( $intnum % 10, 5 ) ];
		
		return ( $show_number ? "$number " : '' ) . $titles[ $title_index ];
	}
	function token_salt($length = 32) {
		$string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		
		$max = strlen($string) - 1;
		
		$token = '';
		
		for ($i = 0; $i < $length; $i++) {
			$token .= $string[mt_rand(0, $max)];
		}
		
		return $token;
	}