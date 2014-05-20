<div class="row">

	<div class="col-sm-3">
		<div class="the-box no-border bg-success">
			<div class="tiles-inner text-center">
				<p class="tiles-title">Today Actions</p>
				<h1 class="bolded">{{ $overviews['today_total_actions'] }}</h1>
				<p class="tiles-sub-title">(total actions &amp; tracking received)</p>
			</div>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="the-box no-border bg-success">
			<div class="tiles-inner text-center">
				<p class="tiles-title">Today Items</p>
				<h1 class="bolded">{{ $overviews['today_total_items'] }}</h1>
				<p class="tiles-sub-title">(only new items of today)</p>
			</div>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="the-box no-border bg-success">
			<div class="tiles-inner text-center">
				<p class="tiles-title">Today Sales</p>
				<h1 class="bolded">{{ $overviews['today_total_buy_action'] }}</h1>
				<p class="tiles-sub-title">(taken from buy action)</p>
			</div>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="the-box no-border bg-success">
			<div class="tiles-inner text-center">
				<p class="tiles-title">Completion Rate</p>
				<h1 class="bolded">{{ $overviews['completion_rate'] }}%</h1>
				<p class="tiles-sub-title">Purchase flow of recommended item</p>
			</div>
		</div>
	</div>


</div>