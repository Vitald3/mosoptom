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

		if (!is_null($css)) {
			echo $css;
		}

		?>
    </style>
    <div id="preview" class="container">
        @if (!$langs->isEmpty())
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

            <div class="tab-content pt-1" style="height: 100%;padding-top: 40px !important;padding-bottom: 40px !important">
                @foreach($langs as $key => $l)
                    <div class="tab-pane{{ $key == 0 ? ' active' : '' }}" id="lid-{{ $l['language_id'] }}" role="tabpanel" aria-labelledby="label-{{ $l['language_id'] }}">
                        {!! !empty($html[$l['code']]) ? $html[$l['code']] : '' !!}
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</main>
</body>
</html>