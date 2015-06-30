<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 18, 2014 5:10:42 PM
 * File         : app/controllers/SitesController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     :
 */

namespace App\Controllers\User;

use App\Controllers\BaseController,
    App\Models\AccountMeta,
    App\Models\Action,
    App\Models\ActionMeta,
    App\Models\Industry,
    App\Models\Item,
    App\Models\Site,
    App\Models\SiteBusiness,
    App\Models\SiteCategory,
    App\Pongo\Repository\SiteRepository,
    Auth,
    Event,
    File,
    HTML,
    Input,
    Paginator,
    Redirect,
    Request,
    Response,
    Session,
    Str,
    URL,
    Validator,
    View;

class SitesController extends BaseController
{

    private $repository;

    public function __construct(SiteRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
        $scripts          = ['data_collection.js' => HTML::script('assets/js/data_collection.js')];
        View::share(array("ca" => get_class(), "moduleName" => "Site", "view" => true, "edit" => false, "custom_action" => "true", "delete" => false, "scripts" => $scripts));
    }

    /**
     * Display a listing of the resource.
     *
     */
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
            "pageTitle"          => "Manage Members",
            "table_header"       => $this->model->manage_table_header,
            "page"               => $page,
            "custom_action_view" => "frontend.panels.sites.customactionview"
        );

        return View::make("frontend.panels.manage", $output);
    }

    public function getCreate()
    {
        return View::make("frontend.panels.sites.form", array("type" => "create", 'pageTitle' => "Add New Site"));
    }

    public function postCreate()
    {
        $input               = Input::only("name", "url");
        $input['account_id'] = Auth::user()->id;

        $site      = new Site();
        $validator = Validator::make($input, $site->rules, array("regex" => "The name format should start with character. Ex. ABC123"));

        if ($validator->passes()) {

            $salt = uniqid(mt_rand(), true);

            $site->name             = $input['name'];
            $site->api_key          = md5($input['url']);
            $site->api_secret       = md5($input['url'] . $salt);
            $site->account_id       = $input['account_id'];
            $site->url              = $input['url'];
            $site->site_category_id = SiteCategory::first()->id;
            $id                     = $site->save();

            Event::fire("site.set_default_actions", array($site));
            Event::fire("site.set_default_funnel_preferences", array($site));

            if (Request::ajax()) {
                if ($id)
                    return Response::json(array("status" => "success", "response" => "/home"));
                else
                    return Response::json(
                                    array("status"   => "error",
                                        "response" => \View::make("frontend.panels.sites.addform", array(
                                            "flash_error" => "Inserting problem. Please check your inputs."
                                        ))->render()));
            }else {

                if ($id)
                    return \Redirect::to("v2/sites/{$site->name}/integration")->with("flash_message", "Successfully added new site.");
                else
                    return \Redirect::back()->with("flash_error", "Inserting problem. Please try again.");
            }
        }
        else {
            if (Request::ajax()) {
                return Response::json(
                                array("status"   => "error",
                                    "response" => \View::make("frontend.panels.sites.addform", array(
                                        "flash_error" => "Inserting problem. Please check your inputs."
                                    ))->withInput($input)->withErrors($validator)->render()));
            }
            else
                return \Redirect::back()->withInput()->withErrors($validator);
        }
    }

    /**
     * 
     * @param int $id
     * @return view
     */
    public function getEdit($id)
    {
        $site = Site::find($id);
        return \View::make("frontend.panels.sites.form", array("site" => $site, "type" => "edit", 'pageTitle' => "Edit Site"));
    }

    public function postEdit($id)
    {
        $site      = Site::find($id);
        $input     = Input::only("name", "url");
        $validator = Validator::make($input, $site->rules);
        if ($validator->passes()) {
            $site->name = $input['name'];
            $site->url  = $input['url'];
            $site->update();

            return Redirect::back()->with("flash_message", "Data successfully updated.");
        }
        else {
            return Redirect::back()->withInput()->withErrors($validator);
        }
    }

    public function postDelete($id)
    {
        $exists_and_belongs_to_him = Site::where("id", $id)->where("account_id", \Auth::user()->id)->first();

        if ($exists_and_belongs_to_him) {

            //REMOVE SITE_MEMBERS
            $this->repository->removeSiteMembers($id);

            //REMOVE ACTIONS
            $this->repository->removeActions($id);

            //REMOVE ITEMS
            Item::where("site_id", $id)->delete();

            //REMOVE SESSIONS
            Session::where("site_id", $id)->delete();

            //FINALLY REMOVE SITE
            Site::find($id)->delete();

            //REMOVE ACTIVE_SITE_SESSIONS
            Session::remove("active_site_id");
            Session::remove("active_site_name");

            return Redirect::back()->with("flash_message", "Site data has been removed.");
        }
    }

    public function getDefault($id)
    {
        $site = Site::where("id", $id)->where("account_id", Auth::user()->id)->first();
        if ($site->id) {
            Session::set("active_site_id", $site->id);
            Session::set("active_site_name", $site->name);
            Session::remove("default_action_view");
        }
        return Redirect::route('home');
    }

    public function getSiteWizard()
    {
        $custom_script = "<script type='text/javascript'>";
        $custom_script .= "var site_url = '" . URL::to('/') . "';";
        $custom_script .= "</script>";

        $output = array(
            "pageTitle"     => "Welcome to predictry. Create your first site.",
            "modalTitle"    => "Add New Site",
            "custom_script" => $custom_script,
            "sites"         => array()
        );

        return \View::make('frontend.panels.sites.addwell', $output);
    }

    public function getModalCreate()
    {
        return View::make("frontend.panels.sites.addform");
    }

    public function postAjaxCreate()
    {
        $input               = Input::only("name", "url");
        $input['account_id'] = Auth::user()->id;

        $site      = new Site();
        $validator = Validator::make($input, $site->rules);

        if ($validator->passes()) {
            $salt = uniqid(mt_rand(), true);

            $site->name       = $input['name'];
            $site->api_key    = md5($input['url']);
            $site->api_secret = md5($input['url'] . $salt);
            $site->account_id = $input['account_id'];
            $site->url        = $input['url'];
            $id               = $site->save();

            //can be migrate to table
            $default_actions = array(
                "view"        => array("score" => 1),
                "rate"        => array("score" => 2),
                "add_to_cart" => array("score" => 3),
                "buy"         => array("score" => 4)
            );

            //set default action types for the site
            foreach ($default_actions as $key => $arr) {
                $action              = new Action();
                $action->name        = $key;
                $action->description = null;
                $action->site_id     = $site->id;
                $action->save();

                foreach ($arr as $key2 => $val) {
                    $action_meta            = new ActionMeta();
                    $action_meta->key       = $key2;
                    $action_meta->value     = $val;
                    $action_meta->action_id = $action->id;
                    $action_meta->save();
                }
            }

            if ($id)
                return Response::json(array("status" => "success"));
            else
                return Response::json(
                                array("status"   => "error",
                                    "response" => \View::make("frontend.panels.sites.addform", array(
                                        "flash_error" => "Inserting problem. Please check your inputs."
                                    ))->render()));
        }
        else
            return Response::json(
                            array("status"   => "error",
                                "response" => \View::make("frontend.panels.sites.addform", array(
                                    "flash_error" => "Inserting problem. Please check your inputs."
                                ))->withInput($input)->withErrors($validator)->render()));
    }

    /**
     * Update Business Detail
     * 
     * @param type $tenant_id
     * @return object
     */
    public function getBusiness($tenant_id)
    {
        if (is_null($tenant_id)) {
            return \Redirect::to('sites');
        }

        $site          = Site::where('name', $tenant_id)->where('account_id', \Auth::user()->id)->first();
        $site_category = ($site) ? SiteCategory::find($site->site_category_id)->first() : null;

        if (is_null($site_category))
            return \Redirect::to('sites');

        $industries = Industry::all()->lists("name", "id");
        $output     = [
            'industries'                     => $industries,
            'selected_industry_id'           => 1,
            'range_number_of_users'          => ['0_to_1k' => '0 to 1k', '0_to_10k' => '0 to 10k', '0_to_100k' => '0 to 100k', '0_to_1M' => '0 to 1M'],
            'selected_range_number_of_users' => '0_to_1k',
            'range_number_of_items'          => ['0_to_100' => '0 to 100', '0_to_500' => '0 to 500', '0_to_1k' => '0 to 1k', '0_to_10k' => '0 to 10k'],
            'selected_range_number_of_items' => '0_to_100',
            'site'                           => $site,
            'site_category'                  => $site_category
        ];

        return View::make('frontend.panels.sites.business', $output);
    }

    /**
     * 
     * @param string $tenant_id
     * @return object
     */
    public function postBusiness($tenant_id)
    {
        if (is_null($tenant_id)) {
            return \Redirect::to('sites');
        }

        $site_business = new SiteBusiness();
        $validator     = Validator::make(\Input::all(), $site_business->rules);

        if ($validator->passes()) {

            $input           = \Input::all();
            $site_repository = new SiteRepository();

            if ($site_repository->isBelongToHim($tenant_id)) {

                $site = Site::where('name', $tenant_id)->first();

                if ($site->url !== $input['url']) {

                    $url_validator = $site_repository->validateUniqueUrl($input['url']);
                    if (is_bool($url_validator)) {
                        $site->url = $input['url'];
                        $site->update();
                    }
                    else
                        return \Redirect::back()->withInput()->withErrors($url_validator);
                }

                $site_business = SiteBusiness::firstOrCreate([
                            'name'                  => $input['name'],
                            'site_id'               => $site->id,
                            'range_number_of_users' => isset($input['range_number_of_users']) ? $input['range_number_of_users'] : '',
                            'range_number_of_items' => isset($input['range_number_of_items']) ? $input['range_number_of_items'] : '',
                            'industry_id'           => $input['industry_id']
                ]);
                return \Redirect::to("sites/{$site->name}/integration")->with('flash_message', 'Site business has been updated.');
            }
            else
                return \Redirect::to("sites");
        }

        return \Redirect::back()->withInput()->withErrors($validator);
    }

    public function getImplementationWizard($tenant_id)
    {
        //@TODO - CREATE WIZARD VIEW FOR DATA COLLECTION STEPS
        $validator = Validator::make(['name' => $tenant_id], ['name' => 'required|exists:sites,name']);
        $site      = $this->repository->isBelongToHim($tenant_id);
        if ($validator->passes() && $site) {

            $reco_js_url = asset('reco.js');
            $reco_js_url = str_replace("https", "", $reco_js_url);
            $reco_js_url = str_replace("http", "", $reco_js_url);

            $site_category = Site::find(Session::get('active_site_id'))->siteCategory()->first();
            if ($site_category) {
                $site_category_name_slug = Str::slug($site_category->name, '_');
                $json_path               = public_path() . '/data/' . $site_category_name_slug . '.json';
                $data                    = json_decode(File::get($json_path));

                $custom_script = "<script type='text/javascript'>";
                $custom_script .= "var site_url = '" . URL::to('/') . "';";
                $custom_script .= "</script>";
            }

            $output = [
                'reco_js_url'   => $reco_js_url,
                'site'          => $site,
                'data'          => $data,
                'tenant_id'     => $tenant_id,
                'custom_script' => $custom_script,
                'pageTitle'     => "Implementation Wizard"
            ];
            return View::make('frontend.panels.sites.implementation_wizard', $output);
        }

        return \Redirect::to('sites')->with('flash_error', $validator->messages()->first());
    }

    public function ajaxPostImplementationWizard($tenant_id)
    {
        $action_names        = \Input::get("action_names");
        $excluded_properties = \Input::get("excluded_properties");

        if (is_array($action_names)) {
            foreach ($action_names as $action_name) {
                $action = Action::where('name', $action_name)->where('site_id', Session::get('active_site_id'))->first();
                if ($action) {
                    $action_meta = ActionMeta::firstOrNew(['key' => 'excluded_properties', 'action_id' => $action->id]);
                    if (isset($excluded_properties[$action_name]) && is_array(($excluded_properties[$action_name]))) {
                        $action_meta->value = json_encode($excluded_properties[$action_name]);
                        $action_meta->save();
                    }
                    else {
                        $action_meta->value = json_encode(array());
                        $action_meta->update();
                    }


                    \Session::remove('is_new_account');
                    $this->is_new_account = true;

                    $is_new_account_meta = AccountMeta::where('account_id', \Auth::user()->id)->where('key', 'is_new_account')->first();
                    if ($is_new_account_meta) {
                        $is_new_account_meta->value = false;
                        $is_new_account_meta->update();
                    }
                }
            }
        }
        return \Response::json([
                    "error" => false,
                    "data"  => [
                        'redirect' => url("sites")
                    ]
        ]);
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

                $custom_script = "<script type='text/javascript'>";
                $custom_script .= "var site_url = '" . URL::to('/') . "';";
                $custom_script .= "</script>";

                $output = [
                    'data'          => $data,
                    'tenant_id'     => $tenant_id,
                    'custom_script' => $custom_script
                ];

                return View::make('frontend.panels.sites.data_collection', $output);
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

        return \Response::json([
                    "error"    => false,
                    "data"     => [
                        "action" => $selected_action
                    ],
                    "response" => View::make("frontend.panels.sites.list_action_properties", ['action' => $selected_action, "tenant_id" => $tenant_id, "action_name" => $action_name, "excluded_properties" => ($excluded_properties) ? json_decode($excluded_properties->value) : []])->render()
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
            $excluded_properties     = \Input::get("excluded_properties");

            $js_snipped_data = $this->repository->buildSnippedJSData($action_name, $selected_action, $data->common, $excluded_properties);
        }

        return \Response::json([
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
            return \Response::json([
                        'error'   => true,
                        'message' => "Action not found"
            ]);

        return \Response::json([
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
