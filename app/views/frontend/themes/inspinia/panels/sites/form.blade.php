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
                        {{ Form::open(array('url' => 'sites/submit', 'class' => 'form-horizontal loginForm')) }}
                    <?php elseif ($type === "edit") : ?>
                        {{ Form::model($site, array('route' => array('sites.update', $site->id), 'url' => 'sites/'.$site->id.'/edit', 'class' => 'form-horizontal siteForm')) }}
                    <?php endif; ?>
                    <div class="form-group {{$var = $errors->first('name')}} {{ ($var !== '') ? 'has-error' : ''}}">
                        <label for="name" class="col-sm-2 control-label">Tenant ID</label>
                        <div class="col-sm-10">
                            <?php echo Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'TENANT_ID', 'id' => 'name')); ?>
                            <span class="help-block">{{$errors->first('name')}}</span>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group {{$var = $errors->first('url')}} {{ ($var !== '') ? 'has-error' : ''}}">
                        <label for="email" class="col-sm-2 control-label">URL Address</label>
                        <div class="col-sm-10">
                            <?php echo Form::text('url', null, array('class' => 'form-control', 'placeholder' => 'http://www.website.com', 'id' => 'url')); ?>
                            <span class="help-block">{{$errors->first('url')}}</span>
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