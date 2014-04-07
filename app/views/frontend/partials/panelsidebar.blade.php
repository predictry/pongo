<div class="col-sm-3 col-md-2 sidebar">
	<ul class="nav nav-sidebar">
		<li class="<?php if ($ca === 'user\PanelController') echo 'active'; ?>"><a href="{{ URL::route('dashboard'); }}">Overview</a></li>
		<li class="<?php if ($ca === 'user\SitesController') echo 'active'; ?>"><a href="{{ URL::route('sites'); }}">Sites</a></li>
		<li class="<?php if ($ca === 'user\MembersController') echo 'active'; ?>"><a href="{{ URL::route('members'); }}">Members</a></li>
		<!--<li class="<?php if ($ca === 'user\ReportsController') echo 'active'; ?>"><a href="{{ URL::route('members'); }}">Reports</a></li>-->
	</ul>
	<!--	<ul class="nav nav-sidebar">
			<li><a href="">Nav item</a></li>
		</ul>-->
</div>