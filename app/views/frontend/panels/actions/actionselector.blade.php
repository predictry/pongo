<div class="col-sm-6 pull-right">
	{{ Form::open(array('url' => 'actions/submitSelector', 'class' => 'form-inline text-right actionSelectorForm')) }}
	<div class="form-group">
		<label class="" for="name">Choose Action</label>
		<?php echo Form::select('action_id', $dropdown, $default_action_view_id, array("class" => "form-control")); ?>
	</div>
	<button type="submit" class="btn btn-default">Submit</button>
	{{ Form::close() }}
</div>