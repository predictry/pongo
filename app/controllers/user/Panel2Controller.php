<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Jan 5, 2015 12:15:12 PM
 * File         : app/controllers/Panel2Controller.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Controllers\User;

use App\Controllers\BaseController,
    App\Models\Action,
    App\Models\ActionInstance,
    App\Pongo\Libraries\Helper,
    App\Pongo\Repository\PanelRepository,
    Cache,
    URL,
    View;

class Panel2Controller extends BaseController
{

    protected $panel_repository = null;

    function __construct(PanelRepository $repository)
    {
        parent::__construct();

        $this->panel_repository                 = $repository;
        $this->panel_repository->active_site_id = $this->active_site_id;

        $custom_script = "var site_url = '" . URL::to('/') . "';";
        View::share(array("ca" => get_class(), "custom_script" => $custom_script));
    }

    public function index($dt_range_group = "today", $dt_start = null, $dt_end = null)
    {
        $ranges   = Helper::getSelectedFilterDateRange($dt_range_group, $dt_start, $dt_end);
        $dt_start = $ranges['dt_start'];
        $dt_end   = $ranges['dt_end'];

        //@TODO GET Numbers below
        //1.a Total Pageviews (Regular)
        //1.b Total Pageviews (Recommendation)
        /*
         * Scenario
         * 1. Get pageviews
         */
        $cache_pageviews_stat = Cache::get("pageviews_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}");
        if (is_null($cache_pageviews_stat)) {
            $pageviews_stat = $this->panel_repository->getPageViewStats($dt_start, $dt_end);
            Cache::add("pageviews_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}", $pageviews_stat, 14400);
        }
        else
            $pageviews_stat = $cache_pageviews_stat;

        //2 Unique Visitors (By Session)
        //Optional (By IP)
        /*
         * Scenario by Session
         * 1. Get count(id) from sessions where site_id = {current_site_id} distinct(session)
         */
        $cache_n_session = Cache::get("n_session_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}");

        if (is_null($cache_n_session)) {
            $n_session = $this->panel_repository->getTotalUniqueVisitorBySession($dt_start, $dt_end);
            Cache::add("n_session_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}", $n_session, 14400);
        }
        else
            $n_session = $cache_n_session;

        //3 Conversion
        // - Sales
        /*
         * Scenario
         * 1. Get all id" of "buy" action from action_instances (Certain time period)
         * 2.Get total unique visitors (by session) (Certain time period)
         * 3. Sum "sub_total" from action_instance_metas FK with id.action_instances
         * 
         * Formula:
         * ( Count(1) / (2) ) * 100 = N <-- Conversion rate 
         * 
         */

        $cache_n_item_purchased = Cache::get("cache_n_item_purchased_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}");
        if (is_null($cache_n_item_purchased)) {
            $n_item_purchased = $this->panel_repository->getTotalBuyAction($dt_start, $dt_end);
            Cache::add("cache_n_item_purchased_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}", $n_item_purchased, 14400);
        }
        else
            $n_item_purchased = $cache_n_item_purchased;

        $cache_sales_stat = Cache::get("cache_sales_stat_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}");
        if (is_null($cache_sales_stat)) {
            $sales_stat = $this->panel_repository->getSalesStats($dt_start, $dt_end);
            Cache::add("cache_sales_stat_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}", $sales_stat, 14400);
        }
        else
            $sales_stat = $cache_sales_stat;

        $cache_n_orders = Cache::get("cache_n_orders_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}");
        if (is_null($cache_n_orders)) {
            $n_orders = $this->panel_repository->getTotalOrders($dt_start, $dt_end);
            Cache::add("cache_n_orders_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}", $n_orders, 14400);
        }
        else
            $n_orders = $cache_n_orders;

        // 
        // - Basket Size
//        $queries = DB::getQueryLog();
//        echo '<pre>';
//        print_r($pageviews_stat);
//        echo "<br/>----<br/>";
//        print_r($n_session);
//        echo "<br/>----<br/>";
//        print_r($n_item_purchased);
//        echo "<br/>----<br/>";
//        print_r($sales_stat);
//        echo "<br/>----<br/>";
//        print_r($n_orders);
//        echo "<br/>----<br/>";
//        print_r($queries);
//        echo "<br/>----<br/>";
//        echo '</pre>';
//        die;

        $buy_action  = Action::where("site_id", $this->active_site_id)->where("name", "buy")->get()->first(); //buy action
        $view_action = Action::where("site_id", $this->active_site_id)->where("name", "view")->get()->first(); //view action

        if ($buy_action)
            $output_top_items['top_purchased_items'] = ActionInstance::getMostItems($buy_action->id);

        if ($view_action)
            $output_top_items['top_viewed_items'] = ActionInstance::getMostItems($view_action->id);



        $output = [
            'overviews'           => [
                'total_pageviews'      => number_format($pageviews_stat['regular']),
                'total_uvs'            => number_format($n_session),
                'total_sales_amount'   => number_format($sales_stat['regular']),
                'total_item_purchased' => number_format($n_item_purchased),
                'total_orders'         => number_format($n_orders),
                //thousands
//                'total_uvs'            => \App\Pongo\Libraries\Helper::thousandsCurrencyFormat($n_session),
//                'total_pageviews'      => \App\Pongo\Libraries\Helper::thousandsCurrencyFormat($pageviews_stat['regular']),
//                'total_sales_amount'   => \App\Pongo\Libraries\Helper::thousandsCurrencyFormat($sales_stat['regular']),
//                'total_item_purchased' => \App\Pongo\Libraries\Helper::thousandsCurrencyFormat($n_item_purchased),
//                'total_orders'         => \App\Pongo\Libraries\Helper::thousandsCurrencyFormat($n_orders),
                'total_avg_sales'      => 0,
                'conversion_rate'      => Helper::calcConversionRate($n_orders, $pageviews_stat['regular'])
            ],
            'dt_range'            => [
                'start' => $dt_start->format("F d, Y"),
                'end'   => $dt_end->format("F d, Y")
            ],
            'dt_start'            => $dt_start,
            'dt_end'              => $dt_end,
            'top_purchased_items' => is_object($buy_action) ? ActionInstance::getMostItems($buy_action->id, 10) : [],
            'top_viewed_items'    => is_object($view_action) ? ActionInstance::getMostItems($view_action->id, 10) : [],
            'pageTitle'           => "Dashboard"
        ];
        return \View::make(getenv('FRONTEND_SKINS') . $this->theme . '.panels.dashboard', $output);
//        return \View::make('frontend.panels.dashboard3', $output);
    }

}

/* End of file Panel2Controller.php */
/* Location: ./application/controllers/Panel2Controller.php */
