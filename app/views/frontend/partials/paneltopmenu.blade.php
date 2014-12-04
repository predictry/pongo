<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <!--<a class="navbar-brand" href="<?php echo URL::to('home'); ?>">{{ $siteName or '' }}</a>-->
            <ul class="nav navbar-nav navbar-left">
                @if (count($sites) > 0)
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ $activeSiteName or '' }} <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        @if (count($sites) === 1 && Auth::user()->plan_id !== 3)
                        <li><a href="{{ URL::to('sites/create') }}"><i class="fa fa-plus"></i> <?php echo Lang::get("panel.create.site"); ?></a></li>
                        @else
                        @foreach ($sites as $site)
                        @if ($site['id'] !== \Session::get("active_site_id"))
                        <li><a href="{{ URL::to('sites/' . $site['id'] . '/default') }}">{{ $site['name'] }}</a></li>
                        @endif
                        @endforeach
                        <li class="divider"></li>
                        <li><a href="{{ URL::to('sites/create') }}"><i class="fa fa-plus"></i> <?php echo Lang::get("panel.create.site"); ?></a></li>
                        @endif
                    </ul>
                </li>
                @endif

            </ul>
        </div>

        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                    <!--<li class="navbar-text"><?php // echo Lang::get("panel.welcome");             ?>, <span class="displayName"> <?php // echo Auth::user()->name;             ?></span></li>-->
                <li class="dropdown">
                    <a id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="javascript:void(0);"><?php echo Lang::get("panel.settings"); ?> <span class="caret"></span></a>
                    <ul class="dropdown-menu text-right" role="menu" aria-labelledby="dLabel">
                        <?php if (Session::get("role") !== "member" && Session::get("active_site_id") !== null) : ?>
                            <li><a href="<?php echo URL::to('sites'); ?>"><?php echo Lang::get("panel.manage.sites"); ?></a></li>
                            <li><a href="<?php echo URL::to('members'); ?>"><?php echo Lang::get("panel.manage.members"); ?></a></li>
                        <?php endif; ?>
                        <li><a href="#"><?php echo Lang::get("panel.help"); ?></a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="javascript:void(0);"><?php echo strtolower(Auth::user()->email); ?> <span class="caret"></span></a>
                    <ul class="dropdown-menu text-right" role="menu" aria-labelledby="dLabel">
                        <li><a href="<?php echo URL::to('profile'); ?>"> <?php echo Lang::get("panel.edit.profile"); ?></a></li>
                        <li><a href="<?php echo URL::to('password'); ?>"><?php echo Lang::get("panel.edit.password"); ?></a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo URL::to('user/logout'); ?>"><?php echo Lang::get("panel.logout"); ?></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
