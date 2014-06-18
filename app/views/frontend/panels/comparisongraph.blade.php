<div class="col-sm-12">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="btn-group pull-right" data-toggle="buttons" id="graph_type">
				<label class="btn btn-default btn-sm active">
					<input type="radio" name="options" class="options_graph_style" value="bar">  Bar
				</label>
				<label class="btn  btn-default btn-sm">
					<input type="radio" name="options" class="options_graph_style" value="line"> Line
				</label>
			</div>
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
						var graph_title = "{{ $js_graph_title }}";
			</script>
			<!--<div id="comparisonGraph" style="height: 250px;"></div>-->
			<div id="highChartComparisonGraph"></div>

		</div>
	</div>
</div>