@if ($index_item_widget_ruleset > 1)
<div class="clearfix"></div>
@endif
<div class="item_rule" id="item_rule{{ $index_item_widget_ruleset }}">
	<div class="col-sm-4 form-group">
		<label for='item{{ $index_item_widget_ruleset }}' class="control-label pb10">Ruleset Name</label>
		<div class="clearfix"></div>
		<?php echo Form::select("item_id[]", $ruleset_list, null, array('class' => 'form-control chosen-select-item', 'id' => "item{$index_item_widget_ruleset}", 'data-placeholder' => "Choose Item...")); ?>
	</div>
	<div class="col-sm-3 form-group">
		<label for='item1' class="control-label pb10">Status</label>
		<div class="clearfix"></div>
		<div class="btn-group" data-toggle="buttons">
			@if($type=="edit")
			<label class="btn btn-default <?php echo ($activated === true) ? 'active' : ''; ?>">
				<input type="radio" name="active{{ $index_item_widget_ruleset }}" value="activated" > Activated
			</label>
			<label class="btn btn-default <?php echo ($activated === false) ? 'active' : ''; ?>">
				<input type="radio" name="active{{ $index_item_widget_ruleset }}" value="deactivated"> Deactivated
			</label>
			@else
			<label class="btn btn-default active">
				<input type="radio" name="active{{ $index_item_widget_ruleset }}" value="activated" checked=""> Activated
			</label>
			<label class="btn btn-default">
				<input type="radio" name="active{{ $index_item_widget_ruleset }}" value="deactivated"> Deactivated
			</label>
			@endif
		</div>
	</div>
	<div class="col-sm-3 form-group">
		<label for='item1' class="control-label">&nbsp;</label>
		<div class="clearfix"></div>
		@if ($index_item_widget_ruleset > 1)
		<a href="javascript:void(0);" class="btn btnRemoveItemPlacementRuleset btn-danger" onClick="removeItemRule({{ $index_item_widget_ruleset }}, 'item_rule');"><i class="fa fa-minus"></i></a>
		@endif
	</div>
	<?php echo Form::hidden("item_ruleset_id[]", "-1"); ?>
</div>