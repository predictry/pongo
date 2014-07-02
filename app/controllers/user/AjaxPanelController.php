<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Jun 25, 2014 12:18:58 PM
 * File         : app/controllers/AjaxPanelController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Controllers\User;

use Carbon\Carbon;

class AjaxPanelController extends \App\Controllers\BaseController
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

	public function comparisonGraph()
	{
		$inputs			 = \Input::only("dt_start", "dt_end", "comparison_type", "date_unit");
		$dt_start_ori	 = new Carbon($inputs['dt_start']);
		$dt_end_ori		 = new Carbon($inputs['dt_end']);

		$dt_start	 = new Carbon($inputs['dt_start']);
		$dt_end		 = new Carbon($inputs['dt_end']);
		$dt_end		 = $dt_end->endOfDay();

		$dt_average_start	 = new Carbon($inputs['dt_start']);
		$dt_average_end		 = new Carbon($inputs['dt_end']);

		$inputs['comparison_type']	 = ($inputs['comparison_type'] !== 'sales') ? 'pageviews' : $inputs['comparison_type'];
		$dt_ranges					 = $this->_getDateRanges($dt_start, $dt_end, $inputs['date_unit']);
		$stats						 = $highchart_categories		 = $highchart_series			 = array();
		$highchart_options_series	 = array(
			array(
				'name'	 => 'Total ' . ucwords($inputs['comparison_type']),
				'type'	 => 'column',
				'data'	 => array(),
				'color'	 => '#0077CC'
			),
			array(
				'name'	 => 'Recommended ' . ucwords($inputs['comparison_type']),
				'type'	 => 'spline',
				'data'	 => array(),
				'color'	 => '#FF9900'
		));

		$stats		 = ($inputs['comparison_type'] === 'sales') ? $this->_populateSalesStatsByRangesFromCache($dt_ranges, $inputs['date_unit']) : $this->_populatePageViewStatsByRanges($dt_ranges, $inputs['date_unit']);
		$other_stats = ($inputs['comparison_type'] === 'sales') ? $this->_populatePageViewStatsByRanges($dt_ranges, $inputs['date_unit']) : $this->_populateSalesStatsByRangesFromCache($dt_ranges, $inputs['date_unit']);

		$stats_summary				 = $this->_getSummaryOfStats($stats);
		$other_stats_summary		 = $this->_getSummaryOfStats($other_stats);
		$average_cart_sales_and_qty	 = $this->_getAverageOfCart($dt_average_start->startOfDay(), $dt_average_end->endOfDay());

		foreach ($stats as $stat)
		{
			if ($inputs['date_unit'] === "day")
				$xlabel	 = ( $stat['start']->format('d-m'));
			else
				$xlabel	 = ( $stat['start']->format('d-m')) . ' - ' . ( $stat['end']->format('d-m'));

			array_push($highchart_categories, $xlabel);
			array_push($highchart_options_series[1]['data'], $stat['stat']['recommended']);
			array_push($highchart_options_series[0]['data'], $stat['stat']['overall']);
		}


		$highchart_pie_data = array(
			array(ucwords("regular"), ($stats_summary['overall'] - $stats_summary['recommended'])),
			array(ucwords("recommended"), $stats_summary['recommended'])
		);

		$highchart_other_pie_data = array(
			array(ucwords("regular"), ($other_stats_summary['overall'] - $other_stats_summary['recommended'])),
			array(ucwords("recommended"), $other_stats_summary['recommended'])
		);

		$highchart_average_recommended_items_pie_data = array(
			array("Regular Cart Items", $average_cart_sales_and_qty['average_regular_qty_items'] * 1),
			array("Recommended Cart Items", $average_cart_sales_and_qty['average_recommended_qty_items'] * 1)
		);

		$highchart_average_recommended_sales_pie_data = array(
			array("Regular Cart Items", $average_cart_sales_and_qty['average_regular_sub_totals'] * 1),
			array("Recommended Cart Items", $average_cart_sales_and_qty['average_recommended_sub_totals'] * 1)
		);

		$response = array(
			'highchart_categories'							 => $highchart_categories,
			'highchart_options_series'						 => $highchart_options_series,
			'comparison_summaries'							 => $stats_summary,
			'other_comparison_summaries'					 => $stats_summary,
			'highchart_pie_data'							 => $highchart_pie_data,
			'highchart_other_pie_data'						 => $highchart_other_pie_data,
			'highchart_average_recommended_items_pie_data'	 => $highchart_average_recommended_items_pie_data,
			'highchart_average_recommended_sales_pie_data'	 => $highchart_average_recommended_sales_pie_data,
			'average_cart_sales_and_qty'					 => $average_cart_sales_and_qty,
			'y_title'										 => ($inputs['comparison_type'] === 'sales') ? 'RM' : 'Value'
		);

		return \Response::json(array("message" => "", "status" => "success", "response" => $response), "200");
	}

	function _getDateRanges($dt_start, $dt_end, &$date_unit = "day")
	{
		$dt_ranges	 = array();
		$n_diff		 = $dt_start->diffInDays($dt_end);

		if ($date_unit === "day" && $n_diff <= 31)
		{
			for ($i = 0; $i <= floor($n_diff); $i++)
			{
				$dt_start_temp	 = new Carbon($dt_start->toDateString());
				$dt_end_temp	 = new Carbon($dt_start->toDateString());
				array_push($dt_ranges, array("start" => $dt_start_temp->startOfDay(), "end" => $dt_end_temp->endOfDay()));
				$dt_start->addDay();
			}
		}
		else if (($date_unit === "week") && ($n_diff <= 120))
		{
			do
			{
				$dt_start_temp	 = new Carbon($dt_start->toDateString());
				$dt_end_temp	 = new Carbon($dt_start->toDateString());
				$dt_end_temp->endOfWeek();

				array_push($dt_ranges, array("start" => $dt_start_temp->startOfDay(), "end" => $dt_end_temp->endOfDay()));
				$dt_start = new Carbon($dt_end_temp);
				$dt_start->addDay();
			} while ($dt_end_temp->diffInDays($dt_end) > 7);
		}
		else if (($date_unit === "month" && $n_diff > 31) || $n_diff > 120)
		{
			$date_unit		 = "month";
			$n_month_diff	 = $dt_start->diffInMonths($dt_end);

			for ($i = 1; $i <= $n_month_diff; $i++)
			{
				$dt_start_temp	 = new Carbon($dt_start->toDateString());
				$dt_end_temp	 = new Carbon($dt_start->toDateString());
				$dt_end_temp->addMonth();

				array_push($dt_ranges, array("start" => $dt_start_temp->startOfDay(), "end" => $dt_end_temp->endOfDay()));
				$dt_start = new Carbon($dt_end_temp);
				$dt_start->addDay();
			}
		}
		if (count($dt_ranges) > 0)
		{
			$last_dt_range					 = end($dt_ranges);
			$n_diff_start_with_end_ranges	 = $last_dt_range['end']->diffInDays($dt_end);

			if ($n_diff_start_with_end_ranges > 0)
			{
				$dt_start_temp = new Carbon($last_dt_range['end']->toDateString());
				$dt_start_temp->addDay();

				$dt_end_temp = new Carbon($last_dt_range['end']->toDateString());
				$dt_end_temp->addDays($n_diff_start_with_end_ranges);

				array_push($dt_ranges, array("start" => $dt_start_temp->startOfDay(), "end" => $dt_end_temp->endOfDay()));
			}
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

	function _getSummaryOfStats($stats)
	{
		$total_overall		 = $total_recommended	 = 0;
		for ($i = 0; $i < count($stats); $i++)
		{
			$total_overall += $stats[$i]['stat']['overall'];
			$total_recommended += $stats[$i]['stat']['recommended'];
		}

		return array('overall' => $total_overall, 'recommended' => $total_recommended, 'regular' => $total_overall - $total_recommended);
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
					'total_carts'						 => count($cart_ids),
					'total_combination_of_qty'			 => $sum_recommended_qty + $sum_regular_qty,
					'total_combination_of_sub_totals'	 => $sum_recommended_sub_totals + $sum_regular_sub_totals,
					'sum_recommended_qty'				 => $sum_recommended_qty,
					'average_recommended_qty_items'		 => number_format($average_recommended_qty_items, 2),
					'sum_regular_qty'					 => $sum_regular_qty,
					'average_regular_qty_items'			 => number_format($average_regular_qty_items, 2),
					'sum_recommended_sub_totals'		 => $sum_recommended_sub_totals,
					'average_recommended_sub_totals'	 => number_format($average_recommended_sub_totals, 2),
					'sum_regular_sub_totals'			 => $sum_regular_sub_totals,
					'average_regular_sub_totals'		 => number_format($average_regular_sub_totals, 2),
				);
			}
		}

		return array(
			'total_carts'						 => 0,
			'total_combination_of_qty'			 => 0,
			'total_combination_of_sub_totals'	 => 0,
			'sum_recommended_qty'				 => 0,
			'average_recommended_qty_items'		 => number_format(0, 2),
			'sum_regular_qty'					 => 0,
			'average_regular_qty_items'			 => number_format(0, 2),
			'sum_recommended_sub_totals'		 => 0,
			'average_recommended_sub_totals'	 => number_format(0, 2),
			'sum_regular_sub_totals'			 => 0,
			'average_regular_sub_totals'		 => number_format(0, 2),
		);
	}

	function _populateSalesStatsByRangesFromCache($dt_ranges, $date_unit)
	{
		$stats = array();

		if ($date_unit === "day")
			$cache_stats = \Cache::get('sales.thirty_days_ago');
		else if ($date_unit === "week")
			$cache_stats = \Cache::get('sales.thirty_six_weeks_ago');
		else if ($date_unit === "month")
			$cache_stats = \Cache::get('sales.twelve_months_ago');
		else
			$cache_stats = array();

		foreach ($dt_ranges as $range)
		{
			$start	 = $range['start'];
			$end	 = $range['end'];
			$obj	 = ($cache_stats !== null) ? $this->_isAvailableInCache($cache_stats, $start->toDateString(), $end->toDateString()) : false;
			if (!$obj)
				array_push($stats, array('start' => $start, 'end' => $end, 'stat' => $this->_getSalesStats($range['start'], $range['end']), 'str_start' => $start->toDateString(), 'str_end' => $end->toDateString()));
			else
				array_push($stats, $obj);
		}

		if (count($stats) > 0)
		{
			if ($date_unit === "day")
				\Cache::add('sales.thirty_days_ago', $stats, 1440);
			else if ($date_unit === "week")
				\Cache::add('sales.thirty_six_weeks_ago', $stats, 1440);
			else if ($date_unit === "month")
				\Cache::add('sales.twelve_months_ago', $stats, 1440);
		}

		return $stats;
	}

	function _isAvailableInCache($cache, $str_start, $str_end)
	{
		foreach ($cache as $obj)
		{
			if ($obj['str_start'] === $str_start && $obj['str_end'] === $str_end)
				return $obj;
		}

		return false;
	}

	function _populatePageViewStatsByRangesFromCache($dt_ranges, $date_unit)
	{
		$stats = array();

		if ($date_unit === "day")
			$cache_stats = \Cache::get('pageviews.thirty_days_ago');
		else if ($date_unit === "week")
			$cache_stats = \Cache::get('pageviews.thirty_six_weeks_ago');
		else if ($date_unit === "month")
			$cache_stats = \Cache::get('pageviews.twelve_months_ago');
		else
			$cache_stats = array();

		foreach ($dt_ranges as $range)
		{
			$start	 = $range['start'];
			$end	 = $range['end'];

			$obj = ($cache_stats !== null) ? $this->_isAvailableInCache($cache_stats, $start->toDateString(), $end->toDateString()) : false;

			if (!$obj)
				array_push($stats, array('start' => $start, 'end' => $end, 'stat' => $this->_getPageViewStats($range['start'], $range['end']), 'str_start' => $start->toDateString(), 'str_end' => $end->toDateString()));
			else
				array_push($stats, $obj);
		}

		return $stats;
	}

}

/* End of file AjaxPanelController.php */
/* Location: ./application/controllers/AjaxPanelController.php */
