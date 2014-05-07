<div class="item_rule" id="item_rule{{ $index_item_placement_ruleset }}">
	<div class="col-sm-4 form-group">
		<label for='item{{ $index_item_placement_ruleset }}' class="control-label">Ruleset Name</label>
		<div class="clearfix"></div>
		<?php echo Form::select("item_id[]", $ruleset_list, $obj['ruleset_id'], array('class' => 'form-control chosen-select-item', 'id' => "item{$index_item_placement_ruleset}", 'data-placeholder' => "Choose Item...")); ?>
	</div>
	<div class="col-sm-3 form-group">
		<label for='item1' class="control-label">Status</label>
		<div class="clearfix"></div>
		<div class="btn-group" data-toggle="buttons">
			<label class="btn btn-default btn-sm  <?php echo ($obj['active'] === "activated") ? 'active' : ''; ?>">
				<input type="radio" name="active{{ $index_item_placement_ruleset }}" value="activated" <?php echo ($obj['active'] === "activated") ? 'checked' : ''; ?>> Activated
			</label>
			<label class="btn btn-default btn-sm <?php echo ($obj['active'] === "deactivated") ? 'active' : ''; ?>">
				<input type="radio" name="active{{ $index_item_placement_ruleset }}" value="deactivated" <?php echo ($obj['active'] === "deactivated") ? 'checked' : ''; ?>> Deactivated
			</label>
		</div>
	</div>
	<?php echo Form::hidden("item_ruleset_id[]", $obj['id']); ?>
	<div class="col-sm-3 form-group">
		<label for='item1' class="control-label">&nbsp;</label>
		<div class="clearfix"></div>
		@if($obj['last_index'] === $index_item_placement_ruleset)
		<a href="javascript:void(0);" class="btn btn-default btnAddItemPlacementRuleset" onClick="addItemPlacementRuleset();">Add</a>
		@else
		<a href="javascript:void(0);" class="btn btnRemoveItemRule btn-danger" onClick="removeItemRule({{ $index_item_placement_ruleset }}, 'item_rule');">Remove</a>
		@endif
	</div>
</div>