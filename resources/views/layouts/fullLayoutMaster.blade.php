<!DOCTYPE html>
@isset($pageConfigs)
  {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset
@php
  $configData = Helper::applClasses();
  use App\Models\Settings;

    $settings = Settings::where('code', 'settings')->value('value');

    if (!empty($settings['favicon'])) {
        $favicon = asset($settings['favicon']);
    } else {
        $favicon = asset('assets/admin/img/favicon.ico');
    }
@endphp

<html class="loading" lang="ru" data-textdirection="ltr">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="robots" content="noindex, nofollow">
  <title>@yield('title')</title>
  @if($favicon)
    <link rel="shortcut icon" type="image/x-icon" href="{{ $favicon }}">
  @endif

  @include('panels.styles')
</head>

<body class="vertical-layout 1-column navbar-sticky {{$configData['bodyCustomClass']}} footer-static blank-page
  @if($configData['theme'] === 'dark'){{'dark-layout'}} @elseif($configData['theme'] === 'semi-dark'){{'semi-dark-layout'}} @else {{'light-layout'}} @endif" data-open="click" data-menu="vertical-menu-modern" data-col="1-column">
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="content-wrapper">
    <div class="content-header row">
    </div>
    <div class="content-body">
      @yield('content')
    </div>
  </div>
</div>

@include('panels.scripts')
</body>
</html>