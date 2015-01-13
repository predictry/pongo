<div id="wrapper">
    @include(getenv('FRONTEND_SKINS') . $theme . '.partials.header', array('styles' => array(HTML::style('assets/css/daterangepicker-bs3.css'), HTML::style('assets/css/bootstrap-datetimepicker.min.css'))))
    @if(Session::get("active_site_id") !== null)
    @include(getenv('FRONTEND_SKINS') . $theme . '.partials.panelsidebar')
    @endif
    <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
            @include(getenv('FRONTEND_SKINS') . $theme . '.partials.navbar_static_top')
        </div>
        @yield('content')
        @include(getenv('FRONTEND_SKINS') . $theme . '.partials.copyright')
    </div>

</div>
@include(getenv('FRONTEND_SKINS') . $theme . '.partials.footer', array('custom_script' => $custom_script, 'scripts' => array(HTML::script('assets/js/chosen-1.1.0/chosen.jquery.min.js'), HTML::script('assets/js/moment.min.js'), HTML::script('assets/js/daterangepicker.js'), HTML::script('assets/js/highcharts.js'), HTML::script('assets/js/bootstrap-datetimepicker.min.js'), HTML::script('assets/js/script.panel.helper.js'), HTML::script('assets/js/script.panel.filters.js')))))
