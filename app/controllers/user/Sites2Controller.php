<?php

namespace App\Controllers\User;

use App\Controllers\BaseController,
    App\Models\AccountMeta,
    App\Models\Action,
    App\Models\ActionMeta,
    App\Models\Site,
    App\Pongo\Repository\SiteRepository,
    Auth,
    File,
    Input,
    Paginator,
    Redirect,
    Response,
    Session,
    Str,
    URL,
    Validator,
    View;

/**
 * Author       : Rifki Yandhi
 * Date Created : Jan 13, 2015 5:16:06 PM
 * File         : app/controllers/Sites2Controller.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class Sites2Controller extends BaseController
{

    private $repository;
    protected $custom_script = '';

    function __construct(SiteRepository $repository)
    {
        parent::__construct();

        $this->repository    = $repository;
        $this->custom_script = "var site_url = '" . URL::to('v2/') . "';";

        View::share(array("ca" => get_class(), "moduleName" => "Site", "view" => false, "custom_action" => "true", "delete" => false, "custom_script" => $this->custom_script));

        if (Auth::user()->plan_id === 3) { //redmart
            View::share(array("create" => false, "edit" => false));
        }
    }

    public function index()
    {
        $this->model = new Site();
        $page        = Input::get('page', 1);
        $data        = $this->getByPage($page, $this->manageViewConfig['limit_per_page'], "account_id", Auth::user()->id, 'id', 'ASC');
        $message     = '';

        if (!is_array($data) && !is_object($data)) {
            $message   = $data;
            $paginator = null;
        }
        else {
            $paginator = Paginator::make($data->items, $data->totalItems, $data->limit);
        }

        $output = array(
            'paginator'          => $paginator,
            'str_message'        => $message,
            "pageTitle"          => "Manage Sites",
            "table_header"       => $this->model->manage_table_header,
            "page"               => $page,
            "custom_action_view" => getenv('FRONTEND_SKINS') . $this->theme . ".panels.sites.customactionview",
            "upper"              => []
        );

        return View::make(getenv('FRONTEND_SKINS') . $this->theme . ".panels.manage", $output);
    }

    public function getCreate()
    {
        return View::make(getenv('FRONTEND_SKINS') . $this->theme . ".panels.sites.form", array("type" => "create", 'pageTitle' => "Add New Site"));
    }

    public function getEdit($id)
    {
        $site = Site::find($id);
        return View::make(getenv('FRONTEND_SKINS') . $this->theme . ".panels.sites.form", array("site" => $site, "type" => "edit", 'pageTitle' => "Edit Site"));
    }

    public function getDefault($id)
    {
        $site = Site::where("id", $id)->where("account_id", Auth::user()->id)->first();
        if ($site->id) {
            Session::set("active_site_id", $site->id);
            Session::set("active_site_name", $site->name);
            Session::remove("default_action_view");
        }
        return Redirect::to('v2/home');
    }

    /*
     * Implementation Wizard
     */

    public function getImplementationWizard($tenant_id)
    {
        $validator = Validator::make(['name' => $tenant_id], ['name' => 'required|exists:sites,name']);
        $site      = $this->repository->isBelongToHim($tenant_id);
        $data      = [];
        if ($validator->passes() && $site) {

            $reco_js_url = asset('reco.js');
            $reco_js_url = str_replace("https", "", $reco_js_url);
            $reco_js_url = str_replace("http", "", $reco_js_url);

            $site_id = !is_null(Session::get('active_site_id')) ? Session::get('active_site_id') : $site->id;
            Session::set('active_site_id', $site->id);

            $site_category = Site::find($site_id)->siteCategory()->first();

            if ($site_category) {
                $site_category_name_slug = Str::slug($site_category->name, '_');
                $json_path               = public_path() . '/data/' . $site_category_name_slug . '.json';
                $data                    = json_decode(File::get($json_path));
            }

            if (is_object($data) || count($data) > 0) {

                $output = [
                    'reco_js_url' => $reco_js_url,
                    'site'        => $site,
                    'data'        => $data,
                    'tenant_id'   => $tenant_id,
                    'pageTitle'   => "Implementation Wizard"
                ];
                return View::make(getenv('FRONTEND_SKINS') . $this->theme . '.panels.sites.wizard.implementation', $output);
            }
            else
                return Redirect::to('v2/sites')->with('flash_error', "Something error.");
        }

        return Redirect::to('v2/sites')->with('flash_error', $validator->messages()->first());
    }

    public function ajaxPostImplementationWizard($tenant_id)
    {
        $action_names        = Input::get("action_names");
        $excluded_properties = Input::get("excluded_properties");


        if (is_array($action_names)) {
            foreach ($action_names as $action_name) {
                $action = Action::where('name', $action_name)->where('site_id', Session::get('active_site_id'))->first();
                if ($action) {
                    $action_meta = ActionMeta::where('key', 'excluded_properties')->where('action_id', $action->id)->first();

                    if (!is_object($action_meta)) {
                        $action_meta = ActionMeta::create([
                                    'key'       => 'excluded_properties',
                                    'action_id' => $action->id,
                                    'value'     => (isset($excluded_properties[$action_name]) && is_array($excluded_properties[$action_name])) ? json_encode($excluded_properties[$action_name]) : json_encode(array())
                        ]);
                    }
                    else {
                        $action_meta->value = (isset($excluded_properties[$action_name]) && is_array($excluded_properties[$action_name])) ? json_encode($excluded_properties[$action_name]) : json_encode(array());
                        $action_meta->update();
                    }

                    Session::remove('is_new_account');
                    $this->is_new_account = true;

                    $is_new_account_meta = AccountMeta::where('account_id', Auth::user()->id)->where('key', 'is_new_account')->first();
                    if ($is_new_account_meta) {
                        $is_new_account_meta->value = false;
                        $is_new_account_meta->update();
                    }

                    Session::flash('flash_message', 'Sucessfully updated.');
                    return Response::json([
                                "error" => false,
                                "data"  => ['redirect' => url("v2/sites")]
                    ]);
                }
            }
        }
    }

    public function getDataCollection($tenant_id)
    {
        $validator = Validator::make(['name' => $tenant_id], ['name' => 'required|exists:sites,name']);
        if ($validator->passes() && $this->repository->isBelongToHim($tenant_id)) {

            $site_category = Site::find(Session::get('active_site_id'))->siteCategory()->first();

            if ($site_category) {

                $site_category_name_slug = Str::slug($site_category->name, '_');
                $json_path               = public_path() . '/data/' . $site_category_name_slug . '.json';
                $data                    = json_decode(File::get($json_path));

                $output = [
                    'data'      => $data,
                    'tenant_id' => $tenant_id
                ];

                return View::make(getenv('FRONTEND_SKINS') . $this->theme . '.panels.sites.wizard.data_collection', $output);
            }
        }
        else
            return Redirect::to('sites')->with('flash_error', $validator->messages()->first());
    }

    public function ajaxGetActionProperties($tenant_id, $action_name)
    {
        $site_category = Site::find(Session::get('active_site_id'))->siteCategory()->first();

        if ($site_category) {

            $site_category_name_slug = Str::slug($site_category->name, '_');
            $json_path               = public_path() . '/data/' . $site_category_name_slug . '.json';
            $data                    = json_decode(File::get($json_path));
            $selected_action         = $this->repository->getSelectedActionFromJson($data, $action_name);

            $excluded_properties = $this->repository->getExcludedProperties(Session::get('active_site_id'), $action_name);
        }

        return Response::json([
                    "error"    => false,
                    "data"     => [
                        "action" => $selected_action
                    ],
                    "response" => View::make(getenv('FRONTEND_SKINS') . $this->theme . ".panels.sites.wizard.list_action_properties", ['action' => $selected_action, "tenant_id" => $tenant_id, "action_name" => $action_name, "excluded_properties" => ($excluded_properties) ? json_decode($excluded_properties->value) : []])->render()
        ]);
    }

    public function ajaxGetActionSnipped($tenant_id, $action_name)
    {
        $site_category = Site::find(Session::get('active_site_id'))->siteCategory()->first();

        if ($site_category) {

            $site_category_name_slug = Str::slug($site_category->name, '_');
            $json_path               = public_path() . '/data/' . $site_category_name_slug . '.json';
            $data                    = json_decode(File::get($json_path));
            $selected_action         = $this->repository->getSelectedActionFromJson($data, $action_name);
            $excluded_properties     = Input::get("excluded_properties");

            $js_snipped_data = $this->repository->buildSnippedJSData($action_name, $selected_action, $data->common, $excluded_properties);
        }

        return Response::json([
                    "error" => false,
                    "data"  => [
                        "snipped"   => json_encode($js_snipped_data, JSON_PRETTY_PRINT),
                        "tenant_id" => $tenant_id
                    ]
        ]);
    }

    public function ajaxGetCheckIfActionImplemented($tenant_id, $action_name)
    {
        $action = Action::where('name', $action_name)->where('site_id', Session::get('active_site_id'))->first();

        if ($action) {
            $current_total_action_received = Action::getNumberOfTotalActionsOverallByActionId($action->id);
            $is_action_received            = ($current_total_action_received > 0) ? true : false;
        }
        else
            return Response::json([
                        'error'   => true,
                        'message' => "Action not found"
            ]);

        return Response::json([
                    'error' => false,
                    'data'  => [
                        'tenant_id'                     => $tenant_id,
                        'action_name'                   => $action_name,
                        'action_recieved'               => $is_action_received,
                        'current_total_action_received' => $current_total_action_received
                    ]
        ]);
    }

}

/* End of file Sites2Controller.php */
/* Location: ./application/controllers/Sites2Controller.php */
