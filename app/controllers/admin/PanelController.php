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
        \View::share(array("ca" => get_class(), "custom_script" => $custom_script));
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
        $sites         = Site::all();
        $allowed_sites = null;
        //filter account under specific account
//        $allowed_sites = Account::find(10)->sites(); //null

        if ($allowed_sites) {
            $allowed_sites = $allowed_sites->get();
        }

        $output = [
            'pageTitle' => 'Admin Dashboard Overview',
            'dt_range'  => [
                'start' => $dt_start->format("F d, Y"),
                'end'   => $dt_end->format("F d, Y")
            ],
            'dt_start'  => $dt_start,
            'dt_end'    => $dt_end,
            'sites'     => $allowed_sites ? $allowed_sites : $sites,
        ];

        return View::make('admin.panels.dashboard', $output);
    }

}
