<div class="col-sm-3 col-md-2 sidebar">
	<?php if (Session::get("role") !== "member") : ?>
		<?php // if (Auth::user()->plan_id !== 3) : ?>
<!--			<div class="text-center">
				<a class="btn btn-success text-center" href="{{ URL::to('widgets/wizard'); }}"><?php // echo Lang::get("panel.create.recommendation"); ?></a>
			</div>
			<div class="clearfix mb20"></div>-->
		<?php // endif; ?>
		<ul class="nav nav-sidebar">
			<li class="<?php if ($ca === 'App\Controllers\User\PanelController') echo 'active'; ?>"><a href="{{ URL::to('home'); }}"><?php echo Lang::get("panel.dashboard"); ?></a></li>
			<li class="<?php if ($ca === 'App\Controllers\User\PlacementsController') echo 'active'; ?>"><a href="{{ URL::to('widgets'); }}"><?php echo Lang::get("panel.recommendations"); ?></a></li>
			<li class="<?php if ($ca === 'App\Controllers\User\ItemsController') echo 'active'; ?>"><a href="{{ URL::to('items'); }}"><?php echo Lang::get("panel.your.items"); ?></a></li>
			<!--<li class="<?php // if ($ca === 'App\Controllers\User\MembersController') echo 'active';  ?>"><a href="{{ URL::to('members'); }}"><i class="fa fa-users"></i> <?php echo Lang::get("panel.members"); ?></a></li>-->
<!--			<li class="<?php if ($ca === 'App\Controllers\User\ItemsController') echo 'active'; ?>"><a href="{{ URL::to('items'); }}"><i class="fa fa-exchange"></i> <?php echo Lang::get("panel.items"); ?></a></li>
			<li class="<?php if ($ca === 'App\Controllers\User\StatisticsController') echo 'active'; ?>"><a href="{{ URL::to('statistics'); }}"><i class="fa fa-signal"></i> <?php echo Lang::get("panel.statistics"); ?></a></li>
			<li class="<?php if ($ca === 'App\Controllers\User\ActionsController') echo 'active'; ?>"><a href="{{ URL::to('actions'); }}"><i class="fa fa-tasks"></i> <?php echo Lang::get("panel.actions"); ?></a></li>
			<?php if (Auth::user()->plan_id !== 3) : ?>
				<li class="<?php if ($ca === 'App\Controllers\User\RulesController') echo 'active'; ?>"><a href="{{ URL::to('rules'); }}"><i class="fa fa-beer"></i> <?php echo Lang::get("panel.rulesets"); ?></a></li>
				<li class="<?php if ($ca === 'App\Controllers\User\FiltersController') echo 'active'; ?>"><a href="{{ URL::to('filters'); }}"><i class="fa fa-filter"></i> <?php echo Lang::get("panel.filters"); ?></a></li>
			<?php endif; ?>
			<li class="<?php if ($ca === 'App\Controllers\User\PlacementsController') echo 'active'; ?>"><a href="{{ URL::to('widgets'); }}"><i class="fa fa-tasks"></i> <?php echo Lang::get("panel.widgets"); ?></a></li>-->
		</ul>
	<?php else: ?>
		<ul class="nav nav-sidebar">
			<li class="<?php if ($ca === 'App\Controllers\User\PanelController') echo 'active'; ?>"><a href="{{ URL::to('home'); }}"><?php echo Lang::get("panel.overview"); ?></a></li>
		</ul>
	<?php endif; ?>
	<!--	<ul class="nav nav-sidebar">
			<li><a href="">Nav item</a></li>
		</ul>-->
</div>
