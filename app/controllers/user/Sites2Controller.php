<?php

namespace App\Controllers\User;

use App\Controllers\BaseController,
    App\Models\Site,
    Auth,
    Input,
    Paginator,
    Redirect,
    Session,
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

    function __construct()
    {
        parent::__construct();
        View::share(array("ca" => get_class(), "moduleName" => "Site", "view" => false, "custom_action" => "true", "delete" => false));

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
            "pageTitle"          => "Manage Members",
            "table_header"       => $this->model->manage_table_header,
            "page"               => $page,
            "custom_action_view" => getenv('FRONTEND_SKINS') . $this->theme . ".panels.sites.customactionview"
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
        return \View::make(getenv('FRONTEND_SKINS') . $this->theme . ".panels.sites.form", array("site" => $site, "type" => "edit", 'pageTitle' => "Edit Site"));
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

}

/* End of file Sites2Controller.php */
/* Location: ./application/controllers/Sites2Controller.php */
