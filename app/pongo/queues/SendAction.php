<?php

namespace App\Pongo\Queues;

use App\Models\Item,
    App\Models\Site,
    App\Pongo\Repository\ActionRepository,
    Carbon\Carbon,
    Gui,
    Validator;

/**
 * Author       : Rifki Yandhi
 * Date Created : Nov 11, 2014 1:03:16 PM
 * File         : SendAction.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class SendAction
{

    protected $response          = array();
    protected $action_repository = null;
    protected $is_new_item, $is_new_visitor, $is_new_action, $is_new_session, $is_new_browser, $is_anonymous;
    protected $action_id, $item_id, $visitor_id, $action_instance_id, $session_id, $browser_id, $site_id;
    protected $visitor_dt_created_timestamp, $action_dt_created_timestamp;
    protected $action_type       = "single";
    protected $action_data       = array();
    protected $gui_domain_auth   = array();

    public function __construct(ActionRepository $repository)
    {
        $this->action_repository = $repository;

        $this->is_new_item    = $this->is_new_visitor = $this->is_new_action  = false;
        $this->is_new_session = $this->is_new_browser = $this->is_anonymous   = false;

        $this->action_id          = $this->item_id            = $this->visitor_id         = 0;
        $this->action_instance_id = $this->session_id         = $this->browser_id         = 0;

        $this->visitor_dt_created_timestamp = $this->action_dt_created_timestamp  = 0;

        $this->response = array(
            "error"          => false,
            "status"         => 200,
            "message"        => "",
            "client_message" => ""
        );
    }

    public function store($job, $data)
    {
        $this->response = array(
            "error"          => false,
            "status"         => 200,
            "message"        => "",
            "client_message" => ""
        );

        try
        {
            if (isset($data['site_id'])) {
                $this->site_id = $data['site_id'];
            }
            else {
                if (isset($data['browser_inputs']['tenant_id']) && isset($data['browser_inputs']['api_key'])) {
                    $site          = Site::where('name', $data['browser_inputs']['tenant_id'])->where('api_key', $data['browser_inputs']['api_key'])->first();
                    $this->site_id = ($site) ? $site->id : null;
                }
            }

            if (isset($this->site_id)) {
                $inputs = array_merge($data['browser_inputs'], $data['inputs']);
                $this->_initGui($data['browser_inputs']['tenant_id']);
                $this->_proceed($inputs);
                $job->delete();
            }
        }
        catch (Exception $ex)
        {
            \Log::error($ex->getMessage());
        }

        return;
    }

    function _initGui($tenant_id)
    {
        Gui::setUri($_ENV['TAPIRUS_API_URL']);
        Gui::setCredential($_ENV['TAPIRUS_HTTP_USERNAME'], $_ENV['TAPIRUS_HTTP_PASSWORD']);
        Gui::setDomainAuth("pongo", $tenant_id);
    }

    function _proceed($inputs)
    {
        //validating user data
        $user_property_rules = array(
            "email" => (isset($inputs['user']['email']) && $inputs['user']['email'] !== "") ? "required|email" : ""
        );

        //if client didn't set the user object values, then replace it using generated uid
        if (!isset($inputs['user']) || count($inputs['user']) <= 0) {
            $inputs['user']     = array_add($inputs['user'], "user_id", $inputs['user_id']);
            $this->is_anonymous = true;
        }
        else {

            if (!isset($inputs['user']['user_id'])) {
                $inputs['user']['user_id'] = isset($inputs['user_id']) ? $inputs['user_id'] : 0;
            }

            $this->is_anonymous = false;
        }

        $user_validator = Validator::make($inputs['user'], $user_property_rules);

        if ($user_validator->passes()) {
            try
            {
                $this->browser_id = $this->action_repository->getBrowserID($inputs['browser_id'], $this->is_new_browser); //get browser_id
                //if user is anonymous, don't create visitor info (only for identified user)
                if (!$this->is_anonymous) {
                    $this->visitor_id = $this->action_repository->getVisitorID($inputs['user'], $inputs['session_id'], $this->site_id);
                    //possibility, user anonymous and session expired.
                    if (!$this->visitor_id) {

                        $visitor = $this->action_repository->setVisitor($inputs['user']);
                        if ($visitor) {

                            $this->is_new_visitor               = $this->visitor_id                   = $visitor->id;
                            $dt_created                         = new Carbon($visitor->created_at);
                            $this->visitor_dt_created_timestamp = $dt_created->timestamp;
                        }
                        else
                            return;
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
                else {
                    $this->_proceedCustomAction($inputs);
                }
            }
            catch (Exception $ex)
            {
                \Log::error($ex->getMessage());
            }
        }

        \DB::reconnect();
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
        if ($this->action_id) {

            $log_dt_created  = $this->action_repository->isLogContainDateTime($action_data);
            $action_instance = $this->action_repository->getActionInstance($this->action_id, $this->item_id, $this->session_id, ($log_dt_created) ? $log_dt_created : false);

            if (is_object($action_instance)) {
                $this->action_instance_id = $action_instance->id;
                $this->action_repository->setActionMeta($action_instance->id, $action_data['action']);
            }

            if ($this->response['error'])
                return false;

            $this->action_data = $action_data;
            return true;
        }

        return false;
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

            $item_model = Item::firstOrCreate([
                        'identifier' => $item['item_id'],
                        'site_id'    => $this->site_id
            ]);

            //what if the item not found?
            if ($item_model) {
                $this->item_id   = $item_model->id;
                $log_dt_created  = $this->action_repository->isLogContainDateTime($action_data);
                $action_instance = $this->action_repository->getActionInstance($this->action_id, $this->item_id, $this->session_id, ($log_dt_created) ? $log_dt_created : false);
                if (is_object($action_instance)) {
                    $this->action_instance_id = $action_instance->id;
                    $this->action_repository->setActionMeta($action_instance->id, $action_properties);
                }
            }
            else {
                $this->response['error']   = true;
                $this->response['message'] = "Item not found";
                \Log::alert("_proceedBulkAction {$action_name}", $this->response);
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

/* End of file Action.php */