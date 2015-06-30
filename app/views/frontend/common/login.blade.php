@extends('frontend.layouts.basic')

@section('content')
<div class="container">
    <div id="loginContainer">
        <div class="middle-box text-center loginscreen  animated fadeInDown">
            <div class="row">
                @include('frontend.partials.notification')
                <h2 class="text-center">Predictry</h2>
                <hr class="line">
                {{ Form::open(array('url' => 'login/submit', 'class' => 'loginForm', 'autocomplete' => 'off')) }}
                <div class="input-group {{$var = $errors->first('email')}} {{ ($var !== '') ? 'has-error' : ''}}">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    <?php echo Form::text('email', '', array('class' => 'form-control', 'placeholder' => Lang::get("fields.email.address"), 'id' => 'email')); ?>
                </div>
                <span class="help-block">{{$errors->first('email')}}</span>

                <div class="input-group {{$var = $errors->first('password')}} {{ ($var !== '') ? 'has-error' : ''}}">
                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                    <?php echo Form::password('password', array('class' => 'form-control', 'placeholder' => Lang::get("fields.password"), 'id' => 'password')); ?>
                </div>
                <span class="help-block">{{$errors->first('password')}}</span>
                <?php echo Form::submit(Lang::get("home.login"), array('class' => 'btn btn-lg btn-primary btn-block')); ?>
                <label class="checkbox col-xs-12 col-sm-6">
                    <input type="checkbox" value="1" name="remember"><?php echo Lang::get("home.remember.me"); ?>
                </label>
                <div class="col-xs-12 col-sm-6">
                    <p class="forgotPwd">
                        <a href="<?php echo URL::to('forgot'); ?>"><?php echo Lang::get("home.forgot.password"); ?></a>	
                    </p>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@stop

{{ Form::open(array('url' => 'foo/bar')) }}

{{ Form::close() }}
