<div class="col-lg-offset-2 col-lg-8">
	{{ Form::open(array('url' => 'actions/submitSelector', 'class' => 'form-horizontal text-left actionNoDefaultSelectorForm')) }}
	<div class="form-group {{ $var = $errors->first('name') }} {{ ($var !== '') ? 'has-error' : '' }}">
		<label for='name' class="control-label">Name</label>
		<?php echo Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'Name', 'id' => 'name')); ?>
		<span class="help-block">{{ $errors->first('name') }}</span>
	</div>
	<div class="form-group {{ $var = $errors->first('action_id') }} {{ ($var !== '') ? 'has-error' : '' }}">
		<label for='action_id' class="control-label">Choose Action</label>
		<?php echo Form::select('action_id[]', $available_non_default_site_actions_dropdown, null, array("class" => "form-control chosen-select-item", "id" => "trackingActionChosen", "multiple" => "")); ?>
		<span class="help-block">{{ $errors->first('action_id') }}</span>
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-default" id="btnSubmitActionNonDefaultSelector">Submit</button>
	</div>
	{{ Form::close() }}
</div>
<script type="text/javascript">
	//CHOSEN ACTION FOR TRACKING COMPARISON
	$("#trackingActionChosen").chosen();

	$("#btnSubmitActionNonDefaultSelector").on("click", function(e) {
		e.preventDefault();
		//show loading
		var form = $(".actionNoDefaultSelectorForm");

		$.ajax({
			url: site_url + "/panel/ajaxRenderGraph",
			type: 'POST',
			data: form.serialize(),
			dataType: 'json',
			success: function(data)
			{
				if (data.status === "success") {
					window.location = site_url + data.response;
				} else {
					$(".modal-body").html(data.response);
				}
			},
			error: function() {
			}
		});

		return;
	});
</script>
<div class="clearfix"></div>