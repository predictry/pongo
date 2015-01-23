<div class="item_rule" id="item_rule{{ $index_item_widget_ruleset }}">

    <div class="row">

        <div class="col-sm-6">
            <div class="form-group">
                <label for='item{{ $index_item_widget_ruleset }}' class="control-label col-sm-4">Ruleset Name</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <?php echo Form::select("item_id[]", $ruleset_list, $obj['ruleset_id'], array('class' => 'chosen-select', 'id' => "item{$index_item_widget_ruleset}", 'data-placeholder' => "Choose Item...", "style" => "width:350px;")); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="form-group">
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-default <?php echo ($obj['active'] === "activated") ? 'active' : ''; ?>">
                        <input type="radio" name="active{{ $index_item_widget_ruleset }}" value="activated" <?php echo ($obj['active'] === "activated") ? 'checked' : ''; ?>> Activated
                    </label>
                    <label class="btn btn-default <?php echo ($obj['active'] === "deactivated") ? 'active' : ''; ?>">
                        <input type="radio" name="active{{ $index_item_widget_ruleset }}" value="deactivated" <?php echo ($obj['active'] === "deactivated") ? 'checked' : ''; ?>> Deactivated
                    </label>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            @if ($index_item_widget_ruleset > 1)
            <a href="javascript:void(0);" class="btn btnRemoveItemPlacementRuleset btn-danger" onClick="removeItemRule({{ $index_item_widget_ruleset }}, 'item_rule');"><i class="fa fa-minus"></i></a>
            @endif
        </div>
        <?php echo Form::hidden("item_ruleset_id[]", $obj['id']); ?>
    </div>

</div>