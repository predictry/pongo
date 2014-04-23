@if($flash_error)
<div class="alert alert-danger alert-dismissable fade in">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<strong>Error!</strong> {{ $flash_error }}
</div>
@endif
<!-- Nav tabs -->
<ul class="nav nav-tabs">
	<li class="active"><a href="#tabRuleInfo" data-toggle="tab">Info</a></li>
	<li><a href="#tabRuleDetail" data-toggle="tab">Details</a></li>
	<li><a href="#tabRuleItem" data-toggle="tab" id="toggleTabRuleItem">Rules</a></li>
</ul>
<?php if ($type === "create") : ?>
	{{ Form::open(array('url' => 'rules/submit', 'class' => 'modalRulesetForm')) }}
<?php endif; ?>
<div class="tab-content">
	<div class="tab-pane active pt20 form-horizontal" id="tabRuleInfo">
		<!-- combination -->
		<div class="form-group {{ $var = $errors->first('name') }} {{ ($var !== '') ? 'has-error' : '' }}">
			<label for='name' class="col-sm-2 control-label">Name</label>
			<div class="col-sm-6">
				<?php echo Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'Name', 'id' => 'name')); ?>
			</div>
			<span class="help-block">{{ $errors->first('name') }}</span>
		</div>

		<div class="form-group {{ $var = $errors->first('description') }} {{ ($var !== '') ? 'has-error' : '' }}">
			<label for='description' class="col-sm-2 control-label">Description</label>
			<div class="col-sm-6">
				<?php echo Form::textarea('description', null, array('class' => 'form-control', 'placeholder' => 'Description', 'id' => 'description', 'rows' => 4)); ?>
			</div>
			<span class="help-block">{{ $errors->first('description') }}</span>
		</div>
	</div>
	<div class="tab-pane pt20 form-horizontal" id="tabRuleDetail">
		<!-- ruleset -->
		<div class="form-group {{ $var = $errors->first('expiry_type') }} {{ ($var !== '') ? 'has-error' : '' }}">
			<label for='expiry_type' class="col-sm-2 control-label">Expiry Type</label>
			<div class="col-sm-6">
				@if($type === 'edit')
				<?php echo Form::select('expiry_type', $enum_expiry_types, $ruleset->expiry_type, array('class' => 'form-control', 'id' => 'expiry_type')); ?>
				@else
				<?php echo Form::select('expiry_type', $enum_expiry_types, "no_expiry", array('class' => 'form-control', 'id' => 'expiry_type')); ?>
				@endif
			</div>
			<span class="help-block">{{ $errors->first('expiry_type') }}</span>
		</div>		
		<div class="form-group {{ $var = $errors->first('expiry_value') }} {{ ($var !== '') ? 'has-error' : '' }}">
			<label for='expiry_date_or_value' class="col-sm-2 control-label">Expiry Value</label>
			<div class="col-sm-6" id="expiry_value_box">
				<?php
				if ($type === 'edit')
				{
					if (!isset($ruleset->expiry_datetime))
					{
						echo Form::text('expiry_value', null, array('class' => 'form-control', 'placeholder' => 'Expiry Value', 'id' => 'expiry_value'));
						?>
						<script type="text/javascript">
							var expiry_date = '';
						</script>
						<div class='input-group date hide' id='datetimepicker' data-date-format="YYYY-MM-DD hh:mm:ss A">
							<input type='text' class="form-control disabled" name="expiry_value_temp" readonly="" />
							<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
						<?php
					}
					else
					{
						$expiry_datetime = new \Carbon\Carbon($ruleset->expiry_datetime);
						echo Form::text('expiry_value_temp', $expiry_datetime->toDateTimeString(), array('class' => 'form-control hide', 'placeholder' => 'Expiry Value', 'id' => 'expiry_value'));
						?>
						<script type="text/javascript">
							var expiry_date = '<?php echo $expiry_datetime->toDateTimeString(); ?>';
						</script>
						<div class='input-group date' id='datetimepicker' data-date-format="YYYY-MM-DD hh:mm:ss A">
							<input type='text' class="form-control disabled" name="expiry_value" readonly=""/>
							<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
						<?php
					}
				}
				else
				{
					echo Form::text('expiry_value', null, array('class' => 'form-control', 'placeholder' => 'Expiry Value', 'id' => 'expiry_value'));
					?>
					<script type="text/javascript">
						var expiry_date = '';
					</script>
					<div class='input-group date hide' id='datetimepicker' data-date-format="YYYY-MM-DD hh:mm:ss A">
						<input type='text' class="form-control disabled" name="expiry_value_temp" readonly="" />
						<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
						</span>
					</div>
				<?php } ?>
			</div>
			<span class="help-block">{{ $errors->first('expiry_value') }}</span>
		</div>

		<?php echo Form::hidden("expiry_value_dt", "", array("id" => "expiry_value_dt")); ?>
	</div>


	<div class="tab-pane pt20" id="tabRuleItem">
		<!-- rule item -->
		<div class="" id="modal_item_rules_container">
			<div class="action_buttons pull-right">
				<a href="javascript:void(0);" class="btn btn-default" onClick="addItemRule(true, 'modal_item_rules_container');"><i class="fa fa-plus"></i></a>
			</div>
			<div class="clearfix"></div>

			<?php if ($type === "create") : ?>
				@include('frontend.panels.rules.modalitemrule')	
			<?php endif; ?>
		</div>
	</div>
	<div class="clearfix mb20"></div>
	<div class="pull-right">
		<button type="submit" class="btn btn-primary" id="btnSubmitModalRuleset">Submit</button>
	</div>
</div><!-- end of tab-content -->
{{ Form::close(); }}
<div class="clearfix"></div>
<script type="text/javascript">


	// INITIALIZE CHOSEN FOR THE FIRST ITEM //
	$("#modalType1").chosen();
	$("#modalItem1").chosen();

	// WIZARD (MODAL ADD NEW RULESET) //
	$("#btnSubmitModalRuleset").on('click', function(e) {
		e.preventDefault();
		var form = $(".modalRulesetForm");
		console.log(form.serialize());
		$.ajax({
			url: site_url + "/placements/ajaxSubmitWizardAddRuleset",
			type: 'POST',
			data: form.serialize(),
			dataType: 'json',
			success: function(data)
			{
				if (data.status === "success") {
					$('#viewModal').modal('hide');
					addItemPlacementRuleset();
				}
				else {
					$(".modal-body").html(data.response);
				}
			},
			error: function() {
			}
		});

		return;
	});
</script>