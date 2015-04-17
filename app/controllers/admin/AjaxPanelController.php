<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminBaseController,
    App\Pongo\Libraries\Helper,
    Guzzle\Service\Client,
    Illuminate\Support\Facades\Cache,
    Illuminate\Support\Facades\Request,
    Illuminate\Support\Facades\Response,
    Illuminate\Support\Facades\View,
    Input;

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 9, 2015 1:24:34 PM
 * File         : AjaxPanelController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class AjaxPanelController extends AdminBaseController
{

    public function postSiteOverviewSummary($dt_range_group = "today", $dt_start = null, $dt_end = null)
    {
        if (Request::ajax()) {

            $tenant = Input::get("tenant");
            $client = new Client($_ENV['PREDICTRY_ANALYTICS_URL'] . 'tenants/' . $tenant . '/');

            $ranges   = Helper::getSelectedFilterDateRange($dt_range_group, $dt_start, $dt_end);
            $dt_start = $ranges['dt_start'];
            $dt_end   = $ranges['dt_end'];

            /*
             * Scenario
             * 1. Get pageviews
             */
            $cache_pageviews_stat = Cache::get("pageviews_{$tenant}_{$dt_start->toDateString()}_{$dt_end->toDateString()}");
            if (is_null($cache_pageviews_stat)) {
                $is_reco_included = true;
                $response         = $client->get("stats-summary/pageviews/{$dt_start}/{$dt_end}/{$is_reco_included}")->send();
                $arr_response     = $response->json();

                $pageviews_stat = (isset($arr_response['error']) && $arr_response['error']) ? ['overall' => 0, 'regular' => 0, 'recommended' => 0] : array_get($arr_response, 'data.pageviews');
                Cache::add("pageviews_{$tenant}_{$dt_start->toDateString()}_{$dt_end->toDateString()}", $pageviews_stat, 60);
            }
            else {
                $pageviews_stat = $cache_pageviews_stat;
            }

            /*
             * Items Purchased
             */
            $cache_summary_item_purchased = Cache::get("cache_summary_item_purchased _{$tenant}_{$dt_start->toDateString()}_{$dt_end->toDateString()}");
            if (is_null($cache_summary_item_purchased)) {
                $response               = $client->get("stats-summary/item-purchased/{$dt_start}/{$dt_end}")->send();
                $arr_response           = $response->json();
                $summary_item_purchased = (isset($arr_response['error']) && $arr_response['error']) ? ['overall' => 0, 'regular' => 0, 'recommended' => 0] : array_get($arr_response, 'data.sum');

                Cache::add("cache_summary_item_purchased _{$tenant}_{$dt_start->toDateString()}_{$dt_end->toDateString()}", $summary_item_purchased, 60);
            }
            else
                $summary_item_purchased = $cache_summary_item_purchased;

            /*
             * Sales Amount
             */
            $cache_summary_sales = Cache::get("cache_summary_sales_{$tenant}_{$dt_start->toDateString()}_{$dt_end->toDateString()}");
            if (is_null($cache_summary_sales)) {
                $response      = $client->get("stats-summary/sales-amount/{$dt_start}/{$dt_end}")->send();
                $arr_response  = $response->json();
                $summary_sales = (isset($arr_response['error']) && $arr_response['error']) ? ['overall' => 0, 'regular' => 0, 'recommended' => 0] : array_get($arr_response, 'data.sum');

                Cache::add("cache_summary_sales_{$tenant}_{$dt_start->toDateString()}_{$dt_end->toDateString()}", $summary_sales, 60);
            }
            else
                $summary_sales = $cache_summary_sales;

            /*
             * Orders 
             */
            $cache_n_orders = Cache::get("cache_n_orders_{$tenant}_{$dt_start->toDateString()}_{$dt_end->toDateString()}");
            if (is_null($cache_n_orders)) {
                $response     = $client->get("stats-summary/orders/{$dt_start}/{$dt_end}")->send();
                $arr_response = $response->json();
                $n_orders     = (isset($arr_response['error']) && $arr_response['error']) ? ['overall' => 0, 'regular' => 0, 'recommended' => 0] : array_get($arr_response, 'data.count');
                Cache::add("cache_n_orders_{$tenant}_{$dt_start->toDateString()}_{$dt_end->toDateString()}", $n_orders, 60);
            }
            else
                $n_orders = $cache_n_orders;

            /*
             * Sessions
             */
            $cache_n_sessions = Cache::get("cache_n_sessions_{$tenant}_{$dt_start->toDateString()}_{$dt_end->toDateString()}");
            if (is_null($cache_n_sessions)) {
                $response     = $client->get("stats-summary/unique-visitors/{$dt_start}/{$dt_end}/session")->send();
                $arr_response = $response->json();
                $n_sessions   = (isset($arr_response['error']) && $arr_response['error']) ? 0 : array_get($arr_response, 'data.count');
                Cache::add("cache_n_sessions_{$tenant}_{$dt_start->toDateString()}_{$dt_end->toDateString()}", $n_sessions, 60);
            }
            else
                $n_sessions = $cache_n_sessions;

            /*
             * Total Skus
             */
            $cache_n_skus = Cache::get("cache_n_skus_{$tenant}");
            if (is_null($cache_n_skus)) {
                $response     = $client->get("stats-summary/total-skus")->send();
                $arr_response = $response->json();
                $n_skus       = (isset($arr_response['error']) && $arr_response['error']) ? 0 : array_get($arr_response, 'data.count');
                Cache::add("cache_n_skus_{$tenant}", $n_skus, 60);
            }
            else {
                $n_skus = $cache_n_skus;
            }

            /*
             * Top Bought Items
             */
            $limit                    = 10;
            $cache_n_top_bought_items = Cache::get("cache_n_top_bought_items_{$tenant}");
            if (is_null($cache_n_top_bought_items)) {
                $response         = $client->get("stats-summary/top-bought-items/{$limit}")->send();
                $arr_response     = $response->json();
                $top_bought_items = (isset($arr_response['error']) && $arr_response['error']) ? 0 : array_get($arr_response, 'data.items');
                Cache::add("cache_n_top_bought_items_{$tenant}", $top_bought_items, 60);
            }
            else {
                $top_bought_items = $cache_n_top_bought_items;
            }

            $output = [
                'overviews' => [
                    'total_pageviews'      => number_format($pageviews_stat['regular']),
                    'total_uvs'            => number_format($n_sessions),
                    'total_sales_amount'   => number_format($summary_sales['overall']),
                    'total_item_purchased' => number_format($summary_item_purchased['regular']),
                    'total_orders'         => number_format($n_orders),
                    'total_skus'           => number_format($n_skus),
                    'total_item_per_cart'  => number_format(($n_orders) > 0 ? ($summary_item_purchased['regular'] / $n_orders) : 0, 2),
                    'total_sales_per_cart' => number_format(($n_orders) > 0 ? ($summary_sales['regular'] / $n_orders) : 0),
                    'conversion_rate'      => Helper::calcConversionRate($n_orders, $pageviews_stat['regular']),
                    'top_bought_items'     => $top_bought_items
                ]
            ];

            $html = View::make('admin.panels.dashboard.overview_summary', $output)->render();
            return Response::json([
                        'error'   => false,
                        'data'    => $html,
                        'message' => ''
            ]);
        }
    }

}

/* End of file AjaxPanelController.php */
