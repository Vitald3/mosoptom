<style>
    .main-menu .navbar-header .navbar-brand .brand-logo {
        height: auto;
        width: 100%;
        float: none;
    }
    .main-menu .navbar-header .navbar-brand .brand-logo .logo {
        height: 40px;
        width: 100%;
    }
</style>
@php
    if(!empty($settings['logo'])) {
        $logo = url('/') . '/' . $settings['logo'];
    } else {
        $logo = false;
    }
@endphp
<div class="main-menu menu-fixed @if($configData['theme'] === 'light') {{"menu-light"}} @else {{'menu-dark'}} @endif menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto" style="width: 100%">
                    <a class="navbar-brand" href="{{asset('admin')}}">
                        <div class="brand-logo">
                            @if($logo)
                                <img src="{{ $logo }}" class="logo" alt=" ">
                            @else
                                VPanel
                            @endif
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation" data-icon-style="lines">
                @if(!empty($menuData[0]) && isset($menuData[0]))
                    @foreach ($menuData[0]->menu as $menu)
                        @if(isset($menu->navheader))
                            <li class="navigation-header"><span>{{$menu->navheader}}</span></li>
                        @else
                            @if(isset($menu->url))
                                <li class="nav-item {{(request()->is($menu->url.'*')) ? 'active' : '' }}">
                                    <a href="@if(isset($menu->url)){{asset($menu->url)}} @endif" @if(isset($menu->newTab)){{"target=_blank"}}@endif>
                                        @if(!empty($menu->icon))
                                            <i class="menu-livicon" data-icon="{{$menu->icon}}"></i>
                                        @endif
                                        @if(isset($menu->name))
                                            <span class="menu-title">{{ $menu->name }}</span>
                                        @endif
                                        @if(isset($menu->tag))
                                            <span class="{{$menu->tagcustom}}">{{$menu->tag}}</span>
                                        @endif
                                    </a>
                                    @if(isset($menu->submenu))
                                        @include('panels.sidebar-submenu',['menu' => $menu->submenu])
                                    @endif
                                </li>
                            @endif
                        @endif
                    @endforeach
                @endif
            </ul>
        </div>
    </div>