<div class="col-sm-9 pull-right">
	{{ Form::open(array('url' => 'panel/submitSelector', 'class' => 'form-inline text-right actionSelectorForm')) }}
	@if($funel_selected_dropdown !== null)
	<div class="form-group">
		<label class="" for="name">Choose Funnel</label>
		<?php echo Form::select('funel_preference_id', $funel_dropdown, $funel_selected_dropdown, array("class" => "form-control")); ?>
	</div>
	<button type="submit" class="btn btn-default">Submit</button>
	<a href="{{URL::to('panel/createFunel')}}" class="btn btn-default"><i class="fa fa-plus"></i> Funnel</a>
	@else
	<a href="{{URL::to('panel/createFunel')}}" class="btn btn-default"><i class="fa fa-plus"></i> Funnel</a>
	@endif
	{{ Form::close() }}
</div>