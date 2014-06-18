<div class="col-sm-12 pull-left">

	<script type="text/javascript">
		var donut_average_recommended_items_data = {{ $js_donut_average_recommended_items_data }};
				var highchart_pie_data = {{ $js_highchart_pie_data }};</script>
	<div class="row">
		<div class="col-sm-4 pull-left">
			<div id="comparisonDonut"></div>
		</div>

		<div class="col-sm-4">
			<h4>{{ ($selected_comparison === 'sales') ? '$ ' : '' }}{{ $total_overall }} Total {{ ucwords($selected_comparison) }}</h4>
			<h5>{{ ($selected_comparison === 'sales') ? '$ ' : '' }}{{ $total_regular }} Total Regular {{ ucwords($selected_comparison) }}</h5>
			<h5>{{ ($selected_comparison === 'sales') ? '$ ' : '' }}{{ $total_recommended }} Total Recommended {{ ucwords($selected_comparison) }}</h5>
		</div>
	</div>
</div>
<div class="col-sm-12 pull-left">
	<div class="well">	
		<div class="row">
			<div class="col-sm-4 pull-left">
				<div id="averageRecommendedItemsDonut"></div>
			</div>
			<div class="col-sm-8">
				<h3>Average Cart Items with Recommended Items</h3>
				<h4>{{ $average_cart_items['average_recommended_qty_items'] }} items in carts with recommended items</h4>
				<h5>{{ $average_cart_items['average_regular_qty_items'] }} items in carts without recommended items</h5>
			</div>
		</div>
	</div>
</div>
