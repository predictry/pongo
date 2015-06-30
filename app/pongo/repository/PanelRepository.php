<?php

namespace App\Pongo\Repository;

use App\Models\Action,
    App\Models\ActionInstance,
    App\Models\ActionInstanceMeta,
    App\Models\FunelPreference,
    App\Models\FunelPreferenceMeta,
    App\Models\Site,
    App\Models\Widget,
    App\Models\WidgetInstance,
    Aws\DynamoDb\Model\Item,
    Cache,
    Carbon\Carbon,
    DB;

/**
 * Author       : Rifki Yandhi
 * Date Created : Sep 25, 2014 11:53:49 AM
 * File         : PanelRepository.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class PanelRepository
{

    public $active_site_id;
    public $buy_action_id = null;

    public function getSiteActions($site_id)
    {
        $action_ids = Site::find($site_id)->actions()->toArray();
        return $action_ids;
    }

    public function getActionStats($action_ids, $is_raw = true)
    {
        $data = [];
        if (!$is_raw) {
            return $this->getFormatedActionStatsInfo($data);
        }
    }

    public function getActionStatsInfo($site_id)
    {
        $action_ids = $this->getSiteActions($site_id);
    }

    public function getFormatedActionStatsInfo($data)
    {
        $output_today_default_data = [
            "graph_today_stats_data" => $data['stats']['data'],
            "js_graph_stats_data"    => json_encode($data['stats']['data']),
            "graph_y_keys"           => json_encode($data['stats']['y_keys'])
        ];

        return $output_today_default_data;
    }

    public function populateDateRangeActionStats($dt_start, $dt_end, $action_ids, $action_names, $index = false, $is_recommended = false, $all = true, $result_only = false)
    {
        if (!$result_only) {
            if (!$index)
                $graph_data   = array("date" => $dt_start->toDateString());
            else
                $graph_data[] = $dt_start->toDateString();
        }else {
            $graph_data = array();
        }

        $i = 0;

        foreach ($action_ids as $id) {
            if (!$all) {
                $total_recommended_action = Action::find($id)->action_instances_and_metas()
                        ->where("action_instance_metas.key", "rec")
                        ->where("action_instance_metas.value", "true")
                        ->whereBetween('created', [$dt_start, $dt_end])
                        ->count();

                if ($is_recommended)
                    $total_action = $total_recommended_action;
                else {
                    $total_action = (Action::find($id)->action_instances()
                                    ->whereBetween('created', [$dt_start, $dt_end])
                                    ->count()) - $total_recommended_action;
                }
            }
            else
                $total_action = Action::find($id)->action_instances()
                        ->whereBetween('created', [$dt_start, $dt_end])
                        ->count();

            if (!$result_only) {
                if (!$index)
                    $graph_data[$action_names[$i]] = $total_action;
                else
                    $graph_data[]                  = $total_action;
            }
            else
                $graph_data[$action_names[$i]] = $total_action;
            $i++;
        }

        return $graph_data;
    }

    public function setDefaultFunelPreferenceMetas($funel_preference_id, $non_default_action_ids)
    {
        FunelPreferenceMeta::where("funel_preference_id", $funel_preference_id)->delete();
        $i = 0;
        foreach ($non_default_action_ids as $action_id) {
            $funel_preference_meta                      = new FunelPreferenceMeta();
            $funel_preference_meta->action_id           = $action_id;
            $funel_preference_meta->funel_preference_id = $funel_preference_id;
            $funel_preference_meta->sort                = $i++;
            $funel_preference_meta->save();
        }
    }

    public function getOverviewSummary()
    {
        $today               = new Carbon("today"); //today begining
        $tomorrow            = new Carbon("tomorrow"); //tomorrow
        $end_of_today        = $tomorrow->subSeconds(1); //today ending
        //today actions
        $action_ids          = Action::where("site_id", $this->active_site_id)->get()->lists("id");
        $today_total_actions = ActionInstance::whereIn("action_id", $action_ids)->whereBetween('created', [$today, $end_of_today])->count();

        //today items
        $today_total_items = Item::where("site_id", $this->active_site_id)->whereBetween('created_at', [$today, $end_of_today])->count();

        //today buy
        $today_total_buy_action = 0;

        if (is_null($this->buy_action_id)) {
            $buy_action          = Action::where("site_id", $this->active_site_id)->where("name", "buy")->get()->first();
            $this->buy_action_id = ($buy_action) ? $buy_action->id : null;
        }

        if (!is_null($this->buy_action_id))
            $today_total_buy_action = ActionInstance::where("action_id", $buy_action->id)->whereBetween('created', [$today, $end_of_today])->count();

        //completion rate
        //check if he have default funnel preference
        $funel_default     = FunelPreference::where("site_id", $this->active_site_id)->where("is_default", true)->first();
        $funnel_stats_data = array();
        $rates             = array();

        if ($funel_default && strtolower($funel_default->name) !== "default") {
            $funnel_action_ids = FunelPreferenceMeta::where("funel_preference_id", $funel_default->id)->orderBy('sort', 'ASC')->get()->lists("action_id");
            foreach ($funnel_action_ids as $action_id) {
                $funnel_stats_data[] = Action::getNumberOfTotalActionsOverallByActionId($action_id);
            }

            for ($i = 0; $i < count($funnel_stats_data); $i++) {
                if (($i + 1) <= count($funnel_stats_data) - 1) {
                    $rates[] = ($funnel_stats_data[$i + 1] / $funnel_stats_data[$i]) * 100;
                }
            }
        }

        $overview_results = array(
            'today_total_actions'    => $today_total_actions,
            'today_total_items'      => $today_total_items,
            'today_total_buy_action' => $today_total_buy_action,
            'completion_rate'        => (count($rates) > 0) ? number_format(end($rates), 2) : 0,
            "funel_default_name"     => $funel_default->name
        );

        return $overview_results;
    }

    function getShowStats()
    {
        $today          = new Carbon("today"); //today begining
        $tomorrow       = new Carbon("tomorrow"); //tomorrow
        $end_of_today   = $tomorrow->subSeconds(1); //today ending
        $dt_start_range = $today->toDateTimeString();
        $dt_end_range   = $end_of_today->toDateTimeString();

        /*
         * TODAY OVERALL, REGULAR AND RECOMMENDED STATS OF VIEW AND COMPLETE_PURCHASE / BUY
         */
        $page_view_stats = $this->getPageViewStats();

        /*
         * ALRIGHT, IT SEEMS NOW IS POSSIBLE TO FETCH THE SALES AMOUNT! SINCE WE HAVE SOME SORT OF 'AWESOME' ACTION INSTANCE META KEY CALLED 'SUB_TOTAL' OF BUY ACTION
         * 
         * REMEMBER COMPLETE_PURCHASE EQUAL (=) or SAME (SAMA) TO BUY
         */
        $sales_stats = $this->getSalesStats();

        /*
         * FUNNEL (IS NOT REALLY SOMETHING THAT WE CAN RELY ON NOW. BETTER FIX THE FLOW BEFORE COME OUT WITH THE RESULTS)
         * "YOU KNOW NOTHING, JOHN SNOW."
         * 
         * KBYE
         */
        $funnel_actions           = Action::where("site_id", $this->active_site_id)->whereIn("name", array("view", "add_to_cart", "complete_purchase"))->get()->toArray(); //limit 4 (first 4 are default actions)
        $funnel_regular_stats     = $this->getActionStatsInfo($funnel_actions, $today, $end_of_today, false, false);
        $funnel_recommended_stats = $this->getActionStatsInfo($funnel_actions, $today, $end_of_today, true, false);
    }

    public function getSalesStats($dt_start = null, $dt_end = null)
    {
        if (!isset($dt_start) && !isset($dt_end)) {
            $dt_start = new Carbon("today"); //today begining
            $dt_end   = new Carbon("today"); //today ending
            $dt_end   = $dt_end->endOfDay();
        }

        if (is_null($this->buy_action_id)) {
            $complete_purchase_action = Action::where("name", "buy")->where("site_id", $this->active_site_id)->first();
            $this->buy_action_id      = is_object($complete_purchase_action) ? $complete_purchase_action->id : null;
        }

        if (!is_null($this->buy_action_id)) {
            $sales_stats['overall'] = Action::find($this->buy_action_id)
                            ->action_instances_and_metas()
                            ->where("action_instance_metas.key", "sub_total")
                            ->whereBetween('created', [$dt_start, $dt_end])
                            ->get(array("action_instances.id AS action_instance_id", "action_instance_metas.key", "action_instance_metas.value"))->sum('value');

            $action_instance_ids = Action::find($this->buy_action_id)
                            ->action_instances_and_metas()
                            ->where("action_instance_metas.key", "rec")
                            ->where("action_instance_metas.value", "true")
                            ->whereBetween('created', [$dt_start, $dt_end])
                            ->get(array("action_instances.id AS action_instance_id", "action_instance_metas.key", "action_instance_metas.value"))->lists("action_instance_id");

            if (count($action_instance_ids) > 0) {
                $sales_stats['recommended'] = ActionInstanceMeta::whereIn("action_instance_id", $action_instance_ids)
                        ->where("action_instance_metas.key", "sub_total")
                        ->get()
                        ->sum('value');
            }
            else
                $sales_stats['recommended'] = 0;
        }
        else {
            $sales_stats['overall']     = $sales_stats['regular']     = $sales_stats['recommended'] = 0;
        }

        $sales_stats['regular'] = $sales_stats['overall'] - $sales_stats['recommended'];

        return $sales_stats;
    }

    public function getPageViewStats($dt_start = null, $dt_end = null)
    {
        if (!isset($dt_start) && !isset($dt_end)) {
            $dt_start = new Carbon("today"); //today begining
            $dt_end   = new Carbon("today"); //today ending
            $dt_end   = $dt_end->endOfDay();
        }

        $view_action = Action::where("name", "view")->where("site_id", $this->active_site_id)->get()->first();
        $temp        = null;

        if (is_object($view_action)) {
            /*
             * TODAY OVERALL, REGULAR AND RECOMMENDED STATS OF VIEW AND COMPLETE_PURCHASE / BUY
             */
            $temp                           = $this->populateDateRangeActionStats($dt_start, $dt_end, array($view_action->id), array($view_action->name), false, false, true, true);
            $page_view_stats['overall']     = (isset($temp) && is_array($temp)) ? current($temp) : FALSE;
            $temp                           = $this->populateDateRangeActionStats($dt_start, $dt_end, array($view_action->id), array($view_action->name), false, true, false, true);
            $page_view_stats['recommended'] = (isset($temp) && is_array($temp)) ? current($temp) : FALSE;
            $temp                           = $this->populateDateRangeActionStats($dt_start, $dt_end, array($view_action->id), array($view_action->name), false, false, false, true);
            $page_view_stats['regular']     = (isset($temp) && is_array($temp)) ? current($temp) : FALSE;
        }
        else
            $page_view_stats['overall']     = $page_view_stats['recommended'] = $page_view_stats['regular']     = 0;

        return $page_view_stats;
    }

    public function getDateRanges($dt_start, $dt_end, &$date_unit = "day")
    {
        $dt_ranges = array();
        $n_diff    = $dt_start->diffInDays($dt_end);

        if ($date_unit === "day" && $n_diff <= 31) {
            for ($i = 0; $i <= floor($n_diff); $i++) {
                $dt_start_temp = new Carbon($dt_start->toDateString());
                $dt_end_temp   = new Carbon($dt_start->toDateString());
                array_push($dt_ranges, array("start" => $dt_start_temp->startOfDay(), "end" => $dt_end_temp->endOfDay()));
                $dt_start->addDay();
            }
        }
        else if ($date_unit === "week" && $n_diff <= 120) {
            do {
                $dt_start_temp = new Carbon($dt_start->toDateString());
                $dt_end_temp   = new Carbon($dt_start->toDateString());
                $dt_end_temp->endOfWeek();

                array_push($dt_ranges, array("start" => $dt_start_temp->startOfDay(), "end" => $dt_end_temp->endOfDay()));
                $dt_start = new Carbon($dt_end_temp);
                $dt_start->addDay();
            } while ($dt_end_temp->diffInDays($dt_end) > 7);
        }
        else if (($date_unit === "month" && $n_diff > 31) || $n_diff > 120) {
            $date_unit = "month";

            $n_month_diff = $dt_start->diffInMonths($dt_end);
            for ($i = 1; $i <= $n_month_diff; $i++) {
                $dt_start_temp = new Carbon($dt_start->toDateString());
                $dt_end_temp   = new Carbon($dt_start->toDateString());
                $dt_end_temp->addMonth();

                array_push($dt_ranges, array("start" => $dt_start_temp->startOfDay(), "end" => $dt_end_temp->endOfDay()));
                $dt_start = new Carbon($dt_end_temp);
                $dt_start->addDay();
            }
        }
        if (count($dt_ranges) > 0) {
            $last_dt_range                = end($dt_ranges);
            $n_diff_start_with_end_ranges = $last_dt_range['end']->diffInDays($dt_end);

            if ($n_diff_start_with_end_ranges > 0) {
                $dt_start_temp = new Carbon($last_dt_range['end']->toDateString());
                $dt_start_temp->addDay();

                $dt_end_temp = new Carbon($last_dt_range['end']->toDateString());
                $dt_end_temp->addDays($n_diff_start_with_end_ranges);

                array_push($dt_ranges, array("start" => $dt_start_temp->startOfDay(), "end" => $dt_end_temp->endOfDay()));
            }
        }


        return $dt_ranges;
    }

    public function populateSalesStatsByRanges($dt_ranges)
    {
        $stats = array();

        foreach ($dt_ranges as $range) {
            $start = $range['start'];
            $end   = $range['end'];
            array_push($stats, array('start' => $start, 'end' => $end, 'stat' => $this->getSalesStats($range['start'], $range['end']), 'str_start' => $start->toDateString(), 'str_end' => $end->toDateString()));
        }

        return $stats;
    }

    public function populateSalesStatsByRangesFromCache($dt_ranges, $date_unit)
    {
        $stats = array();

        if ($date_unit === "day")
            $cache_stats = Cache::get("sales_{$this->active_site_id}.thirty_days_ago");
        else if ($date_unit === "week")
            $cache_stats = Cache::get("sales_{$this->active_site_id}.thirty_six_weeks_ago");
        else if ($date_unit === "month")
            $cache_stats = Cache::get("sales_{$this->active_site_id}.twelve_months_ago");
        else
            $cache_stats = array();

        foreach ($dt_ranges as $range) {
            $start = $range['start'];
            $end   = $range['end'];
            $obj   = ($cache_stats !== null) ? $this->isAvailableInCache($cache_stats, $start->toDateString(), $end->toDateString()) : false;
            if (!$obj)
                array_push($stats, array('start' => $start, 'end' => $end, 'stat' => $this->getSalesStats($range['start'], $range['end']), 'str_start' => $start->toDateString(), 'str_end' => $end->toDateString()));
            else
                array_push($stats, $obj);
        }

        if (count($stats) > 0) {
            if ($date_unit === "day")
                Cache::add("sales_{$this->active_site_id}.thirty_days_ago", $stats, 1440);
            else if ($date_unit === "week")
                Cache::add("sales_{$this->active_site_id}.thirty_six_weeks_ago", $stats, 1440);
            else if ($date_unit === "month")
                Cache::add("sales_{$this->active_site_id}.twelve_months_ago", $stats, 1440);
        }

        return $stats;
    }

    public function isAvailableInCache($cache, $str_start, $str_end)
    {
        foreach ($cache as $obj) {
            if ($obj['str_start'] === $str_start && $obj['str_end'] === $str_end)
                return $obj;
        }

        return false;
    }

    public function populatePageViewStatsByRanges($dt_ranges)
    {
        $stats = array();
        foreach ($dt_ranges as $range) {
            $start = $range['start'];
            $end   = $range['end'];
            array_push($stats, array('start' => $start, 'end' => $end, 'stat' => $this->getPageViewStats($range['start'], $range['end']), 'str_start' => $start->toDateString(), 'str_end' => $end->toDateString()));
        }

        return $stats;
    }

    public function populatePageViewStatsByRangesFromCache($dt_ranges, $date_unit)
    {
        $stats = array();

        if ($date_unit === "day")
            $cache_stats = Cache::get("pageviews__{$this->active_site_id}.thirty_days_ago");
        else if ($date_unit === "week")
            $cache_stats = Cache::get("pageviews_{$this->active_site_id}.thirty_six_weeks_ago");
        else if ($date_unit === "month")
            $cache_stats = Cache::get("pageviews_{$this->active_site_id}.twelve_months_ago");
        else
            $cache_stats = array();

        foreach ($dt_ranges as $range) {
            $start = $range['start'];
            $end   = $range['end'];

            $obj = ($cache_stats !== null) ? $this->isAvailableInCache($cache_stats, $start->toDateString(), $end->toDateString()) : false;

            if (!$obj)
                array_push($stats, array('start' => $start, 'end' => $end, 'stat' => $this->getPageViewStats($range['start'], $range['end']), 'str_start' => $start->toDateString(), 'str_end' => $end->toDateString()));
            else
                array_push($stats, $obj);
        }


        if (count($stats) > 0) {
            if ($date_unit === "day")
                Cache::add("pageviews_{$this->active_site_id}.thirty_days_ago", $stats, 1440);
            else if ($date_unit === "week")
                Cache::add("pageviews_{$this->active_site_id}.thirty_six_weeks_ago", $stats, 1440);
            else if ($date_unit === "month")
                Cache::add("pageviews_{$this->active_site_id}.twelve_months_ago", $stats, 1440);
        }

        return $stats;
    }

    public function getCartSummaryDetails($dt_start = null, $dt_end = null)
    {
        if (!isset($dt_start) && !isset($dt_end)) {
            $dt_start = new Carbon("today"); //today begining
            $dt_end   = new Carbon("today"); //today ending
            $dt_end   = $dt_end->endOfDay();
        }

        if (is_null($this->buy_action_id)) {
            $complete_purchase_action = Action::where("name", "buy")->where("site_id", $this->active_site_id)->first();
            $this->buy_action_id      = is_object($complete_purchase_action) ? $complete_purchase_action->id : null;
        }

        if (!is_null($this->buy_action_id)) {
            $action_instance_ids = Action::find($this->buy_action_id)
                            ->action_instances()
                            ->whereBetween('created', [$dt_start, $dt_end])
                            ->get(array("action_instances.id AS action_instance_id"))->lists("action_instance_id");

            if (count($action_instance_ids) > 0) {

                $cart_ids = array_unique(ActionInstanceMeta::whereIn("action_instance_id", $action_instance_ids)
                                ->where("action_instance_metas.key", "cart_id")
                                ->get(array("action_instance_metas.value AS value"))->lists("value"));

                $recommended_action_instance_metas = Action::find($this->buy_action_id)
                                ->action_instances_and_metas()
                                ->where("action_instance_metas.key", "rec")
                                ->where("action_instance_metas.value", "true")
                                ->whereBetween('created', [$dt_start, $dt_end])
                                ->get(array("action_instances.id AS action_instance_id", "action_instance_metas.key", "action_instance_metas.value"))->lists("action_instance_id");


                $regular_action_instance_metas = array_diff($action_instance_ids, $recommended_action_instance_metas);

                // Recommendation
                if (count($recommended_action_instance_metas) > 0) {
                    $sum_recommended_qty = ActionInstanceMeta::whereIn("action_instance_id", $recommended_action_instance_metas)
                            ->where("action_instance_metas.key", "qty")
                            ->get()
                            ->sum('value');

                    $sum_recommended_sub_totals = ActionInstanceMeta::whereIn("action_instance_id", $recommended_action_instance_metas)
                            ->where("action_instance_metas.key", "sub_total")
                            ->get()
                            ->sum('value');
                }
                else {
                    $sum_recommended_qty        = $sum_recommended_sub_totals = 0;
                }

                // Regular
                if (count($regular_action_instance_metas) > 0) {
                    $sum_regular_qty = ActionInstanceMeta::whereIn("action_instance_id", $regular_action_instance_metas)
                            ->where("action_instance_metas.key", "qty")
                            ->get()
                            ->sum('value');

                    $sum_regular_sub_totals = ActionInstanceMeta::whereIn("action_instance_id", $regular_action_instance_metas)
                            ->where("action_instance_metas.key", "sub_total")
                            ->get()
                            ->sum('value');
                }
                else {
                    $sum_regular_qty        = $sum_regular_sub_totals = 0;
                }

                if (count($cart_ids) > 0) {
                    $average_recommended_qty_items = ($sum_recommended_qty) / count($cart_ids);
//				$average_recommended_qty_items	 = (($sum_recommended_qty) / ($sum_recommended_qty + $sum_recommended_qty)) * 100;
                    $average_regular_qty_items     = ($sum_regular_qty) / count($cart_ids);

                    $average_recommended_sub_totals = ($sum_recommended_sub_totals) / count($cart_ids);
//				$average_recommended_sub_totals	 = (($sum_recommended_sub_totals) / ($sum_recommended_sub_totals + $sum_regular_sub_totals)) * 100;
                    $average_regular_sub_totals     = ($sum_regular_sub_totals) / count($cart_ids);

                    return array(
                        'total_carts'                               => count($cart_ids),
                        'total_combination_of_qty'                  => $sum_recommended_qty + $sum_recommended_qty,
                        'total_combination_of_sub_totals'           => $sum_recommended_sub_totals + $sum_regular_sub_totals,
                        'sum_recommended_qty'                       => $sum_recommended_qty,
                        'average_recommended_qty_items'             => number_format($average_recommended_qty_items, 2),
                        'average_percentage_recommended_qty_items'  => number_format(($sum_recommended_qty + $sum_recommended_qty) > 0 ? $sum_recommended_qty / ($sum_recommended_qty + $sum_recommended_qty) * 100 : 0, 2),
                        'sum_regular_qty'                           => $sum_regular_qty,
                        'average_regular_qty_items'                 => number_format($average_regular_qty_items, 2),
                        'sum_recommended_sub_totals'                => $sum_recommended_sub_totals,
                        'average_recommended_sub_totals'            => number_format($average_recommended_sub_totals, 2),
                        'average_percentage_recommended_sub_totals' => number_format(($sum_recommended_sub_totals + $sum_regular_sub_totals) > 0 ? $sum_recommended_sub_totals / ($sum_recommended_sub_totals + $sum_regular_sub_totals) * 100 : 0, 2),
                        'sum_regular_sub_totals'                    => $sum_regular_sub_totals,
                        'average_regular_sub_totals'                => number_format($average_regular_sub_totals, 2),
                    );
                }
            }
        }
        return array(
            'total_carts'                               => 0,
            'total_combination_of_qty'                  => 0,
            'total_combination_of_sub_totals'           => 0,
            'sum_recommended_qty'                       => 0,
            'average_recommended_qty_items'             => number_format(0, 2),
            'average_percentage_recommended_qty_items'  => number_format(0, 2),
            'sum_regular_qty'                           => 0,
            'average_regular_qty_items'                 => number_format(0, 2),
            'sum_recommended_sub_totals'                => 0,
            'average_recommended_sub_totals'            => number_format(0, 2),
            'average_percentage_recommended_sub_totals' => number_format(0, 2),
            'sum_regular_sub_totals'                    => 0,
            'average_regular_sub_totals'                => number_format(0, 2),
        );
    }

    public function getMostGenerateRecommendedItems()
    {
        $widget_ids = Widget::where("site_id", $this->active_site_id)->get()->lists('id');
        $items      = array();

        if (count($widget_ids) > 0) {
            $item_ids = array();
            foreach ($widget_ids as $widget_id) {
                $item_ids = Widget::find($widget_id)->widget_instances_and_items()
                        ->groupBy('widget_instance_items.item_id')
                        ->groupBy('widget_instances.widget_id')
                        ->having(DB::raw('COUNT(*)'), '>', 1)
                        ->orderBy('total', 'DESC')
                        ->limit(10)
                        ->get(array('widget_instance_items.item_id', DB::raw('COUNT(*) AS total')))
                        ->lists("item_id");
            }


            foreach ($item_ids as $id) {
                array_push($items, Item::find($id)->item_metas()->get()->toArray());
            }
        }
        return $items;
    }

    public function getMostRecommendedItems($type = "view", $dt_start = null, $dt_end = null)
    {
        if (!isset($dt_start) && !isset($dt_end)) {
            $dt_start = new Carbon("today"); //today begining
            $dt_end   = new Carbon("today"); //today ending
            $dt_end   = $dt_end->endOfDay();
        }

        $action_name = ($type === "sales") ? "buy" : "view";
        $action      = Action::where("name", $action_name)->where("site_id", $this->active_site_id)->get()->first();
        $item_ids    = array();

        if (is_object($action)) {
            $item_ids = Action::find($action->id)
                    ->action_instances_and_metas()
                    ->where("action_instance_metas.key", "rec")
                    ->where("action_instance_metas.value", "true")
                    ->whereBetween('created', [$dt_start, $dt_end])
                    ->groupBy('action_instances.item_id')
                    ->groupBy('action_instances.action_id')
                    ->having(DB::raw('COUNT(*)'), '>', 1)
                    ->orderBy('total', 'DESC')
                    ->limit(10)
                    ->get(array("action_instances.item_id", DB::raw('COUNT(*) AS total')))
                    ->lists("item_id");
        }

        $items = array();

        foreach ($item_ids as $id) {
            $item = Item::find($id);
            if ($item) {
                $item_metas = $item->item_metas()->where("item_metas.key", "img_url")
                        ->where("item_metas.value", "!=", "")
                        ->limit(1)
                        ->get(array('item_metas.key', 'item_metas.value'))
                        ->toArray();
                if (!count($item_metas) > 0)
                    $item_metas = null;

                array_push($items, array('item' => $item->toArray(), 'item_metas' => ($item_metas !== null) ? $item_metas[0] : null));
            }
        }
        return $items;
    }

    public function getCTRData($dt_start = null, $dt_end = null)
    {
        $page_view_stats = $this->getPageViewStats($dt_start, $dt_end);
        $widget_ids      = Widget::where("site_id", $this->active_site_id)->get()->lists("id");
        if (count($widget_ids) > 0) {
            $n_widget_instances = WidgetInstance::whereIn("widget_id", $widget_ids)
                    ->whereBetween("created_at", [$dt_start, $dt_end])
                    ->count();

            $total_regular_pageviews        = $page_view_stats['regular'];
            $total_recommended_pageviews    = $page_view_stats['recommended'];
            $total_recommendation_generated = $n_widget_instances * 24; //dummy kali 20

            $result = array(
                'np'         => $total_regular_pageviews,
                'nr'         => $total_recommended_pageviews,
                'ngr'        => $total_recommendation_generated,
//			'ngr'		 => 10152, //dummy
                'ctr'        => ($total_recommendation_generated > 0) ? ($total_recommended_pageviews / $total_recommendation_generated) * 100 : 0,
//			'ctr'		 => ($total_recommendation_generated > 0) ? ($total_recommended_pageviews / 10152) * 100 : 0, //dummy
                'impression' => $total_recommendation_generated
//			'impression' => 10152 //dummy
            );

            $result['ngr'] = ($result['ngr'] > 0) ? $result['ngr'] : 1;
        }
        else {
            $result = array(
                'np'         => 0,
                'nr'         => 0,
                'ngr'        => 0,
                'ctr'        => 0,
                'impression' => 0
            );
        }

        return $result;
    }

    public function getTotalUniqueVisitorBySession($dt_start = null, $dt_end = null)
    {
        $n_session = 0;

        if (!is_null($dt_start) && !is_null($dt_end)) {
            $n_session = \App\Models\Session::where("site_id", $this->active_site_id)->whereBetween('created_at', [$dt_start, $dt_end])->count();
        }
        else
            $n_session = \App\Models\Session::where("site_id", $this->active_site_id)->count();

        return $n_session;
    }

    public function getTotalBuyAction($dt_start = null, $dt_end = null)
    {
        if (is_null($this->buy_action_id)) {
            $complete_purchase_action = Action::where("name", "buy")->where("site_id", $this->active_site_id)->first();
            $this->buy_action_id      = is_object($complete_purchase_action) ? $complete_purchase_action->id : null;
        }

        $n_item_purchased = !is_null($this->buy_action_id) ? ActionInstance::where("action_id", $this->buy_action_id)->whereBetween('created', [$dt_start, $dt_end])->count() : 0;

        $results = !is_null($this->buy_action_id) ? DB::select('SELECT COUNT(DISTINCT tbl.action_instance_id) AS total FROM ('
                        . 'SELECT * FROM "action_instance_metas" '
                        . 'INNER JOIN "action_instances" on "action_instances"."id" = "action_instance_metas"."action_instance_id"'
                        . 'WHERE "action_instances"."action_id" = ? '
                        . 'AND "action_instance_metas"."key" = ? '
                        . 'AND "created" between ? AND ? '
                        . ') as tbl LIMIT 1', [$this->buy_action_id, "rec", $dt_start, $dt_end]) : null;

        $n_item_recommend_purchased = !is_null($results) && is_object(current($results)) ? current($results)->total : 0;

        return [
            'overall'     => $n_item_purchased,
            'regular'     => $n_item_purchased - $n_item_recommend_purchased,
            'recommended' => $n_item_recommend_purchased
        ];
    }

    public function getTotalOrders($dt_start = null, $dt_end = null)
    {

        if (is_null($this->buy_action_id)) {
            $complete_purchase_action = Action::where("name", "buy")->where("site_id", $this->active_site_id)->first();
            $this->buy_action_id      = is_object($complete_purchase_action) ? $complete_purchase_action->id : null;
        }

        $results = !is_null($this->buy_action_id) ? DB::select('SELECT COUNT(DISTINCT tbl.value) AS total FROM ('
                        . 'SELECT * FROM "action_instance_metas" '
                        . 'INNER JOIN "action_instances" on "action_instances"."id" = "action_instance_metas"."action_instance_id"'
                        . 'WHERE "action_instances"."action_id" = ? '
                        . 'AND "action_instance_metas"."key" = ? '
                        . 'AND "created" between ? AND ? '
                        . ') as tbl LIMIT 1', [$this->buy_action_id, "cart_id", $dt_start, $dt_end]) : null;

        $n_orders = !is_null($results) && is_object(current($results)) ? current($results)->total : 0;

        return $n_orders;
    }
    
    
    /*
     * Panel Overviews
     */
    

}

/* End of file PanelRepository.php */