<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element"> <span>
                        <!--<img alt="image" class="img-circle" src="{{$user_info['gravatar_url']}}" />-->
                        <img alt="image" class="img-circle" src="{{asset('assets/img/no-avatar.jpeg')}}" />
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
                    <img src="{{asset("assets/img/logo-sm.png")}}"/>
                </div>
            </li>
            <li class="<?php if ($ca === 'App\Controllers\User\Panel2Controller') echo 'active'; ?>">
                <a href="<?php echo URL::to('v2/home'); ?>"><i class="fa fa-th-list"></i> <span class="nav-label">Dashboard </span> </a>
            </li>
            <!--<li class="<?php if ($ca === 'App\Controllers\User\Widgets2Controller' || $ca === 'App\Controllers\User\Filters2Controller' || $ca === 'App\Controllers\User\Rules2Controller') echo 'active'; ?>">
                <a href="javascript:void(0);"><i class="fa fa-send-o"></i> <span class="nav-label">Recommendations </span></a>
                <ul class="nav nav-second-level">
                    <li class="<?php if ($ca === 'App\Controllers\User\Widgets2Controller') echo 'active'; ?>"><a href="<?php echo URL::to('v2/widgets'); ?>"><i class="fa fa-list-ul"></i> Widgets</a></li>
                    <li class="<?php if ($ca === 'App\Controllers\User\Filters2Controller') echo 'active'; ?>"><a href="<?php echo URL::to('v2/filters'); ?>"><i class="fa fa-list-ul"></i> Filters</a></li>
                    <li class="<?php if ($ca === 'App\Controllers\User\Rules2Controller') echo 'active'; ?>"><a href="<?php echo URL::to('v2/rules'); ?>"><i class="fa fa-list-ul"></i> Rule Sets</a></li>
                    <li class="<?php if ($ca === 'App\Controllers\User\DemoController') echo 'active'; ?>"><a href="<?php echo URL::to('v2/demo'); ?>"><i class="fa fa-list-ul"></i> Demo</a></li>
                </ul>
            </li>

            <li class="<?php if ($ca === 'App\Controllers\User\Items2Controller') echo 'active'; ?>">
                <a href="<?php echo URL::to('v2/items'); ?>"><i class="fa fa-cubes"></i> <span class="nav-label">Your Items </span> </a>
            </li> -->
        </ul>

    </div>
</nav>
