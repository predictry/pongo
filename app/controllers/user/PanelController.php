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

		if (!$this->active_site_id)
		{
			return \Redirect::to('sites/wizard');
		}

		$custom_script = "<script type='text/javascript'>";
		$custom_script .= "var site_url = '" . \URL::to('/') . "';";
		$custom_script .= "</script>";

		\View::share(array("ca" => get_class(), "custom_script" => $custom_script));
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
			return \Redirect::to('sites/wizard');
		}

		$today			 = new Carbon("today"); //today begining
		$tomorrow		 = new Carbon("tomorrow"); //tomorrow
		$end_of_today	 = $tomorrow->subSeconds(1); //today ending
		$graph_x_keys	 = "date";
		$dt_start_range	 = $today->toDateTimeString();
		$dt_end_range	 = $end_of_today->toDateTimeString();


		$available_default_site_actions	 = \App\Models\Action::where("site_id", $this->active_site_id)->limit(4)->get()->toArray(); //limit 4 (first 4 are default actions)
		$today_default_data				 = $this->_getActionStatsInfo($available_default_site_actions, $today, $end_of_today);
		$output_today_default_data		 = array(
			"graph_today_stats_data" => $today_default_data['stats']['data'],
			"js_graph_stats_data"	 => json_encode($today_default_data['stats']['data']),
			"graph_y_keys"			 => json_encode($today_default_data['stats']['y_keys']));


		$available_site_action_ids		 = $today_default_data['action_ids'];
		$today_funel_default_data		 = $this->_getFunelStatsInfo($available_site_action_ids, $today, $end_of_today);
		$output_today_funel_default_data = array(
			"graph_non_default_today_stats_data" => $today_funel_default_data['stats']['data'],
			"js_non_default_graph_stats_data"	 => json_encode($today_funel_default_data['stats']['data']),
			"graph_y_non_defaulty_keys"			 => json_encode($today_funel_default_data['stats']['y_keys']));

		//trends data
		$output_trends_data['trends_data'] = $this->_getActionTrends("today");

		//overview data
		$overviews = $this->_getOverviewSummary();

		//funel dropdown
		$funel_dropdown = \App\Models\FunelPreference::where("site_id", $this->active_site_id)->get()->lists("name", "id");

		$output = array(
			"total_action_today"		 => 0,
			"graph_x_keys"				 => $graph_x_keys,
			"str_date_range"			 => $dt_start_range . ' to ' . $dt_end_range,
			"funel_name"				 => $today_funel_default_data['funel_name'],
			"funel_selected_dropdown"	 => $today_funel_default_data['funel_selected_dropdown'],
			"funel_dropdown"			 => $funel_dropdown,
			"overviews"					 => $overviews,
			"pageTitle"					 => "dashboard"
		);


		$buy_action	 = \App\Models\Action::where("site_id", $this->active_site_id)->where("name", "buy")->get()->first(); //buy action
		$view_action = \App\Models\Action::where("site_id", $this->active_site_id)->where("name", "view")->get()->first(); //view action

		if ($buy_action)
			$output_top_items['top_purchased_items'] = \App\Models\ActionInstance::getMostItems($buy_action->id);

		if ($view_action)
			$output_top_items['top_viewed_items'] = \App\Models\ActionInstance::getMostItems($view_action->id);

		$output = array_merge($output, $output_today_default_data, $output_today_funel_default_data, $output_trends_data, $output_top_items);

		return \View::make('frontend.panels.dashboard', $output);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index2($type = "31_d_ago", $type_by = "day", $dt_start = null, $dt_end = null, $selected_comparison = "sales")
	{
		$inputs = array(
			"type"					 => $type,
			"type_by"				 => $type_by,
			"dt_start"				 => $dt_start,
			"dt_end"				 => $dt_end,
			"selected_comparison"	 => $selected_comparison);

		if (isset($dt_start) && isset($dt_end))
		{
			$inputs["dt_start"]	 = isset($dt_start) ? new Carbon($dt_start) : new Carbon('today');
			$inputs["dt_end"]	 = isset($dt_end) ? new Carbon($dt_end) : new Carbon('today');
		}

		switch ($type)
		{
			case "range":
				break;

			case "36_w_ago":
				$n		 = 36;
				$type_by = "week";
				break;

			case "12_m_ago":
				$n		 = 12;
				$type_by = "month";
				break;

			case "31_d_ago":
			default:
				$n		 = 31;
				$type_by = "day";
				break;
		}

		$dt_ranges			 = $this->_getDateRanges($n, $type_by);
		$sales_stats		 = $this->_populateSalesStatsByRanges($dt_ranges);
		$page_views_stats	 = $this->_populatePageViewStatsByRanges($dt_ranges);

		echo '<pre>';
		print_r($page_views_stats);
		echo "<br/>----<br/>";
		print_r($sales_stats);
		echo "<br/>----<br/>";
		echo '</pre>';
		die;

		$output = array(
			"bar_stats"	 => array(),
			"pie_stats"	 => array("total_sales" => 0, "regular_sales" => 0, "recommended_sales" => 0),
			"pageTitle"	 => "dashboard"
		);

		return \View::make('frontend.panels.dashboard', $output);
	}

	function _getActionStatsInfo($site_actions, $dt_start, $dt_end, $is_recommended = false, $all = true)
	{
		$action_ids		 = array_fetch($site_actions, "id");
		$action_names	 = array_fetch($site_actions, "name");

		$graph_data[] = $this->_populateDateRangeActionStats($dt_start, $dt_end, $action_ids, $action_names, false, $is_recommended, $all);

		$output = array(
			"stats"			 => array(
				"data"	 => $graph_data,
				"x_keys" => "date",
				"y_keys" => $action_names
			),
			"action_ids"	 => $action_ids,
			"action_names"	 => $action_names
		);

		return $output;
	}

	function _getFunelStatsInfo($action_ids, $dt_start, $dt_end)
	{
		//this is basically funnel
		$available_non_default_site_action_ids	 = \App\Models\Action::where("site_id", $this->active_site_id)->whereNotIn("id", $action_ids)->get()->lists("id");
		$available_non_default_site_action_names = \App\Models\Action::where("site_id", $this->active_site_id)->whereNotIn("id", $action_ids)->get()->lists("name");

		//check if he have default funnel preference
		$total_funnels = \App\Models\FunelPreference::where("site_id", $this->active_site_id)->get()->count();

		if ($total_funnels <= 0)
		{
			$funel_preference				 = new \App\Models\FunelPreference();
			$funel_preference->site_id		 = $this->active_site_id;
			$funel_preference->name			 = "Default";
			$funel_preference->is_default	 = true;
			$funel_preference->save();

			$i = 1;
			foreach ($available_non_default_site_action_ids as $action_id)
			{
				$funel_preference_meta						 = new \App\Models\FunelPreferenceMeta();
				$funel_preference_meta->action_id			 = $action_id;
				$funel_preference_meta->funel_preference_id	 = $funel_preference->id;
				$funel_preference_meta->sort				 = $i++;
				$funel_preference_meta->save();
			}
		}

		$funel_default = \App\Models\FunelPreference::where("site_id", $this->active_site_id)->where("is_default", true)->first();
		if ($funel_default)
		{
			if (strtolower($funel_default->name) === "default")
				$this->_setDefaultFunelPreferenceMetas($funel_default->id, $available_non_default_site_action_ids);

			$funel_default_preference_metas_ids	 = \App\Models\FunelPreferenceMeta::where("funel_preference_id", $funel_default->id)->orderBy('sort', 'ASC')->get()->lists("action_id");
			$funel_default_preference_meta_names = array();
			foreach ($funel_default_preference_metas_ids as $val)
			{
				$o = \App\Models\Action::find($val);
				if ($o)
					array_push($funel_default_preference_meta_names, $o->name);
			}

			$graph_data[] = $this->_populateDateRangeActionStats($dt_start, $dt_end, $funel_default_preference_metas_ids, $funel_default_preference_meta_names);

			$output = array(
				"stats"						 => array(
					"data"	 => $graph_data,
					"x_keys" => "date",
					"y_keys" => $funel_default_preference_meta_names
				),
				"action_ids"				 => $funel_default_preference_metas_ids,
				"action_names"				 => $funel_default_preference_meta_names,
				'funel_selected_dropdown'	 => $funel_default->id,
				'funel_name'				 => strtolower($funel_default->name)
			);
		}else
		{
			$graph_data[] = $this->_populateDateRangeActionStats($dt_start, $dt_end, $available_non_default_site_action_ids, $available_non_default_site_action_names);

			$output = array(
				"stats"						 => array(
					"data"	 => $graph_data,
					"x_keys" => "date",
					"y_keys" => $funel_default_preference_meta_names
				),
				"action_ids"				 => $available_non_default_site_action_ids,
				"action_names"				 => $available_non_default_site_action_names,
				'funel_selected_dropdown'	 => null,
				'funel_name'				 => 'not found'
			);
		}

		return $output;
	}

	function _getActionTrends($type = "today")
	{
		$site_actions = \App\Models\Action::where("site_id", $this->active_site_id)->get(array("id", "name"))->toArray();

		$before_dt_start = $before_dt_end	 = $after_dt_start	 = $after_dt_end	 = null;
		$head_after		 = "today";
		$head_before	 = "yesterday";
		switch ($type)
		{
			case "today":
				$today				 = new Carbon("today"); //today begining
				$tomorrow			 = new Carbon("tomorrow"); //tomorrow
				$yesterday			 = new Carbon("yesterday");
				$end_of_today		 = $tomorrow->subSeconds(1); //today ending
				$end_of_yesterday	 = $today->subSeconds(1);

				$after_dt_start	 = new Carbon("today");
				$after_dt_end	 = $end_of_today;
				$before_dt_start = new Carbon("yesterday");
				$before_dt_end	 = $end_of_yesterday;
				break;

			case "week":
				$this_week	 = new Carbon("this week");
				$last_week	 = new Carbon("last week");
				$next_week	 = new Carbon("next week");

				$after_dt_start	 = $this_week->createFromTimestamp($this_week->getTimestamp())->hour(0)->minute(0)->second(0);
				$after_dt_end	 = $next_week->createFromTimestamp($next_week->getTimestamp())->hour(0)->minute(0)->second(0)->subSeconds(1);
				$before_dt_start = $last_week->createFromTimestamp($last_week->getTimestamp())->hour(0)->minute(0)->second(0);
				$before_dt_end	 = $this_week->createFromTimestamp($this_week->getTimestamp())->hour(0)->minute(0)->second(0)->subSeconds(1);
				$head_after		 = "this week";
				$head_before	 = "last week";
				break;

			default:
				$this_month	 = new Carbon("this month");
				$last_month	 = new Carbon("last month");
				$next_month	 = new Carbon("next month");

				$after_dt_start	 = $this_month->createFromTimestamp($this_month->getTimestamp())->hour(0)->minute(0)->second(0);
				$after_dt_end	 = $next_month->createFromTimestamp($next_month->getTimestamp())->hour(0)->minute(0)->second(0)->subSeconds(1);
				$before_dt_start = $last_month->createFromTimestamp($last_month->getTimestamp())->hour(0)->minute(0)->second(0);
				$before_dt_end	 = $this_month->createFromTimestamp($this_month->getTimestamp())->hour(0)->minute(0)->second(0)->subSeconds(1);
				$head_after		 = "this month";
				$head_before	 = "last month";
				break;
		}

		$trends_data = array(
			'header' => array(
				'#', 'name', $head_after, $head_before, 'changes'
			),
			'data'	 => array()
		);

//		echo '<pre>';
//		print_r($after_dt_start);
//		echo "<br/>----<br/>";
//		print_r($after_dt_end);
//		echo "<br/>----<br/>";
//		print_r($before_dt_start);
//		echo "<br/>----<br/>";
//		print_r($before_dt_end);
//		echo "<br/>----<br/>";
//		echo '</pre>';
//		die;


		$i = 0;
		foreach ($site_actions as $action)
		{
			$total_after	 = \App\Models\ActionInstance::where("action_id", $action['id'])->whereBetween('created', [$after_dt_start, $after_dt_end])->count();
			$total_before	 = \App\Models\ActionInstance::where("action_id", $action['id'])->whereBetween('created', [$before_dt_start, $before_dt_end])->count();

			$changes = ($total_before > 0) ? (($total_after - $total_before) / $total_before) * 100 : ($total_after) * 100;

			if ($changes !== 0)
				$trends_data['data'][] = array(
					"#"			 => $i+=1,
					"name"		 => $action['name'],
					"after"		 => $total_after,
					"before"	 => $total_before,
					"changes"	 => number_format($changes, 2)
				);
		}

		return $trends_data;
	}

	function getTrends()
	{
		if (\Request::ajax())
		{
			$type		 = \Input::get("type");
			$trends_data = $this->_getActionTrends($type);

			return \Response::json(
							array("status"	 => "success",
								"response"	 => \View::make("frontend.panels.dashboard.trendscontentsummary", array(
									"trends_data" => $trends_data)
								)->render()
			));
		}
	}

	public function getCreateFunel($is_modal = false)
	{
		$is_modal = \Request::segment(3) !== "" ? \Request::segment(3) : false;

		$available_default_site_actions				 = \App\Models\Action::where("site_id", $this->active_site_id)->limit(4)->get()->toArray(); //limit 4 (first 4 are default actions)
		$available_site_action_ids					 = array_fetch($available_default_site_actions, "id");
		$available_non_default_site_actions_dropdown = \App\Models\Action::where("site_id", $this->active_site_id)->whereNotIn("id", $available_site_action_ids)->get()->lists("name", "id");

		$form = ($is_modal) ? "frontend.panels.funels.multiplechosenform" : "frontend.panels.funels.form";
		return \View::make($form, array(
					"available_non_default_site_actions_dropdown"	 => $available_non_default_site_actions_dropdown,
					"type"											 => "create",
					"index_item"									 => 1
		));
	}

	public function getItemFunel()
	{
		$index_item = \Input::get("index");

		$available_default_site_actions				 = \App\Models\Action::where("site_id", $this->active_site_id)->limit(4)->get()->toArray(); //limit 4 (first 4 are default actions)
		$available_site_action_ids					 = array_fetch($available_default_site_actions, "id");
		$available_non_default_site_actions_dropdown = \App\Models\Action::where("site_id", $this->active_site_id)->whereNotIn("id", $available_site_action_ids)->get()->lists("name", "id");

		return \Response::json(
						array("status"	 => "success",
							"response"	 => \View::make("frontend.panels.funels.itemaction", array(
								"available_non_default_site_actions_dropdown"	 => $available_non_default_site_actions_dropdown,
								"index_item"									 => $index_item)
							)->render()
		));
	}

	public function postCreateFunel()
	{

		$input		 = \Input::only("name", "action_id");
		$validator	 = \Validator::make($input, array(
					"name"		 => "required",
					"action_id"	 => "required"
		));

		if ($validator->passes())
		{

			$funel				 = new \App\Models\FunelPreference();
			$funel->name		 = $input['name'];
			$funel->site_id		 = $this->active_site_id;
			$funel->is_default	 = false;
			$funel->save();

			if ($funel->id)
			{
				$i = 0;
				foreach ($input['action_id'] as $action_id)
				{
					$i++;
					$funel_meta						 = new \App\Models\FunelPreferenceMeta();
					$funel_meta->action_id			 = $action_id;
					$funel_meta->funel_preference_id = $funel->id;
					$funel_meta->sort				 = $i;
					$funel_meta->save();
				}
			}
			if (\Request::ajax())
			{
				return \Response::json(array("status" => "success", "response" => "/dashboard"));
			}
			else
			{
				return \Redirect::route('home')->with("flash_message", "Successfully added funel.");
			}
		}

		$available_default_site_actions				 = \App\Models\Action::where("site_id", $this->active_site_id)->limit(4)->get()->toArray(); //limit 4 (first 4 are default actions)
		$available_site_action_ids					 = array_fetch($available_default_site_actions, "id");
		$available_non_default_site_actions_dropdown = \App\Models\Action::where("site_id", $this->active_site_id)->whereNotIn("id", $available_site_action_ids)->get()->lists("name", "id");
		if (\Request::ajax())
		{
			return \Response::json(
							array("status"	 => "error",
								"response"	 => \View::make("frontend.panels.funels.multiplechosenform", array(
									"available_non_default_site_actions_dropdown" => $available_non_default_site_actions_dropdown
								))->withErrors($validator)->render()
			));
		}
		else
		{
			return \Redirect::back()->withErrors($validator);
		}
	}

	public function postDefaultFunel()
	{
		\App\Models\FunelPreference::where("site_id", $this->active_site_id)->update(array("is_default" => false));

		$funel				 = \App\Models\FunelPreference::find(\Input::get("funel_preference_id"));
		$funel->is_default	 = true;
		$funel->update();

		return \Redirect::to('home');
	}

	public function postDeleteFunel()
	{
		$funel_preference_id = \Input::get("funel_preference_id");

		if ($funel_preference_id)
		{
			$funel_default = \App\Models\FunelPreference::where("site_id", $this->active_site_id)->where("name", "Default")->get()->first();


			if ($funel_default)
			{
				$funel_default->is_default = true;
				$funel_default->update();
			}
			\App\Models\FunelPreferenceMeta::where("funel_preference_id", $funel_preference_id)->delete();
			\App\Models\FunelPreference::where("site_id", $this->active_site_id)->where("id", $funel_preference_id)->delete();
		}

		return \Redirect::to('home');
	}

	function _populateDateRangeActionStats($dt_start, $dt_end, $action_ids, $action_names, $index = false, $is_recommended = false, $all = true, $result_only = false)
	{

		if (!$result_only)
		{
			if (!$index)
				$graph_data		 = array("date" => $dt_start->toDateString());
			else
				$graph_data[]	 = $dt_start->toDateString();
		}

		$i = 0;

		foreach ($action_ids as $id)
		{
			if (!$all)
			{
				$total_recommended_action = \App\Models\Action::find($id)->action_instances_and_metas()
								->where("action_instance_metas.key", "rec")
								->where("action_instance_metas.value", "true")
								->whereBetween('created', [$dt_start, $dt_end])
								->get()->count();

				if ($is_recommended)
					$total_action = $total_recommended_action;
				else
				{
					$total_action = (\App\Models\Action::find($id)->action_instances()
									->whereBetween('created', [$dt_start, $dt_end])
									->get()->count()) - $total_recommended_action;
				}
			}
			else
				$total_action = \App\Models\Action::find($id)->action_instances()
						->whereBetween('created', [$dt_start, $dt_end])
						->count();

			if (!$result_only)
			{
				if (!$index)
					$graph_data[$action_names[$i]]	 = $total_action;
				else
					$graph_data[]					 = $total_action;
			}
			else
				$graph_data[$action_names[$i]] = $total_action;
			$i++;
		}

		return $graph_data;
	}

	function _setDefaultFunelPreferenceMetas($funel_preference_id, $non_default_action_ids)
	{
		\App\Models\FunelPreferenceMeta::where("funel_preference_id", $funel_preference_id)->delete();
		$i = 0;
		foreach ($non_default_action_ids as $action_id)
		{
			$funel_preference_meta						 = new \App\Models\FunelPreferenceMeta();
			$funel_preference_meta->action_id			 = $action_id;
			$funel_preference_meta->funel_preference_id	 = $funel_preference_id;
			$funel_preference_meta->sort				 = $i++;
			$funel_preference_meta->save();
		}
	}

	function _getOverviewSummary()
	{
		$today				 = new Carbon("today"); //today begining
		$tomorrow			 = new Carbon("tomorrow"); //tomorrow
		$end_of_today		 = $tomorrow->subSeconds(1); //today ending
		//today actions
		$action_ids			 = \App\Models\Action::where("site_id", $this->active_site_id)->get()->lists("id");
		$today_total_actions = \App\Models\ActionInstance::whereIn("action_id", $action_ids)->whereBetween('created', [$today, $end_of_today])->count();

		//today items
		$today_total_items = \App\Models\Item::where("site_id", $this->active_site_id)->whereBetween('created_at', [$today, $end_of_today])->count();

		//today buy
		$today_total_buy_action	 = 0;
		$buy_action				 = \App\Models\Action::where("site_id", $this->active_site_id)->where("name", "buy")->get()->first();
		if ($buy_action)
			$today_total_buy_action	 = \App\Models\ActionInstance::where("action_id", $buy_action->id)->whereBetween('created', [$today, $end_of_today])->count();

		//completion rate
		//check if he have default funnel preference
		$funel_default		 = \App\Models\FunelPreference::where("site_id", $this->active_site_id)->where("is_default", true)->first();
		$funnel_stats_data	 = array();
		$rates				 = array();

		if ($funel_default && strtolower($funel_default->name) !== "default")
		{
			$funnel_action_ids = \App\Models\FunelPreferenceMeta::where("funel_preference_id", $funel_default->id)->orderBy('sort', 'ASC')->get()->lists("action_id");
			foreach ($funnel_action_ids as $action_id)
			{
				$funnel_stats_data[] = \App\Models\Action::getNumberOfTotalActionsOverallByActionId($action_id);
			}

			for ($i = 0; $i < count($funnel_stats_data); $i++)
			{
				if (($i + 1) <= count($funnel_stats_data) - 1)
				{
					$rates[] = ($funnel_stats_data[$i + 1] / $funnel_stats_data[$i]) * 100;
				}
			}
		}

//		echo '<pre>';
//		print_r($today_total_actions);
//		echo "<br/>----<br/>";
//		print_r($today_total_items);
//		echo "<br/>----<br/>";
//		print_r($today_total_buy_action);
//		echo "<br/>----<br/>";
//		print_r($tomorrow);
//		echo "<br/>----<br/>";
//		print_r($funnel_stats_data);
//		echo "<br/>----<br/>";
//		print_r($rates);
//		echo '</pre>';
//		die;

		$overview_results = array(
			'today_total_actions'	 => $today_total_actions,
			'today_total_items'		 => $today_total_items,
			'today_total_buy_action' => $today_total_buy_action,
			'completion_rate'		 => (count($rates) > 0) ? number_format(end($rates), 2) : 0,
			"funel_default_name"	 => $funel_default->name
		);

		return $overview_results;
	}

	function _getTotalByRangeType($range_type = "today", $start = null, $end = null)
	{
		$graph = array();
		switch ($range_type)
		{
			case "today":
				$tomorrow		 = new Carbon("tomorrow"); //tomorrow
				$today			 = new Carbon("today"); //today
				$end_of_today	 = $tomorrow->subSeconds(1); //today ending
				$dt_start		 = $today->toDateTimeString();
				$dt_end			 = $end_of_today->toDateTimeString();


				break;

			case "past_31_days":
				for ($i = 30; $i >= 1; $i--)
				{
					if ($i > 1)
					{
						$yesterday	 = new Carbon(($i - 1) . " days ago"); //tomorrow
						$today		 = new Carbon($i . " days ago"); //today
					}
					else
					{
						$yesterday	 = new Carbon("today"); //tomorrow
						$today		 = new Carbon("yesterday"); //today
					}
					$dt_start	 = $today->createFromTimestamp($today->getTimestamp())->hour(0)->minute(0)->second(0);
					$dt_end		 = $yesterday->createFromTimestamp($yesterday->getTimestamp())->hour(0)->minute(0)->second(0)->subSeconds(1);
				}

				break;

			case "range":
				$dt_start	 = new Carbon($start);
				$dt_end		 = new Carbon($end);
				break;

			default:
				break;
		}

		return $graph;
	}

	function getShowStats()
	{
		$today			 = new Carbon("today"); //today begining
		$tomorrow		 = new Carbon("tomorrow"); //tomorrow
		$end_of_today	 = $tomorrow->subSeconds(1); //today ending
		$dt_start_range	 = $today->toDateTimeString();
		$dt_end_range	 = $end_of_today->toDateTimeString();

		/*
		 * TODAY OVERALL, REGULAR AND RECOMMENDED STATS OF VIEW AND COMPLETE_PURCHASE / BUY
		 */
		$page_view_stats = $this->_getPageViewStats();

		/*
		 * ALRIGHT, IT SEEMS NOW IS POSSIBLE TO FETCH THE SALES AMOUNT! SINCE WE HAVE SOME SORT OF 'AWESOME' ACTION INSTANCE META KEY CALLED 'SUB_TOTAL' OF BUY ACTION
		 * 
		 * REMEMBER COMPLETE_PURCHASE EQUAL (=) or SAME (SAMA) TO BUY
		 */
		$sales_stats = $this->_getSalesStats();

		/*
		 * FUNNEL (IS NOT REALLY SOMETHING THAT WE CAN RELY ON NOW. BETTER FIX THE FLOW BEFORE COME OUT WITH THE RESULTS)
		 * "YOU KNOW NOTHING, JOHN SNOW."
		 * 
		 * KBYE
		 */
		$funnel_actions				 = \App\Models\Action::where("site_id", $this->active_site_id)->whereIn("name", array("view", "add_to_cart", "complete_purchase"))->get()->toArray(); //limit 4 (first 4 are default actions)
		$funnel_regular_stats		 = $this->_getActionStatsInfo($funnel_actions, $today, $end_of_today, false, false);
		$funnel_recommended_stats	 = $this->_getActionStatsInfo($funnel_actions, $today, $end_of_today, true, false);

		echo "<h3>Today Stats ({$dt_start_range} - {$dt_end_range})</h3>";
		echo "Total Overall Page Views => " . $page_view_stats['overall']['view'];
		echo "<br/>----<br/>";
		echo "Total Regular Page Views => " . $page_view_stats['regular']['view'];
		echo "<br/>----<br/>";
		echo "Total Recommended Page Views => " . $page_view_stats['recommended']['view'];
		echo "<br/>----<br/>";
		echo "Total Overall Item Purchased => " . $page_view_stats['overall']['buy'];
		echo "<br/>----<br/>";
		echo "Total Regular Item Purchased => " . $page_view_stats['regular']['buy'];
		echo "<br/>----<br/>";
		echo "Total Recommended Item Purchased => " . $page_view_stats['recommended']['buy'];
		echo "<br/>----<br/>";
		echo "Total Sales ($) Overall Item Purchased => {$sales_stats['overall']}";
		echo "<br/>----<br/>";
		echo "Total Sales ($) Regular Item Purchased => " . ($sales_stats['regular']);
		echo "<br/>----<br/>";
		echo "Total Sales ($) Recommended Item Purchased => {$sales_stats['recommended']}";
		echo "<br/>----";

		die;
	}

	function _getSalesStats()
	{
		if (!isset($dt_start) && !isset($dt_end))
		{
			$dt_start	 = new Carbon("today"); //today begining
			$dt_end		 = new Carbon("today"); //today ending
			$dt_end		 = $dt_end->endOfDay();
		}

		$complete_purchase_action = \App\Models\Action::where("name", "buy")->where("site_id", $this->active_site_id)->get()->first();

		$sales_stats['overall'] = \App\Models\Action::find($complete_purchase_action->id)
						->action_instances_and_metas()
						->where("action_instance_metas.key", "sub_total")
						->whereBetween('created', [$dt_start, $dt_end])
						->get(array("action_instances.id AS action_instance_id", "action_instance_metas.key", "action_instance_metas.value"))->sum('value');

		$action_instance_ids = \App\Models\Action::find($complete_purchase_action->id)
						->action_instances_and_metas()
						->where("action_instance_metas.key", "rec")
						->where("action_instance_metas.value", "true")
						->whereBetween('created', [$dt_start, $dt_end])
						->get(array("action_instances.id AS action_instance_id", "action_instance_metas.key", "action_instance_metas.value"))->lists("action_instance_id");

		if (count($action_instance_ids) > 0)
		{
			$sales_stats['recommended'] = \App\Models\ActionInstanceMeta::whereIn("action_instance_id", $action_instance_ids)
					->where("action_instance_metas.key", "sub_total")
					->get()
					->sum('value');
		}
		else
			$sales_stats['recommended'] = 0;

		$sales_stats['regular'] = $sales_stats['overall'] - $sales_stats['recommended'];

		return $sales_stats;
	}

	function _getPageViewStats($dt_start = null, $dt_end = null)
	{
		if (!isset($dt_start) && !isset($dt_end))
		{
			$dt_start	 = new Carbon("today"); //today begining
			$dt_end		 = new Carbon("today"); //today ending
			$dt_end		 = $dt_end->endOfDay();
		}

		$view_action = \App\Models\Action::where("name", "view")->where("site_id", $this->active_site_id)->get()->first();

		/*
		 * TODAY OVERALL, REGULAR AND RECOMMENDED STATS OF VIEW AND COMPLETE_PURCHASE / BUY
		 */
		$page_view_stats['overall']		 = current($this->_populateDateRangeActionStats($dt_start, $dt_end, array($view_action->id), array($view_action->name), false, false, true, true));
		$page_view_stats['recommended']	 = current($this->_populateDateRangeActionStats($dt_start, $dt_end, array($view_action->id), array($view_action->name), false, true, false, true));
		$page_view_stats['regular']		 = current($this->_populateDateRangeActionStats($dt_start, $dt_end, array($view_action->id), array($view_action->name), false, false, false, true));

		return $page_view_stats;
	}

	function _getDateRanges($n, $type_by)
	{
		$dt_ranges = array();
		for ($i = $n; $i >= 1; $i--)
		{
			if ($type_by === "day")
			{
				$dt_end		 = new Carbon("{$i} {$type_by} ago");
				$dt_start	 = new Carbon("{$i} {$type_by} ago");
			}
			else
			{
				$dt_end		 = new Carbon("{$i} {$type_by} ago");
				$dt_start	 = new Carbon(($i + 1) . " {$type_by} ago");
			}
			array_push($dt_ranges, array("start" => $dt_start->startOfDay(), "end" => $dt_end->endOfDay()));
		}

		return $dt_ranges;
	}

	function _populateSalesStatsByRanges($dt_ranges)
	{
		$stats = array();
		foreach ($dt_ranges as $range)
		{
			array_push($stats, array('start' => $range['start']->toDateTimeString(), 'end' => $range['end']->toDateTimeString(), 'stat' => $this->_getSalesStats($range['start'], $range['end'])));
		}

		return $stats;
	}

	function _populatePageViewStatsByRanges($dt_ranges)
	{
		$stats = array();
		foreach ($dt_ranges as $range)
		{
			array_push($stats, array('start' => $range['start']->toDateTimeString(), 'end' => $range['end']->toDateTimeString(), 'stat' => $this->_getPageViewStats($range['start'], $range['end'])));
		}
		return $stats;
	}

}
