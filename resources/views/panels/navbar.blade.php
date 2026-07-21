<div class="header-navbar-shadow"></div>
<nav class="header-navbar main-header-navbar navbar-expand-lg navbar navbar-with-menu
@if(isset($configData['navbarType'])){{$configData['navbarClass']}} @endif"
     data-bgcolor="@if(isset($configData['navbarBgColor'])){{$configData['navbarBgColor']}}@endif">
  <div class="navbar-wrapper">
    <div class="navbar-container content">
      <div class="navbar-collapse" id="navbar-mobile">
        <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
          <ul class="nav navbar-nav">
            <li class="nav-item mobile-menu d-xl-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ficon bx bx-menu"></i></a></li>
          </ul>
          @isset($breadcrumbs)
            {!! $breadcrumbs !!}
          @endisset
        </div>
        <ul class="nav navbar-nav float-right">
          <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand"><i class="ficon bx bx-fullscreen"></i></a></li>
          <li class="dropdown dropdown-user nav-item">
            <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
              <div class="user-nav d-sm-flex d-none">
                <span class="user-name">@if (!is_null(auth()->user())){{ auth()->user()->name }}@endif</span>
                <span class="user-status text-muted">
                  @php
                    if (Auth::user()) {

                      $role = DB::table('users')->leftjoin('roles as r', 'r.id', '=', 'users.role_id')->where('users.id', auth()->user()->id)->value('r.name');

                      echo $role;
                    }
                  @endphp
                </span>
              </div>
              <span><img class="round" src="{{asset('assets/admin/img/user.png')}}" alt="avatar" height="40" width="40"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right pb-0">
              <a class="dropdown-item" href="{{asset('admin/cache_clear')}}">Очистить кеш изображений</a>
              <a class="dropdown-item" href="{{asset('admin/backup')}}">Создать бекап файлов сайта</a>
              <a class="dropdown-item" href="{{asset('admin/logout')}}"><i class="bx bx-power-off mr-50"></i>Выход</a>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>