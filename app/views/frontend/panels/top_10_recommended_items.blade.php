<h5 class="text-center pb20">Top 10 Recommended Items Sold</h5>
<table class="table small" id="top10recommended">
	<tbody>
		<?php $i = 1; ?>
		@if (count($top_10_most_recommended_items) > 0)
		@foreach ($top_10_most_recommended_items as $data)
		<tr style="border: 0;">
			<!--<td>{{ $i }}</td>-->
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