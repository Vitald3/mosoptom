<script>
    var assetBaseUrl = "{{ asset('') }}";
</script>
<script src="{{asset('assets/admin/js/vendors.min.js')}}"></script>
<script src="{{asset('assets/admin/js/LivIconsEvo.tools.min.js')}}"></script>
<script src="{{asset('assets/admin/js/LivIconsEvo.defaults.min.js')}}"></script>
<script src="{{asset('assets/admin/js/LivIconsEvo.min.js')}}"></script>

@yield('vendor-scripts')
<script src="{{asset('assets/admin/js/vertical-menu-light.js')}}"></script>
<script src="{{asset('assets/admin/js/app-menu.js')}}"></script>
<script src="{{asset('assets/admin/js/app.js')}}"></script>
<script src="{{asset('assets/admin/js/scripts.js')}}"></script>
@yield('page-scripts')