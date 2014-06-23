<h4>Top 10 Recommended Items</h4>
<br/>
<div class="form-group text-left pull-right">
	<!-- Single button -->
	<div class="btn-group" id="range-type">
		<button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown">
			{{ ucwords($selected_comparison) }} <span class="caret"></span>
		</button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li><a href="{{ URL::to('home2/'. $comparison_type_by[0] . '/' . $type . '/' . $type_by . '/'  . $dt_start . '/' . $dt_end) }}">{{ ucwords($comparison_type_by[0]) }}</a></li>
			<li><a href="{{ URL::to('home2/'. $comparison_type_by[1] . '/' . $type . '/' . $type_by . '/'  . $dt_start . '/' . $dt_end) }}">{{ ucwords($comparison_type_by[1]) }}</a></li>
		</ul>
	</div>
</div>
<table class="table table-bordered small">
	<thead>
		<tr>
			<th>No.</th>
			<th>Name</th>
			<th>IMG</th>
		</tr>
	</thead>

	<tbody>
		<?php $i = 1; ?>
		@if (count($top_10_most_recommended_items) > 0)
		@foreach ($top_10_most_recommended_items as $data)
		<tr>
			<td>{{ $i }}</td>
			<td>{{ $data['item']['name'] }}</td>
			<td>
				@if (isset($data['item_metas']))
				<img src="{{ $data['item_metas']['value'] }}" height="32" width="32"/>
				@else
				<img src="holder.js/32x32/#000:#fff/text:?">
				@endif
			</td>
		</tr>
		<?php $i++; ?>
		@endforeach
		@else
		<tr>
			<td colspan="3" class="text-center">No Recommended Item Available</td>
		</tr>
		@endif

	</tbody>
</table>