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
    /* App\Models\Action,
    App\Models\ActionInstance, */
    App\Pongo\Libraries\Helper,
    App\Pongo\Repository\PanelRepository,
    Cache,
    Guzzle\Service\Client,
    URL,
    Response,
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
        $client   = new Client($_ENV['PREDICTRY_ANALYTICS_URL'] . 'stat/');
        $current_site = \Session::get("active_site_name");
        
        $ranges   = Helper::getSelectedFilterDateRange($dt_range_group, $dt_start, $dt_end);
        /* $dt_start = $ranges['dt_start']; */
        /* $dt_end   = $ranges['dt_end']; */
        
        $o_sd     = "20150601";
        $o_ed     = "20150605";
        $o_sh     = "01";
        $o_eh     = "10";

        $dt_start = $o_sd . $o_sh;
        $dt_end   = $o_ed . $o_eh; 
        
        
        $cache_pageviews_stat = null;

        /* just a global response for dashboard */
        $response     = $client->get("overview?tenantId=". $current_site . "&startDate=" . $dt_start. "&endDate=" . $dt_end)->send();

        if (is_null($cache_pageviews_stat)) {
            $arr_response = $response->json();
            $pageviews_regular_sum = (isset($arr_response['error']) && $arr_response['error']) ? false : array_get($arr_response, 'pageView');
            $pageviews_stat = [
                'overall'     => ($pageviews_regular_sum) ? $pageviews_regular_sum['overall'] : 0,
                'recommended' => ($pageviews_regular_sum) ? $pageviews_regular_sum['recommended'] : 0,
                'regular'     => ($pageviews_regular_sum) ? $pageviews_regular_sum['regular'] : 0
            ];
        }
        else {
            $pageviews_stat = $cache_pageviews_stat;
        }

        $cache_summary_item_purchased = null; 
        if (is_null($cache_summary_item_purchased)) {
            $arr_response               = $response->json();
            $item_purchased_regular_sum = (isset($arr_response['error']) && $arr_response['error']) ? false : array_get($arr_response, 'itemPurchased');

            $summary_item_purchased = [
                'overall'     => ($item_purchased_regular_sum) ? $item_purchased_regular_sum['overall'] : 0,
                'recommended' => ($item_purchased_regular_sum) ? $item_purchased_regular_sum['recommended'] : 0,
                'regular'     => ($item_purchased_regular_sum) ? $item_purchased_regular_sum['regular'] : 0
            ];
        }
        else {
          $summary_item_purchased = $cache_summary_item_purchased;
        }

        $cache_n_orders = null;
        if (is_null($cache_n_orders)) {
            $arr_response = $response->json();
            $n_orders     = (isset($arr_response['error']) && $arr_response['error']) ? 0 : array_get($arr_response, 'orders');
        }
        else {
            $n_orders = $cache_n_orders; 
        }


        $cache_summary_sales = null;
        if (is_null($cache_summary_sales)) {
            $arr_response      = $response->json();
            $sales_regular_sum = (isset($arr_response['error']) && $arr_response['error']) ? 0 : array_get($arr_response, 'salesPerCart');
            $summary_sales     = [
                'overall'     => ($sales_regular_sum) ? $sales_regular_sum['overall'] : 0,
                'recommended' => ($sales_regular_sum) ? $sales_regular_sum['recommended'] : 0,
                'regular'     => ($sales_regular_sum) ? $sales_regular_sum['regular'] : 0
            ];
        }
        else {
            $summary_sales = $cache_summary_sales;
        }
        
        
        
        $output_top_items['top_purchased_items'] = [];
        $output_top_items['top_viewed_items']    = [];
        
        $tstart = strtotime("$o_sd");
        $tend   = strtotime("$o_ed");
        $output = [
            'overviews'           => [
                'total_pageviews'      => number_format($pageviews_stat['regular']),
                // 'total_uvs'            => number_format($n_session),
                'total_uvs'            => number_format(1000),
                'total_sales_amount'   => number_format($summary_sales['overall']),
                'total_item_purchased' => number_format($summary_item_purchased['regular']),
                'total_orders'         => number_format($n_orders),
                'total_item_per_cart'  => number_format(($n_orders) > 0 ? ($summary_item_purchased['regular'] / $n_orders) : 0, 2),
                'total_sales_per_cart' => number_format(($summary_sales['regular'])),
                'conversion_rate'      => Helper::calcConversionRate($n_orders, $pageviews_stat['regular'])
            ],
            'dt_range'            => [
                'start' => date("F d, Y", $tstart),
                'end'   => date("F d, Y", $tend)
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
