@include('frontend.partials.header', array('styles' => array(HTML::style('assets/css/dashboard.css'), HTML::style('assets/css/morris.js-0.4.3/morris.css'))))
@include('frontend.partials.paneltopmenu')
<div class="container-fluid">
	<div class="row">
		@include('frontend.partials.panelsidebar')
		@yield('content')
	</div><!-- END OF ROW -->
</div><!-- END OF CONTAINER FLUID -->
@include('frontend.partials.footer', array('scripts'=>array(HTML::script('assets/js/morris.js-0.4.3/raphael-min.js'), HTML::script('assets/js/morris.js-0.4.3/morris.min.js'))))