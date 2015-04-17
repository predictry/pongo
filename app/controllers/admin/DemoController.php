<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminBaseController,
    App\Models\Account,
    App\Models\Site,
    Guzzle\Http\Client,
    Input,
    Paginator,
    Symfony\Component\HttpFoundation\Response,
    Validator,
    View;

class DemoController extends AdminBaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $table_header = [
            'name' => 'Tenant ID',
            'url'  => 'URL Address'
        ];

        //list of sites
        $per_page = 10;

        //filter account under specific account
        $allowed_sites = Account::find(10)->sites(); //null

        if ($allowed_sites) {
            $allowed_sites = $allowed_sites->get();
        }
        else
            $allowed_sites = Site::all();

        $paginator = Paginator::make($allowed_sites->toArray(), count($allowed_sites), $per_page);
        return View::make('admin.panels.demo.manage', ['items' => $allowed_sites, 'paginator' => $paginator, 'moduleName' => 'Demo Site', 'table_header' => $table_header]);
    }

    public function show($site_id)
    {
        $items        = null;
        $per_page     = 12;
        $total_items  = 0;
        $current_page = \Input::get("page", 1);

        $validator = Validator::make(['site_id' => $site_id], ['site_id' => 'required|exists:sites,id']);
        if ($validator->passes()) {
            $skip         = (int) ($current_page == 1) ? 0 : ($current_page * $per_page) - $per_page;
            $tenant       = Site::find($site_id);
            $client       = new Client('http://localhost/predictry-analytics/public/api/v1/tenants/' . $tenant->name . '/');
            $response     = $client->get("items/paginated-items/{$skip}/{$per_page}")->send();
            $arr_response = $response->json();

            $items       = (isset($arr_response['error']) && $arr_response['error']) ? [] : array_get($arr_response, 'data.items');
            $total_items = (isset($arr_response['error']) && $arr_response['error']) ? [] : array_get($arr_response, 'data.total');
        }

        $paginator = Paginator::make($items, $total_items, $per_page);

        $output = array(
            'paginator' => $paginator,
            'site_id'   => $site_id
        );



        return View::make("admin.panels.demo.catalog", $output);
    }

    public function getItemDetail($site_id, $item_id)
    {
        $item      = null;
        $validator = Validator::make(['site_id' => $site_id], ['site_id' => 'required|exists:sites,id']);
        if ($validator->passes()) {
            $tenant       = Site::find($site_id);
            $client       = new Client('http://localhost/predictry-analytics/public/api/v1/tenants/' . $tenant->name . '/');
            $response     = $client->get("items/detail/{$item_id}")->send();
            $arr_response = $response->json();

            $item = (isset($arr_response['error']) && $arr_response['error']) ? null : array_get($arr_response, 'data.item');
        }

        return View::make('admin.panels.demo.product_detail', [
                    'item'                => $item,
                    'site_id'             => $site_id,
                    'item_id'             => $item_id,
                    'widget'              => [],
                    'dummy_reco_response' => [],
                    'api_credential_vars' => "var tenant_id= '{$tenant->name}'; var api_key = '{$tenant->api_key}';",
        ]);
    }

}
