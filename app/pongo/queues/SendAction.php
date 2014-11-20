<?php

namespace App\Pongo\Queues;

use App\Models\Action,
    App\Models\ActionInstance,
    App\Models\ActionInstanceMeta,
    App\Models\ActionMeta,
    App\Models\Browser,
    App\Models\BrowserSession,
    App\Models\Item,
    App\Models\ItemMeta,
    App\Models\Session,
    App\Models\Visitor,
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

    protected $response    = array();
    protected $curl        = null;
    protected $is_new_item, $is_new_visitor, $is_new_action, $is_new_session, $is_new_browser, $is_anonymous;
    protected $action_id, $item_id, $visitor_id, $action_instance_id, $session_id, $browser_id, $site_id;
    protected $visitor_dt_created_timestamp, $action_dt_created_timestamp;
    protected $action_type = "single";
    protected $action_data = array();

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
                    $site          = \App\Models\Site::where('name', $data['browser_inputs']['tenant_id'])->where('api_key', $data['browser_inputs']['api_key'])->first();
                    $this->site_id = ($site) ? $site->id : null;
                }
            }

            if (isset($this->site_id)) {
                $inputs = array_merge($data['browser_inputs'], $data['inputs']);
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

    function _proceed($inputs)
    {
        //validating user data
        $user_property_rules = array(
            "email" => (isset($inputs['user']['email']) && $inputs['user']['email'] !== "") ? "required|email" : ""
        );

        //if client didn't set the user object values, then replace it using generated uid
        if (!isset($inputs['user'])) {
            $inputs['user']     = array_add($inputs['user'], "user_id", $inputs['user_id']);
            $this->is_anonymous = true;
        }

        $user_validator = Validator::make($inputs['user'], $user_property_rules);

        if ($user_validator->passes()) {
            $this->browser_id = $this->_getBrowserID($inputs['browser_id']); //get browser_id
            //if user is anonymous, don't create visitor info (only for identified user)
            if (!$this->is_anonymous) {
                $this->visitor_id = $this->_getVisitorID($inputs['user'], $inputs['session_id']);
                $this->session_id = $this->_getSessionID($inputs['session_id'], $this->visitor_id); //get session_id
            }
            else
                $this->session_id = $this->_getSessionID($inputs['session_id']); //get session_id

            if ($inputs['action']['name'] === 'view') {
                $items = $inputs['items'];
                foreach ($items as $item) {
                    $inputs['item'] = $item;

                    if ($this->_proceedSingleAction($inputs['action']['name'], $inputs))
                        $this->_proceedToGui($item, $inputs['user'], $inputs['action']);
                    else
                        break;
                }

                $response = $this->response;
            }
            else if ($inputs['action']['name'] === 'buy' || $inputs['action']['name'] === "started_checkout" || $inputs['action']['name'] === "started_payment") {
                if ($this->_proceedBulkAction($inputs['action']['name'], $inputs))
                    $this->http_status = 200;

                $response = $this->response;
            }
            else {
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

                $response = $this->response;
            }
        }

        \DB::reconnect();
    }

    function _proceedSingleAction($action_name, $action_data)
    {
        $items_data = (isset($action_data['item'])) ? $action_data['item'] : [];

        $item_property_rules = array(
            "item_id"  => "required|alpha_num",
            "name"     => "required",
            "price"    => "required|numeric",
            "img_url"  => "required|url",
            "item_url" => "required|url"
        );

        $item_validator = Validator::make($items_data, $item_property_rules);

        //validating the item first is important since, item will always associated with the action
        //item will no longer compulsary
        if ($item_validator->passes())
            $this->item_id = $this->_getItemID($items_data, $this->is_new_item);

        $this->action_id = $this->_getActionID(array("name" => $action_name), $this->is_new_action);
        $action_instance = new ActionInstance();

        //process action instance
        $action_instance->action_id  = $this->action_id;
        $action_instance->item_id    = ($this->item_id !== 0) ? $this->item_id : null;
        $action_instance->session_id = $this->session_id;
        $action_instance->created    = new Carbon("now");
        $action_instance->save();

        $this->action_instance_id = $action_instance->id;
        $this->_setActionMeta($action_instance->id, $action_data['action']);

        if ($this->response['error'])
            return false;


        $this->action_data = $action_data;
        return true;
    }

    function _proceedBulkAction($action_name, $action_data)
    {
        $items           = $action_data['items'];
        $this->action_id = $this->_getActionID(array("name" => $action_name), $this->is_new_action);

        $action_properties_without_name = array_merge($action_data['action'], ['cart_id', $this->_getCartID($this->session_id)]);
        unset($action_properties_without_name['name']);

        $i = 0;
        foreach ($items as $item) {
            $action_properties = array_merge($action_properties_without_name, $item);
            unset($action_properties['item_id']);

            $item_model = Item::where("identifier", $item['item_id'])->where("site_id", $this->site_id)->get()->first();

            if ($item_model) {
                $action_instance = new ActionInstance();

                //process action instance
                $action_instance->action_id  = $this->action_id;
                $action_instance->item_id    = $item['item_id'];
                $action_instance->session_id = $this->session_id;
                $action_instance->created    = new Carbon("now");
                $action_instance->save();

                $this->item_id            = $item_model->id;
                $this->action_instance_id = $action_instance->id;

                $this->_setActionMeta($action_instance->id, $action_properties);
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

    function _getActionID($action_data)
    {
        $action = Action::where("name", $action_data['name'])->where("site_id", $this->site_id)->first();
        if (!$action) {
            $action_id           = $this->_setAction($action_data);
            $this->is_new_action = true;
        }
        else
            $action_id = $action->id;

        return $action_id;
    }

    function _setAction($action_data)
    {
        $action              = new Action();
        $action->name        = $action_data['name'];
        $action->description = (isset($action_data['description'])) ? $action_data['description'] : '';
        $action->site_id     = $this->site_id;
        $action->save();

        if (isset($action_data['score']) && $action->id) {
            $action_meta            = new ActionMeta();
            $action_meta->key       = "score";
            $action_meta->value     = $action_data['score'];
            $action_meta->action_id = $action->id;
            $action_meta->save();
        }
        return $action->id;
    }

    function _setActionMeta($action_instance_id, $properties)
    {
        if (is_array($properties) && count($properties) > 0) {
            foreach ($properties as $key => $val) {
                if ($val !== "" && (is_array($val) && count($val) > 0 )) {
                    $action_instance_meta                     = new ActionInstanceMeta();
                    $action_instance_meta->key                = $key;
                    $action_instance_meta->value              = is_array($val) ? json_encode($val) : $val;
                    $action_instance_meta->action_instance_id = $action_instance_id;
                    $action_instance_meta->save();
                }
            }
        }
    }

    function _setBrowseSession($browser_id, $session_id)
    {
        $browser_session = BrowserSession::firstOrCreate(array("browser_id" => $browser_id, "session_id" => $session_id));
        return $browser_session->id;
    }

    function _setVisitor($visitor_data)
    {
        $visitor             = new Visitor();
        $visitor->identifier = $visitor_data['user_id'];
        $visitor->email      = $visitor_data['email'];
        $visitor->save();

        $this->is_new_visitor = true;
        $this->visitor_id     = $visitor->id;

        $dt_created                         = new Carbon($visitor->created_at);
        $this->visitor_dt_created_timestamp = $dt_created->timestamp;
        return $visitor->id;
    }

    function _getVisitorID($user_data, $session)
    {
        //since the email is optional. Please assign empty if not set
        $visitor_id = false;
        $user_data  = array_add($user_data, "email", isset($user_data['email']) ? $user_data['email'] : "" );
        $session    = Session::firstOrNew(array("session" => $session));

        if ($session) { // if session exists, update visitor data
            $visitor_id = ($session->visitor_id !== null) ? $session->visitor_id : false;
            if ($visitor_id) {
                $visitor             = Visitor::find($visitor_id);
                $visitor->identifier = $user_data['user_id'];
                $visitor->email      = $user_data['email'];
                $visitor->update();
            }
        }

        //get the visitors list that have the same email
        if (!$visitor_id && isset($user_data['email']) && $user_data['email'] !== "") {
            $visitors = Visitor::where("email", $user_data['email'])->get()->toArray();
            //check if one of the visitor used by one of the session from the specific site.
            foreach ($visitors as $v) {
                $session = Session::where("site_id", $this->site_id)->where("visitor_id", $v['id'])->get()->first();
                if ($session) {
                    $visitor_id = $v['id'];
                    break;
                }
            }
        }

        //if one of the session use the visitor_id. Then we need to update that visitor info
        //possibility, user logged in after browsing as an anonymous.
        if ($visitor_id && $visitor_id !== null) {
            $visitor             = Visitor::find($visitor_id);
            $visitor->identifier = $user_data['user_id'];
            $visitor->email      = $user_data['email'];
            $visitor->update();
        }

        //none of the session that use any of the visitor_id then create new for that
        //possibility, user anonymous and session expired.
        if (!$visitor_id)
            $visitor_id = $this->_setVisitor($user_data);

        return $visitor_id;
    }

    function _getItemID($item_data)
    {
        $item = Item::where("identifier", $item_data['item_id'])->where("site_id", $this->site_id)->first();

        if ($item) {
            if (isset($item_data['name']) && $item_data['name'] !== "" && ($item->name !== $item_data['name'])) {
                $item->name = $item_data['name'];
                $item->update();
            }

            $properties_keys = array_keys($item_data);
            unset($properties_keys['item_id']);

            $item_metas = ItemMeta::where("item_id", $item->id)->get();

            //update item properties
            foreach ($item_metas as $meta) {
                if (in_array($meta->key, $properties_keys)) {
                    $value = $item_data[$meta->key];
                    if (is_array($value))
                        $value = json_encode($item_data[$meta->key]);

                    if ($meta->value !== trim($value)) {
                        $meta->value = trim($value);
                        $meta->update();
                    }
                    $index = array_search($meta->key, $properties_keys);
                    unset($properties_keys[$index]);
                }
            }

            if (count($properties_keys) > 0) {//means have new additional properties
                foreach ($properties_keys as $key) {
                    $item_meta          = new ItemMeta();
                    $item_meta->key     = $key;
                    $item_meta->value   = is_array($item_data[$key]) ? json_encode($item_data[$key]) : $item_data[$key];
                    $item_meta->item_id = $item->id;
                    $item_meta->save();
                }
            }

            return $item->id;
        }
        else {
            $item             = new Item();
            $item->identifier = $item_data['item_id'];
            $item->name       = $item_data['name'];
            $item->site_id    = $this->site_id;
            $item->save();

            if ($item->id) {
                foreach ($item_data as $key => $value) {
                    $item_meta          = new ItemMeta();
                    $item_meta->item_id = $item->id;
                    $item_meta->key     = $key;

                    if (is_array($value))
                        $value = json_encode($value);

                    $item_meta->value = $value;
                    $item_meta->save();
                }

                $this->is_new_item = true;
                $this->item_id     = $item->id;

                $this->is_new_item = true;
                return $item->id;
            }
            else
                return false;
        }
    }

    function _getSessionID($session, $visitor_id = null)
    {
        $session_visitor_id = 0;
        //start processing visitor
        $visitor_session    = Session::firstOrNew(array("session" => $session, "site_id" => $this->site_id, "visitor_id" => $visitor_id));

        if (!isset($visitor_session->id)) {
            $visitor_session->save();

            $this->is_new_session = true;
            $this->session_id     = $session_visitor_id   = $visitor_session->id;
            $this->_setBrowseSession($this->browser_id, $visitor_session->id); //set browser session
        }
        else {
            if ($visitor_id !== null) {
                $visitor_session->visitor_id = $visitor_id;
                $visitor_session->update();
            }

            $session_visitor_id = $visitor_session->id;
        }

        return $session_visitor_id;
    }

    function _getBrowserID($browser_id)
    {
        $browser = Browser::firstOrNew(array("identifier" => $browser_id));
        if (!isset($browser->id)) {
            $browser->save();
            $this->is_new_browser = true;
        }
        $this->browser_id = $browser->id;
        return $browser->id;
    }

    function _getCartID($session_id)
    {
        $cart_id           = -1;
        $cart              = \App\Models\Cart::where("session_id", $session_id)->get()->last();
        $count_used_before = \App\Models\ActionInstanceMeta::where("key", "cart_id")->where("value", $cart->id)->get()->count();

        if ($count_used_before > 0) {
            $new_cart = \App\Models\Cart::create(['session_id' => $session_id]);
            $cart_id  = ($new_cart->id) ? $new_cart->id : -1;
        }
        else
            $cart_id = $cart->id;

        return $cart_id;
    }

    private function _translateActionToRating($str_action)
    {
        $actions = array(
            "view"        => 1,
            "rate"        => 2,
            "add_to_cart" => 3,
            "buy"         => 4
        );

        if (key_exists($str_action, $actions)) {
            return $actions[$str_action];
        }
        else
            return false;
    }

    private function _getBuyAlias($action_name)
    {
        if ($action_name === 'complete_purchase')
            return 'buy';

        return $action_name;
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