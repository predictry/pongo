@if ($index_item > 1)
<div class="clearfix"></div>
@endif
<div class="form-group {{ $var = $errors->first('action_id') }} {{ ($var !== '') ? 'has-error' : '' }}" id="item_funel{{$index_item}}">
	<div class="row">
		<label for='score' class="col-sm-2 control-label">
			@if ($index_item == 1)
			Filter
			@endif
		</label>
		<div class="col-sm-4">
			<?php echo Form::select('property[]', $properties, $obj['property'], array("class" => "form-control chosen-select-item", "id" => "action{$index_item}", 'data-placeholder' => "Choose Properties...")); ?>
		</div>
		<div class="col-sm-2">
			<?php echo Form::select('operator_key[]', $operator_types, $obj['operator'], array("class" => "form-control", 'data-placeholder' => "Choose Operators...")); ?>
		</div>
		<div class="col-sm-3">
			<?php echo Form::text('value[]', $obj['value'], array('class' => 'form-control', 'placeholder' => '', 'id' => "value{$index_item}")); ?>
		</div>
		<!--@if ($index_item > 1)-->
		<div class="col-sm-1">
			<a href="javascript:void(0);" class="btn btnRemoveItem btn-danger" onClick="removeItem({{ $index_item }}, 'item_funel');"><i class="fa fa-minus"></i></a>
		</div>
		<!--@endif-->	
	</div>
	<?php echo Form::hidden("filter_meta_id[]", $obj['id']); ?>
</div>
