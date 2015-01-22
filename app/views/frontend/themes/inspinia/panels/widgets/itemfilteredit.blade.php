<div class="item_filter" id="item_filter{{ $index_item_widget_filter }}">

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for='item{{ $index_item_widget_filter }}' class="control-label col-sm-4">Filter Name</label>
                <div class="col-sm-8">
                    <?php echo Form::select("filter_id[]", $filter_list, $obj['filter_id'], array('class' => 'form-control chosen-select', 'id' => "itemfilter{$index_item_widget_filter}", 'data-placeholder' => "Choose Item...", "style" => "width:350px;")); ?>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="form-group">
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-default <?php echo ($obj['active'] === 'activated') ? 'active' : ''; ?>">
                        <input type="radio" name="filter_active{{ $index_item_widget_filter }}" value="activated" <?php echo ($obj['active'] === "activated") ? 'checked' : ''; ?>> Activated
                    </label>
                    <label class="btn btn-default <?php echo ($obj['active'] === 'deactivated') ? 'active' : ''; ?>">
                        <input type="radio" name="filter_active{{ $index_item_widget_filter }}" value="deactivated" <?php echo ($obj['active'] === "deactivated") ? 'checked' : ''; ?>> Deactivated
                    </label>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            @if ($index_item_widget_filter > 1)
            <a href="javascript:void(0);" class="btn btnRemoveItemPlacementRuleset btn-danger" onClick="removeItemRule({{ $index_item_widget_filter }}, 'item_rule');"><i class="fa fa-minus"></i></a>
            @endif
        </div>
    </div>

    <?php echo Form::hidden("item_filter_id[]", $obj['id']); ?>
</div>