<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element"> <span>
                        <img alt="image" class="img-circle" src="{{$user_info['gravatar_url']}}" />
                    </span>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold">{{Auth::user()->name}}</strong>
                            </span> <span class="text-muted text-xs block">Account <b class="caret"></b></span> </span> </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a href="<?php echo URL::to('v2/profile'); ?>">Profile</a></li>
                        <li><a href="<?php echo URL::to('v2/password'); ?>">Password</a></li>
                        <li class="divider"></li>
                        <li><a href="login.html">Logout</a></li>
                    </ul>
                </div>
                <div class="logo-element">
                    PT
                </div>
            </li>
            <li>
                <a href="<?php echo URL::to('v2/home'); ?>"><i class="fa fa-th-large"></i> <span class="nav-label">Dashboard </span> </a>
            </li>
        </ul>

    </div>
</nav>