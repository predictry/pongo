<div class="col-sm-12 pull-left">
	<script type="text/javascript">
		var donut_average_recommended_items_data = {{ $js_donut_average_recommended_items_data }};
				var highchart_pie_data = {{ $js_highchart_pie_data }};
				var highchart_average_recommended_items_data = {{ $js_highchart_average_recommended_items_pie_data }};
				var highchart_average_recommended_sales_data = {{ $js_highchart_average_recommended_sales_pie_data }};
				var highchart_ctr_data = {{ $js_highchart_ctr_data }};
				var ctr_of_recommendation = {{ $js_ctr_of_recommendation }};
	</script>
	<div class="row">
		<div class="col-sm-8">	
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="row">

								<div class="col-sm-6">
									<div id="ctrDonut" style="min-width: 300px; height: 300px; max-width: 300px; margin: 0 auto"></div>
									<div id="addText" style="position:absolute; left:0px; top:0px;"></div>
								</div>
								<div class="col-sm-6">
									<h4>{{ $ctr_data['nr'] }} recommended products clicked</h4>
									<h5>{{ $ctr_data['ngr'] }} recommended products viewed</h5>
								</div>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-6">
									<div id="averageRecommendedItemsDonut" style="min-width: 300px; height: 300px; max-width: 300px; margin: 0 auto"></div>
								</div>
								<div class="col-sm-6">
									<h4>{{ $average_cart_items['average_recommended_qty_items'] }} items in carts with recommended items</h4>
									<h5>{{ $average_cart_items['average_regular_qty_items'] }} items in carts without recommended items</h5>
								</div>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-6">
									<div id="averageRecommendedSalesDonut" style="min-width: 300px; height: 300px; max-width: 300px; margin: 0 auto"></div>
								</div>
								<div class="col-sm-6">
									<h4>${{ $average_cart_items['average_recommended_sub_totals'] }} for carts with recommended items</h4>
									<h5>${{ $average_cart_items['average_regular_sub_totals'] }} for carts without recommended items</h5>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-sm-4">
			@include('frontend.panels.top_10_recommended_items')
		</div>
	</div>
</div>
