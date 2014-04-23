<div class="item_rule" id="item_rule{{ $index_item_rule }}">
	<div class="col-sm-3 form-group">
		<label for='item{{ $index_item_rule }}' class="control-label">Item Name</label>
		<div class="clearfix"></div>
		<?php echo Form::select("item_id[]", $items, $obj['item_id'], array('class' => 'form-control chosen-select-item', 'id' => "item{$index_item_rule}", 'data-placeholder' => "Choose Item...")); ?>
	</div>
	<div class="col-sm-3 form-group">
		<label for='item1' class="control-label">Rule Type</label>
		<div class="clearfix"></div>
		<?php echo Form::select("type[]", $enum_types, $obj['type'], array('class' => 'form-control', 'id' => "type{$index_item_rule}")); ?>
	</div>
	<div class="col-sm-2 form-group">
		<label for='item1' class="control-label">Likelihood</label>
		<div class="clearfix"></div>
		<div class="input-group col-sm-8 pull-left">
			<?php
			$likelihood = $obj['likelihood'] * 1000;
			echo Form::text('likelihood[]', $likelihood, array('class' => 'form-control', 'placeholder' => '0', 'id' => 'likelihood'));
			?>
			<span class="input-group-addon">%</span>
		</div>
	</div>
	<?php echo Form::hidden("item_rule_id[]", $obj['id']); ?>

	<div class="col-sm-3 form-group">
		<label for='item1' class="control-label">&nbsp;</label>
		<div class="clearfix"></div>
		@if($index_item_rule > 1)
		<a href="javascript:void(0);" class="btn btnRemoveItemRule btn-danger" onClick="removeItemRule({{ $index_item_rule }});"><i class="fa fa-minus"></i></a>
		@endif
	</div>
</div>
<div class="clearfix"></div>