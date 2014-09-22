<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 18, 2014 5:10:42 PM
 * File         : app/controllers/ActionsController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     :
 */

namespace App\Controllers\User;

use App\Controllers\BaseController,
    View,
    Input,
    Redirect,
    Validator,
    Paginator;

class ActionsController extends BaseController
{

    public $default_action_view_id = null;

    public function __construct()
    {
        parent::__construct();
        View::share(array(
            "ca"         => get_class(),
            "moduleName" => "actions",
            "view"       => false,
            "edit"       => false,
            "delete"     => false,
            "create"     => false,
            "isManage"   => false,
            "selector"   => true
        ));

        $this->default_action_view_id = \Session::get("default_action_view");
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $this->model = new \App\Models\ActionInstance();
        $page        = Input::get('page', 1);

        $action_type_dropdown   = \App\Models\Action::where("site_id", $this->active_site_id)->lists("name", "id");
        $available_site_actions = \App\Models\Action::where("site_id", $this->active_site_id)->get()->toArray();

        $available_site_action_ids   = array_fetch($available_site_actions, "id");
        $available_site_action_names = array_fetch($available_site_actions, "name");


        if ($this->default_action_view_id === null) { // if not set
            \Session::set("default_action_view", $available_site_action_ids[0]);
            $this->default_action_view_id = $available_site_action_ids[0]; // set the first action as default
        }

        $index_name  = array_search($this->default_action_view_id, $available_site_action_ids);
        $action_name = $available_site_action_names[$index_name];

        $data = $this->getByPage($page, $this->manageViewConfig['limit_per_page'], "action_id", $this->default_action_view_id);

        $message = '';

        if (!is_array($data) && !is_object($data)) {
            $message   = $data;
            $paginator = null;
        }
        else {
            foreach ($data->items as $item) {
                $item->name      = $action_name;
                $visitor_session = \App\Models\Session::find($item->session_id)->visitor;
                $obj_item        = \App\Models\Item::find($item->item_id);

                $item->user_id            = (isset($visitor_session->identifier)) ? $visitor_session->identifier : '-';
                $item->item_identifier_id = (isset($obj_item->identifier)) ? $obj_item->identifier : '-';
            }
            $paginator = Paginator::make($data->items, $data->totalItems, $data->limit);
        }

        $output = array(
            'paginator'     => $paginator,
            'str_message'   => $message,
            "pageTitle"     => "List Action ({$action_name})",
            "table_header"  => $this->model->manage_table_header,
            "page"          => $page,
            "selector_view" => "frontend.panels.actions.actionselector",
            "selector_vars" => array("dropdown" => $action_type_dropdown, "default_action_view_id" => $this->default_action_view_id)
        );
        return View::make("frontend.panels.manage", $output);
    }

    public function getCreate()
    {
        return View::make("frontend.panels.actions.form", array("type" => "create", 'pageTitle' => "Add New Action"));
    }

    public function postCreate()
    {
        $input            = Input::only("name", "score");
        $input['site_id'] = $this->active_site_id;

        $site      = new \App\Models\ActionType();
        $validator = Validator::make($input, $site->rules);

        if ($validator->passes()) {
            $siteAction = new \App\Models\SiteAction();
            $actionType = new \App\Models\ActionType();


            if ($id)
                return Redirect::route('sites')->with("flash_message", "Successfully added new action.");
            else
                return Redirect::back()->with("flash_error", "Inserting problem. Please try again.");
        }
        else
            return Redirect::back()->withInput()->withErrors($validator);
    }

    function postSelector()
    {
        $action_id                    = Input::get("action_id");
        \Session::set("default_action_view", $action_id);
        $this->default_action_view_id = $action_id;
        return Redirect::route("actions");
    }

}
