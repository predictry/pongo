@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard')
@section('content')
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
            {{ Form::open(array('url' => 'user/profile/submit', 'class' => 'form-horizontal loginForm')) }}
            <div class="form-group {{$var = $errors->first('name')}} {{ ($var !== '') ? 'has-error' : ''}}">
                <label class="col-sm-2 control-label">Full Name</label>
                <div class="col-sm-10">
                    <?php echo Form::text('name', Auth::user()->name, array('class' => 'form-control', 'placeholder' => 'fullname', 'id' => 'name')); ?>
                    <span class="help-block m-b-none">{{$errors->first('name')}}</span>
                </div>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="form-group {{$var = $errors->first('email')}} {{ ($var !== '') ? 'has-error' : ''}}">
                <label class="col-sm-2 control-label">Email Address</label>
                <div class="col-sm-10">
                    <?php echo Form::text('email', Auth::user()->email, array('class' => 'form-control', 'placeholder' => 'email address', 'id' => 'email')); ?>
                    <span class="help-block m-b-none">{{$errors->first('email')}}</span>
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
@stop