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
        {{ HTML::style('assets/inspinia/css/style.css') }}
        <link media="all" type="text/css" rel="stylesheet" href="https://d2gq0qsnoi5tbv.cloudfront.net/assets/p.min.css"/>
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

        {{"<script type='text/javascript'>"}}
        {{ $api_credential_vars or 'var tenant_id = ""; var api_key=""; ' }}
        {{"</script>"}}

        <script type="text/javascript">
            var _predictry = _predictry || [];
            (function () {
                _predictry.push(['setTenantId', tenant_id], ['setApiKey', api_key]);
                var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
                g.type = 'text/javascript';
                g.defer = true;
                g.async = true;
//                g.src = '//d2gq0qsnoi5tbv.cloudfront.net/v3/p.min.js';
                g.src = '//dashboard.predictry.dev/v3-dev/p.js';
                s.parentNode.insertBefore(g, s);
            })();
        </script>
    </head>
    <body>
        <div id="wrapper">
            @if(Session::get("active_site_id") !== null)
            @include('admin.partials.panel_sidebar')
            @endif
            <div id="page-wrapper" class="gray-bg">
                <div class="row border-bottom">
                    @include(getenv('FRONTEND_SKINS') . $theme . '.partials.navbar_static_top')
                </div>
                @yield('content')
                @include(getenv('FRONTEND_SKINS') . $theme . '.partials.copyright')
            </div>

        </div>
        @include(getenv('FRONTEND_SKINS') . $theme . '.partials.footer', array('custom_script' => $custom_script))
