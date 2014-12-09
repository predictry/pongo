<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Aug 14, 2014 4:21:58 PM
 * File         : app/controllers/Action2Controller.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     :
 */

namespace App\Controllers\Api;

use App\Controllers\ApiBaseController,
    Gui,
    Input,
    Response,
    Validator;

class Action3Controller extends ApiBaseController
{

    protected $curl        = null;
    protected $is_new_item, $is_new_visitor, $is_new_action, $is_new_session, $is_new_browser, $is_anonymous;
    protected $action_id, $item_id, $visitor_id, $action_instance_id, $session_id, $browser_id;
    protected $visitor_dt_created_timestamp, $action_dt_created_timestamp;
    protected $action_type = "single";
    protected $action_data = array();

    public function __construct()
    {
        parent::__construct();
        $this->is_new_item        = $this->is_new_visitor     = $this->is_new_action      = false;
        $this->is_new_session     = $this->is_new_browser     = $this->is_anonymous       = false;
        $this->action_id          = $this->item_id            = $this->visitor_id         = 0;
        $this->action_instance_id = $this->session_id         = $this->browser_id         = 0;

        $this->visitor_dt_created_timestamp = 0;
        $this->action_dt_created_timestamp  = 0;

        $this->gui_domain_auth = array(
//            'appid'  => $this->predictry_server_api_key,
            'appid'  => "pongo", //hardcoded in the moment
            'domain' => $this->predictry_server_tenant_id
        );

        Gui::setUri(GUI_RESTAPI_URL);
        Gui::setCredential(GUI_HTTP_USERNAME, GUI_HTTP_PASSWORD);
        Gui::setDomainAuth($this->gui_domain_auth['appid'], $this->gui_domain_auth['domain']);
    }

    public function index()
    {
        $name = Input::get("name");
        return "gotcha " . $name;
    }

    public function missingMethod($parameters = array())
    {
        return "Missing methods";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $action_validator = Validator::make(Input::only("action"), array("action" => "required"));

        if ($action_validator->passes()) {
            $browser_inputs = Input::only("session_id", "browser_id", "user_id");
            $browser_rules  = array(
                "session_id" => "required",
                "browser_id" => "",
                "user_id"    => ""
            );

            $rules           = array_merge($browser_rules, array('items' => 'array'));
            $inputs          = array_merge($browser_inputs, Input::only("action", "user", "items"));
            $input_validator = Validator::make($inputs, $rules);
            if ($input_validator->passes()) {

                $browser_validator = \Validator::make($browser_inputs, $browser_rules);
                if ($browser_validator->passes()) {
                    /**
                     * queue data
                     */
                    $data['browser_inputs'] = array_merge(['tenant_id' => $this->predictry_server_tenant_id, 'api_key' => $this->predictry_server_api_key], $browser_inputs);
                    $data['inputs']         = $inputs;
                    $data['site_id']        = $this->site_id;
                    \Queue::push('App\Pongo\Queues\SendAction@store', $data);
                }
                else {
                    $this->response = $this->getErrorResponse("", "200", "", $input_validator->errors()->first());
                }
            }
            else
                $this->response = $this->getErrorResponse("errorValidator", "200", "", $input_validator->errors()->first());
        }
        else
            $this->response = $this->getErrorResponse("errorValidator", "200", "", $action_validator->errors()->first());

        return Response::json($this->response, $this->http_status);
    }

}

/* End of file Action3Controller.php */
/* Location: ./application/controllers/Action3Controller.php */
