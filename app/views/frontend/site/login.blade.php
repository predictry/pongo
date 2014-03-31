@extends('frontend.layouts.basic')

@section('content')
<div class="container">
    <div id="loginContainer">
		<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
			<div class="row">
				@include('frontend.partials.notification')
				<h2 class="text-center">Login with</h2>
				<hr class="line">
				<div class="socialButtons">
					<div class="">
						<a href="#" class="btn btn-lg btn-block btn-default btn-facebook">
							<i class="fa fa-facebook visible-xs"></i>
							<span class="hidden-xs">Facebook</span>
						</a>
					</div>
				</div>
				<div class="text-center">
					<span class="spanOr">or</span>
				</div>
				{{ Form::open(array('url' => 'login/submit', 'class' => 'loginForm', 'autocomplete' => 'off')) }}
				<div class="input-group {{$var = $errors->first('email')}} {{ ($var !== '') ? 'has-error' : ''}}">
					<span class="input-group-addon"><i class="fa fa-user"></i></span>
					<?php echo Form::text('email', '', array('class' => 'form-control', 'placeholder' => 'email address', 'id' => 'email')); ?>
				</div>
				<span class="help-block">{{$errors->first('email')}}</span>

				<div class="input-group {{$var = $errors->first('password')}} {{ ($var !== '') ? 'has-error' : ''}}">
					<span class="input-group-addon"><i class="fa fa-lock"></i></span>
					<?php echo Form::password('password', array('class' => 'form-control', 'placeholder' => 'password', 'id' => 'password')); ?>
				</div>
				<span class="help-block">{{$errors->first('password')}}</span>
				<?php echo Form::submit('Login', array('class' => 'btn btn-lg btn-default btn-block')); ?>
				<label class="checkbox col-xs-12 col-sm-6">
					<input type="checkbox" value="1" name="remember">Remember Me
				</label>
				<div class="col-xs-12 col-sm-6">
					<p class="forgotPwd">
						<a href="#">Forgot password?</a> | 
						<a href="<?php echo URL::to('register'); ?>">Register Account</a>
					</p>
				</div>
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>
@stop
