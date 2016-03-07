@include(getenv('FRONTEND_SKINS') . $theme . '.partials.header', [
    'styles' => [
        HTML::style('assets/css/daterangepicker-bs3.css'),
        HTML::style('assets/css/bootstrap-datetimepicker.min.css'),
        HTML::style('assets/css/metricsgraphics.css')
    ],
    'extraStyles' => $styles
])
<div id="wrapper">
    @if(Session::get("active_site_id") !== null)
    @include(getenv('FRONTEND_SKINS') . $theme . '.partials.panel_sidebar')
    @endif
    <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
            @include(getenv('FRONTEND_SKINS') . $theme . '.partials.navbar_static_top')
        </div>
        @yield('content')
        @include(getenv('FRONTEND_SKINS') . $theme . '.partials.copyright')
    </div>

</div>
@include(getenv('FRONTEND_SKINS') . $theme . '.partials.footer', array('custom_script' => $custom_script))
