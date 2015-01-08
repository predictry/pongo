@extends('frontend.layouts.dashboard')
@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	<div class="page-header">
		<h1>Overview <small>({{$activeSiteName}})</small></h1>
	</div>
	<div class="row">
		@include('frontend.panels.ajaxcomparisonfilter')	
		@include('frontend.panels.comparisongraph')	
		@include('frontend.panels.comparisondonut')	
	</div>
</div>
@include('frontend.partials.viewmodalnormal')	
@stop