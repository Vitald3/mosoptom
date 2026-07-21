<body class="vertical-layout vertical-menu-modern 2-columns
@if($configData['isMenuCollapsed'] == true){{'menu-collapsed'}}@endif
@if($configData['theme'] === 'dark'){{'dark-layout'}} @elseif($configData['theme'] === 'semi-dark'){{'semi-dark-layout'}} @else {{'light-layout'}} @endif
@if($configData['isContentSidebar'] === true) {{'content-left-sidebar'}} @endif @if(isset($configData['navbarType'])){{$configData['navbarType']}}@endif
@if(isset($configData['footerType'])) {{$configData['footerType']}} @endif
{{$configData['bodyCustomClass']}}
@if($configData['mainLayoutType'] === 'vertical-menu-boxicons'){{'boxicon-layout'}}@endif
@if($configData['isCardShadow'] === false){{'no-card-shadow'}}@endif"
	  data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

@include('panels.navbar')
@include('panels.sidebar')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="content-wrapper">
		<div class="content-body">
			@yield('content')
		</div>
	</div>
</div>

<div class="sidenav-overlay"></div>
<div class="drag-target"></div>

@include('panels.footer')
@include('panels.scripts')
</body>