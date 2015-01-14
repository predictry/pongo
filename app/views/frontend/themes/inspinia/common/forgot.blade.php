@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.basic')

@section('content')
<div class="middle-box text-center loginscreen  animated fadeInDown">
    <div>
        <div>
            <h1 class="logo-name">PT</h1>
        </div>
        @include('frontend.partials.notification')
        <h3>Reset Password</h3>
        <p>Fill your email below to reset the password.</p>
        {{ Form::open(array('url' => 'forgot/submit', 'class' => 'forgotForm')) }}
        <div class="form-group {{$var = $errors->first('email')}} {{ ($var !== '') ? 'has-error' : ''}}">
            <?php echo Form::text('email', '', array('class' => 'form-control input-lg', 'placeholder' => Lang::get("fields.email.address"), 'id' => 'email')); ?>
            <span class="help-block">{{$errors->first('email')}}</span>
        </div>
        <?php echo Form::submit(Lang::get("fields.submit.reset"), array('class' => 'btn btn-primary block full-width m-b')); ?>
        {{ Form::close() }}
        <p class="text-muted text-center"><small>Suddenly remember your credential?</small></p>
        <a class="btn btn-sm btn-white btn-block" href="{{ URL::to('v2/login') }}">Login</a>
        <p class="m-t"> <small>&copy; {{date('Y')}} Predictry. A <a href='http://www.vventures.asia'>V Ventures</a> company. <br/> <span class="cl-fade"> Made with <i class="fa fa-heart-o"></i> in KL</span></small> </p>
    </div>
</div>
@stop
