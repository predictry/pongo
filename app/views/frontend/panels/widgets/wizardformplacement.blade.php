{{ Form::open(array('url' => 'widgets/postAjaxWizardPlacement', 'class' => 'wizardPlacementForm')) }}
<div class="form-group {{ $var = $errors->first('name') }} {{ ($var !== '') ? 'has-error' : '' }}">
	<label for='name' class="col-sm-2 control-label">Name</label>
	<div class="col-sm-4">
		<?php echo Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'Name', 'id' => 'name')); ?>
	</div>
	<span class="help-block">{{ $errors->first('name') }}</span>
</div>
<div class="form-group {{ $var = $errors->first('description') }} {{ ($var !== '') ? 'has-error' : '' }}">
	<label for='description' class="col-sm-2 control-label">Description</label>
	<div class="col-sm-4">
		<?php echo Form::textarea('description', null, array('class' => 'form-control', 'placeholder' => 'Description', 'id' => 'description', 'rows' => 4)); ?>
	</div>
	<span class="help-block">{{ $errors->first('description') }}</span>
</div>
<div class="clearfix"></div>
{{ Form::close() }}