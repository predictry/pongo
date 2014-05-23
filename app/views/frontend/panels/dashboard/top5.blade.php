<div class="col-sm-6">
	<div class="panel panel-default">
		<div class="panel-heading">
			{{ $title }}
			<i class="fa fa-info-circle tt pull-right" data-toggle="tooltip" data-placement="top" title="The stats from selected funnel data"></i>
		</div>
		<!-- Table -->
		<table class="table table-bordered small">
			<thead>
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$i = 1;
				foreach ($contents as $item)
				{
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $item['name']; ?></td>
						<td><?php echo $item['total']; ?></td>
					</tr>
					<?php
					$i++;
				}	
				?>
			</tbody>
		</table>
	</div>
</div>