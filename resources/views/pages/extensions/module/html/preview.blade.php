<!DOCTYPE html>
<html dir="ltr" lang="ru">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="HandheldFriendly" content="true">
    <title>Просмотр</title>
    <link rel="stylesheet" href="{{ asset('assets/site/css/style.css') }}" />

    <style>
        .nav.nav-tabs {
            border-bottom-color: #ededed;
        }
        .nav {
            display: flex;
            flex-wrap: wrap;
            padding-left: 0;
            margin-bottom: 0;
            list-style: none;
        }

        .nav-tabs {
            border-bottom: 1px solid #7E8FA3;
        }
        .nav.nav-tabs .nav-item, .nav.nav-pills .nav-item {
            margin-right: 0.8rem;
        }
        .nav.nav-tabs .nav-item {
            padding-bottom: 0.8rem;
            position: relative;
        }
        .nav-tabs .nav-item {
            margin-bottom: -1px;
        }
        .nav-fill .nav-item {
            flex: 1 1 auto;
            text-align: center;
        }
        .nav.nav-tabs .nav-item .nav-link.active, .nav.nav-pills .nav-item .nav-link.active {
            box-shadow: 0 2px 4px 0 rgb(90 141 238 / 50%);
        }

        .nav.nav-tabs .nav-item .nav-link, .nav.nav-pills .nav-item .nav-link {
            border-radius: 0.267rem;
        }
        .nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link {
            color: #FFFFFF;
            background-color: #5A8DEE;
            border-color: transparent;
        }
        .nav-tabs .nav-link, .nav-pills .nav-link {
            background-color: #f2f4f4;
            color: #475F7B;
        }
        .nav-tabs .nav-link {
            border: 1px solid transparent;
            border-top-left-radius: 0.267rem;
            border-top-right-radius: 0.267rem;
        }
        .nav-link {
            display: block;
            padding: 0.567rem 1.33rem;
        }
        .nav.nav-tabs ~ .tab-content {
            color: #475F7B;
        }
        .pt-1, .py-1 {
            padding-top: 1rem !important;
        }
        .tab-content > .tab-pane {
            display: none;
        }
        .tab-content > .active {
            display: block;
        }
        .tab-pane.active .row {margin: 0}
    </style>
    <script src="{{ asset('assets/site/js/jquery.js') }}"></script>
