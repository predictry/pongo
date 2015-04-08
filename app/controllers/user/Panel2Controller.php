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
    Guzzle\Service\Client,
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
        $cache_summary_sales = null; // Cache::get("cache_summary_sales_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}");
        if (is_null($cache_summary_sales)) {
            $summary_sales = $this->panel_repository->getSalesStats($dt_start, $dt_end);
            Cache::add("cache_summary_sales_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}", $summary_sales, 14400);
        }
        else
            $summary_sales = $cache_summary_sales;

        //4 Orders
        /*
         * 
         */
        $cache_n_orders = null; // Cache::get("cache_n_orders_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}");
        if (is_null($cache_n_orders)) {
            $n_orders = $this->panel_repository->getTotalOrders($dt_start, $dt_end);
            Cache::add("cache_n_orders_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}", $n_orders, 14400);
        }
        else
            $n_orders = $cache_n_orders;


        $cache_summary_item_purchased = null; //Cache::get("cache_summary_item_purchased _{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}");
        if (is_null($cache_summary_item_purchased)) {
            $summary_item_purchased = $this->panel_repository->getTotalBuyAction($dt_start, $dt_end);
            Cache::add("cache_summary_item_purchased _{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}", $summary_item_purchased, 14400);
        }
        else
            $summary_item_purchased = $cache_summary_item_purchased;

        //5 Cart summary (qty and sales)
        /*
         * 
         */
