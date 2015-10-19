@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard')
@section('content')
@include(getenv('FRONTEND_SKINS') . $theme . '.partials.page_heading_without_action', ['upper' => ['Sites' => 'v2/sites']])
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Form</h5>
                </div>
                <div class="ibox-content">
                    @include('frontend.partials.notification')
                    <?php if ($type === "create") : ?>
                        {{ Form::open(array('url' => 'v2/sites/submit', 'class' => 'form-horizontal loginForm')) }}
                    <?php elseif ($type === "edit") : ?>
                        {{ Form::model($site, array('route' => array('sites.update', $site->id), 'url' => 'sites/'.$site->id.'/edit', 'class' => 'form-horizontal siteForm')) }}
                    <?php endif; ?>
                    
                    <div class="form-group {{$var = $errors->first('url')}} {{ ($var !== '') ? 'has-error' : ''}}">
                        <label for="url" class="col-sm-2 control-label">URL Address</label>
                        <div class="col-sm-10">
                            <?php echo Form::text('url', null, array('class' => 'form-control', 'placeholder' => 'http://www.website.com', 'id' => 'url')); ?>
                            <span class="help-block">{{$errors->first('url')}}</span>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    
                    <div class="form-group {{$var = $errors->first('relative_url_desktop')}} {{ ($var !== '') ? 'has-error' : ''}}">
                        <label for="relative_url_desktop" class="col-sm-2 control-label">Relative URL for Desktop</label>
                        <div class="col-sm-10">
                            <?php echo Form::text('relative_url_desktop', null, array('class' => 'form-control', 'placeholder' => 'http://www.website.com', 'id' => 'relative_url_desktop')); ?>
                            <span class="help-block">{{$errors->first('relative_url_desktop')}}</span>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group {{$var = $errors->first('relative_url_mobile')}} {{ ($var !== '') ? 'has-error' : ''}}">
                        <label for="relative_url_mobile" class="col-sm-2 control-label">For Mobile</label>
                        <div class="col-sm-10">
                            <?php echo Form::text('relative_url_mobile', null, array('class' => 'form-control', 'placeholder' => 'http://www.website.com', 'id' => 'relative_url_mobile')); ?>
                            <span class="help-block">{{$errors->first('relative_url_mobile')}}</span>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    
                    <div class="form-group {{$var = $errors->first('relative_url_others')}} {{ ($var !== '') ? 'has-error' : ''}}">
                        <label for="relative_url_others" class="col-sm-2 control-label">For Other Platforms</label>
                        <div class="col-sm-10">
                            <?php echo Form::text('relative_url_others', null, array('class' => 'form-control', 'placeholder' => 'http://www.website.com', 'id' => 'relative_url_other')); ?>
                            <span class="help-block">{{$errors->first('relative_url_others')}}</span>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group {{$var = $errors->first('currency')}} {{ ($var !== '') ? 'has-error' : ''}}">
                        <label for="currency" class="col-sm-2 control-label">Currency</label>
                        <div class="col-sm-10">
                            <?php echo Form::text('currency', null, array('class' => 'form-control', 'placeholder' => 'USD,RM,SGD ...', 'id' => 'currency')); ?>
                            <span class="help-block">{{$errors->first('currency')}}</span>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    
                    <div class="form-group {{$var = $errors->first('site_category_id')}} {{ ($var !== '') ? 'has-error' : ''}}">
                        <label for="email" class="col-sm-2 control-label">Site Category</label>
                        <div class="col-sm-10">
                            <?php echo Form::select('site_category_id', $site_category_list, 1, ['class' => 'form-control', 'id' => 'site_category_id', "tabindex" => 6]) ?>
                            <span class="help-block">{{$errors->first('site_category_id')}}</span>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-white" type="reset">Cancel</button>
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop
