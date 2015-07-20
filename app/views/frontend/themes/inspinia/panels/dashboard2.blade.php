@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard', 
['scripts' => array(
  HTML::script('assets/js/chosen-1.1.0/chosen.jquery.min.js'), 
  HTML::script('assets/js/moment.min.js'), 
  HTML::script('assets/js/daterangepicker.js'), 
  HTML::script('assets/js/highcharts.js'), 
  HTML::script('assets/inspinia/js/plugins/chartJs/Chart.min.js'),
  HTML::script('assets/js/bootstrap-datetimepicker.min.js'), 
  HTML::script('assets/js/script.helper.js'), 
  HTML::script('assets/js/script.panel.filters.js'),
  HTML::script('assets/js/metricsgraphics.js'),
  HTML::script('assets/js/visual.js'))
])
@section('content')
<!-- Content wrapper -->
<div class="wrapper wrapper-content">
    
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>Overview Summary of ({{$activeSiteName}})</h5>
            <div class="pull-right">
                @include(getenv('FRONTEND_SKINS') . $theme . '.panels.filter_date')
            </div>
        </div>
    </div>

    @include(getenv('FRONTEND_SKINS') . $theme . '.panels.overview_summary2')


    <div class="top_items_wrapper row">
      
      <div class="col-md-12 col-xs-12 col-sm-12 col-lg-12">
          @include(getenv('FRONTEND_SKINS') . $theme . '.panels.top_5', ['tableHeader' => 'Top 10 (Most Purchased Items)', 'contents' => $top_purchased_items])
        </div>
        
      <div class="col-md-12 col-xs-12 col-sm-12 col-lg-12">
            @include(getenv('FRONTEND_SKINS') . $theme . '.panels.top_5', ['tableHeader' => 'Top 10 (Most Viewed Items)', 'contents' => $top_viewed_items])
        </div>
    </div>

</div><!-- End of wrapper-content -->
@stop
