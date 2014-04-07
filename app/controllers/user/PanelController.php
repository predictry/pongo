<?php

namespace user;

use Carbon\Carbon;

class PanelController extends \BaseController
{

	public function __construct()
	{
		parent::__construct();
		\View::share(array("ca" => get_class()));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	private $active_site_id = null;

	public function index()
	{
		$this->active_site_id = \Session::get('active_site_id');
		if (!$this->active_site_id)
		{
			$this->active_site_id = 1;
//			return "Not found any site activated as a default display.";
		}

		$dt_2_days_ago		 = new Carbon("2 days ago");
		$graph_y_keys		 = array("view", "rate", "add_to_cart", "buy");
		$graph_x_keys		 = "date";
		$graph_stats_data	 = array();

		$dt_start_range = $dt_2_days_ago->toDateString();

		for ($i = 0; $i < 5; $i++)
		{ //showing by day start from 2 days ago until 2 days after
			if ($i > 0)
			{
				$dt_2_days_ago = $dt_2_days_ago->addDay($i);
			}

			$dt_start	 = $dt_2_days_ago->createFromTimestamp($dt_2_days_ago->getTimestamp())->hour(0)->minute(0)->second(0);
			$dt_end		 = $dt_2_days_ago->createFromTimestamp($dt_2_days_ago->getTimestamp())->hour(23)->minute(59)->second(59);

			array_push($graph_stats_data, $this->_populateTodayActionStats($dt_start, $dt_end, $graph_y_keys));
		}

		$total_action_today = \Action::where("site_id", $this->active_site_id)->whereBetween('created_at', [$dt_start, $dt_end])->count();

		$dt_end_range = $dt_2_days_ago->toDateString();

		$output = array(
			"total_action_today"	 => $total_action_today,
			"graph_today_stats_data" => $graph_stats_data,
			"js_graph_stats_data"	 => json_encode($graph_stats_data),
			"graph_y_keys"			 => json_encode($graph_y_keys),
			"graph_x_keys"			 => $graph_x_keys,
			"str_date_range"		 => $dt_start_range . ' to ' . $dt_end_range,
			"pageTitle"				 => "Dashboard"
		);

		return \View::make('frontend.panels.dashboard', $output);
	}

	function _populateTodayActionStats($dt_start, $dt_end, $graph_y_keys)
	{
		$graph_data = array("date" => $dt_start->toDateString());

		foreach ($graph_y_keys as $val)
		{
			$total_action			 = \Action::where("site_id", $this->active_site_id)->where("name", $val)->whereBetween('created_at', [$dt_start, $dt_end])->count();
			$total_action_overall	 = \Action::where("site_id", $this->active_site_id)->where("name", $val)->count();

//			$total_action_today_details[$val]	 = $total_action;
//			$total_action_overall_details[$val]	 = $total_action_overall;

			$graph_data[$val] = $total_action;
		}

		return $graph_data;
	}

}
