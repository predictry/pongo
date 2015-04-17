@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard', ['scripts' => array(HTML::script('assets/js/chosen-1.1.0/chosen.jquery.min.js'), HTML::script('assets/js/moment.min.js'), HTML::script('assets/js/daterangepicker.js'), HTML::script('assets/js/highcharts.js'), HTML::script('assets/js/bootstrap-datetimepicker.min.js'), HTML::script('assets/js/script.helper.js'), HTML::script('assets/js/script.panel.filters.js'))])
@section('content')
<div class="wrapper wrapper-content">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>Overview Summary of ({{$activeSiteName}})</h5>
            <div class="pull-right">
                @include(getenv('FRONTEND_SKINS') . $theme . '.panels.filter_date')
            </div>
        </div>
    </div>
    @include(getenv('FRONTEND_SKINS') . $theme . '.panels.overview_summary')
    <div class="row">
        <div class="col-lg-6">
            {{--@include(getenv('FRONTEND_SKINS') . $theme . '.panels.top_5', ['tableHeader' => 'Top 10 (Most Purchased Items)', 'contents' => $top_purchased_items])--}}
        </div>
        <div class="col-lg-6">
            {{--@include(getenv('FRONTEND_SKINS') . $theme . '.panels.top_5', ['tableHeader' => 'Top 10 (Most Viewed Items)', 'contents' => $top_viewed_items])--}}
        </div>
    </div>
</div>
@stop
