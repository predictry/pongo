<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="{{asset("assets/img/favicon.ico")}}" type="image/x-icon">
        <link rel="icon" href="{{asset("assets/img/favicon.ico")}}" type="image/x-icon">
        <title>{{ $pageTitle or $siteName }} </title>

        <!-- Mobile Specific Metas
        ================================================== -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- CSS
        ================================================== -->
        {{ HTML::style('assets/inspinia/css/bootstrap.min.css') }}
        {{ HTML::style('assets/inspinia/font-awesome/css/font-awesome.min.css') }}
        {{ HTML::style('assets/inspinia/css/animate.css') }}
        {{ HTML::style('assets/inspinia/css/plugins/chosen/chosen.css') }}
        {{ HTML::style('assets/css/prism.css') }}
        {{ HTML::style('assets/inspinia/css/style.css') }}

        @if(count($styles) > 0)

        @foreach ($styles as $style)
        {{ $style or '' }}
        @endforeach

        @endif

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="gray-bg">
        <div id="page-wrapper" class="empty">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top  " role="navigation" style="margin-bottom: 0">
                    <ul class="nav navbar-top-links navbar-right">
                        <li>
                            <span class="m-r-sm text-muted welcome-message">Welcome to Predictry</span>
                        </li>
                        <li class="dropdown">
                            <a id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="javascript:void(0);"><i class="fa fa-wrench"></i> <?php echo Lang::get("panel.settings"); ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu text-right" role="menu" aria-labelledby="dLabel">
                                <?php if (Session::get("role") !== "member" && Session::get("active_site_id") !== null) : ?>
                                    <li><a href="<?php echo URL::to('v2/sites'); ?>"><?php echo Lang::get("panel.manage.sites"); ?></a></li>
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
            </div>
            @yield('content')
            @include(getenv('FRONTEND_SKINS') . $theme . '.partials.copyright')
        </div>
        @include(getenv('FRONTEND_SKINS') . $theme . '.partials.footer', array('custom_script' => $custom_script))
