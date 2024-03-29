@include(getenv('FRONTEND_SKINS') . $theme . '.partials.header', array('styles' => array(HTML::style('assets/css/daterangepicker-bs3.css'), HTML::style('assets/css/bootstrap-datetimepicker.min.css'), HTML::style('assets/inspinia/css/plugins/switchery/switchery.css'))))
<div id="wrapper">
    @if(Session::get("active_site_id") !== null)
    @include('admin.partials.panel_sidebar')
    @endif
    <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
            @include('admin.partials.navbar_static_top')
        </div>
        @yield('content')
        @include(getenv('FRONTEND_SKINS') . $theme . '.partials.copyright')
    </div>

</div>
@include(getenv('FRONTEND_SKINS') . $theme . '.partials.footer', array('custom_script' => $custom_script))
