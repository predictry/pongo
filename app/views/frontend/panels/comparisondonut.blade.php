<div class="col-sm-12 pull-left">
	<script type="text/javascript">
		var donut_average_recommended_items_data = {{ $js_donut_average_recommended_items_data }};
				var highchart_pie_data = {{ $js_highchart_pie_data }};
				var highchart_pageview_pie_data = {{ $js_highchart_pageview_pie_data }};
				var highchart_average_recommended_items_data = {{ $js_highchart_average_recommended_items_pie_data }};
				var highchart_average_recommended_sales_data = {{ $js_highchart_average_recommended_sales_pie_data }};
				var highchart_ctr_data = {{ $js_highchart_ctr_data }};
				var ctr_of_recommendation = {{ $js_ctr_of_recommendation }};
//				var ctr_of_recommendation = "41.5";
				@if ($ctr_data['ngr'] > 0)
				var ctr_percentage = "{{ number_format($ctr_data['nr'] / $ctr_data['ngr'], 2) * 100 }}";
				@ else
				var ctr_percentage = "0";
				@endif
	</script>
	<div class="row">
		<div class="col-sm-8">	
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="row">

								<div class="col-sm-6">
									<div id="ctrDonut" style="min-width: 220px; height: 220px; max-width: 220px; margin: 0 auto; opacity: 0.4;"></div>
									<div id="addText" style="position:absolute; left:27%; top:34%;"></div>
								</div>
								<div class="col-sm-6">
									<p style="font-size: 21px; margin-top: 50px;"><span class="percentageOfCTR">{{ $js_ctr_of_recommendation }}% Click Through Rate (CTR) on Recommendations</span></p>
									<p class="small cl-fade" id="ctrSummaryInfo">{{ $ctr_data['nr'] }} recommended items clicked out of {{ $ctr_data['ngr'] }} views</p>
									<!--<h5>{{ $ctr_data['ngr'] }} recommended products viewed</h5>-->
								</div>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-6">
									<!--<div id="averageRecommendedItemsDonut" style="min-width: 300px; height: 300px; max-width: 300px; margin: 0 auto"></div>-->
									<div id="itemInCartStat">
										<?php
										$percentage_of_qty		 = number_format(($average_cart_items['average_recommended_qty_items'] / $average_cart_items['average_regular_qty_items'] * 100), 2);
//										$percentage_of_qty		 = number_format($average_cart_items['average_recommended_qty_items'], 2);
										$whole					 = floor($percentage_of_qty);
										?>
										@if ($average_cart_items['average_regular_qty_items'] > 0)
										<span class="<?php echo (($percentage_of_qty - $whole) > 0) ? 'text-float2' : 'text-float'; ?> percentageOfAverageRecommendationQty">{{ $percentage_of_qty }}%</span>
										@else
										<span class="text-float percentageOfAverageRecommendationQty">0%</span>
										@endif
									</div>
								</div>
								<div class="col-sm-6">
									@if ($average_cart_items['average_regular_qty_items'] > 0)
									<p style="font-size: 21px; margin-top: 40px;"><span class="percentageOfAverageRecommendationQty">{{ $percentage_of_qty }}</span>% of the cart items are recommended items</p>
									@else
									<p style="font-size: 21px; margin-top: 40px;">0% of the item in the carts from recommended items</p>
									@endif
									<p class="small cl-fade" id="qtySummaryInfo">{{ $average_cart_items['average_recommended_qty_items'] }} out of {{ $average_cart_items['average_regular_qty_items'] }} items in the carts from recommended items</p>
								</div>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-6">
									<div id="itemSalesInCartStat">
										<?php
										$percentage_of_sub_items = number_format(($average_cart_items['average_recommended_sub_totals'] / $average_cart_items['average_regular_sub_totals'] * 100), 2);
//										$percentage_of_sub_items = $average_cart_items['average_recommended_sub_totals'];
										$whole					 = floor($percentage_of_sub_items);
										?>
										@if ($average_cart_items['average_regular_sub_totals'] > 0)
										<span class="<?php echo (($percentage_of_sub_items - $whole) > 0) ? 'text-float2' : 'text-float'; ?> percentageOfAverageRecommendationSales">{{ $percentage_of_sub_items }}%</span>
										@else
										<span class="text-float percentageOfAverageRecommendationSales">0%</span>
										@endif
									</div>
									<!--<div id="averageRecommendedSalesDonut" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>-->
								</div>
								<div class="col-sm-6">
									@if ($average_cart_items['average_regular_sub_totals'] > 0)
									<p style="font-size: 21px; margin-top: 40px;"><span class="percentageOfAverageRecommendationSales">{{ $percentage_of_sub_items }}</span>% of the cart value is made up of recommended items.</p>
									@else
									<p style="font-size: 21px; margin-top: 40px;"><span class="percentageOfAverageRecommendationSales">0%</span> of total sales in carts from recommended items.</p>
									@endif
									<p class="small cl-fade" id="salesSummaryInfo">RM {{ $average_cart_items['average_recommended_sub_totals'] }} out of RM {{ number_format($average_cart_items['average_regular_sub_totals'], 2) }} with recommended items</p>
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
