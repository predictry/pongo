<dl class="dl-horizontal">
	@foreach ($columns as $key => $val)
	<dt>{{ $val }}</dt>
	<dd>:<?php
		if ($key === "active")
		{
			$val = ($item->{$key}) ? "Yes" : "No";
			echo "<td>" . $val . "</td>";
		}
		else
			echo "<td>" . $item->{$key} . "</td>";
		?>
	</dd>
	@endforeach
</dl>
<div class="clearfix"></div>

<pre>
	<?php
	foreach ($properties as $p)
		echo $p->key . ":" . $p->value . "\n";
	?>
</pre>