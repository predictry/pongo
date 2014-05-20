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
//			\Auth::logout();
//			return "Not found any site activated as a default display.";
			return \Redirect::to('sites/wizard');
		}

		$overviews = $this->_getOverviewSummary();

		$dt_2_days_ago	 = new Carbon("2 days ago");
		$graph_x_keys	 = "date";
		$dt_start_range	 = $dt_2_days_ago->toDateString();

		$available_default_site_actions	 = \App\Models\Action::where("site_id", $this->active_site_id)->limit(4)->get()->toArray(); //limit 4 (first 4 are default actions)
		$available_site_action_ids		 = array_fetch($available_default_site_actions, "id");
		$available_site_action_names	 = array_fetch($available_default_site_actions, "name");
		$graph_y_keys					 = $available_site_action_names;

		//this is basically funnel
		$available_non_default_site_actions		 = \App\Models\Action::where("site_id", $this->active_site_id)->whereNotIn("id", $available_site_action_ids)->get()->toArray();
		$available_non_default_site_action_ids	 = array_fetch($available_non_default_site_actions, "id");
		$available_non_default_site_action_names = array_fetch($available_non_default_site_actions, "name");
		$graph_y_non_default_keys				 = $available_non_default_site_action_names;

		//check if he have default funnel preference
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

			$graph_default_funel_preference_stats_data = $this->_getActionGraphLineStats($dt_2_days_ago, $funel_default_preference_metas_ids, $funel_default_preference_meta_names, 3);
		}

		$graph_default_stats_data		 = $this->_getActionGraphLineStats($dt_2_days_ago, $available_site_action_ids, $available_site_action_names, 5);
		$graph_non_default_stats_data	 = $this->_getActionGraphLineStats($dt_2_days_ago, $available_non_default_site_action_ids, $available_non_default_site_action_names, 5);

		$total_action_today = 0;

		$dt_end_range = $dt_2_days_ago->toDateString();

		$graph_default_data = array(
			"graph_today_stats_data" => $graph_default_stats_data,
			"js_graph_stats_data"	 => json_encode($graph_default_stats_data),
			"graph_y_keys"			 => json_encode($graph_y_keys));

		if ($funel_default && count($funel_default_preference_metas_ids) > 0)
		{
			$graph_non_default_data = array(
				"graph_non_default_today_stats_data" => $graph_default_funel_preference_stats_data,
				"js_non_default_graph_stats_data"	 => json_encode($graph_default_funel_preference_stats_data),
				"graph_y_non_defaulty_keys"			 => json_encode($funel_default_preference_meta_names));
		}
		else
		{
			$graph_non_default_data = array(
				"graph_non_default_today_stats_data" => $graph_non_default_stats_data,
				"js_non_default_graph_stats_data"	 => json_encode($graph_non_default_stats_data),
				"graph_y_non_defaulty_keys"			 => json_encode($available_non_default_site_action_names));
		}
		//funel dropdown
		$funel_dropdown = \App\Models\FunelPreference::where("site_id", $this->active_site_id)->get()->lists("name", "id");

		$output	 = array(
			"total_action_today"		 => $total_action_today,
			"graph_x_keys"				 => $graph_x_keys,
			"str_date_range"			 => $dt_start_range . ' to ' . $dt_end_range,
			"funel_selected_dropdown"	 => isset($funel_default) && isset($graph_default_funel_preference_stats_data) ? $funel_default->id : null,
			"funel_dropdown"			 => $funel_dropdown,
			"overviews"					 => $overviews,
			"pageTitle"					 => "dashboard"
		);
		$output	 = array_merge($output, $graph_default_data, $graph_non_default_data);

		return \View::make('frontend.panels.dashboard', $output);
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
				return \Redirect::route('dashboard')->with("flash_message", "Successfully added funel.");
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

		return \Redirect::to("dashboard");
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

		return \Redirect::to("dashboard");
	}

	function _populateTodayActionStats($dt_start, $dt_end, $action_ids, $action_names, $index = false)
	{

		if (!$index)
			$graph_data		 = array("date" => $dt_start->toDateString());
		else
			$graph_data[]	 = $dt_start->toDateString();


		$i = 0;
		foreach ($action_ids as $id)
		{
			$total_action			 = \App\Models\ActionInstance::where("action_id", $id)->whereBetween('created', [$dt_start, $dt_end])->count();
			$total_action_overall	 = \App\Models\ActionInstance::where("action_id", $id)->count();

//			$total_action_today_details[$val]	 = $total_action;
//			$total_action_overall_details[$val]	 = $total_action_overall;

			if (!$index)
				$graph_data[$action_names[$i]]	 = $total_action;
			else
				$graph_data[]					 = $total_action;
			$i++;
		}

		return $graph_data;
	}

	function _getActionGraphLineStats($dt_start, $ids, $names, $until_next_few_days = 1, $index = false)
	{
		$graph_stats_data = array();

		for ($i = 0; $i < $until_next_few_days; $i++)
		{ //showing by day start from 2 days ago until 2 days after
			if ($i > 0)
			{
				$dt_start = $dt_start->addDay($i);
			}

			$dt_start	 = $dt_start->createFromTimestamp($dt_start->getTimestamp())->hour(0)->minute(0)->second(0);
			$dt_end		 = $dt_start->createFromTimestamp($dt_start->getTimestamp())->hour(23)->minute(59)->second(59);

			array_push($graph_stats_data, $this->_populateTodayActionStats($dt_start, $dt_end, $ids, $names, $index));
		}

		return $graph_stats_data;
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

}
