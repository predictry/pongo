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
                        <li>
                            <a href="<?php echo URL::to('user/logout'); ?>">
                            Logout</a>
                        </li>
                    </ul>
                </div>
                <div class="logo-element">
                    <img src="{{asset("assets/img/logo-sm.png")}}"/>
                </div>
            </li>
            <li class="<?php if ($ca === 'App\Controllers\User\Panel2Controller') echo 'active'; ?>">
                <a href="<?php echo URL::to('v2/home'); ?>"><i class="fa fa-th-list"></i> <span class="nav-label">Dashboard </span> </a>
            </li>
          
            <li class="<?php if ($ca === 'App\Controllers\User\Sites2Controller') echo 'active'; ?>">
                <a href="#"><i class="fa fa-cogs"></i> <span class="nav-label">Integration</span>
                <ul class="nav nav-second-level">
                  <li>
                    <a href="/v2/sites/{{ $current_site }}/integration"><span class="nav-label">Javascript <i class="fa fa-code"></i> </span> </a>
                  <li>
                  <li>
                      <a href="/v2/sites/{{ $current_site }}/woocommerce"><span class="nav-label">WooCommerce </span></a>
                  </li>
                  <li>
                      <a href="/v2/sites/{{ $current_site }}/magento"><span class="nav-label">Magento </span></a>
                  </li>
                </ul>
                </a>
            </li>
            @if (array_search(Auth::user()->email, ['jocki.predictry@gmail.com', 'jocki@vventures.asia', 'prawn9189@hotmail.com', 'stewart@vventures.asia', 'stewartchen@gmail.com']))
            <li class="<?php if ($ca === 'App\Controllers\Email\EmailTargeting') echo 'active'; ?>">
                <a href="/v2/email/home"><i class="fa fa-envelope"></i> <span class="nav-label">Email Targeting<span class="badge badge-primary">Beta</span></span> </a>
            </li>
            @endif
            
            <!--
            <li class="<?php if ($ca === 'App\Controllers\User\Items2Controller') echo 'active'; ?>">
                <a href="<?php echo URL::to('v2/items'); ?>"><i class="fa fa-table"></i> <span class="nav-label">Your Items</span> </a>
            </li>
            -->
        </ul>

    </div>
</nav>
