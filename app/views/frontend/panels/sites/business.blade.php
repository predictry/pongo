@extends('frontend.layouts.blankdashboard')
@section('content')
<div class="col-sm-12 col-md-12 main">
    @include('frontend.partials.notification')
    {{ Form::open(array('url' => 'sites/'. $site->name .'/business/submit', 'class' => 'form-horizontal loginForm')) }}
    <h2><?php echo Lang::get("user.site.business"); ?></h2>
    <hr/>

    <div class="form-group {{$var = $errors->first('name')}} {{ ($var !== '') ? 'has-error' : ''}}">
        <label class="col-sm-3 control-label" for="name"><?php echo Lang::get("fields.site.business.name"); ?></label>
        <div class="col-sm-4">
            <?php echo Form::text('name', null, array('class' => 'form-control', 'placeholder' => Lang::get("fields.site.business.name.placeholder"), 'id' => 'name', 'required' => '', "tabindex" => 1)); ?>
        </div>
        <span class="help-block">{{$errors->first('name')}}</span>
    </div>

    <div class="form-group {{$var = $errors->first('url')}} {{ ($var !== '') ? 'has-error' : ''}}">
        <label for="email" class="col-sm-3 control-label">URL Address</label>
        <div class="col-sm-4">
            <?php echo Form::text('url', $site->url, array('class' => 'form-control', 'placeholder' => 'http://www.website.com', 'id' => 'url', "tabindex" => 2)); ?>
        </div>
        <span class="help-block">{{$errors->first('url')}}</span>
    </div>

    <!--    <div class="form-group {{$var = $errors->first('range_number_of_users')}} {{ ($var !== '') ? 'has-error' : ''}}">
            <label for="email" class="col-sm-3 control-label">Number of Users</label>
            <div class="col-sm-3">
    <?php // echo Form::select('range_number_of_users', $range_number_of_users, $selected_range_number_of_users, ['class' => 'form-control', 'id' => 'range_number_of_users', "tabindex" => 3]) ?>
            </div>
            <span class="help-block">{{$errors->first('range_number_of_users')}}</span>
        </div>-->

    <div class="form-group {{$var = $errors->first('range_number_of_items')}} {{ ($var !== '') ? 'has-error' : ''}}">
        <label for="email" class="col-sm-3 control-label">Number of Items</label>
        <div class="col-sm-3">
            <?php echo Form::select('range_number_of_items', $range_number_of_items, $selected_range_number_of_items, ['class' => 'form-control', 'id' => 'range_number_of_items', "tabindex" => 4]) ?>
        </div>
        <span class="help-block">{{$errors->first('range_number_of_items')}}</span>
    </div>

    <div class="form-group {{$var = $errors->first('industry_id')}} {{ ($var !== '') ? 'has-error' : ''}}">
        <label for="email" class="col-sm-3 control-label">Industry</label>
        <div class="col-sm-3">
            <?php echo Form::select('industry_id', $industries, $selected_industry_id, ['class' => 'form-control', 'id' => 'industry_id', "tabindex" => 5]) ?>
        </div>
        <span class="help-block">{{$errors->first('industry_id')}}</span>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-4">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
    {{ Form::close() }}
</div>
@stop