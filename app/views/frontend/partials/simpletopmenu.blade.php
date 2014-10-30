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
        </div>

        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="javascript:void(0);"><?php echo strtolower(Auth::user()->email); ?> <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
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