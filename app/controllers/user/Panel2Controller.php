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
        $client = new Client($_ENV['PREDICTRY_ANALYTICS_URL'] . 'tenants/' . \Session::get("active_site_name") . '/');

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
                'overall'     => $pageviews_regular_sum['overall'],
                'recommended' => $pageviews_regular_sum['recommended'],
                'regular'     => $pageviews_regular_sum['regular']
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
                'overall'     => $item_purchased_regular_sum['overall'],
                'recommended' => $item_purchased_regular_sum['recommended'],
                'regular'     => $item_purchased_regular_sum['regular']
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
                'overall'     => $sales_regular_sum['overall'],
                'recommended' => $sales_regular_sum['recommended'],
                'regular'     => $sales_regular_sum['regular']
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
