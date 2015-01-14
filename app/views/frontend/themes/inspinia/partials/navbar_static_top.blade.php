<nav class="navbar navbar-static-top  " role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">
        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="javascript:void(0);"><i class="fa fa-bars"></i> </a>
    </div>
    <ul class="nav navbar-top-links navbar-right">
        <li>
            <span class="m-r-sm text-muted welcome-message">Welcome to Predictry</span>
        </li>
        <li class="dropdown">
            <a id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="javascript:void(0);"><i class="fa fa-wrench"></i> <?php echo Lang::get("panel.settings"); ?> <span class="caret"></span></a>
            <ul class="dropdown-menu text-right" role="menu" aria-labelledby="dLabel">
                <?php if (Session::get("role") !== "member" && Session::get("active_site_id") !== null) : ?>
                    <li><a href="<?php echo URL::to('v2/sites'); ?>"><?php echo Lang::get("panel.manage.sites"); ?></a></li>
                    <!--<li><a href="<?php // echo URL::to('members'); ?>"><?php // echo Lang::get("panel.manage.members"); ?></a></li>-->
                <?php endif; ?>
                <li><a href="#"><?php echo Lang::get("panel.help"); ?></a></li>
            </ul>
        </li>
        <li>
            <a href="<?php echo URL::to('user/logout'); ?>">
                <i class="fa fa-sign-out"></i> Log out
            </a>
        </li>
    </ul>

</nav>