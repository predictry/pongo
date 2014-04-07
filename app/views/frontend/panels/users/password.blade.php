@extends('frontend.layouts.dashboard')

@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	@include('frontend.partials.notification')
	{{ Form::open(array('url' => 'user/password/submit', 'class' => 'form-horizontal loginForm')) }}
	<h2>Edit Password</h2>
	<hr/>
	<!-- Password input-->
	<div class="form-group {{$var = $errors->first('password')}} {{ ($var !== '') ? 'has-error' : ''}}">
		<label class="col-sm-2 control-label" for="password">Current Password</label>
		<div class="col-sm-4">
			<?php echo Form::password('password', array('class' => 'form-control', 'placeholder' => 'current password', 'id' => 'password', 'required' => '')); ?>
		</div>
		<span class="help-block">{{$errors->first('password')}}</span>
	</div>

	<!-- Password input-->
	<div class="form-group {{$var = $errors->first('new_password')}} {{ ($var !== '') ? 'has-error' : ''}}">
		<label class="col-sm-2 control-label" for="new_password">New Password</label>
		<div class="col-sm-4">
			<?php echo Form::password('new_password', array('class' => 'form-control', 'placeholder' => 'new password', 'id' => 'new_password', 'required' => '')); ?>
		</div>
		<span class="help-block">{{$errors->first('new_password')}}</span>
	</div>

	<!-- Password input-->
	<div class="form-group {{$var = $errors->first('new_password_confirmation')}} {{ ($var !== '') ? 'has-error' : ''}}">
		<label class="col-sm-2 control-label" for="new_password_confirmation">Confirm Password</label>
		<div class="col-sm-4">
			<?php echo Form::password('new_password_confirmation', array('class' => 'form-control', 'placeholder' => 'new password confirmation', 'id' => 'new_password_confirmation', 'required' => '')); ?>
		</div>
		<span class="help-block">{{$errors->first('new_password_confirmation')}}</span>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-4">
			<button type="submit" class="btn btn-primary">Submit</button>
		</div>
	</div>
	{{ Form::close() }}
</div>
@stop