<div class="col-sm-3 col-md-2 sidebar">
	<?php if (Session::get("role") !== "member") : ?>
		<ul class="nav nav-sidebar">
			<li class="<?php if ($ca === 'App\Controllers\User\PanelController') echo 'active'; ?>"><a href="{{ URL::to('dashboard'); }}">Overview</a></li>
			<li class="<?php if ($ca === 'App\Controllers\User\MembersController') echo 'active'; ?>"><a href="{{ URL::to('members'); }}"><i class="fa fa-users"></i> Members</a></li>
			<li class="<?php if ($ca === 'App\Controllers\User\ItemsController') echo 'active'; ?>"><a href="{{ URL::to('items'); }}"><i class="fa fa-exchange"></i> Items</a></li>
			<li class="<?php if ($ca === 'App\Controllers\User\RulesController') echo 'active'; ?>"><a href="{{ URL::to('rules'); }}"><i class="fa fa-beer"></i> Rulesets</a></li>
			<li class="<?php if ($ca === 'App\Controllers\User\StatisticsController') echo 'active'; ?>"><a href="{{ URL::to('statistics'); }}"><i class="fa fa-signal"></i> Statistics</a></li>
			<li class="<?php if ($ca === 'App\Controllers\User\ActionsController') echo 'active'; ?>"><a href="{{ URL::to('actions'); }}"><i class="fa fa-tasks"></i> Actions</a></li>
			<li class="<?php if ($ca === 'App\Controllers\User\PlacementsController') echo 'active'; ?>"><a href="{{ URL::to('placements'); }}"><i class="fa fa-tasks"></i> Placements</a></li>
			<li class="<?php if ($ca === 'App\Controllers\User\PlacementsController@getWizard') echo 'active'; ?>"><a href="{{ URL::to('placements/wizard'); }}"><i class="fa fa-tasks"></i> Recommendation Wizard</a></li>
		</ul>
	<?php else: ?>
		<ul class="nav nav-sidebar">
			<li class="<?php if ($ca === 'App\Controllers\User\PanelController') echo 'active'; ?>"><a href="{{ URL::to('dashboard'); }}">Overview</a></li>
		</ul>

	<?php endif; ?>
	<!--	<ul class="nav nav-sidebar">
			<li><a href="">Nav item</a></li>
		</ul>-->
</div>