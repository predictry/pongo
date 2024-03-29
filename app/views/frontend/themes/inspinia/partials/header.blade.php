@section('header')
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="{{asset("assets/img/favicon.ico")}}" type="image/x-icon">
        <link rel="icon" href="{{asset("assets/img/favicon.ico")}}" type="image/x-icon">
        <title>{{ $pageTitle or $siteName }} </title>

        <!-- Mainly scripts -->
        {{ HTML::script('assets/inspinia/js/jquery-2.1.1.js') }}
        {{ HTML::script('assets/inspinia/js/bootstrap.min.js') }}
        {{ HTML::script('assets/inspinia/js/plugins/metisMenu/jquery.metisMenu.js') }}
        {{ HTML::script('assets/inspinia/js/plugins/slimscroll/jquery.slimscroll.min.js') }}

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
        {{ HTML::script('assets/js/d3.js') }}
        {{ HTML::script('assets/js/metricsgraphics.js') }}

        @if (isset($styles))
            @foreach ($styles as $style)
                {{ $style or '' }}
            @endforeach
        @endif

        @if (isset($extraStyles))
            @foreach ($extraStyles as $style)
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
    <body>
        @show
