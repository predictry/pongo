<thead>
	<tr>
		<?php
		foreach ($trends_data['header'] as $head)
		{
			echo "<th>" . ucwords($head) . "</th>";
		}
		?>
	</tr>
</thead>

<tbody>

	<?php
	foreach ($trends_data['data'] as $trend)
	{
		$changes_cls = '';

		if ($trend['changes'] > 0)
			$changes_cls = 'text-success';
		else if ($trend['changes'] < 0)
			$changes_cls = 'text-danger';
		?>
		<tr>
			<td><?php echo $trend['#']; ?></td>
			<td><?php echo $trend['name']; ?></td>
			<td><?php echo $trend['after']; ?></td>
			<td><?php echo $trend['before']; ?></td>
			<td class="<?php echo $changes_cls; ?>"><?php echo $trend['changes']; ?>%</td>
		</tr>
		<?php
	}
	?>
</tbody>