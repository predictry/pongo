@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard')
@section('content')
<!--@include(getenv('FRONTEND_SKINS') . $theme . '.partials.page_heading_without_action')-->
<div class="wrapper wrapper-content">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>Overview Summary of ({{$activeSiteName}})</h5>
            <div class="pull-right">
                @include(getenv('FRONTEND_SKINS') . $theme . '.panels.filter_date')
                <!--<span class="">{{$dt_start->toFormattedDateString()}} to {{$dt_end->toFormattedDateString()}}</span>-->
            </div>
        </div>
    </div>
    @include(getenv('FRONTEND_SKINS') . $theme . '.panels.overview_summary')
    <div class="row">
        <div class="col-lg-6">
            @include(getenv('FRONTEND_SKINS') . $theme . '.panels.top_5', ['tableHeader' => 'Top 10 (Most Purchased Items)', 'contents' => $top_purchased_items])
        </div>
        <div class="col-lg-6">
            @include(getenv('FRONTEND_SKINS') . $theme . '.panels.top_5', ['tableHeader' => 'Top 10 (Most Viewed Items)', 'contents' => $top_viewed_items])
        </div>
    </div>
</div>
@stop
