@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.basic')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
            @include('frontend.partials.notification')
            <h2 class="text-center">reset.password</h2>
            <hr class="line">
            {{ Form::open(array('url' => 'v2/password/reset/submit', 'class' => 'resetPasswordForm',  'role' => 'form')) }}
            <?php echo Form::hidden('token', $token); ?>
            <div class="form-group  {{$var = $errors->first('email')}} {{ ($var !== '') ? 'has-error' : ''}}">
                <?php echo Form::text('email', '', array('class' => 'form-control', 'placeholder' => 'email.address', 'id' => 'email', "tabindex" => 1)); ?>
                <span class="help-block">{{$errors->first('email')}}</span>
            </div>
            <div class="form-group {{$var = $errors->first('password')}} {{ ($var !== '') ? 'has-error' : ''}}">
                <?php echo Form::password('password', array('class' => 'form-control', 'placeholder' => 'password', 'id' => 'password', "tabindex" => 2)); ?>
                <span class="help-block">{{$errors->first('password')}}</span>

            </div>
            <div class="form-group  {{$var = $errors->first('password_confirmation')}} {{ ($var !== '') ? 'has-error' : ''}}">
                <?php echo Form::password('password_confirmation', array('class' => 'form-control', 'placeholder' => 'confirm.password', 'id' => 'password_confirmation', "tabindex" => 3)); ?>
                <span class="help-block">{{$errors->first('password_confirmation')}}</span>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <?php echo Form::submit('submit.reset', array('class' => 'btn btn-lg btn-primary btn-block btn-lg', 'tabindex' => 4)); ?>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop