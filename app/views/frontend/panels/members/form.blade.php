@extends('frontend.layouts.dashboard')
@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	@include('frontend.partials.notification')
	<?php if ($type === "create") : ?>
		{{ Form::open(array('url' => 'members/submit', 'class' => 'form-horizontal loginForm')) }}
	<?php elseif ($type === "edit") : ?>
		{{ Form::model($member, array('route' => array('members.update', $member->id), 'url' => 'members/' . $member->id . '/edit', 'class' => 'form-horizontal memberForm')) }}
	<?php endif; ?>
	<a class="btn btn-default" href="{{ URL::previous(); }}"><i class="fa fa-reply"></i> Back</a>
	<h2>{{ $pageTitle or 'Form Member' }}</h2>
	<hr/>
	<div class="form-group {{$var = $errors->first('name')}} {{ ($var !== '') ? 'has-error' : ''}}">
		<label for="name" class="col-sm-2 control-label">Full Name</label>
		<div class="col-sm-4">
			<?php echo Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'fullname', 'id' => 'name')); ?>
		</div>
		<span class="help-block">{{$errors->first('name')}}</span>
	</div>

	<div class="form-group {{$var = $errors->first('email')}} {{ ($var !== '') ? 'has-error' : ''}}">
		<label for="email" class="col-sm-2 control-label">Email Address</label>
		<div class="col-sm-4">
			<?php echo Form::text('email', null, array('class' => 'form-control', 'placeholder' => 'email address', 'id' => 'email')); ?>
		</div>
		<span class="help-block">{{$errors->first('email')}}</span>
	</div>

	<!-- Password input-->
	<div class="form-group {{$var = $errors->first('password')}} {{ ($var !== '') ? 'has-error' : ''}}">
		<label class="col-sm-2 control-label" for="password">Password</label>
		<div class="col-sm-4">
			<?php echo Form::password('password', array('class' => 'form-control', 'placeholder' => 'new password', 'id' => 'password')); ?>
		</div>
		<span class="help-block">{{$errors->first('password')}}</span>
	</div>

	<!-- Password input-->
	<div class="form-group {{$var = $errors->first('password_confirmation')}} {{ ($var !== '') ? 'has-error' : ''}}">
		<label class="col-sm-2 control-label" for="password_confirmation">Confirm Password</label>
		<div class="col-sm-4">
			<?php echo Form::password('password_confirmation', array('class' => 'form-control', 'placeholder' => 'password confirmation', 'id' => 'password_confirmation')); ?>
		</div>
		<span class="help-block">{{$errors->first('password_confirmation')}}</span>
	</div>
	<!-- Multiple Checkboxes (inline) -->
	<div class="form-group">
		<label class="col-md-2 control-label" for="notify"></label>
		<div class="col-md-4">
			<label class="checkbox-inline" for="notify-0">
				<input name="notify" id="notify-0" value="1" type="checkbox" checked="">
				Notify via email
			</label>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-4">
			<button type="submit" class="btn btn-primary">Submit</button>
		</div>
	</div>
	{{ Form::close() }}
</div>
@stop