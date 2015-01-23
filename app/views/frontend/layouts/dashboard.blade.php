@include('frontend.partials.header', array('styles' => array(HTML::style('assets/css/dashboard.css'), HTML::style('assets/css/morris.js-0.4.3/morris.css'), HTML::style('assets/css/chosen-1.1.0/chosen.css'), HTML::style('assets/css/daterangepicker-bs3.css'), HTML::style('assets/css/bootstrap-datetimepicker.min.css'))))
@include('frontend.partials.paneltopmenu')
<div class="container-fluid">
    <div class="row">
        @if(Session::get("active_site_id") !== null)
        @include('frontend.partials.panelsidebar')
        @endif
        @yield('content')
    </div><!-- END OF ROW -->
</div><!-- END OF CONTAINER FLUID -->
@include('frontend.partials.footer', array('scripts'=>array(HTML::script('assets/js/morris.js-0.4.3/raphael-min.js'), HTML::script('assets/js/morris.js-0.4.3/morris.min.js'), HTML::script('assets/js/chosen-1.1.0/chosen.jquery.min.js'), HTML::script('assets/js/moment.min.js'), HTML::script('assets/js/daterangepicker.js'), HTML::script('assets/js/highcharts.js'), HTML::script('assets/js/bootstrap-datetimepicker.min.js'), HTML::script('assets/js/script.js')), 'custom_script' => $custom_script))