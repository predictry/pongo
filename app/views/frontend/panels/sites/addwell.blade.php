@extends('frontend.layouts.blankdashboard')
@section('content')
<div class="col-xs-12 main">
	<div class="text-center">
		<h1>Welcome to Predictry.</h1>
		<div class="row">
			<div class="col-xs-offset-3 col-xs-6">
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reiciendis, cumque soluta fugit ullam officia rem possimus modi! Inventore, esse soluta repellendus minus deleniti totam molestiae minima nemo voluptatibus tenetur incidunt?</p>
			</div>
		</div>
	</div>
	<div class="well-create-button-container text-center">
		<a data-toggle="modal" id="btnViewModal" data-target="#viewModal" href=" {{ URL::to("sites/getModal") }} " class="btn btn-success btn-lg btnViewModal tt">Add New Site</a>
	</div>
</div>
@include('frontend.partials.viewmodalnormal')	
@stop