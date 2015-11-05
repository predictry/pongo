<?php

namespace App\Controllers\User;

use App\Controllers\BaseController,
    App\Models\Item,
    App\Models\Site,
    App\Models\Widget,
    Input,
    Paginator,
    Redirect,
    Session,
    Response,
    View;

class DemoController extends BaseController
{

    public function __construct()
    {
        parent::__construct();

        $count = Widget::where('site_id', $this->active_site_id)->count();

        if ($count <= 0) {
            Redirect::to('v2/home')->with("flash_error", "Currently, you don't have any ruleset to set into widget.");
        }

        View::share('custom_script', "jQuery(document).ready(function () { $('.navbar-minimalize').trigger('click'); });");
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $widgets = Widget::where('site_id', $this->active_site_id)->lists("name", "id");

        $this->model = new Item();
        $page        = Input::get('page', 1);
        $data        = $this->getByPage($page, 16, "site_id", $this->active_site_id);
        $message     = '';
        $metas       = [];
        $current_site = \Session::get("active_site_name");


        if (!is_array($data) && !is_object($data)) {
            $message   = $data;
            $paginator = null;
        }
        else {

            foreach ($data->items as $obj) {
                $item_metas     = \App\Models\ItemMeta::where('item_id', $obj->id)->get()->lists("value", "key");
                $arr_item_metas = ($item_metas) ? $item_metas : [];
                $metas          = array_add($metas, $obj->id, $arr_item_metas);
            }


            $paginator = Paginator::make($data->items, $data->totalItems, $data->limit);
        }

        $output = array(
            'paginator'    => $paginator,
            'metas'        => $metas,
            "str_message"  => $message,
            "pageTitle"    => "Manage Items",
            "table_header" => $this->model->manage_table_header,
            "page"         => $page,
            "modalTitle"   => "View Item",
            "current_site" => $current_site,
            "upper"        => [],
            'widgets'      => $widgets
        );

        return View::make(getenv('FRONTEND_SKINS') . $this->theme . ".panels.demo.catalog", $output);
    }

    public function catalog()
    {
        //Show catalog with pagination
    }

    public function show($item_id)
    {
        $api_credential_vars = "";

        $dummy_reco_response = (object) [
                    "status"             => 200,
                    "data"               => (object) [
                        "items" => [
                            (object) [
                                "id"       => "189800",
                                "name"     => "[Up to 51% Off] Packages of Fries, Burgers, & Hotdogs from Fries With Stranger",
                                "item_url" => "http:\/\/groupon.co.id\/promo.php?i=71567",
                                "img_url"  => "https://static2.groupon.co.id/images/picpromobig/f0935e4cd5920aa6c7c996a5ee53a70f71738.jpg",
                                "price"    => "55000.00"
                            ],
                            (object) [
                                "id"       => "189826",
                                "name"     => "[Up to 51% Off] Chinese Food & Suki Set Menu Package for 2/4/6 Persons from XO Suki & Dim Sum - BayWalk Mall Pluit",
                                "item_url" => "http:\/\/groupon.co.id\/promo.php?i=189826",
                                "img_url"  => "https:\/\/static2.groupon.co.id\/images\/picpromobig\/6364d3f0f495b6ab9dcf8d3b5c6e0b0171196.jpg",
                                "price"    => "99000.00"
                            ],
                            (object) [
                                "id"       => "189847",
                                "name"     => "[30% Off] Voucher Value Rp. 35.000,- Nett (Worth Rp. 50.000,-) for Daisuki Products at Carrefour",
                                "item_url" => "http:\/\/groupon.co.id\/voucher\/jakarta\/71716\/daisuki-voucher-value-rp-35000-nett-worth-rp-50000",
                                "img_url"  => "https:\/\/static2.groupon.co.id\/images\/picpromobig\/dc6a70712a252123c40d2adba6a11d8471716.jpg",
                                "price"    => "199000.00"
                            ],
                            (object) [
                                "id"       => "189885",
                                "name"     => "Grilled Chicken Breast, Pork Chop or Lamb Chop at Romeos Bar & Grillery by Ossotel Legian, Bali. Starting from Rp. 114.000,- Nett / Person",
                                "item_url" => "http:\/\/groupon.co.id\/voucher\/travel-deals\/71458\/grilled-cuisine-at-romeos-bar-grillery-by-ossotel-legian-bali",
                                "img_url"  => "https:\/\/static2.groupon.co.id\/images\/picpromobig\/2823f4797102ce1a1aec05359cc16dd971458.jpg",
                                "price"    => "114000.00"
                            ],
                            (object) [
                                "id"       => "189994",
                                "name"     => "[Up to 53% Off] Menu Package of Grilled Menu, Light Meal and Ice Cream from Stranough Music Café",
                                "item_url" => "http:\/\/groupon.co.id\/voucher\/bandung\/71113\/menu-package-of-grilled-menu-light-meal-and-ice-cream-from-stranough-music",
                                "img_url"  => "https:\/\/static2.groupon.co.id\/images\/picpromobig\/6c3cf77d52820cd0fe646d38bc2145ca71113.jpg",
                                "price"    => "49000.00"
                            ],
                            (object) [
                                "id"       => "190022",
                                "name"     => "[Up to 56% Off] Lapis Gulung, Bolu Gulung, Lapis Surabaya dan Lapis Legit dari Loy Cake",
                                "item_url" => "http:\/\/groupon.co.id\/voucher\/travel-deals\/71458\/grilled-cuisine-at-romeos-bar-grillery-by-ossotel-legian-bali",
                                "img_url"  => "https:\/\/static2.groupon.co.id\/images\/picpromobig\/e6b4b2a746ed40e1af829d1fa82daa1071641.jpg",
                                "price"    => "114000.00"
                            ]
                        ]
                    ],
                    "widget_instance_id" => 50
        ];

        $site = Site::find($this->active_site_id);

        if ($site) {

            $api_credential_vars .= 'var tenant_id = "' . $site->name . '";';
            $api_credential_vars .= 'var api_key = "' . $site->api_key . '";';

            $item       = Item::find($item_id);
            $item_metas = ($item) ? $item->item_metas()->lists("value", "key") : [];

            if ($item && ($site->id !== $item->site_id)) {
                return Redirect::to('v2/demo')->with('flash_error', 'You are not allowed to access this item.');
            }

            if ($item) {

                $widget = Widget::where('site_id', $this->active_site_id)->first();

                $custom_script = "jQuery(document).ready(function () { $('.navbar-minimalize').trigger('click'); testCallback(response); });";

                $output = [
                    "pageTitle"           => "Recommendation Demo",
                    'item'                => $item->toArray(),
                    'item_metas'          => $item_metas,
                    'widget'              => is_object($widget) ? $widget->toArray() : ['id' => 0],
                    'api_credential_vars' => $api_credential_vars,
                    'dummy_reco_response' => $dummy_reco_response,
                    'custom_script'       => $custom_script
                ];

                return View::make(getenv('FRONTEND_SKINS') . $this->theme . ".panels.demo.product_detail", $output);
            }
        }
    }

}
