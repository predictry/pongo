<ul class = "list-group">
    <?php
    $i = 1;
    foreach ($action->entities as $entity) {
        $properties = $entity->properties;
        if (count($properties) > 0) {
            ?>
            <li class="list-group-item">
                <div class="row toggle-property" id="dropdown-detail-property-{{ $i }}" data-toggle="detail-property-{{ $i }}">
                    <div class="col-xs-10">{{ $entity->entity_name }}</div>
                    <div class="col-xs-2"><i class="fa fa-chevron-down pull-right"></i></div>
                </div>
                <div id="detail-property-{{ $i }}" class="property-wrap">
                    <div class="row">
                        <?php
                        foreach ($properties as $property) {
                            $disabled = 'disabled="" checked=""';
                            if (!isset($property->hidden)) {
                                ?>
                                <div class="col-xs-10">
                                    <div class="checkbox">
                                        <label>
                                            <input class="property <?php if ($property->compulsary) echo "disabled"; ?>" type="checkbox" value="{{ $property->field }}" <?php if (!$property->compulsary && !in_array($property->field, $excluded_properties)) echo "checked=''"; ?>  <?php if ($property->compulsary) echo $disabled; ?>>{{ $property->field }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-xs-2 info">
                                    <i class="fa fa-info-circle" data-toggle="tooltip" role="tooltip" data-placement="right" title="{{ $property->info or "" }}"></i>
                                </div>
                                <?php
                            }
                        }
                        echo "</div></li>";
                    }
                    $i++;
                }
                ?>
            </div>
        </div>
    </li>
</ul>
<script type="text/javascript">

    $(document).ready(function () {
        var tenant_name = '{{ $tenant_id }}';
        var action_name = '{{ $action_name }}';
        $(".property").change(function () {
            var excludedProperties = getUncheckedList($(".property"));
            getSnippedJSData(tenant_name, action_name, excludedProperties);
        });
        $('[data-toggle="tooltip"]').tooltip();
        var excludedProperties = getUncheckedList($(".property"));
    });

    $('.toggle-property').click(function () {
        $input = $(this);
        $target = $('#' + $input.attr('data-toggle'));
        $target.slideToggle();
    });
</script>