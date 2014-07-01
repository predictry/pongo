<div class="col-sm-12">
	<div class="panel panel-default">
		<div class="panel-heading">
			<!-- Single button -->
			<div class="dropdown" id="range-type">
				<a class="dropdown-toggle" data-toggle="dropdown">
					{{ $comparison_list[0] }} <span class="caret"></span>
				</a>
				<ul class="dropdown-menu" role="menu">
					<li><a href="javascript:void(0);" onclick="setDefaultComparisonType('{{ $comparison_type_by[0] }}');">{{ $comparison_list[0] }}</a></li>
					<li><a href="javascript:void(0);" onclick="setDefaultComparisonType('{{ $comparison_type_by[1] }}');">{{ $comparison_list[1] }}</a></li>
				</ul>
				<!--				<ul class="dropdown-menu" role="menu">
									<li><a href="{{ URL::to('home2/'. $comparison_type_by[0] . '/' . $type . '/' . $type_by . '/'  . $dt_start . '/' . $dt_end) }}">{{ $comparison_list[0] }}</a></li>
									<li><a href="{{ URL::to('home2/'. $comparison_type_by[1] . '/' . $type . '/' . $type_by . '/'  . $dt_start . '/' . $dt_end) }}">{{ $comparison_list[1] }}</a></li>
								</ul>-->
			</div>
			<!--			<div class="btn-group pull-right" data-toggle="buttons" id="graph_type">
							<label class="btn btn-default btn-sm active">
								<input type="radio" name="options" class="options_graph_style" value="bar">  Bar
							</label>
							<label class="btn  btn-default btn-sm">
								<input type="radio" name="options" class="options_graph_style" value="line"> Line
							</label>
						</div>-->
						<!--<i class="fa fa-info-circle tt pull-right" data-toggle="tooltip" data-placement="left" title="Today stats of default actions"></i>-->
			<div class="clearfix"></div>
		</div>
		<div class="panel-body">
			<p class="pull-right text-info"></p>
			<div class="clearfix"></div>
			<script type="text/javascript">
								var graph_comparison_data = {{ $js_graph_comparison_data }};
//						var comparison_graph_y_keys = ['a', 'b'];
//						var comparison_graph_x_keys = 'y';
//						var comparison_labels = {{ $js_graph_labels }};
								var bar_type = {{ $bar_type }};
								var y_max = {{ $ymax }};
								var highchart_categories_data = {{ $js_highchart_categories_data }};
								var highchart_series_data = {{ $js_highchart_series_data }};
								var highchart_combination_graph_of_comparison = {{ $js_highchart_combination_graph_of_comparison }};
								var graph_title = "{{ $js_graph_title }}";
								var type = "{{ $type }}";
								var selected_comparison = "{{ $selected_comparison }}";
								var type_by = "{{ $type_by }}";
								var y_title = "{{ $y_title }}";
								var dt_start = "{{ $dt_start }}";
								var dt_end = "{{ $dt_end }}";
								var filters = {
								dt_start: dt_start,
										dt_end: dt_end,
										comparison_type: 'sales',
										date_unit: 'day'
								};

			</script>
			<!--<div id="comparisonGraph" style="height: 250px;"></div>-->
			<div id="highChartComparisonGraph"></div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="row panel-body">
			<div class="col-sm-6">
				<div id="comparisonDonut" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
			</div>
			<div class="col-sm-6">
				<div id="comparisonDonut2" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
			</div>
			{{-- <div class="col-sm-6">
				<h4>{{ ($selected_comparison === 'sales') ? $y_title . ' ' : '' }}{{ $total_overall }} Total {{ ucwords($selected_comparison) }}</h4>
			<h5>{{ ($selected_comparison === 'sales') ? $y_title . ' ' : '' }}{{ $total_regular }} Total Regular {{ ucwords($selected_comparison) }}</h5>
			<h5>{{ ($selected_comparison === 'sales') ? $y_title . ' ' : '' }}{{ $total_recommended }} Total Recommended {{ ucwords($selected_comparison) }}</h5>
		</div> --}}
	</div>
</div>
</div>