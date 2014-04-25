@extends('frontend.layouts.basic')

@section('content')
<div class="container">
    <div id="loginContainer">
		<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
			<div class="row">
				@include('frontend.partials.notification')
				<h2 class="text-center"><?php echo Lang::get("home.forgot.password"); ?></h2>
				<hr class="line">
				{{ Form::open(array('url' => 'forgot/submit', 'class' => 'forgotForm')) }}
				<div class="input-group {{$var = $errors->first('email')}} {{ ($var !== '') ? 'has-error' : ''}}">
					<span class="input-group-addon"><i class="fa fa-user"></i></span>
					<?php echo Form::text('email', '', array('class' => 'form-control input-lg', 'placeholder' => Lang::get("fields.email.address"), 'id' => 'email')); ?>
					<span class="input-group-btn">
						<?php echo Form::submit(Lang::get("fields.submit.reset"), array('class' => 'btn btn-default input-lg')); ?>
					</span>
				</div>
				<span class="help-block">{{$errors->first('email')}}</span>
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>
@stop
