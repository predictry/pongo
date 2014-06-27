<div class="form-inline">
	<div class="col-sm-3">
		<div class="form-group">
			<div class="form-group text-left">
				<!-- Single button -->
				<div class="btn-group" id="range-type">
					<button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown">
						{{ $comparison_list[0] }} <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a href="{{ URL::to('home2/'. $comparison_type_by[0] . '/' . $type . '/' . $type_by . '/'  . $dt_start . '/' . $dt_end) }}">{{ $comparison_list[0] }}</a></li>
						<li><a href="{{ URL::to('home2/'. $comparison_type_by[1] . '/' . $type . '/' . $type_by . '/'  . $dt_start . '/' . $dt_end) }}">{{ $comparison_list[1] }}</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 pull-right text-right">
		<div class="form-group text-left">
			<div id="reportrange" class="btn-group pull-right">
				<button class="btn btn-default btn-sm"><i class="fa fa-calendar fa-sm"></i> <span><?php echo $dt_begining; ?> - <?php echo $dt_over; ?></span> <b class="caret"></b></button> 
			</div>
		</div>
		<!--		<div class="form-group text-left">
					 Single button 
					<div class="btn-group" id="range-type">
						<button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown">
							{{ $dt_begining }} - {{ $dt_over }} <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="{{ URL::to('home2/'. $selected_comparison . '/31_d_ago' . '/' . $type_by) }}">Past 31 days</a></li>
							<li><a href="{{ URL::to('home2/'. $selected_comparison . '/36_w_ago' . '/' . $type_by) }}">Past 36 Weeks</a></li>
							<li><a href="{{ URL::to('home2/'. $selected_comparison . '/12_m_ago' . '/' . $type_by) }}">Past 12 Months</a></li>
							<li class="divider"></li>
							<li><a href="#">Pick Date Range</a></li>
						</ul>
					</div>
				</div>-->
		<!--		<div class="form-group">
		<?php // echo Form::select('display_mode', array("stacked" => "Stacked", "grouped" => "Grouped"), "stacked", array("class" => "form-control input-sm", "id" => "type_by")); ?>
				</div>-->
		<div class="form-group text-left">
			<div class="btn-group" id="range-type">
				<button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown">
					{{ ucwords($type_by) }} <span class="caret"></span>
				</button>
				<ul class="dropdown-menu pull-right" role="menu">
					<li><a href="{{ URL::to('home2/'. $selected_comparison . '/' . $type. '/' . 'day/' . $dt_start . '/' . $dt_end) }}">Day</a></li>
					<li><a href="{{ URL::to('home2/'. $selected_comparison . '/' . $type . '/' . 'week/' . $dt_start . '/' . $dt_end) }}">Week</a></li>
					<li><a href="{{ URL::to('home2/'. $selected_comparison  . '/' . $type . '/' . 'month/' . $dt_start . '/' . $dt_end) }}">Month</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="clearfix mb10"></div>

