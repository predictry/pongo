@extends('frontend.layouts.dashboard')
@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <div class="page-header">
        <h1>Overview <small>({{$activeSiteName}}) - {{$dt_range['start']}} / {{$dt_range['end']}}</small></h1>
    </div>
    @include('frontend.panels.dashboard.overviewsummary2')	
</div>
@stop