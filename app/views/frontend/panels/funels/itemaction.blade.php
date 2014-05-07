<div class="form-group {{ $var = $errors->first('action_id') }} {{ ($var !== '') ? 'has-error' : '' }}" id="item_funel{{$index_item}}">
	<label for='score' class="col-sm-2 control-label">Action</label>
	<div class="col-sm-4">
		<?php echo Form::select('action_id[]', $available_non_default_site_actions_dropdown, null, array("class" => "form-control chosen-select-item", "id" => "action{$index_item}", 'data-placeholder' => "Choose Action...")); ?>
	</div>
	@if ($index_item > 1)
	<div class="col-sm-3">
		<a href="javascript:void(0);" class="btn btnRemoveItem btn-danger" onClick="removeItem({{ $index_item }}, 'item_funel');"><i class="fa fa-minus"></i></a>
	</div>
	@endif	
</div>
<?php echo Form::hidden("item_rule_id[]", "-1"); ?>

