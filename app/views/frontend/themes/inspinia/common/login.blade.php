@extends(getenv('FRONTEND_SKINS'). $theme . '.layouts.basic')
@section('content')
<div class="middle-box text-center loginscreen  animated fadeInDown">
    <div>
        <div>
            <h1 class="logo-name">
                <a href="#">
                    <img src="{{asset("assets/img/logo.png")}}"/>
                </a>
            </h1>
        </div>
        @include('frontend.partials.notification')
        <h3 class="">Welcome to Predictry</h3>
        <p>Login in. To see it in action.</p>
        {{ Form::open(array('url' => 'v2/login/submit', 'class' => 'm-t', 'autocomplete' => 'off', 'role' => 'form')) }}
        <div class="form-group {{$var = $errors->first('email')}} {{ ($var !== '') ? 'has-error' : ''}}">
            <?php echo Form::text('email', '', array('class' => 'form-control', 'placeholder' => Lang::get("fields.email.address"), 'id' => 'email')); ?>
            <span class="help-block">{{$errors->first('email')}}</span>
        </div>

        <div class="form-group {{$var = $errors->first('password')}} {{ ($var !== '') ? 'has-error' : ''}}">
            <?php echo Form::password('password', array('class' => 'form-control', 'placeholder' => Lang::get("fields.password"), 'id' => 'password')); ?>
            <span class="help-block">{{$errors->first('password')}}</span>
        </div>

        <?php echo Form::submit(Lang::get("home.login"), array('class' => 'btn btn-primary btn-block full-width m-b')); ?>
        <a href="<?php echo URL::to('v2/forgot'); ?>"><small><?php echo Lang::get("home.forgot.password"); ?></small></a>	
<!--        <p class="text-muted text-center"><small>Do not have an account?</small></p>
        {{--<a class="btn btn-sm btn-white btn-block" href="<?php // echo URL::to('v2/register'); ?>">Create an account</a>--}}-->
        {{ Form::close() }}
        <p class="m-t"> <small>&copy; {{date('Y')}} Predictry. A <a href='http://www.vventures.asia'>V Ventures</a> company. <br/> <span class="cl-fade"> Made with <i class="fa fa-heart-o"></i> in KL</span></small> </p>
    </div>
</div>
@stop
