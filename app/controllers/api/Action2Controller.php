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
    App\Models\Item,
    App\Pongo\Repository\ActionRepository,
    Carbon\Carbon,
    Gui,
    Input,
    Response,
    Validator;

class Action2Controller extends ApiBaseController
{

    protected $curl              = null;
    protected $action_repository = null;
    protected $is_new_item, $is_new_visitor, $is_new_action, $is_new_session, $is_new_browser, $is_anonymous;
    protected $action_id, $item_id, $visitor_id, $action_instance_id, $session_id, $browser_id;
    protected $visitor_dt_created_timestamp, $action_dt_created_timestamp;
    protected $action_type       = "single";
    protected $action_data       = array();

    public function __construct(ActionRepository $repository)
    {
        parent::__construct();
        $this->action_repository  = $repository;
        $this->is_new_item        = $this->is_new_visitor     = $this->is_new_action      = false;
        $this->is_new_session     = $this->is_new_browser     = $this->is_anonymous       = false;
        $this->action_id          = $this->item_id            = $this->visitor_id         = 0;
        $this->action_instance_id = $this->session_id         = $this->browser_id         = 0;

        $this->visitor_dt_created_timestamp = 0;
        $this->action_dt_created_timestamp  = 0;

        $this->response = array(
            "error"          => false,
            "status"         => 200,
            "message"        => "",
            "client_message" => ""
        );

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
            try
            {
                $browser_inputs = Input::only("session_id", "browser_id", "user_id");
                $inputs         = array_merge($browser_inputs, Input::only("action", "user", "items"));
                $rules          = array(
                    "session_id" => "required",
                    "browser_id" => "",
                    "user_id"    => "",
                    "items"      => "array"
                );

                $input_validator = Validator::make($inputs, $rules);

                if ($input_validator->passes()) {

                    //validating user data
                    $user_property_rules = array("email" => (isset($inputs['user']['email']) && $inputs['user']['email'] !== "") ? "required|email" : "");

                    //if client didn't set the user object values, then replace it using generated uid
                    if (!isset($inputs['user'])) {
                        $inputs['user']     = array_add($inputs['user'], "user_id", $inputs['user_id']);
                        $this->is_anonymous = true;
                    }

                    $user_validator = Validator::make($inputs['user'], $user_property_rules);
                    if ($user_validator->passes()) {
                        $this->browser_id = $this->action_repository->getBrowserID($inputs['browser_id'], $this->is_new_browser); //get browser_id
                        //if user is anonymous, don't create visitor info (only for identified user)
                        if (!$this->is_anonymous) {

                            $this->visitor_id = $this->action_repository->getVisitorID($inputs['user'], $inputs['session_id']);
                            //none of the session that use any of the visitor_id then create new for that
                            //possibility, user anonymous and session expired.
                            if (!$this->visitor_id) {

                                $visitor = $this->action_repository->setVisitor($inputs['user']);
                                if ($visitor) {

                                    $this->is_new_visitor               = $this->visitor_id                   = $visitor->id;
                                    $dt_created                         = new Carbon($visitor->created_at);
                                    $this->visitor_dt_created_timestamp = $dt_created->timestamp;
                                }
                            }

                            $this->session_id = $this->action_repository->getSessionID($inputs['session_id'], $this->site_id, $this->browser_id, $this->visitor_id, $this->is_new_session); //get session_id
                        }
                        else
                            $this->session_id = $this->action_repository->getSessionID($inputs['session_id'], $this->site_id, $this->browser_id, null, $this->is_new_session); //get session_id

                        if ($inputs['action']['name'] === 'view') {
                            $this->_proceedViewAction($inputs);
                        }
                        else if ($inputs['action']['name'] === 'add_to_cart' || $inputs['action']['name'] === 'buy' || $inputs['action']['name'] === "started_checkout" || $inputs['action']['name'] === "started_payment") {
                            if ($this->_proceedBulkAction($inputs['action']['name'], $inputs))
                                $this->http_status = 200;
                        }
                        else
                            $this->_proceedCustomAction($inputs);
                    }
                    else
                        $this->response = $this->getErrorResponse("errorValidator", "200", "", $user_validator->errors()->first());
                }
                else
                    $this->response = $this->getErrorResponse("errorValidator", "200", "", $input_validator->errors()->first());
            }
            catch (Exception $ex)
            {
                \Log::error($ex->getMessage());
            }
        }
        else
            $this->response = $this->getErrorResponse("errorValidator", "200", "", $action_validator->errors()->first());

