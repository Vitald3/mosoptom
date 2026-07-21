<!DOCTYPE html>
@isset($pageConfigs)
    {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset
@php
    use App\Models\Settings;

    $configData = Helper::applClasses();

    $settings = Settings::where('code', 'settings')->value('value');

    if (!empty($settings['favicon'])) {
        $favicon = asset($settings['favicon']);
    } else {
        $favicon = asset('assets/admin/img/favicon.ico');
    }
@endphp

<html class="loading" lang="ru" data-textdirection="ltr">
<head>
    <meta  charset="UTF-8">
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

@include('layouts.verticalLayoutMaster')
</html>