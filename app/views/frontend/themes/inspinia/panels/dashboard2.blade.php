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
  HTML::script('assets/js/visual.js'))
])
@section('content')
<!-- Content wrapper -->
<div class="wrapper wrapper-content">
    
    <div class="ibox float-e-margins">
        <div class="ibox-title">
          <div class="pull-left">
            <h5>Overview Summary of ({{ $site['name'] }})</h5>
          </div>
          
          <div class="pull-right">
              @include(getenv('FRONTEND_SKINS') . $theme . '.panels.filter_date')
          </div>
        </div>
        <div class="ibox-content">
          <p>
            Ok, so what's next ?
            <ol>
              <li><strike>Sign up</strike></li>
              <li>Put the tracking script and get analytics on your dashboard</li>
              <li>Wait for a week as our owls get more data from your traffic</li>
              <li>View a demo of the recommendations on our site</li>
              <li>Insert the recommendation script on your site and win more customers</li>
            </ol>
            Questions? Need help?
          </p>
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
