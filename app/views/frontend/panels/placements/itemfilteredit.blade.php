<div class="item_filter" id="item_filter{{ $index_item_placement_filter }}">
	<div class="col-sm-4 form-group">
		<label for='item{{ $index_item_placement_filter }}' class="control-label pb10">Filter Name</label>
		<div class="clearfix"></div>
		<?php echo Form::select("filter_id[]", $filter_list, $obj['filter_id'], array('class' => 'form-control chosen-select-item', 'id' => "itemfilter{$index_item_placement_filter}", 'data-placeholder' => "Choose Item...")); ?>
	</div>
	<div class="col-sm-3 form-group">
		<label for='item1' class="control-label pb10">Status</label>
		<div class="clearfix"></div>
		<div class="btn-group" data-toggle="buttons">
			<label class="btn btn-default <?php echo ($obj['active'] === 'activated') ? 'active' : ''; ?>">
				<input type="radio" name="filter_active{{ $index_item_placement_filter }}" value="activated" <?php echo ($obj['active'] === "activated") ? 'checked' : ''; ?>> Activated
			</label>
			<label class="btn btn-default <?php echo ($obj['active'] === 'deactivated') ? 'active' : ''; ?>">
				<input type="radio" name="filter_active{{ $index_item_placement_filter }}" value="deactivated" <?php echo ($obj['active'] === "deactivated") ? 'checked' : ''; ?>> Deactivated
			</label>
		</div>
	</div>
	<?php echo Form::hidden("item_filter_id[]", $obj['id']); ?>
	<div class="col-sm-3 form-group">
		<label for='item1' class="control-label">&nbsp;</label>
		<div class="clearfix"></div>
		@if ($index_item_placement_filter > 1)
		<a href="javascript:void(0);" class="btn btnRemoveItemPlacementRuleset btn-danger" onClick="removeItemRule({{ $index_item_placement_filter }}, 'item_rule');"><i class="fa fa-minus"></i></a>
		@endif
	</div>
</div>