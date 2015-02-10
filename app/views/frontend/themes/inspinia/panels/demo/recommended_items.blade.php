<div id="recommended_items">
</div>

<script type="text/javascript">
    var site_url = "<?php echo URL::to('v2'); ?>";
    var response = <?php echo json_encode($dummy_reco_response); ?>
</script>

<ins class="predictry" data-predictry-widget-id="{{ $widget['id'] }}" data-predictry-item-id="{{ $item['identifier'] }}" data-predictry-callback="testCallback"></ins>
<!--<script type="text/javascript">_predictry.push(['getWidget']);</script>-->