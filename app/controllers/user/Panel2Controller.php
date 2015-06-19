<?php

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

class Panel2Controller extends BaseController   {
    protected $panel_repository = null;
    
    function __construct(PanelRepository $repository) {
        parent::__construct();

        $this->panel_repository                 = $repository;
        $this->panel_repository->active_site_id = $this->active_site_id;

        $custom_script = "var site_url = '" . URL::to('/') . "';";
        View::share(array("ca" => get_class(), "custom_script" => $custom_script));
    }

    public function index($dt_range_group = "today", $dt_start = null, $dt_end = null)  {
        $client   = new Client($_ENV['PREDICTRY_ANALYTICS_URL'] . 'stat/');
        $top_client   = new Client($_ENV['PREDICTRY_ANALYTICS_URL'] . 'top/');
        
        // $current_site = \Session::get("active_site_name");    
        $current_site = "BUKALAPAK";
        $ranges   = Helper::getSelectedFilterDateRange($dt_range_group, $dt_start, $dt_end); 
        
        $o_sd     = date("YmdH",strtotime($ranges['dt_start']));
        $o_ed     = date("YmdH",strtotime($ranges['dt_end']));   
        
        $dt_start = $o_sd;
        $dt_end   = $o_ed;

        /* just a global response for dashboard */
        $response     = $client->get("overview?tenantId=". $current_site . "&startDate=" . $dt_start . "&endDate=" . $dt_end)->send();
       
        $arr_response = $response->json();
        $pageviews_regular_sum = (isset($arr_response['error']) && $arr_response['error']) ? false : array_get($arr_response, 'pageView');
        $pageviews_stat = [
            'overall'     => ($pageviews_regular_sum) ? $pageviews_regular_sum['overall'] : 0,
            'recommended' => ($pageviews_regular_sum) ? $pageviews_regular_sum['recommended'] : 0,
            'regular'     => ($pageviews_regular_sum) ? $pageviews_regular_sum['regular'] : 0
        ];
    
        

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

        $top_purchased_items  = $top_client->get("sales")->send()->json();
        $top_viewed_items     = $top_client->get("hits")->send()->json();

        $tstart = strtotime($ranges['dt_start']);
        $tend   = strtotime($ranges['dt_end']);
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
                'start' => date("[H] F d, Y", $tstart),
                'end'   => date("[H] F d, Y", $tend)
              ],

            'dt_start'            => $dt_start,
            'dt_end'              => $dt_end,
            'top_purchased_items' => $top_purchased_items['items'],
            'top_viewed_items'    => $top_viewed_items['items'],
            'pageTitle'           => "Dashboard"
        ];
      return \View::make(getenv('FRONTEND_SKINS') . $this->theme . '.panels.dashboard', $output); 
    }
}

