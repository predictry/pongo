<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminBaseController,
    App\Models\Account,
    App\Models\Site,
    App\Pongo\Libraries\Helper,
    Symfony\Component\HttpFoundation\Response,
    URL,
    View;

class PanelController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();

        $custom_script = "var site_url = '" . URL::to('/v2/admin') . "';";

        \View::share(array("custom_script" => $custom_script));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($dt_range_group = "today", $dt_start = null, $dt_end = null)
    {
        $ranges   = Helper::getSelectedFilterDateRange($dt_range_group, $dt_start, $dt_end);
        $dt_start = $ranges['dt_start'];
        $dt_end   = $ranges['dt_end'];

        //list of sites
        $sites = Site::all();

        //filter account under specific account
        $allowed_sites = Account::find(10)->sites();

        if ($allowed_sites) {
            $allowed_sites = $allowed_sites->get();
        }
        else {
            $allowed_sites = false;
        }

        $pageviews_stat['regular']         = 100;
        $summary_sales['regular']          = 100;
        $summary_sales['overall']          = 100;
        $summary_item_purchased['regular'] = 100;

        $n_session = 100;
        $n_orders  = 100;

        $output = [
            'pageTitle' => 'Admin Dashboard Overview',
            'dt_range'  => [
                'start' => $dt_start->format("F d, Y"),
                'end'   => $dt_end->format("F d, Y")
            ],
            'dt_start'  => $dt_start,
            'dt_end'    => $dt_end,
            'sites'     => $allowed_sites ? $allowed_sites : $sites,
            'overviews' => [
                'total_pageviews'      => number_format($pageviews_stat['regular']),
                'total_uvs'            => number_format($n_session),
                'total_sales_amount'   => number_format($summary_sales['overall']),
                'total_item_purchased' => number_format($summary_item_purchased['regular']),
                'total_orders'         => number_format($n_orders),
                'total_item_per_cart'  => number_format(($n_orders) > 0 ? ($summary_item_purchased['regular'] / $n_orders) : 0, 2),
                'total_sales_per_cart' => number_format(($n_orders) > 0 ? ($summary_sales['regular'] / $n_orders) : 0),
                'conversion_rate'      => Helper::calcConversionRate($n_orders, $pageviews_stat['regular'])
            ]
        ];

        return View::make('admin.panels.dashboard', $output);
    }

}