</head>
<body>
<main>
    <style id="media">
        <?php

        function r($children, $code) {
            foreach($children as $x => $element) {
                if (!empty($element['menu'][$code])) {
                    echo $element['menu'][$code];
                } else {
                    if ($element['code'] == 'img' && pathinfo(trim($element['img']), PATHINFO_EXTENSION) === 'svg' && $svg = @file_get_contents(trim($element['img']))) {
                        if (!empty($element['text'][$code])) {
                            $title = ' title="' . trim($element['text'][$code]) . '"';
                        } else {
                            $title = '';
                        }

                        echo strpos($svg, 'class="') === false ?
                            str_replace('<svg ', '<svg' . $title . ' class="' . $element['class'] . '"', $svg) :
                            str_replace('<svg class="', '<svg' . $title . ' class="' . $element['class'] . ' ', $svg);
                    } else {
                        echo '<' . $element['code'] . ' class="' . $element['class']. '"';

                        if ($element['code'] == 'img' && !empty($element['img'])) {
                            echo ' src="' . trim($element['img']) . '"';
                        }

                        if ($element['code'] == 'a' && !empty($element['link'][$code])) {
                            echo ' href="' . trim($element['link'][$code]) . '"';
                        }

                        if ($element['code'] != 'img') {
                            echo '>';
                        }

                        if (!empty($element['text'][$code])) {
                            if (!in_array($element['code'], ['ul', 'ol'])) {
                                $text = trim($element['text'][$code]);
                            } else {
                                $text = '';
                            }

                            if ($element['code'] == 'img') {
                                $text = empty($text) ? 'Image - №' . $x : $text;
                                echo ' alt="' . $text . '" />';
                            } else {
                                echo nl2br($text);
                            }
                        } elseif ($element['code'] == 'img') {
                            echo ' />';
                        }

                        if (!empty($element['children']) && $element['code'] != 'img') {
                            r($element['children'], $code);
                        }

                        if ($element['code'] != 'img') {
                            echo '</' . $element['code'] . '>';
                        }
                    }
                }
            }
        }

        $media = [];
        $e = [];
        $media_text = [0 => '@media (max-width: 767px)', 1 => '@media (min-width: 768px) and (max-width: 991px)', 2 => '@media (min-width: 992px) and (max-width: 1199px)', 3 => '@media (min-width: 1200px)', 4 => '', 5 => ''];

        function t($children, &$e, &$menu_css) {
            foreach($children as $element) {
                if (isset($element['children'])) {
                    $children = $element['children'];
                    unset($element['children']);
                    $e[$element['element_id']] = $element;
                    t($children, $e, $menu_css);
                } elseif (isset($element[0])) {
                    if (isset($element['css'])) $menu_css .= $element['css'];
                    t([$element[0]], $e, $menu_css);
                }
            }
        }

        $menu_css = '';

        foreach($elements as $element) {
            if (isset($element['element_id'])) {
                $e[$element['element_id']] = $element;

                if (isset($element['children'])) {
                    $children = $element['children'];
                    unset($element['children']);
                    $e[$element['element_id']] = $element;
                    t($children, $e, $menu_css);
                }
            } elseif (isset($element[0])) {
                if (isset($element['css'])) $menu_css .= $element['css'];
                t([$element[0]], $e, $menu_css);
            }
        }

        foreach($settings as $id => $setting) {
            if (isset($e[$id])) {
                $e[$id]['class'] = $setting['class'];

                foreach($setting['setting'] as $key => $setting2) {
                    $style = [];

                    foreach ($setting2 as $property => $value) {
                        if (strpos($property, ':auto') !== false) {
                            $style[] = $property;
                        } else {
                            $style[] = $property . ':' . $value;
                        }
                    }

                    if ($style && isset($media_text[$key])) {
                        $media[$media_text[$key]][] = '.' . $setting['class'] . ($key == 5 ? ':hover' : '') . '{' . implode(';', $style) . '}';
                    }
                }
            }
        }

        if (!empty($menu_css)) {
            echo $menu_css;
        }

        if (!empty($css)) {
            echo $css;
        }

        foreach(array_reverse($media) as $key => $m) {
            $style = '';

            foreach($m as $m2) {
                $style .= $m2;
            }

            if (!$key) {
                echo $style;
            } else {
                echo $key . '{' . $style . '}';
            }
        }

        ?>
    </style>
    <div id="preview">
        @if (!$langs->isEmpty() && !empty($elements))
            @if($langs->count() > 1)
                <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                    @foreach($langs as $key => $l)
                        <li class="nav-item">
                            <a class="nav-link{{ $key == 0 ? ' active' : '' }}" id="label-{{ $l['language_id'] }}" data-toggle="tab" href="#lid-{{ $l['language_id'] }}" role="tab" aria-controls="lid-{{ $l['language_id'] }}" aria-selected="true">
                                {{ $l['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="tab-content pt-1" style="height: 100%;padding-top: 40px !important;padding-bottom: 40px !important;background: #eee">
                @foreach($langs as $key => $l)
                    <div class="tab-pane{{ $key == 0 ? ' active' : '' }}" id="lid-{{ $l['language_id'] }}" role="tabpanel" aria-labelledby="label-{{ $l['language_id'] }}">
                        @foreach($elements as $x => $element)
                            <?php

                            if (!empty($element['menu'][$l['code']])) {
                                echo $element['menu'][$l['code']];
                            } else {
                                if ($element['code'] == 'img' && pathinfo(trim($element['img']), PATHINFO_EXTENSION) === 'svg' && $svg = @file_get_contents(trim($element['img']))) {
                                    if (!empty($element['text'][$l['code']])) {
                                        $title = ' title="' . trim($element['text'][$l['code']]) . '"';
                                    } else {
                                        $title = '';
                                    }

                                    echo strpos($svg, 'class="') === false ?
                                        str_replace('<svg ', '<svg' . $title . ' class="' . $element['class'] . ($x == 0 ? ' ' . $parent_class : '') . '"', $svg) :
                                        str_replace('<svg class="', '<svg' . $title . ' class="' . $element['class'] . ($x == 0 ? ' ' . $parent_class : '') . ' ', $svg);
                                } else {
                                    echo '<' . $element['code'] . ' class="' . $element['class'] . ($x == 0 ? ' ' . $parent_class : '') . '"';

                                    if ($element['code'] == 'img' && !empty($element['img'])) {
                                        echo ' src="' . trim($element['img']) . '"';
                                    }

                                    if ($element['code'] == 'a' && !empty($element['link'][$l['code']])) {
                                        echo ' href="' . trim($element['link'][$l['code']]) . '"';
                                    }

                                    if ($element['code'] != 'img') {
                                        echo '>';
                                    }

                                    if (!empty($element['text'][$l['code']])) {
                                        if (!in_array($element['code'], ['ul', 'ol'])) {
                                            $text = trim($element['text'][$l['code']]);
                                        } else {
                                            $text = '';
                                        }

                                        if ($element['code'] == 'img') {
                                            $text = empty($text) ? 'Image - №' . $x : $text;
                                            echo ' alt="' . $text . '" />';
                                        } else {
                                            echo nl2br($text);
                                        }
                                    } elseif ($element['code'] == 'img') {
                                        echo ' />';
                                    }

                                    if (!empty($element['children']) && $element['code'] != 'img') {
                                        r($element['children'], $l['code']);
                                    }

                                    if ($element['code'] != 'img') {
                                        echo '</' . $element['code'] . '>';
                                    }
                                }
                            }

                            ?>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</main>
</body>
</html>