@extends('frontend.layouts.basic')
@section('content')
<div class="container" onload="document.registerForm.name.focus();">
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
            @include('frontend.partials.notification')
            <h2 class="text-center">{{ Lang::get('home.signup.now') }}</h2>
            <hr class="line">
            <p class="text-center small">
                {{ Lang::get('home.info.top.register_pref') }}
                <a href='{{ URL::to('login') }}'>{{ Lang::get('home.login.now') }}</a>.

            </p>
            {{ Form::open(array('url' => 'register/submit', 'class' => 'registerForm',  'role' => 'form', 'name' => 'registerForm')) }}
            <div class="form-group {{$var = $errors->first('name')}} {{ ($var !== '') ? 'has-error' : ''}}">
                <?php echo Form::text('name', '', array('class' => 'form-control', 'placeholder' => Lang::get("fields.full.name"), 'id' => 'name', "tabindex" => 1)); ?>
                <span class="help-block">{{$errors->first('name')}}</span>

            </div>
            <div class="form-group  {{$var = $errors->first('email')}} {{ ($var !== '') ? 'has-error' : ''}}">
                <?php echo Form::text('email', '', array('class' => 'form-control', 'placeholder' => Lang::get("fields.email.address"), 'id' => 'email', "tabindex" => 2)); ?>
                <span class="help-block">{{$errors->first('email')}}</span>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <div class="form-group {{$var = $errors->first('password')}} {{ ($var !== '') ? 'has-error' : ''}}">
                        <?php echo Form::password('password', array('class' => 'form-control', 'placeholder' => Lang::get("fields.password"), 'id' => 'password', "tabindex" => 3)); ?>
                        <span class="help-block">{{$errors->first('password')}}</span>

                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <div class="form-group {{$var = $errors->first('password_confirmation')}} {{ ($var !== '') ? 'has-error' : ''}}">
                        <?php echo Form::password('password_confirmation', array('class' => 'form-control', 'placeholder' => Lang::get("fields.confirm.password"), 'id' => 'password_confirmation', "tabindex" => 4)); ?>
                        <span class="help-block">{{$errors->first('password_confirmation')}}</span>
                    </div>
                </div>
            </div>
            <div class="form-group {{$var = $errors->first('site_url')}} {{ ($var !== '') ? 'has-error' : ''}}">
                <?php echo Form::text('site_url', '', array('class' => 'form-control', 'placeholder' => Lang::get("fields.site.url"), 'id' => 'site_url', "tabindex" => 6)); ?>

                <span class="help-block">{{$errors->first('site_url')}}</span>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <div class="form-group {{$var = $errors->first('site_category_id')}} {{ ($var !== '') ? 'has-error' : ''}}">
                        <?php echo Form::select('site_category_id', $site_categories, $selected_site_category_id, ['class' => 'form-control', 'id' => 'site_category_id', "tabindex" => 5]) ?>
                        <span class="help-block">{{$errors->first('site_category_id')}}</span>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <div class="form-group has-success {{$var = $errors->first('plan_id')}} {{ ($var !== '') ? 'has-error' : ''}}">
                        <?php echo Form::select('plan_id', $plans, $selected_plan_id, ['class' => 'form-control', 'id' => 'plan_id', "tabindex" => 7]) ?>
                        <span class="help-block">{{$errors->first('plan_id')}}</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <?php echo Form::submit(Lang::get("fields.submit"), array('class' => 'btn btn-lg btn-primary btn-block btn-lg', 'tabindex' => 4)); ?>
                </div>
            </div>

            <div class="row mt10">
                <div class="col-xs-12 col-sm-12 col-md-12 small text-center">
                    <p>
                        {{ Lang::get('home.info.pre.register_pref') }} 
                        <a href="{{ URL::to('login') }}">{{ Lang::get('home.terms.and.conditions') }} </a>
                        {{ Lang::get('home.info.pre.register_suf') }} 
                    </p>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop