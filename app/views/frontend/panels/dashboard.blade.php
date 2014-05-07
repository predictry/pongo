@extends('frontend.layouts.dashboard')
@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	<h1 class="page-header">Tracking Stats ({{$activeSiteName}})</h1>
	@if($funel_selected_dropdown !== null)
	@include('frontend.panels.funelselector')	
	<script type="text/javascript">
		var graph_non_default_data = {{ $js_non_default_graph_stats_data }};
				var graph_y_non_default_keys = {{ $graph_y_non_defaulty_keys }};
				var graph_x_keys = '{{ $graph_x_keys }}';</script>	
	<div class="clearfix"></div>
	<div id="nonDefaultActionsChart" style="height: 250px;"></div>	
	<div class="clearfix mb20">&nbsp;</div>
	@else
	<p class="text-center alert alert-warning">Currently, you don't have any custom tracking action to show in chart :(.<br/> But don't worry, you can start sending it, and then start to create your own 	<a href="{{URL::to('panel/createFunel')}}">Funnel</a>. </p>
	@endif
	<h1 class="page-header">Action Stats ({{$activeSiteName}})</h1>
	<p class="pull-right">{{ $str_date_range }} </p>
	<script type="text/javascript">
				var graph_data = {{ $js_graph_stats_data }};
				var graph_y_keys = {{ $graph_y_keys }};
				var graph_x_keys = '{{ $graph_x_keys }}';</script>
	<div id="defaultActionsChart" style="height: 250px;"></div>
</div>
@include('frontend.partials.viewmodalnormal')	
@stop