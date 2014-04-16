<?php
/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 18, 2014 5:10:42 PM
 * File         : app/controllers/PanelsController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     :
 */

namespace App\Controllers\User;

use Carbon\Carbon;

class PanelController extends \App\Controllers\BaseController
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
	public function index()
	{
		if (!$this->active_site_id)
		{
			return "Not found any site activated as a default display.";
		}

		$dt_2_days_ago		 = new Carbon("2 days ago");
		$graph_x_keys		 = "date";
		$graph_stats_data	 = array();

		$dt_start_range = $dt_2_days_ago->toDateString();


		$available_site_actions		 = \App\Models\Action::where("site_id", $this->active_site_id)->get()->toArray();
		$available_site_action_ids	 = array_fetch($available_site_actions, "id");
		$available_site_action_names = array_fetch($available_site_actions, "name");
		$graph_y_keys				 = $available_site_action_names;

		for ($i = 0; $i < 5; $i++)
		{ //showing by day start from 2 days ago until 2 days after
			if ($i > 0)
			{
				$dt_2_days_ago = $dt_2_days_ago->addDay($i);
			}

			$dt_start	 = $dt_2_days_ago->createFromTimestamp($dt_2_days_ago->getTimestamp())->hour(0)->minute(0)->second(0);
			$dt_end		 = $dt_2_days_ago->createFromTimestamp($dt_2_days_ago->getTimestamp())->hour(23)->minute(59)->second(59);

			array_push($graph_stats_data, $this->_populateTodayActionStats($dt_start, $dt_end, $available_site_action_ids, $available_site_action_names));
		}

		$total_action_today = 0;

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

	function _populateTodayActionStats($dt_start, $dt_end, $action_ids, $action_names)
	{
		$graph_data = array("date" => $dt_start->toDateString());

		$i = 0;
		foreach ($action_ids as $id)
		{
			$total_action			 = \App\Models\ActionInstance::where("action_id", $id)->whereBetween('created', [$dt_start, $dt_end])->count();
			$total_action_overall	 = \App\Models\ActionInstance::where("action_id", $id)->count();

//			$total_action_today_details[$val]	 = $total_action;
//			$total_action_overall_details[$val]	 = $total_action_overall;

			$graph_data[$action_names[$i]] = $total_action;
			$i++;
		}

		return $graph_data;
	}

}
