<div class="form-inline ">
    <div class="col-sm-6 pull-right text-right comparison-filter" data-spy="affix" data-offset-top="10" data-offset-bottom="10">
        <div class="form-group text-left">
            <div id="reportrange2" class="btn-group pull-right">
                <button class="btn btn-default btn-sm"><i class="fa fa-calendar fa-sm"></i> <span><?php echo $dt_begining; ?> - <?php echo $dt_over; ?></span> <b class="caret"></b></button> 
            </div>
        </div>
        <div class="form-group">
            <?php echo Form::select('date_unit', array("day" => "Day", "week" => "Week", "month" => "	Month"), "day", array("class" => "form-control input-sm", "id" => "date_unit")); ?>
        </div>
    </div>
</div>
<div class="clearfix mb10"></div>