<script type="text/javascript">
	var graph_non_default_data = {{ $js_non_default_graph_stats_data }};
			var graph_y_non_default_keys = {{ $graph_y_non_defaulty_keys }};
			var graph_x_keys = '{{ $graph_x_keys }}';
	if (typeof graph_non_default_data !== 'undefined') {
		new Morris.Line({
			element: 'nonDefaultActionsChart',
			data: graph_non_default_data,
			xkey: graph_x_keys,
			ykeys: graph_y_non_default_keys,
			labels: graph_y_non_default_keys,
			xLabels: "day"
		});
	}
</script>	