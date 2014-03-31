@extends('frontend.layouts.basic')
@section('content')
<div class="container">
	<div class="row">
		<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
			@include('frontend.partials.notification')
			<h2 class="text-center">Sign Up Now</h2>
			<hr class="line">
			<p class="text-center small">Already have an account. You can <a href="<?php echo URL::to('login'); ?>">login</a> now.</p>
			{{ Form::open(array('url' => 'register/submit', 'class' => 'registerForm',  'role' => 'form')) }}
			<div class="form-group {{$var = $errors->first('name')}} {{ ($var !== '') ? 'has-error' : ''}}">
				<?php echo Form::text('name', '', array('class' => 'form-control', 'placeholder' => 'Full Name', 'id' => 'name', "tabindex" => 1)); ?>
				<span class="help-block">{{$errors->first('name')}}</span>

			</div>
			<div class="form-group  {{$var = $errors->first('email')}} {{ ($var !== '') ? 'has-error' : ''}}">
				<?php echo Form::text('email', '', array('class' => 'form-control', 'placeholder' => 'Email', 'id' => 'email', "tabindex" => 2)); ?>
				<span class="help-block">{{$errors->first('email')}}</span>

			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-6 col-md-6">
					<div class="form-group {{$var = $errors->first('password')}} {{ ($var !== '') ? 'has-error' : ''}}">
						<?php echo Form::password('password', array('class' => 'form-control', 'placeholder' => 'password', 'id' => 'password', "tabindex" => 3)); ?>
						<span class="help-block">{{$errors->first('password')}}</span>

					</div>
				</div>
				<div class="col-xs-12 col-sm-6 col-md-6">
					<div class="form-group {{$var = $errors->first('password_confirmation')}} {{ ($var !== '') ? 'has-error' : ''}}">
						<?php echo Form::password('password_confirmation', array('class' => 'form-control', 'placeholder' => 'confirm password', 'id' => 'password_confirmation', "tabindex" => 4)); ?>
						<span class="help-block">{{$errors->first('password_confirmation')}}</span>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-md-12">
					<?php echo Form::submit('Submit', array('class' => 'btn btn-lg btn-primary btn-block btn-lg', 'tabindex' => 4)); ?>
				</div>
			</div>

			<div class="row mt10">
				<div class="col-xs-12 col-sm-12 col-md-12 small text-center">
					<p>By clicking register, you agree to the <a href="#">Terms and Conditions</a> including our Cookie Use.</p>
				</div>
			</div>
			{{ Form::close() }}
		</div>
	</div>
</div>
@stop