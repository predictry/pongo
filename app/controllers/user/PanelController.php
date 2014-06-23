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
//	public function index2($selected_comparison = "sales", $type = "31_d_ago", $bar_type = 'stacked', $type_by = "day", $dt_start = null, $dt_end = null)
	public function index2($selected_comparison = "sales", $type = "31_d_ago", $type_by = "day", $dt_start = null, $dt_end = null)
	{
		$inputs = array(
			"type"					 => $type,
//			"bar_type"				 => $bar_type,
			"type_by"				 => $type_by,
			"dt_start"				 => $dt_start,
			"dt_end"				 => $dt_end,
			"selected_comparison"	 => $selected_comparison);

		if (isset($dt_start) && isset($dt_end))
		{
			$dt_start			 = $inputs["dt_start"]	 = isset($dt_start) ? new Carbon($dt_start) : new Carbon('today');
			$dt_end				 = $inputs["dt_end"]	 = isset($dt_end) ? new Carbon($dt_end) : new Carbon('today');
		}
		else
		{
			$dt_start		 = new Carbon('31 days ago');
			$today			 = new Carbon("today");
			$dt_end			 = $end_of_today	 = $today->endOfDay();
		}

		$dt_start_ori	 = new Carbon($dt_start);
		$dt_end_ori		 = new Carbon($dt_end);

		switch ($type)
		{
			case "range":
				$n = 1;

				$diff = $dt_start->diffInDays($dt_end);


				if ($diff >= 14 && $diff <= 31 && $type_by === "week")
				{
					$n = ($diff / 7);
				}
				else if ($diff > 31 && $diff <= 260 && ($type_by === "week" || $type_by === "day"))
				{
					$n		 = ($diff / 7);
					$type_by = "week";
				}
				else if ($diff > 31 && $diff <= 260 && $type_by === "month")
				{
					$n		 = ($diff / 30);
					$type_by = "month";
				}
				else if ($diff > 260)
				{
					$n		 = ($diff / 30);
					$type_by = "month";
				}
				else
				{
					$n		 = $diff;
					$type_by = "day";
				}
				$average_cart_sales_and_qty = $this->_getAverageOfCart($dt_start->startOfDay(), $dt_end->endOfDay());
				break;

			case "36_w_ago":
				$type_by					 = ($type_by === "month") ? $type_by : "week";
				$n							 = ($type_by === "month") ? (36 / 4) : 36;
				$average_cart_sales_and_qty	 = $this->_getAverageOfCart(new Carbon("36 weeks ago"), $end_of_today);
				break;

			case "12_m_ago":
				$n							 = 12;
				$type_by					 = "month";
				$average_cart_sales_and_qty	 = $this->_getAverageOfCart(new Carbon("12 months ago"), $end_of_today);
				break;

			case "31_d_ago":
			default:
				$type	 = "31_d_ago";
				$n		 = 31;

				$type_by = ($type_by === "week" || $type_by === "month") ? $type_by : "day";

				if ($type_by === "week")
					$n = (31 / 7);

				if ($type_by === "month")
					$n = (31 / 30);

				$average_cart_sales_and_qty = $this->_getAverageOfCart(new Carbon("31 days ago"), $end_of_today);
				break;
		}

		if ($type === "range" && $type_by == "day" && $n <= 31)
			$dt_ranges	 = $this->_getDateRanges($dt_start, $n);
		else
			$dt_ranges	 = $this->_getNDateRanges($n, $type_by);

		if ($selected_comparison === "sales")
			$stats	 = $this->_populateSalesStatsByRanges($dt_ranges);
		else
			$stats	 = $this->_populatePageViewStatsByRanges($dt_ranges);

		$graph_data							 = $highchart_categories				 = $graph_data							 = $average_recommended_items_pie_data	 = array();
		$total_overall						 = $total_recommended					 = 0;

		$highchart_series = array(
			array('name' => 'Regular', 'data' => array()),
			array('name' => 'Recommended', 'data' => array())
		);

		foreach ($stats as $stat)
		{
			if ($type_by === "day")
				$xlabel	 = ($stat['start']->format('M. d'));
			else
				$xlabel	 = ($stat['start']->format('M. d')) . '-' . ($stat['end']->format('M. d'));

			$total_overall += $stat['stat']['overall'];
			$total_recommended += $stat['stat']['recommended'];
			array_push($highchart_categories, $xlabel);
			array_push($highchart_series[1]['data'], $stat['stat']['recommended']);
			array_push($highchart_series[0]['data'], $stat['stat']['regular']);
		}

		if ($selected_comparison === "sales")
		{
			$graph_labels		 = array('Regular ($)', 'Recommended ($)');
			$highchart_pie_data	 = array(
				array(ucwords("regular " . $selected_comparison . "($)"), ($total_overall - $total_recommended)),
				array(ucwords("recommended " . $selected_comparison . "($)"), $total_recommended)
			);
		}
		else
		{
			$graph_labels		 = array('Regular', 'Recommended');
			$highchart_pie_data	 = array(
				array(ucwords("regular " . $selected_comparison), ($total_overall - $total_recommended)),
				array(ucwords("recommended " . $selected_comparison), $total_recommended)
			);
		}

		$highchart_average_recommended_items_pie_data = array(
			array("Regular Cart Items", $average_cart_sales_and_qty['average_regular_qty_items'] * 1),
			array("Recommended Cart Items", $average_cart_sales_and_qty['average_recommended_qty_items'] * 1)
		);

		$highchart_average_recommended_sales_pie_data = array(
			array("Regular Cart Items", $average_cart_sales_and_qty['average_regular_sub_totals'] * 1),
			array("Recommended Cart Items", $average_cart_sales_and_qty['average_recommended_sub_totals'] * 1)
		);

		if ($selected_comparison !== "sales")
		{
			$graph_title			 = $comparison_list[]		 = "Total Regular Page Views VS Page Views from Recommended Items";
			$comparison_list[]		 = "Total Regular Sales VS Sales from Recommended Items";
			$comparison_type_by[]	 = "pageviews";
			$comparison_type_by[]	 = "sales";
		}
		else
		{
			$graph_title			 = $comparison_list[]		 = "Total Regular Sales VS Sales from Recommended Items";
			$comparison_list[]		 = "Total Regular Page Views VS Page Views from Recommended Items";
			$comparison_type_by[]	 = "sales";
			$comparison_type_by[]	 = "pageviews";
		}

		//CTR
		$ctrData			 = $this->_getCTRData($dt_start_ori, $dt_end_ori);
		$highchart_ctr_data	 = array(
			array("Impression of Recommended Items", $ctrData['ngr']),
			array("Recommended Items Clicked", $ctrData['nr'])
		);


		$output = array(
			"pageTitle"											 => "dashboard",
			"type"												 => $type,
			"type_by"											 => $type_by,
			"bar_type"											 => true,
			"comparison_list"									 => $comparison_list,
			"comparison_type_by"								 => $comparison_type_by,
			"selected_comparison"								 => $selected_comparison,
			"total_overall"										 => $total_overall,
			"total_regular"										 => ($total_overall - $total_recommended),
			"total_recommended"									 => $total_recommended,
			"js_graph_labels"									 => json_encode($graph_labels),
			'js_graph_comparison_data'							 => json_encode($graph_data),
			'js_donut_average_recommended_items_data'			 => json_encode($average_recommended_items_pie_data),
			'js_highchart_pie_data'								 => json_encode($highchart_pie_data),
			'js_highchart_categories_data'						 => json_encode($highchart_categories),
			'js_highchart_series_data'							 => json_encode($highchart_series),
			'js_highchart_average_recommended_items_pie_data'	 => json_encode($highchart_average_recommended_items_pie_data),
			'js_highchart_average_recommended_sales_pie_data'	 => json_encode($highchart_average_recommended_sales_pie_data),
			'js_graph_title'									 => $graph_title,
			'js_highchart_ctr_data'								 => json_encode($highchart_ctr_data),
			'js_ctr_of_recommendation'							 => number_format($ctrData['ctr'], 2),
			'ctr_data'											 => $ctrData,
			"top_10_most_recommended_items"						 => $this->_getMostRecommendedItems($type, $dt_start_ori->startOfDay(), $dt_end_ori->endOfday()),
			"average_cart_items"								 => $average_cart_sales_and_qty,
			"ymax"												 => 0,
			"dt_begining"										 => $dt_start_ori->startOfDay()->format('F j, Y'),
			"dt_over"											 => $dt_end_ori->endOfday()->format('F j, Y'),
			"dt_start"											 => $dt_start_ori->format("Y-m-d"),
			"dt_end"											 => $dt_end_ori->format("Y-m-d")
		);


		return \View::make('frontend.panels.dashboard2', $output);
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

	function _getSalesStats($dt_start = null, $dt_end = null)
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

	function _getNDateRanges($n, $type_by)
	{
		$n			 = floor($n);
		$dt_ranges	 = array();

		for ($i = $n - 1; $i >= 0; $i--)
		{
			$dt_end = new Carbon("{$i} {$type_by} ago");
			if ($type_by === "day" || $type_by === "week")
			{
				if ($type_by === "week")
					$dt_start	 = new Carbon(($i + 1) . " {$type_by} ago");
				else
					$dt_start	 = new Carbon("{$i} {$type_by} ago");

				array_push($dt_ranges, array("start" => $dt_start->startOfDay(), "end" => $dt_end->endOfDay()));
			}
			else
			{
				$dt_start = new Carbon(($i + 1) . " {$type_by} ago");
				array_push($dt_ranges, array("start" => $dt_start->firstOfMonth(), "end" => $dt_end->endOfMonth()));
			}
		}
		return $dt_ranges;
	}

	function _getDateRanges($dt_start, $diff)
	{
		$dt_ranges = array();
		for ($i = 0; $i <= floor($diff); $i++)
		{
			$dt_start_temp	 = new Carbon($dt_start->toDateString());
			$dt_end_temp	 = new Carbon($dt_start->toDateString());
			array_push($dt_ranges, array("start" => $dt_start_temp->startOfDay(), "end" => $dt_end_temp->endOfDay()));
			$dt_start->addDay();
		}
		return $dt_ranges;
	}

	function _populateSalesStatsByRanges($dt_ranges)
	{
		$stats = array();
		foreach ($dt_ranges as $range)
		{
			$start	 = $range['start'];
			$end	 = $range['end'];
			array_push($stats, array('start' => $start, 'end' => $end, 'stat' => $this->_getSalesStats($range['start'], $range['end'])));
		}

		return $stats;
	}

	function _populatePageViewStatsByRanges($dt_ranges)
	{
		$stats = array();
		foreach ($dt_ranges as $range)
		{
			$start	 = $range['start'];
			$end	 = $range['end'];
			array_push($stats, array('start' => $start, 'end' => $end, 'stat' => $this->_getPageViewStats($range['start'], $range['end'])));
		}

		return $stats;
	}

	function _getAverageOfCart($dt_start = null, $dt_end = null)
	{
		if (!isset($dt_start) && !isset($dt_end))
		{
			$dt_start	 = new Carbon("today"); //today begining
			$dt_end		 = new Carbon("today"); //today ending
			$dt_end		 = $dt_end->endOfDay();
		}

		$complete_purchase_action = \App\Models\Action::where("name", "buy")->where("site_id", $this->active_site_id)->get()->first();

		$action_instance_ids = \App\Models\Action::find($complete_purchase_action->id)
						->action_instances()
						->whereBetween('created', [$dt_start, $dt_end])
						->get(array("action_instances.id AS action_instance_id"))->lists("action_instance_id");

//		$queries = \DB::getQueryLog();
//		
//		echo '<pre>';
//		print_r(end($queries));
//		echo "<br/>----<br/>";
//		echo '</pre>';
//		die;


		if (count($action_instance_ids) > 0)
		{

			$cart_ids = array_unique(\App\Models\ActionInstanceMeta::whereIn("action_instance_id", $action_instance_ids)
							->where("action_instance_metas.key", "cart_id")
							->get(array("action_instance_metas.value AS value"))->lists("value"));

			$recommended_action_instance_metas = \App\Models\Action::find($complete_purchase_action->id)
							->action_instances_and_metas()
							->where("action_instance_metas.key", "rec")
							->where("action_instance_metas.value", "true")
							->whereBetween('created', [$dt_start, $dt_end])
							->get(array("action_instances.id AS action_instance_id", "action_instance_metas.key", "action_instance_metas.value"))->lists("action_instance_id");


			$regular_action_instance_metas = array_diff($action_instance_ids, $recommended_action_instance_metas);

			if (count($recommended_action_instance_metas) > 0)
			{
				$sum_recommended_qty = \App\Models\ActionInstanceMeta::whereIn("action_instance_id", $recommended_action_instance_metas)
						->where("action_instance_metas.key", "qty")
						->get()
						->sum('value');

				$sum_recommended_sub_totals = \App\Models\ActionInstanceMeta::whereIn("action_instance_id", $recommended_action_instance_metas)
						->where("action_instance_metas.key", "sub_total")
						->get()
						->sum('value');
			}
			else
			{
				$sum_recommended_qty		 = $sum_recommended_sub_totals	 = 0;
			}

			if (count($regular_action_instance_metas) > 0)
			{
				$sum_regular_qty = \App\Models\ActionInstanceMeta::whereIn("action_instance_id", $regular_action_instance_metas)
						->where("action_instance_metas.key", "qty")
						->get()
						->sum('value');

				$sum_regular_sub_totals = \App\Models\ActionInstanceMeta::whereIn("action_instance_id", $regular_action_instance_metas)
						->where("action_instance_metas.key", "sub_total")
						->get()
						->sum('value');
			}
			else
			{
				$sum_regular_qty		 = $sum_regular_sub_totals	 = 0;
			}

			if (count($cart_ids) > 0)
			{
				$average_recommended_qty_items	 = ($sum_recommended_qty) / count($cart_ids);
				$average_regular_qty_items		 = ($sum_regular_qty) / count($cart_ids);

				$average_recommended_sub_totals	 = ($sum_recommended_sub_totals) / count($cart_ids);
				$average_regular_sub_totals		 = ($sum_regular_sub_totals) / count($cart_ids);

				return array(
					'total_carts'					 => count($cart_ids),
					'sum_recommended_qty'			 => $sum_recommended_qty,
					'average_recommended_qty_items'	 => number_format($average_recommended_qty_items, 2),
					'sum_regular_qty'				 => $sum_regular_qty,
					'average_regular_qty_items'		 => number_format($average_regular_qty_items, 2),
					'sum_recommended_sub_totals'	 => $sum_recommended_sub_totals,
					'average_recommended_sub_totals' => number_format($average_recommended_sub_totals, 2),
					'sum_regular_sub_totals'		 => $sum_regular_sub_totals,
					'average_regular_sub_totals'	 => number_format($average_regular_sub_totals, 2),
				);
			}
		}

		return array(
			'total_carts'					 => 0,
			'sum_recommended_qty'			 => 0,
			'average_recommended_qty_items'	 => number_format(0, 2),
			'sum_regular_qty'				 => 0,
			'average_regular_qty_items'		 => number_format(0, 2),
			'sum_recommended_sub_totals'	 => 0,
			'average_recommended_sub_totals' => number_format(0, 2),
			'sum_regular_sub_totals'		 => 0,
			'average_regular_sub_totals'	 => number_format(0, 2),
		);
	}

	function _getMostGenerateRecommendedItems()
	{
		$widget_ids	 = \App\Models\Widget::where("site_id", $this->active_site_id)->get()->lists('id');
		$item_ids	 = array();
		foreach ($widget_ids as $widget_id)
		{
			$item_ids = \App\Models\Widget::find($widget_id)->widget_instances_and_items()
					->groupBy('widget_instance_items.item_id')
					->groupBy('widget_instances.widget_id')
					->having(\DB::raw('COUNT(*)'), '>', 1)
					->orderBy('total', 'DESC')
					->limit(10)
					->get(array('widget_instance_items.item_id', \DB::raw('COUNT(*) AS total')))
					->lists("item_id");
		}

		$items = array();

		foreach ($item_ids as $id)
		{
			array_push($items, \App\Models\Item::find($id)->item_metas()->get()->toArray());
		}
		return $items;
	}

	function _getMostRecommendedItems($type = "view", $dt_start = null, $dt_end = null)
	{
		if (!isset($dt_start) && !isset($dt_end))
		{
			$dt_start	 = new Carbon("today"); //today begining
			$dt_end		 = new Carbon("today"); //today ending
			$dt_end		 = $dt_end->endOfDay();
		}

		$action_name = ($type === "sales") ? "buy" : "view";
		$action		 = \App\Models\Action::where("name", $action_name)->where("site_id", $this->active_site_id)->get()->first();

		$item_ids = \App\Models\Action::find($action->id)
				->action_instances_and_metas()
				->where("action_instance_metas.key", "rec")
				->where("action_instance_metas.value", "true")
				->whereBetween('created', [$dt_start, $dt_end])
				->groupBy('action_instances.item_id')
				->groupBy('action_instances.action_id')
				->having(\DB::raw('COUNT(*)'), '>', 1)
				->orderBy('total', 'DESC')
				->limit(10)
				->get(array("action_instances.item_id", \DB::raw('COUNT(*) AS total')))
				->lists("item_id");

		$items = array();

//		$queries = \DB::getQueryLog();
//		
//		echo '<pre>';
//		print_r(end($queries));
//		echo "<br/>----<br/>";
//		echo '</pre>';
//		die;

		foreach ($item_ids as $id)
		{
			$item = \App\Models\Item::find($id);
			if ($item)
			{
				$item_metas	 = $item->item_metas()->where("item_metas.key", "img_url")
						->where("item_metas.value", "!=", "")
						->limit(1)
						->get(array('item_metas.key', 'item_metas.value'))
						->toArray();
				if (!count($item_metas) > 0)
					$item_metas	 = null;

				array_push($items, array('item' => $item->toArray(), 'item_metas' => ($item_metas !== null) ? $item_metas[0] : null));
			}
		}
		return $items;
	}

	function _getCTRData($dt_start = null, $dt_end = null)
	{

		$page_view_stats = $this->_getPageViewStats($dt_start, $dt_end);
		$widget_ids		 = \App\Models\Widget::where("site_id", $this->active_site_id)->get()->lists("id");

		$n_widget_instances = \App\Models\WidgetInstance::whereIn("widget_id", $widget_ids)
				->whereBetween("created_at", [$dt_start, $dt_end])
				->count();

		$total_regular_pageviews		 = $page_view_stats['regular'];
		$total_recommended_pageviews	 = $page_view_stats['recommended'];
		$total_recommendation_generated	 = $n_widget_instances;

		$result = array(
			'np'		 => $total_regular_pageviews,
			'nr'		 => $total_recommended_pageviews,
			'ngr'		 => $total_recommendation_generated,
			'ctr'		 => ($total_recommendation_generated > 0) ? ($total_recommended_pageviews / $total_recommendation_generated) * 100 : 0,
			'impression' => $total_recommendation_generated
		);

		return $result;
	}

}
