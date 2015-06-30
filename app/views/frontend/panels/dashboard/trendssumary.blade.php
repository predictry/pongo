<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Trends
                <i class="fa fa-info-circle tt pull-right" data-toggle="tooltip" data-placement="left" title="Trends are summary of comparison total actions."></i>
            </div>
            <!-- Table -->
            <table class="table table-bordered small mb0" id="trendsContent">
                <thead>
                    <tr>
                        <?php
                        foreach ($trends_data['header'] as $head) {
                            echo "<th>" . ucwords($head) . "</th>";
                        }
                        ?>
                    </tr>
                </thead>

                <tbody>

                    <?php
                    foreach ($trends_data['data'] as $trend) {
                        $changes_cls = '';

                        if ($trend['changes'] > 0)
                            $changes_cls = 'text-success';
                        else if ($trend['changes'] < 0)
                            $changes_cls = 'text-danger';
                        ?>
                        <tr>
                            <td><?php echo $trend['#']; ?></td>
                            <td><?php echo $trend['name']; ?></td>
                            <td><?php echo $trend['after']; ?></td>
                            <td><?php echo $trend['before']; ?></td>
                            <td class="<?php echo $changes_cls; ?>"><?php echo ($trend['changes'] > 0) ? '+' . $trend['changes'] : $trend['changes']; ?>%</td>
                        </tr>
    <?php
}
?>
                </tbody>
            </table>

            <div class="panel-footer text-right">
                <div class="btn-group" data-toggle="buttons" id="type_options">
                    <label class="btn btn-default btn-sm active">
                        <input type="radio" name="options" id="option1" class="options_trend" value="today" checked="checked"> Today
                    </label>
                    <label class="btn btn-default btn-sm">
                        <input type="radio" name="options" id="option2" class="options_trend" value="week"> This Week
                    </label>
                    <label class="btn btn-default btn-sm">
                        <input type="radio" name="options" id="option3" class="options_trend" value="month"> This Month
                    </label>
                </div>
            </div>

        </div>
    </div>
</div>