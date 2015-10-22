@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard', ['scripts' => array(
HTML::script('assets/js/script.helper.js'), 
HTML::script('assets/js/data_collection.js'),
HTML::script('assets/js/prism.js')),
HTML::script('assets/js/chosen-1.1.0/chosen.jquery.min.js'), 
HTML::script('assets/js/moment.min.js'), 
HTML::script('assets/js/daterangepicker.js'), 
HTML::script('assets/js/highcharts.js'), 
HTML::script('assets/inspinia/js/plugins/chartJs/Chart.min.js'),
HTML::script('assets/js/bootstrap-datetimepicker.min.js'), 
HTML::script('assets/js/script.helper.js'), 
HTML::script('assets/js/script.panel.filters.js'),
HTML::script('assets/js/visual.js')])
@section('content')

