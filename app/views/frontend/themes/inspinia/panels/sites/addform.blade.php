<div class="wizardModalSiteFormContainer">
    {{ Form::open(array('url' => 'v2/sites/submit', 'class' => 'form-horizontal wizardModalSiteForm')) }}
    <div class="form-group {{$var = $errors->first('name')}} {{ ($var !== '') ? 'has-error' : ''}}">
        <label for="name" class="col-sm-3 control-label">Tenant ID</label>
        <div class="col-sm-5">
            <?php echo Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'TENANT_ID', 'id' => 'name')); ?>
        </div>
        <span class="help-block">{{$errors->first('name')}}</span>
    </div>

    <div class="form-group {{$var = $errors->first('url')}} {{ ($var !== '') ? 'has-error' : ''}}">
        <label for="email" class="col-sm-3 control-label">URL Address</label>
        <div class="col-sm-5">
            <?php echo Form::text('url', null, array('class' => 'form-control', 'placeholder' => 'http://www.website.com', 'id' => 'url')); ?>
        </div>
        <span class="help-block">{{$errors->first('url')}}</span>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-5">
            <button type="submit" class="btn btn-primary" id="btnWizardSubmitSite">Submit</button>
        </div>
    </div>
    {{ Form::close() }}
</div>