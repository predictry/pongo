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
    function __construct(PanelRepository $repository) {
        parent::__construct();
        $this->panel_repository                 = $repository;
        $this->panel_repository->active_site_id = $this->active_site_id;
        $custom_script                          = "var site_url = '" . URL::to('/') . "';";
        View::share(array("ca" => get_class(), "custom_script" => $custom_script));
    }
    
    public function index($dt_range_group = "today", $dt_start = null, $dt_end = null)  {
        $client   = new Client($_ENV['PREDICTRY_ANALYTICS_URL'] . 'stat/');
        $top_client   = new Client($_ENV['PREDICTRY_ANALYTICS_URL'] . 'top/');

        // active_site_name
        $current_site = \Session::get("active_site_name");
        $ranges   = Helper::getSelectedFilterDateRange($dt_range_group, $dt_start, $dt_end); 
        
        // original time format
        // comes from the url/: directly 
        $o_sd     = date("YmdH",strtotime($ranges['dt_start']));
        $o_ed     = date("YmdH",strtotime($ranges['dt_end']));   
  
        // to show in view 
        // with ISO format
        $dt_start = $o_sd;
        $dt_end   = $o_ed;
  
        // get response from
        // fisher numeric data
        // fisher bucket data /_h
        $response     = $client->get("overview?tenantId=". $current_site . "&startDate=" . $dt_start . "&endDate=" . $dt_end)->send(); 
        $bucket_view  = $client->get("?tenantId=". $current_site . "&startDate=" . $dt_start . "&endDate=" . $dt_end . "&metric=VIEWS&interval=hour")->send(); 
        
        $top_purchased_items  = $top_client->get("sales?tenantId=". $current_site . "&startDate=" . $dt_start . "&endDate=" .$dt_end)->send()->json(); 
        $top_viewed_items  = $top_client->get("hits?tenantId=". $current_site . "&startDate=" . $dt_start . "&endDate=" .$dt_end)->send()->json(); 

        // check the error index
        if (isset($top_purchased_items['error']))
          $tpi = [];
        else 
          $tpi = $top_purchased_items['items'];

        if (isset($top_viewed_items['error']))
          $tvi = [];
        else
          $tvi = $top_viewed_items['items'];
        //end check error
    
        $tstart = strtotime($ranges['dt_start']);
        $tend   = strtotime($ranges['dt_end']);

        if  ($response->getStatusCode() == 200) {
          
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
              $n_orders_sum= (isset($arr_response['error']) && $arr_response['error']) ? 0 : array_get($arr_response, 'orders');
              $n_orders = [
                  'overall'     => ($n_orders_sum) ? $n_orders_sum['overall'] : 0,
                  'recommended' => ($n_orders_sum) ? $n_orders_sum['recommended'] : 0,
                  'regular'     => ($n_orders_sum) ? $n_orders_sum['regular'] : 0
              ];
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
          $output = [
              'overviews'           => [
                  'total_pageviews'      => number_format($pageviews_stat['overall']),
                  'total_uvs'            => number_format($uniqueVisitor['overall']),
                  'total_sales_amount'   => number_format($summary_sales['overall']),
                  'total_item_purchased' => number_format($summary_item_purchased['overall']),
                  'total_orders'         => number_format($n_orders['overall']),
                  'total_item_per_cart'  => number_format($summary_item_per_cart['overall']),
                  'total_sales_per_cart' => number_format($summary_sales['overall']),
                  'conversion_rate'      => $conversionRate
                ],
              'dt_range'            => [
                  'start' => date("[H] F d, Y", $tstart),
                  'end'   => date("[H] F d, Y", $tend)
                ],

              'dt_start'            => $dt_start,
              'dt_end'              => $dt_end,
              'top_purchased_items' => $tpi,
              'top_viewed_items'    => $tvi,
              'pageTitle'           => "Dashboard",
              'bucket_view'         => $arr_bucket_view  
          ];
          return \View::make(getenv('FRONTEND_SKINS') . $this->theme . '.panels.dashboard', $output);  
        } 
        else 
        {
          /* if there is some error with requesting to API */
          /* show this */
          
          $output = [
            'message' => "Seems Like there is no logs recieved from your site"
          ];
          
          return \View::make(getent('FRONTEND_SKINS') . $this->theme. '.panels.nolog', $output);
        }
    }
}