        return Response::json($this->response, $this->http_status);
    }

    function _proceedViewAction($inputs)
    {
        $items = $inputs['items'];
        foreach ($items as $item) {
            $inputs['item'] = $item;

            if ($this->_proceedSingleAction($inputs['action']['name'], $inputs))
                $this->_proceedToGui($item, $inputs['user'], $inputs['action']);
            else
                break;
        }
    }

    function _proceedCustomAction($inputs)
    {
        $items = $inputs['items'];
        if (count($items) > 0) {
            foreach ($items as $item) {
                $inputs['item'] = $item;
                if ($this->_proceedSingleAction($inputs['action']['name'], $inputs)) {
                    $this->_proceedToGui($item, $inputs['user'], $inputs['action']);
                }
                else
                    break;
            }
        }
        else {
            //chances if the actions only tracking
            if ($this->_proceedSingleAction($inputs['action']['name'], $inputs)) {
                $this->_proceedToGui([], $inputs['user'], $inputs['action']);
            }
        }
    }

    function _proceedSingleAction($action_name, $action_data)
    {
        $items_data = (isset($action_data['item'])) ? $action_data['item'] : [];

        $item_property_rules = array(
            "item_id" => "required|alpha_num"
        );

        if ($action_name === "view") {
            $item_property_rules = array_merge($item_property_rules, [
                "name"     => "required",
                "price"    => "required|numeric",
                "img_url"  => "required|url",
                "item_url" => "required|url"
            ]);
        }

        $item_validator = Validator::make($items_data, $item_property_rules);

        //validating the item first is important since, item will always associated with the action
        //item will no longer compulsary
        if ($item_validator->passes())
            $this->item_id = $this->action_repository->getItemID($items_data, $this->site_id, $this->is_new_item);

        $this->action_id = $this->action_repository->getActionID(array("name" => $action_name), $this->site_id, $this->is_new_action);
        $action_instance = $this->action_repository->getActionInstance($this->action_id, $this->item_id, $this->session_id);

        if (is_object($action_instance)) {
            $this->action_instance_id = $action_instance->id;
            $this->action_repository->setActionMeta($action_instance->id, $action_data['action']);
        }

        if ($this->response['error'])
            return false;

        $this->action_data = $action_data;
        return true;
    }

    function _proceedBulkAction($action_name, $action_data)
    {
        $items           = $action_data['items'];
        $this->action_id = $this->action_repository->getActionID(array("name" => $action_name), $this->site_id, $this->is_new_action);

        $action_properties_without_name = array_merge($action_data['action'], ['cart_id' => $this->action_repository->getCartID($this->session_id)]);
        unset($action_properties_without_name['name']);

        $i = 0;
        foreach ($items as $item) {
            $action_properties = array_merge($action_properties_without_name, $item);
            unset($action_properties['item_id']);

            $item_model = Item::where("identifier", $item['item_id'])->where("site_id", $this->site_id)->get()->first();
            if ($item_model) {
                $action_instance = $this->action_repository->getActionInstance($this->action_id, $this->item_id, $this->session_id);
                $this->item_id   = $item_model->id;
                if (is_object($action_instance)) {
                    $this->action_instance_id = $action_instance->id;
                    $this->action_repository->setActionMeta($action_instance->id, $action_properties);
                }
            }
            else {
                $this->response = $this->getErrorResponse("errorValidator", "200", "", "Item not found.");
                break;
            }

            $this->_proceedToGui($item, $action_data['user'], $action_data['action']);
            $i++;
        }

        if ($this->response['error'])
            return false;

        return true;
    }

    private function _proceedToGui($item_data, $user_data, $action_data)
    {
        if ($this->is_new_item)
            $this->_postItemToGui($item_data);

        if ($this->is_new_visitor) {
            $user_data = array_add($user_data, "timestamp", $this->visitor_dt_created_timestamp);
            $this->_postUserToGui($user_data);
        }

        $action_data = array_add($action_data, "item_id", $this->item_id);
        $action_data = array_add($action_data, "browser_id", $this->browser_id);
        $action_data = array_add($action_data, "session_id", $this->session_id);
        $this->_postAction($action_data);
    }

    private function _postItemToGui($item_data)
    {
        $result            = Gui::postItem($this->item_id, $item_data);
        $this->is_new_item = false;

        return ($result) ? $result : false;
    }

    private function _postUserToGui($user_data)
    {
        $result               = Gui::postUser($this->visitor_id, $user_data);
        $this->is_new_visitor = false;

        return ($result) ? $result : false;
    }

    private function _postAction($action_data)
    {
        $result              = Gui::postAction($this->action_instance_id, $action_data);
        $this->is_new_action = false;

        return ($result) ? $result : false;
    }

}

/* End of file Action2Controller.php */
/* Location: ./application/controllers/Action2Controller.php */
