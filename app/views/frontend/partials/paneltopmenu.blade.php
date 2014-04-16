<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container-fluid">
        <div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo URL::to('dashboard'); ?>">{{ $siteName or '' }}
				/ {{ $activeSiteName or '' }}
			</a>
        </div>

        <div class="navbar-collapse collapse">
			<ul class="nav navbar-nav navbar-right">
				<li><a href="<?php echo URL::to('dashboard'); ?>">Dashboard</a></li>
				<li class="dropdown">
					<a id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="javascript:void(0);">Welcome, <span class="displayName"> <?php echo Auth::user()->name; ?> </span> <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        <li><a href="<?php echo URL::route('profile'); ?>">Edit Profile</a></li>
                        <li><a href="<?php echo URL::route('password'); ?>">Edit Password</a></li>
						<li><a href="<?php echo URL::route('sites'); ?>">Manage Sites</a></li>
                        <li><a href="#">Help</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo URL::to('user/logout'); ?>">Logout</a></li>
					</ul>
				</li>
			</ul>
        </div>
	</div>
</div>