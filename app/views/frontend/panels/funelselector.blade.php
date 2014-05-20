<div class="col-sm-offset-1 col-sm-7 pull-right">
	{{ Form::open(array('url' => 'panel/submitSelector', 'class' => 'form-inline pull-left funelSelector', 'id' => 'funelSelector')) }}
	@if($funel_selected_dropdown !== null)
	{{ Form::open(array('url' => 'panel/submitSelector', 'class' => 'form-inline pull-left funelSelector', 'id' => 'funelSelector')) }}
	<div class="form-group">
		<label class="" for="name">Choose Funnel</label>
		<?php echo Form::select('funel_preference_id', $funel_dropdown, $funel_selected_dropdown, array("class" => "form-control", "id" => "funel_preference_id")); ?>
	</div>
	<!--<button type="submit" class="btn btn-default">Submit</button>-->
	<a href="{{URL::to('panel/createFunel')}}" class="btn btn-default tt" data-toggle="tooltip" data-placement="top" title="Create funnel"><i class="fa fa-plus"></i> Funnel</a>
	{{ Form::close() }}
	{{ Form::open(array('url' => 'panel/deleteFunel', 'class' => 'pull-right text-right')) }}
	{{ Form::hidden('funel_preference_id', $funel_selected_dropdown) }}
	<button type="submit" class="btn btn-default tt" data-toggle="tooltip" data-placement="left" title="Delete funnel" onclick="return confirm('are you sure want to delete this funnel?')"><i class="fa fa-times text-error"></i></button>
	{{ Form::close() }}
	@else
	<a href="{{URL::to('panel/createFunel')}}" class="btn btn-default tt" data-toggle="tooltip" data-placement="top" title="Create funnel"><i class="fa fa-plus"></i> Funnel</a>
	@endif
</div>