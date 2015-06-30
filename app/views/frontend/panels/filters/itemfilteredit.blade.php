@if ($index_item > 1)
<div class="clearfix"></div>
@endif
<div class="form-group itemNodes {{ $var = $errors->first('action_id') }} {{ ($var !== '') ? 'has-error' : '' }}" id="item_filter{{$index_item}}">
    <div class="row">
        @if ($index_item == 1)
        <label for='score' class="col-sm-2 control-label">
            Filter
        </label>
        @else
        <div class="col-sm-2 text-right">
            <a href="javascript:void(0);" class="btn btnRemoveItem btn-danger" onClick="removeItem({{ $index_item }}, 'item_funel');"><i class="fa fa-minus"></i></a>
        </div>
        @endif
        <div class="col-sm-2">
            <?php echo Form::select('property[]', $properties, $obj['property'], array("class" => "form-control chosen-select-item", "id" => "action{$index_item}", 'data-placeholder' => "Choose Properties...")); ?>
        </div>
        <div class="col-sm-2">
            <?php echo Form::select('operator_key[]', $operator_types, $obj['operator'], array("class" => "form-control", 'data-placeholder' => "Choose Operators...")); ?>
        </div>
        <div class="col-sm-2">
            <?php echo Form::select('type[]', $types, $obj['type'], array("onchange" => "getFilterType(" . $index_item . ")", "class" => "form-control chosen-select", 'data-placeholder' => "Choose Data Type...", "id" => "type{$index_item}")); ?>
        </div>
        <div class="col-sm-4">
            <?php echo Form::text('value[]', $obj['value'], array('class' => 'form-control', 'placeholder' => '', 'id' => "value{$index_item}")); ?>
        </div>
        <!--        @if ($index_item > 1)
                <div class="col-sm-1">
                    <a href="javascript:void(0);" class="btn btnRemoveItem btn-danger" onClick="removeItem({{ $index_item }}, 'item_funel');"><i class="fa fa-minus"></i></a>
                </div>
                @endif	-->
    </div>
    <?php echo Form::hidden("filter_meta_id[]", $obj['id']); ?>
</div>
