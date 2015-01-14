@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard')
@section('content')
@include(getenv('FRONTEND_SKINS') . $theme . '.partials.page_heading_without_action', ['upper' => [], 'currentPage' => 'Password'])
<div class="wrapper wrapper-content">
    <div class="row border-bottom">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Edit Form <small></small></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    @include('frontend.partials.notification')
                    {{ Form::open(array('url' => 'user/password/submit', 'class' => 'form-horizontal passwordForm')) }}
                    <div class="form-group {{$var = $errors->first('password')}} {{ ($var !== '') ? 'has-error' : ''}}">
                        <label class="col-sm-2 control-label"><?php echo Lang::get("fields.current.password"); ?></label>
                        <div class="col-sm-10">
                            <?php echo Form::password('password', array('class' => 'form-control', 'placeholder' => Lang::get("fields.current.password"), 'id' => 'password', 'required' => '')); ?>
                            <span class="help-block m-b-none">{{$errors->first('password')}}</span>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group {{$var = $errors->first('new_password')}} {{ ($var !== '') ? 'has-error' : ''}}">
                        <label class="col-sm-2 control-label" for="new_password"><?php echo Lang::get("fields.new.password"); ?></label>
                        <div class="col-sm-10">
                            <?php echo Form::password('new_password', array('class' => 'form-control', 'placeholder' => Lang::get("fields.new.password"), 'id' => 'new_password', 'required' => '')); ?>
                            <span class="help-block m-b-none">{{$errors->first('new_password')}}</span>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group {{$var = $errors->first('new_password_confirmation')}} {{ ($var !== '') ? 'has-error' : ''}}">
                        <label class="col-sm-2 control-label" for="new_password_confirmation"><?php echo Lang::get("fields.new.confirm.password"); ?></label>
                        <div class="col-sm-10">
                            <?php echo Form::password('new_password_confirmation', array('class' => 'form-control', 'placeholder' => Lang::get("fields.new.confirm.password"), 'id' => 'new_password_confirmation', 'required' => '')); ?>
                            <span class="help-block m-b-none">{{$errors->first('new_password_confirmation')}}</span>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-white" type="reset">Cancel</button>
                            <button class="btn btn-primary" type="submit">Save changes</button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop