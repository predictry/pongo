@extends('frontend.layouts.dashboard')
@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	<div class="page-header">
		<h1>Overview <small>({{$activeSiteName}})</small></h1>
	</div>
	@include('frontend.panels.dashboard.overviewsummary')	

	<div class="row">
 		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					Tracking Stats
					<i class="fa fa-info-circle tt pull-right" data-toggle="tooltip" data-placement="top" title="The stats from selected funnel data"></i>
				</div>
				<div class="panel-body">
					@if($funel_selected_dropdown !== null)
					@include('frontend.panels.funelselector')	
					<script type="text/javascript">
						var graph_non_default_data = {{ $js_non_default_graph_stats_data }};
								var graph_y_non_default_keys = {{ $graph_y_non_defaulty_keys }};
								var graph_x_keys = '{{ $graph_x_keys }}';</script>	
					<div class="clearfix"></div>
					<div id="nonDefaultActionsChart" style="height: 250px;"></div>	
					<!--<div class="clearfix mb20">&nbsp;</div>-->
					@else
					<p class="text-center alert alert-warning">Currently, you don't have any custom tracking action to show in chart :(.<br/> But don't worry, you can start sending it, and then start to create your own 	<a href="{{URL::to('panel/createFunel')}}">Funnel</a>. </p>
					@endif
				</div>
			</div>
		</div>

		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					Action Stats
					<i class="fa fa-info-circle tt pull-right" data-toggle="tooltip" data-placement="left" title="Today stats of default actions"></i>
				</div>
				<div class="panel-body">
					<p class="pull-right text-info">{{ $str_date_range }} </p>
					<div class="clearfix"></div>
					<script type="text/javascript">
								var graph_data = {{ $js_graph_stats_data }};
								var graph_y_keys = {{ $graph_y_keys }};
								var graph_x_keys = '{{ $graph_x_keys }}';</script>
					<div id="defaultActionsChart" style="height: 250px;"></div>
				</div>
			</div>
		</div>
	</div>
	@include('frontend.panels.dashboard.trendssumary')	

	<div class="row">
		@include('frontend.panels.dashboard.top5', array('title'=>'Top 5 (Most Viewed Items)', 'contents'=>$top_viewed_items))
		@include('frontend.panels.dashboard.top5', array('title'=>'Top 5 (Most Purchased Items)', 'contents'=>$top_purchased_items))
	</div>




</div>
@include('frontend.partials.viewmodalnormal')	
@stop