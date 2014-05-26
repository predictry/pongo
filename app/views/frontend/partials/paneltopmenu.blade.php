<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container-fluid">
        <div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo URL::to('home'); ?>">{{ $siteName or '' }}</a>
			<ul class="nav navbar-nav navbar-left">
			</ul>
		</div>

		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav navbar-right">
				<li class="navbar-text"><?php echo Lang::get("panel.welcome"); ?>, <span class="displayName"> <?php echo Auth::user()->name; ?></span></li>
				{{--<li><a href="<?php echo URL::to('dashboard'); ?>"><?php echo Lang::get("panel.dashboard"); ?></a></li>--}}
				@if (count($sites) > 0)
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ $activeSiteName or '' }} <b class="caret"></b></a>
					<ul class="dropdown-menu">
						@if (count($sites) === 1 && Auth::user()->plan_id !== 3)
						<li><a href="{{ URL::to('sites/create') }}"><?php echo Lang::get("panel.create.site"); ?></a></li>
						@else
						@foreach ($sites as $site)
						@if ($site['id'] !== \Session::get("active_site_id"))
						<li><a href="{{ URL::to('sites/' . $site['id'] . '/default') }}">{{ $site['name'] }}</a></li>
						@endif
						@endforeach
						@endif
					</ul>
				</li>
				@endif
				<li class="dropdown">
					<a id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="javascript:void(0);"><i class="fa fa-wrench"></i> <?php echo Lang::get("panel.settings"); ?> <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
						<li><a href="<?php echo URL::to('profile'); ?>"><i class="fa fa-user"></i> <?php echo Lang::get("panel.edit.profile"); ?></a></li>
						<li><a href="<?php echo URL::to('password'); ?>"><i class="fa fa-lock"></i> <?php echo Lang::get("panel.edit.password"); ?></a></li>
						<?php if (Session::get("role") !== "member" && Session::get("active_site_id") !== null) : ?>
							<li><a href="<?php echo URL::to('sites'); ?>"><i class="fa fa-globe"></i> <?php echo Lang::get("panel.manage.sites"); ?></a></li>
							<li><a href="<?php echo URL::to('members'); ?>"><i class="fa fa-globe"></i> <?php echo Lang::get("panel.manage.members"); ?></a></li>
						<?php endif; ?>
						<li><a href="#"><i class="fa fa-puzzle-piece"></i> <?php echo Lang::get("panel.help"); ?></a></li>
						<li class="divider"></li>
						<li><a href="<?php echo URL::to('user/logout'); ?>"><i class="fa fa-power-off"></i> <?php echo Lang::get("panel.logout"); ?></a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</div>