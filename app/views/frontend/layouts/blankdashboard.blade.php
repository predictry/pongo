@include('frontend.partials.header', array('styles' => array(HTML::style('assets/css/dashboard.css'), HTML::style('assets/css/morris.js-0.4.3/morris.css'), HTML::style('assets/css/chosen-1.1.0/chosen.css'), HTML::style('assets/css/bootstrap-datetimepicker.min.css'))))
@include('frontend.partials.simpletopmenu')
<div class="container-fluid">
    <div class="row">
        @yield('content')
    </div><!-- END OF ROW -->
</div><!-- END OF CONTAINER FLUID -->
@include('frontend.partials.footer', array('scripts' => $scripts, 'custom_script' => $custom_script))