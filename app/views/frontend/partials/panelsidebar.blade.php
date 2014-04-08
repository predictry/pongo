<div class="col-sm-3 col-md-2 sidebar">
	<ul class="nav nav-sidebar">
		<li class="<?php if ($ca === 'user\PanelController') echo 'active'; ?>"><a href="{{ URL::route('dashboard'); }}">Overview</a></li>
		<li class="<?php if ($ca === 'user\SitesController') echo 'active'; ?>"><a href="{{ URL::route('sites'); }}"><i class="fa fa-briefcase"></i> Sites</a></li>
		<li class="<?php if ($ca === 'user\MembersController') echo 'active'; ?>"><a href="{{ URL::route('members'); }}"><i class="fa fa-users"></i> Members</a></li>
		<li class="<?php if ($ca === 'user\ItemsController') echo 'active'; ?>"><a href="{{ URL::route('items'); }}"><i class="fa fa-exchange"></i> Items & Rules</a></li>
		<li class="<?php if ($ca === 'user\StatisticsController') echo 'active'; ?>"><a href="{{ URL::route('statistics'); }}"><i class="fa fa-signal"></i> Statistics</a></li>
	</ul>
	<!--	<ul class="nav nav-sidebar">
			<li><a href="">Nav item</a></li>
		</ul>-->
</div>