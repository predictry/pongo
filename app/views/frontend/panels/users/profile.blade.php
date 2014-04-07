@extends('frontend.layouts.dashboard')

@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	@include('frontend.partials.notification')
	{{ Form::open(array('url' => 'user/profile/submit', 'class' => 'form-horizontal loginForm')) }}
	<h2>Edit Profile</h2>
	<hr/>
	<div class="form-group {{$var = $errors->first('name')}} {{ ($var !== '') ? 'has-error' : ''}}">
		<label for="name" class="col-sm-2 control-label">Full Name</label>
		<div class="col-sm-4">
			<?php echo Form::text('name', Auth::user()->name, array('class' => 'form-control', 'placeholder' => 'fullname', 'id' => 'name')); ?>
		</div>
		<span class="help-block">{{$errors->first('name')}}</span>
	</div>

	<div class="form-group {{$var = $errors->first('email')}} {{ ($var !== '') ? 'has-error' : ''}}">
		<label for="name" class="col-sm-2 control-label">Email Address</label>
		<div class="col-sm-4">
			<?php echo Form::text('email', Auth::user()->email, array('class' => 'form-control', 'placeholder' => 'email address', 'id' => 'email')); ?>
		</div>
		<span class="help-block">{{$errors->first('email')}}</span>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-4">
			<button type="submit" class="btn btn-primary">Submit</button>
		</div>
	</div>
	{{ Form::close() }}
</div>
@stop