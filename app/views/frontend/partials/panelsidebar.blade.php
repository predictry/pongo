<div class="col-sm-3 col-md-2 sidebar">
	<ul class="nav nav-sidebar">
		<li class="<?php if ($ca === 'App\Controllers\User\PanelController') echo 'active'; ?>"><a href="{{ URL::route('dashboard'); }}">Overview</a></li>
		<li class="<?php if ($ca === 'App\Controllers\User\MembersController') echo 'active'; ?>"><a href="{{ URL::route('members'); }}"><i class="fa fa-users"></i> Members</a></li>
		<li class="<?php if ($ca === 'App\Controllers\User\ItemsController') echo 'active'; ?>"><a href="{{ URL::route('items'); }}"><i class="fa fa-exchange"></i> Items</a></li>
		<li class="<?php if ($ca === 'App\Controllers\User\RulesController') echo 'active'; ?>"><a href="{{ URL::route('rules'); }}"><i class="fa fa-beer"></i> Rulesets</a></li>
		<li class="<?php if ($ca === 'App\Controllers\User\StatisticsController') echo 'active'; ?>"><a href="{{ URL::route('statistics'); }}"><i class="fa fa-signal"></i> Statistics</a></li>
		<li class="<?php if ($ca === 'App\Controllers\User\ActionsController') echo 'active'; ?>"><a href="{{ URL::route('actions'); }}"><i class="fa fa-tasks"></i> Actions</a></li>
	</ul>
	<!--	<ul class="nav nav-sidebar">
			<li><a href="">Nav item</a></li>
		</ul>-->
</div>