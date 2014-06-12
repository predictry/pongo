<div class="item_filter" id="item_filter{{ $index_item_widget_filter }}">
	<div class="col-sm-4 form-group">
		<label for='item{{ $index_item_widget_filter }}' class="control-label pb10">Filter Name</label>
		<div class="clearfix"></div>
		<?php echo Form::select("filter_id[]", $filter_list, null, array('class' => 'form-control chosen-select-item', 'id' => "itemfilter{$index_item_widget_filter}", 'data-placeholder' => "Choose Item...")); ?>
	</div>
	<div class="col-sm-3 form-group">
		<label for='item1' class="control-label pb10">Status</label>
		<div class="clearfix"></div>
		<div class="btn-group" data-toggle="buttons">
			@if($type=="edit")
			<label class="btn btn-default <?php echo ($activated === true) ? 'active' : ''; ?>">
				<input type="radio" name="filter_active{{ $index_item_widget_filter }}" value="activated" > Activated
			</label>
			<label class="btn btn-default <?php echo ($activated === false) ? 'active' : ''; ?>">
				<input type="radio" name="filter_active{{ $index_item_widget_filter }}" value="deactivated"> Deactivated
			</label>
			@else
			<label class="btn btn-default active">
				<input type="radio" name="filter_active{{ $index_item_widget_filter }}" value="activated" checked=""> Activated
			</label>
			<label class="btn btn-default">
				<input type="radio" name="filter_active{{ $index_item_widget_filter }}" value="deactivated"> Deactivated
			</label>
			@endif
		</div>
	</div>
	<div class="col-sm-3 form-group">
		<label for='item1' class="control-label">&nbsp;</label>
		<div class="clearfix"></div>
		@if ($index_item_widget_filter > 1)
		<a href="javascript:void(0);" class="btn btnRemoveItemPlacementRuleset btn-danger" onClick="removeItemRule({{ $index_item_widget_filter }}, 'item_rule');"><i class="fa fa-minus"></i></a>
		@endif
	</div>
</div>