//        $cache_summary_details = Cache::get("cache_summary_details_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}");
//        if (is_null($cache_summary_details)) {
//            $cart_summary_details = $this->panel_repository->getCartSummaryDetails($dt_start, $dt_end);
//            Cache::add("cache_n_item_purchased_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}", $summary_item_purchased, 14400);
//        }
//        else
//            $cart_summary_details = $cache_summary_details;
//            
//        $queries = \DB::getQueryLog();
//        echo '<pre>';
//        print_r($pageviews_stat);
//        echo "<br/>----<br/>";
//        print_r($n_session);
//        echo "<br/>----<br/>";
//        print_r($summary_item_purchased);
//        echo "<br/>----<br/>";
//        print_r($summary_sales);
//        echo "<br/>----<br/>";
//        print_r($n_orders);
//        echo "<br/>----<br/>";
//        print_r($queries);
//        echo "<br/>----<br/>";
//        echo '</pre>';
//        die;

        $buy_action  = Action::where("site_id", $this->active_site_id)->where("name", "buy")->first(); //buy action
        $view_action = Action::where("site_id", $this->active_site_id)->where("name", "view")->first(); //view action

        if ($buy_action)
            $output_top_items['top_purchased_items'] = ActionInstance::getMostItems($buy_action->id);

        if ($view_action)
            $output_top_items['top_viewed_items'] = ActionInstance::getMostItems($view_action->id);

        $output = [
            'overviews'           => [
                'total_pageviews'      => number_format($pageviews_stat['regular']),
                'total_uvs'            => number_format($n_session),
                'total_sales_amount'   => number_format($summary_sales['overall']),
                'total_item_purchased' => number_format($summary_item_purchased['regular']),
                'total_orders'         => number_format($n_orders),
                'total_item_per_cart'  => number_format(($n_orders) > 0 ? ($summary_item_purchased['regular'] / $n_orders) : 0, 2),
                'total_sales_per_cart' => number_format(($n_orders) > 0 ? ($summary_sales['regular'] / $n_orders) : 0),
                //thousands
//                'total_uvs'            => \App\Pongo\Libraries\Helper::thousandsCurrencyFormat($n_session),
//                'total_pageviews'      => \App\Pongo\Libraries\Helper::thousandsCurrencyFormat($pageviews_stat['regular']),
//                'total_sales_amount'   => \App\Pongo\Libraries\Helper::thousandsCurrencyFormat($summary_sales['overall']),
//                'total_item_purchased' => \App\Pongo\Libraries\Helper::thousandsCurrencyFormat($summary_item_purchased['regular']),
//                'total_orders'         => \App\Pongo\Libraries\Helper::thousandsCurrencyFormat($n_orders),
//                'total_item_per_cart'  => \App\Pongo\Libraries\Helper::thousandsCurrencyFormat(($n_orders) > 0 ? ($summary_item_purchased['regular'] / $n_orders) : 0),
//                'total_sales_per_cart' => \App\Pongo\Libraries\Helper::thousandsCurrencyFormat(($n_orders) > 0 ? ($summary_sales['regular'] / $n_orders) : 0),
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

    public function ajaxIndex($dt_range_group = "today", $dt_start = null, $dt_end = null)
    {
        $client = new Client('http://localhost/predictry-analytics/public/api/v1/tenants/' . \Session::get("active_site_name") . '/');

        $ranges   = Helper::getSelectedFilterDateRange($dt_range_group, $dt_start, $dt_end);
        $dt_start = $ranges['dt_start'];
        $dt_end   = $ranges['dt_end'];

        /*
         * Scenario
         * 1. Get pageviews
         */
        $cache_pageviews_stat = null; // Cache::pull("pageviews_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}");

        if (is_null($cache_pageviews_stat)) {
            $response     = $client->get("stats-summary/pageviews/{$dt_start}/{$dt_end}")->send();
            $arr_response = $response->json();

            $pageviews_regular_sum = (isset($arr_response['error']) && $arr_response['error']) ? "TEST" : array_get($arr_response, 'data.pageviews');

            $pageviews_stat = [
                'overall'     => $pageviews_regular_sum,
                'recommended' => 0,
                'regular'     => $pageviews_regular_sum
            ];

            Cache::add("pageviews_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}", $pageviews_stat, 14400);
        }
        else {
            $pageviews_stat = $cache_pageviews_stat;
        }

        $cache_summary_item_purchased = null; // Cache::pull("cache_summary_item_purchased _{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}");
        if (is_null($cache_summary_item_purchased)) {
            $response                   = $client->get("stats-summary/item-purchased/{$dt_start}/{$dt_end}")->send();
            $arr_response               = $response->json();
            $item_purchased_regular_sum = (isset($arr_response['error']) && $arr_response['error']) ? 0 : array_get($arr_response, 'data.sum');

            $summary_item_purchased = [
                'overall'     => $item_purchased_regular_sum,
                'recommended' => 0,
                'regular'     => $item_purchased_regular_sum
            ];
            Cache::add("cache_summary_item_purchased _{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}", $summary_item_purchased, 14400);
        }
        else
            $summary_item_purchased = $cache_summary_item_purchased;

        //4 Orders
        /*
         * 
         */
        $cache_n_orders = null; // Cache::pull("cache_n_orders_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}");
        if (is_null($cache_n_orders)) {
            $response     = $client->get("stats-summary/orders/{$dt_start}/{$dt_end}")->send();
            $arr_response = $response->json();
            $n_orders     = (isset($arr_response['error']) && $arr_response['error']) ? 0 : array_get($arr_response, 'data.count');
            Cache::add("cache_n_orders_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}", $n_orders, 14400);
        }
        else
            $n_orders = $cache_n_orders;

        $cache_summary_sales = null; // Cache::get("cache_summary_sales_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}");
        if (is_null($cache_summary_sales)) {
            $response          = $client->get("stats-summary/sales-amount/{$dt_start}/{$dt_end}")->send();
            $arr_response      = $response->json();
            $sales_regular_sum = (isset($arr_response['error']) && $arr_response['error']) ? 0 : array_get($arr_response, 'data.sum');
            $summary_sales     = [
                'overall'     => $sales_regular_sum,
                'recommended' => 0,
                'regular'     => $sales_regular_sum
            ];

            Cache::add("cache_summary_sales_{$this->active_site_id}_{$dt_start->toDateString()}_{$dt_end->toDateString()}", $summary_sales, 14400);
        }
        else
            $summary_sales = $cache_summary_sales;

        $output_top_items['top_purchased_items'] = [];
        $output_top_items['top_viewed_items']    = [];

        $output = [
            'overviews'           => [
                'total_pageviews'      => number_format($pageviews_stat['regular']),
//                'total_uvs'            => number_format($n_session),
                'total_uvs'            => number_format(1000),
                'total_sales_amount'   => number_format($summary_sales['overall']),
                'total_item_purchased' => number_format($summary_item_purchased['regular']),
                'total_orders'         => number_format($n_orders),
                'total_item_per_cart'  => number_format(($n_orders) > 0 ? ($summary_item_purchased['regular'] / $n_orders) : 0, 2),
                'total_sales_per_cart' => number_format(($n_orders) > 0 ? ($summary_sales['regular'] / $n_orders) : 0),
                //thousands
//                'total_uvs'            => \App\Pongo\Libraries\Helper::thousandsCurrencyFormat($n_session),
//                'total_pageviews'      => \App\Pongo\Libraries\Helper::thousandsCurrencyFormat($pageviews_stat['regular']),
//                'total_sales_amount'   => \App\Pongo\Libraries\Helper::thousandsCurrencyFormat($summary_sales['overall']),
//                'total_item_purchased' => \App\Pongo\Libraries\Helper::thousandsCurrencyFormat($summary_item_purchased['regular']),
//                'total_orders'         => \App\Pongo\Libraries\Helper::thousandsCurrencyFormat($n_orders),
//                'total_item_per_cart'  => \App\Pongo\Libraries\Helper::thousandsCurrencyFormat(($n_orders) > 0 ? ($summary_item_purchased['regular'] / $n_orders) : 0),
//                'total_sales_per_cart' => \App\Pongo\Libraries\Helper::thousandsCurrencyFormat(($n_orders) > 0 ? ($summary_sales['regular'] / $n_orders) : 0),
                'conversion_rate'      => Helper::calcConversionRate($n_orders, $pageviews_stat['regular'])
            ],
            'dt_range'            => [
                'start' => $dt_start->format("F d, Y"),
                'end'   => $dt_end->format("F d, Y")
            ],
            'dt_start'            => $dt_start,
            'dt_end'              => $dt_end,
            'top_purchased_items' => [],
            'top_viewed_items'    => [],
            'pageTitle'           => "Dashboard"
        ];
        return \View::make(getenv('FRONTEND_SKINS') . $this->theme . '.panels.dashboard', $output);
    }

}

/* End of file Panel2Controller.php */
/* Location: ./application/controllers/Panel2Controller.php */
