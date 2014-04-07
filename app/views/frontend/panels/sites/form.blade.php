@extends('frontend.layouts.dashboard')
@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	@include('frontend.partials.notification')
	<?php if ($type === "create") : ?>
		{{ Form::open(array('url' => 'sites/submit', 'class' => 'form-horizontal loginForm')) }}
	<?php elseif ($type === "edit") : ?>
		{{ Form::model($site, array('route' => array('sites.update', $site->id), 'url' => 'sites/'.$site->id.'/edit', 'class' => 'form-horizontal siteForm')) }}
	<?php endif; ?>
	<a class="btn btn-default" href="{{ URL::previous(); }}"><i class="fa fa-reply"></i> Back</a>
	<h2>{{ $pageTitle or 'Form Site' }}</h2>
	<hr/>
	<div class="form-group {{$var = $errors->first('name')}} {{ ($var !== '') ? 'has-error' : ''}}">
		<label for="name" class="col-sm-2 control-label">Tenant ID</label>
		<div class="col-sm-4">
			<?php echo Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'TENANT_ID', 'id' => 'name')); ?>
		</div>
		<span class="help-block">{{$errors->first('name')}}</span>
	</div>

	<div class="form-group {{$var = $errors->first('url')}} {{ ($var !== '') ? 'has-error' : ''}}">
		<label for="email" class="col-sm-2 control-label">URL Address</label>
		<div class="col-sm-4">
			<?php echo Form::text('url', null, array('class' => 'form-control', 'placeholder' => 'http://www.website.com', 'id' => 'url')); ?>
		</div>
		<span class="help-block">{{$errors->first('url')}}</span>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-4">
			<button type="submit" class="btn btn-primary">Submit</button>
		</div>
	</div>
	{{ Form::close() }}
</div>
@stop