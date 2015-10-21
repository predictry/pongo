@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.basic')
@section('content')
<div class="middle-box text-center loginscreen  animated fadeInDown">
    <div class="mb20">
        <div>
            <h1 class="logo-name">
                <a href="#">
                    <img class="login_logo register_logo" src="{{asset("assets/img/logo.png")}}"/>
                </a>
            </h1>
        </div>
        @include('frontend.partials.notification')
        <h2 class="wl_predictry">Welcome to Predictry</h2>
        <p>Create an account to see it in action</p>
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
        <div class="form-group {{$var = $errors->first('url')}} {{ ($var !== '') ? 'has-error' : ''}}">            
            <?php echo Form::text('url', null, array('class' => 'form-control', 'placeholder' => 'Your Website URL','id' => 'url', "tabindex" => 5)); ?>
            <span class="help-block">{{$errors->first('url')}}</span>
        </div>
        <div class="form-group {{$var = $errors->first('range_number_of_items')}} {{ ($var !== '') ? 'has-error' : ''}}">
            <?php echo Form::select('range_number_of_items', $range_number_of_items, $selected_range_number_of_items, ['class' => 'form-control', 'id' => 'range_number_of_items', "tabindex" => 6]) ?>
            <span class="help-block">{{$errors->first('range_number_of_items')}}</span>
        </div>
        <div class="form-group {{$var = $errors->first('industry_id')}} {{ ($var !== '') ? 'has-error' : ''}}">
            <?php echo Form::select('industry_id', $industries, $selected_industry_id, ['class' => 'form-control', 'id' => 'industry_id', "tabindex" => 7]) ?>
            <label for="industry_id">Select category of products sold</label>
            <span class="help-block">{{$errors->first('industry_id')}}</span>
        </div>
        <div class="form-group {{$var = $errors->first('pricing_method')}} {{ ($var !== '') ? 'has-error' : ''}}">
            <?php echo Form::select('pricing_method', $pricing_list, $pricing_method, array('class' => 'form-control', 'id' => 'pricing_method', 'placeholder' => 'Choose preferred pricing model' , "tabindex" => 8)); ?>
            <span class="help-block">{{$errors->first('pricing_method')}}</span>
        </div>
        <?php echo Form::submit(Lang::get("fields.start.free.trial.now"), array('class' => 'btn btn-primary block full-width m-b', 'tabindex' => 9)); ?>
        <p class="m-t"> 
            <small>
                {{ Lang::get('home.info.pre.register_pref') }} <br />
                <a href="{{ URL::to('#') }}">{{ Lang::get('home.terms.and.conditions') }} </a>
                {{ Lang::get('home.info.pre.register_suf') }} 
            </small> 
        </p>
        <p class="text-muted text-center"><small><a  href="{{ URL::to('v2/login')">Already have an account? Login</a></small></p>
    </div>
</div>
@stop
