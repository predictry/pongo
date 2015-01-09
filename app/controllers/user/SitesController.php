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
    App\Models\Account,
    App\Models\Action,
    App\Models\ActionInstance,
    App\Models\ActionMeta,
    App\Models\FunelPreference,
    App\Models\Item,
    App\Models\Member,
    App\Models\Site,
    App\Models\SiteMember,
    Auth,
    Input,
    Paginator,
    Redirect,
    Request,
    Response,
    Session,
    URL,
    Validator,
    View;

class SitesController extends BaseController
{

    public function __construct()
    {
        parent::__construct();

        View::share(array("ca" => get_class(), "moduleName" => "Site", "view" => false, "custom_action" => "true", "delete" => false));

        if (Auth::user()->plan_id === 3) { //redmart
            View::share(array("create" => false, "edit" => false));
        }
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

            $site->name       = $input['name'];
            $site->api_key    = md5($input['url']);
            $site->api_secret = md5($input['url'] . $salt);
            $site->account_id = $input['account_id'];
            $site->url        = $input['url'];
            $id               = $site->save();

            //can be migrate to table
            $default_actions = array(
                "view"              => array("score" => 1),
                "rate"              => array("score" => 2),
                "add_to_cart"       => array("score" => 3),
                "buy"               => array("score" => 4),
                "started_checkout"  => array("score" => 5),
                "started_payment"   => array("score" => 6),
                "complete_purchase" => array("score" => 7)
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

            $default_funel_preference             = new FunelPreference();
            $default_funel_preference->site_id    = $site->id;
            $default_funel_preference->name       = "Default";
            $default_funel_preference->is_default = true;
            $default_funel_preference->save();

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
                    return \Redirect::route('sites')->with("flash_message", "Successfully added new site.");
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
            $site_members = SiteMember::where("site_id", $id)->get();
            foreach ($site_members as $site_member) {
                $account = Member::find($site_member->member_id);
                $account->delete();
                Account::find($account->account_id)->delete();
            }
            SiteMember::where("site_id", $id)->delete();

            //REMOVE ACTIONS
            $actions = Action::where("site_id", $id)->get();
            foreach ($actions as $action) {
                ActionInstance::where("action_id", $action->id)->delete();
                ActionMeta::where("action_id", $action->id)->delete();
            }

            Action::where("site_id", $id)->delete();

            //REMOVE ITEMS
            Item::where("site_id", $id)->delete();

            //REMOVE SESSIONS
            \App\Models\Session::where("site_id", $id)->delete();

            Site::find($id)->delete();

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

}
