@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.basic')
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
        <h3>Welcome to Predictry</h3>
        <p>Create account to see it in action.</p>
        {{ Form::open(array('url' => 'v2/register/submit', 'class' => 'registerForm',  'role' => 'form')) }}
        <div class="form-group {{$var = $errors->first('name')}} {{ ($var !== '') ? 'has-error' : ''}}">
            <?php echo Form::text('name', '', array('class' => 'form-control', 'placeholder' => Lang::get("fields.full.name"), 'id' => 'name', "tabindex" => 1)); ?>
            <span class="help-block">{{$errors->first('name')}}</span>
        </div>
        <div class="form-group  {{$var = $errors->first('email')}} {{ ($var !== '') ? 'has-error' : ''}}">
            <?php echo Form::text('email', '', array('class' => 'form-control', 'placeholder' => Lang::get("fields.email.address"), 'id' => 'email', "tabindex" => 2)); ?>
            <span class="help-block">{{$errors->first('email')}}</span>
        </div>
        <div class="form-group {{$var = $errors->first('password')}} {{ ($var !== '') ? 'has-error' : ''}}">
            <?php echo Form::password('password', array('class' => 'form-control', 'placeholder' => Lang::get("fields.password"), 'id' => 'password', "tabindex" => 3)); ?>
            <span class="help-block">{{$errors->first('password')}}</span>
        </div>
        <div class="form-group {{$var = $errors->first('password_confirmation')}} {{ ($var !== '') ? 'has-error' : ''}}">
            <?php echo Form::password('password_confirmation', array('class' => 'form-control', 'placeholder' => Lang::get("fields.confirm.password"), 'id' => 'password_confirmation', "tabindex" => 4)); ?>
            <span class="help-block">{{$errors->first('password_confirmation')}}</span>
        </div>
        <?php echo Form::submit(Lang::get("fields.submit"), array('class' => 'btn btn-primary block full-width m-b', 'tabindex' => 4)); ?>
        <p class="m-t"> 
            <small>
                {{ Lang::get('home.info.pre.register_pref') }} 
                <a href="{{ URL::to('login') }}">{{ Lang::get('home.terms.and.conditions') }} </a>
                {{ Lang::get('home.info.pre.register_suf') }} 
            </small> 
        </p>
        <p class="text-muted text-center"><small>Already have an account?</small></p>
        <a class="btn btn-sm btn-white btn-block" href="{{ URL::to('v2/login') }}">Login</a>
    </div>
</div>
@stop