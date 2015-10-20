<?php
namespace App\Controllers\User;

use App\Controllers\BaseController,
    App\Pongo\Libraries\Helper,
    App\Models\Site,
    Illuminate\Support\Facades\Auth,
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
    
    public function index($dt_range_group = "31_days_ago", $dt_start = null, $dt_end = null)  {
        $client   = new Client($_ENV['PREDICTRY_ANALYTICS_URL'] . 'stat/');
        $top_client   = new Client($_ENV['PREDICTRY_ANALYTICS_URL'] . 'top/');
        // current site object
        $site     = Site::find($this->active_site_id);
        $sites    = Site::all();
        $user = Auth::user();
        if ( substr( $user['email'], -14) == "vventures.asia" ) {
            $admin = 1;
        } else { $admin = 0; }
    
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

          $arr_response = $response->json();
          $salesAmountParse = (isset($arr_response['error']) && $arr_response['error']) ? 0 : array_get($arr_response, 'salesAmount');

          $salesAmount = [
            'overall'     => ($salesAmountParse) ? $salesAmountParse['overall'] : 0,
            'recommended' => ($salesAmountParse) ? $salesAmountParse['recommended'] : 0,
            'regular'     => ($salesAmountParse) ? $salesAmountParse['regular'] : 0
          ];

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

          $unique_item_purchased_sum = (isset($arr_response['error']) && $arr_response['error']) ? 0 : array_get($arr_response, 'uniqueItemPurchased');

          $conversionRate_sum = (isset($arr_response['error']) && $arr_response['error']) ? 0 : array_get($arr_response, 'conversionRate');  
       	  $conversionRate = [
            'overall'     => ($conversionRate_sum) ? $conversionRate_sum['overall'] : 0,
            'recommended' => ($conversionRate_sum) ? $conversionRate_sum['recommended'] : 0,
            'regular'     => ($conversionRate_sum) ? $conversionRate_sum['regular'] : 0
	        ];

          $cartBoost = (isset($arr_response['error']) && $arr_response['error']) ? 0 : array_get($arr_response, 'cartBoost');

          $output = [
              'overviews'           => [
                  'total_pageviews'             => $pageviews_stat['overall'],
                  'total_pageviews_regular'     => $pageviews_stat['regular'],
                  'total_pageviews_recommended' => $pageviews_stat['recommended'],
                  
                  'total_uvs'                   => $uniqueVisitor['overall'],
                  'total_uvs_recommended'       => $uniqueVisitor['recommended'],
                  
                  'total_sales_amount'          => $salesAmount['overall'],
                  'total_sales_recommended'     => $salesAmount['recommended'],

                  'total_item_purchased'              => $summary_item_purchased['overall'],
                  'total_item_purchased_recommended'  => $summary_item_purchased['recommended'],
                  'unique_item_purchased'       => $unique_item_purchased_sum['overall'],

                  'total_orders'                => $n_orders['overall'],
                  'total_orders_recommended'    => $n_orders['recommended'],

                  'total_item_per_cart'         => $summary_item_per_cart['overall'],
                  'total_sales_per_cart'        => $summary_sales['overall'],
                  'conversion_rate'             => $conversionRate['overall'],
                  'cartBoost'                   => $cartBoost
                ],

              'dt_range'            => [
                  'start' => date("F d, Y", $tstart),
                  'end'   => date("F d, Y", $tend)
                ],
              'currency'            => $site['currency'],
              'sites'               => $sites,
              'site'                => $site,
              'admin'               => $admin,
              'dt_start'            => $dt_start,
              'dt_end'              => $dt_end,
              'top_purchased_items' => $tpi,
              'top_viewed_items'    => $tvi,
              'pageTitle'           => "Dashboard",
              'bucket_view'         => $arr_bucket_view  
          ];
          return \View::make(getenv('FRONTEND_SKINS') . $this->theme . '.panels.dashboard2', $output);  
        } 
        else 
        {
          $output = [
            'message' => "Seems Like there is no logs recieved from your site"
          ];
          
          return \View::make(getent('FRONTEND_SKINS') . $this->theme. '.panels.nolog', $output);
        }
    }
    
    
    public function adminPanel($tenantID, $dt_range_group = "31_days_ago", $dt_start = null, $dt_end = null)  {
        $client   = new Client($_ENV['PREDICTRY_ANALYTICS_URL'] . 'stat/');
        $top_client   = new Client($_ENV['PREDICTRY_ANALYTICS_URL'] . 'top/');
        // current site object
        $site     = Site::find($tenantID);
     
        $sites    = Site::all();

        // active_site_name
        $current_site = $site['name']; 
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

          $arr_response = $response->json();
          $salesAmountParse = (isset($arr_response['error']) && $arr_response['error']) ? 0 : array_get($arr_response, 'salesAmount');

          $salesAmount = [
            'overall'     => ($salesAmountParse) ? $salesAmountParse['overall'] : 0,
            'recommended' => ($salesAmountParse) ? $salesAmountParse['recommended'] : 0,
            'regular'     => ($salesAmountParse) ? $salesAmountParse['regular'] : 0
          ];

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

          $unique_item_purchased_sum = (isset($arr_response['error']) && $arr_response['error']) ? 0 : array_get($arr_response, 'uniqueItemPurchased');

          $conversionRate_sum = (isset($arr_response['error']) && $arr_response['error']) ? 0 : array_get($arr_response, 'conversionRate');  
       	  $conversionRate = [
            'overall'     => ($conversionRate_sum) ? $conversionRate_sum['overall'] : 0,
            'recommended' => ($conversionRate_sum) ? $conversionRate_sum['recommended'] : 0,
            'regular'     => ($conversionRate_sum) ? $conversionRate_sum['regular'] : 0
	        ];

          $cartBoost = (isset($arr_response['error']) && $arr_response['error']) ? 0 : array_get($arr_response, 'cartBoost');
          
          $output = [
              'overviews'           => [
                  'total_pageviews'             => $pageviews_stat['overall'],
                  'total_pageviews_regular'     => $pageviews_stat['regular'],
                  'total_pageviews_recommended' => $pageviews_stat['recommended'],
                  
                  'total_uvs'                   => $uniqueVisitor['overall'],
                  'total_uvs_recommended'       => $uniqueVisitor['recommended'],
                  
                  'total_sales_amount'          => $salesAmount['overall'],
                  'total_sales_recommended'     => $salesAmount['recommended'],

                  'total_item_purchased'              => $summary_item_purchased['overall'],
                  'total_item_purchased_recommended'  => $summary_item_purchased['recommended'],
                  'unique_item_purchased'       => $unique_item_purchased_sum['overall'],

                  'total_orders'                => $n_orders['overall'],
                  'total_orders_recommended'    => $n_orders['recommended'],

                  'total_item_per_cart'         => $summary_item_per_cart['overall'],
                  'total_sales_per_cart'        => $summary_sales['overall'],
                  'conversion_rate'             => $conversionRate['overall'],
                  'cartBoost'                   => $cartBoost
                ],

              'dt_range'            => [
                  'start' => date("F d, Y", $tstart),
                  'end'   => date("F d, Y", $tend)
                ],
              'currency'            => $site['currency'],
              'dt_start'            => $dt_start,
              'sites'               => $sites,
              'site'                => $site,   

              'dt_end'              => $dt_end,
              'top_purchased_items' => $tpi,
              'top_viewed_items'    => $tvi,
              'pageTitle'           => "Dashboard",
              'bucket_view'         => $arr_bucket_view  
          ];
          return \View::make(getenv('FRONTEND_SKINS') . $this->theme . '.panels.dashboard2', $output);  
        } 
        else 
        {
          $output = [
            'message' => "Seems Like there is no logs recieved from your site"
          ];
          
          return \View::make(getent('FRONTEND_SKINS') . $this->theme. '.panels.nolog', $output);
        }
    }
}
