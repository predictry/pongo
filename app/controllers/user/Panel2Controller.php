<?php

namespace App\Controllers\User;
use App\Controllers\BaseController,
    App\Pongo\Libraries\Helper,
    App\Pongo\Repository\PanelRepository,
    Cache,
    Guzzle\Service\Client,
    URL,
    Response,
    View;

class Panel2Controller extends BaseController   {
    protected $panel_repository = null;
    /* private funcion */
    function isDomainAvailible($domain)
       {
               //check, if a valid url is provided
               if(!filter_var($domain, FILTER_VALIDATE_URL))
               {
                       return false;
               }

               //initialize curl
               $curlInit = curl_init($domain);
               curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
               curl_setopt($curlInit,CURLOPT_HEADER,true);
               curl_setopt($curlInit,CURLOPT_NOBODY,true);
               curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);

               //get answer
               $response = curl_exec($curlInit);

               curl_close($curlInit);

               if ($response) return true;

               return false;
       }

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
         
        $current_site = "FAMILYNARA2014";
        $ranges   = Helper::getSelectedFilterDateRange($dt_range_group, $dt_start, $dt_end); 
        
        $o_sd     = date("YmdH",strtotime($ranges['dt_start']));
        $o_ed     = date("YmdH",strtotime($ranges['dt_end']));   
        
        $dt_start = $o_sd;
        $dt_end   = $o_ed;
        
    
        $response     = $client->get("overview?tenantId=". $current_site . "&startDate=" . $dt_start . "&endDate=" . $dt_end)->send(); 
        $bucket_view  = $client->get("?tenantId=". $current_site . "&startDate=" . $dt_start . "&endDate=" . $dt_end . "&metric=VIEWS&interval=hour")->send(); 
        
        $arr_bucket_view = $bucket_view->json();
        $arr_response = $response->json();

        $pageviews_regular_sum = (isset($arr_response['error']) && $arr_response['error']) ? false : array_get($arr_response, 'pageView');
        $pageviews_stat = [
            'overall'     => ($pageviews_regular_sum) ? $pageviews_regular_sum['overall'] : 0,
            'recommended' => ($pageviews_regular_sum) ? $pageviews_regular_sum['recommended'] : 0,
            'regular'     => ($pageviews_regular_sum) ? $pageviews_regular_sum['regular'] : 0
        ];

        $uniqueVisitor_sum = (isset($arr_response['error']) && $arr_response['error']) ? false : array_get($arr_response, 'uniqueVisitor');
        $uniqueVisitor = [
          'overall' => ($uniqueVisitor_sum) ? $uniqueVisitor_sum['overall'] : 0,
          'recommended' => ($uniqueVisitor_sum) ? $uniqueVisitor_sum['recommended'] : 0,
          'regular' => ($uniqueVisitor_sum) ? $uniqueVisitor_sum['regular'] : 0
        ];

        $arr_response               = $response->json();
        $item_purchased_regular_sum = (isset($arr_response['error']) && $arr_response['error']) ? false : array_get($arr_response, 'itemPurchased');
        $summary_item_purchased = [
            'overall'     => ($item_purchased_regular_sum) ? $item_purchased_regular_sum['overall'] : 0,
            'recommended' => ($item_purchased_regular_sum) ? $item_purchased_regular_sum['recommended'] : 0,
            'regular'     => ($item_purchased_regular_sum) ? $item_purchased_regular_sum['regular'] : 0
        ];

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
  
        $item_per_cart = (isset($arr_response['error']) && $arr_response['error']) ? 0 : array_get($arr_response, 'itemPerCart');
        $summary_item_per_cart = [
          'overall'     => ($item_per_cart) ? $item_per_cart['overall'] : 0,
          'recommended' => ($item_per_cart) ? $item_per_cart['recommended'] : 0,
          'regular'     => ($item_per_cart) ? $item_per_cart['regular'] : 0
        ];

        $conversionRate = (isset($arr_response['error']) && $arr_response['error']) ? 0 : array_get($arr_response, 'conversionRate'); 
        $top_purchased_items  = $top_client->get("sales")->send()->json(); 
        $top_viewed_items     = $top_client->get("hits")->send()->json();
        $tstart = strtotime($ranges['dt_start']);
        $tend   = strtotime($ranges['dt_end']);
 
        $output = [
            'overviews'           => [
                'total_pageviews'      => number_format($pageviews_stat['overall']),
                'total_uvs'            => number_format($uniqueVisitor['overall']),
                'total_sales_amount'   => number_format($summary_sales['overall']),
                'total_item_purchased' => number_format($summary_item_purchased['overall']),
                'total_orders'         => number_format($n_orders),
                'total_item_per_cart'  => number_format($summary_item_per_cart['overall']),
                'total_sales_per_cart' => number_format($summary_sales['overall']),
                'conversion_rate'      => number_format($conversionRate)
              ],

            'dt_range'            => [
                'start' => date("[H] F d, Y", $tstart),
                'end'   => date("[H] F d, Y", $tend)
              ],

            'dt_start'            => $dt_start,
            'dt_end'              => $dt_end,
            'top_purchased_items' => $top_purchased_items['items'],
            'top_viewed_items'    => $top_viewed_items['items'],
            'pageTitle'           => "Dashboard",

            /* bucket data */
            'bucket_view'         => $arr_bucket_view  
        ];
        return \View::make(getenv('FRONTEND_SKINS') . $this->theme . '.panels.dashboard', $output); 
    }
}

