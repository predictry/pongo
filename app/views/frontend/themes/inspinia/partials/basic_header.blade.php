@section('header')
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="shortcut icon" href="{{asset("assets/img/favicon.ico")}}" type="image/x-icon">
        <link rel="icon" href="{{asset("assets/img/favicon.ico")}}" type="image/x-icon">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $pageTitle or $siteName }} </title>

        <!-- Mobile Specific Metas
        ================================================== -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- CSS
        ================================================== -->
        {{ HTML::style('assets/inspinia/css/bootstrap.min.css') }}
        {{ HTML::style('assets/inspinia/font-awesome/css/font-awesome.min.css') }}
        {{ HTML::style('assets/inspinia/css/animate.css') }}
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
        @show