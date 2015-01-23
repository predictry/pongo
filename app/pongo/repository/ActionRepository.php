<?php

namespace App\Pongo\Repository;

use App\Models\Action,
    App\Models\ActionInstance,
    App\Models\ActionInstanceMeta,
    App\Models\ActionMeta,
    App\Models\Browser,
    App\Models\BrowserSession,
    App\Models\Cart,
    App\Models\Item,
    App\Models\ItemMeta,
    App\Models\Session,
    App\Models\Visitor,
    Carbon\Carbon;

/**
 * Author       : Rifki Yandhi
 * Date Created : Aug 27, 2014 10:21:49 AM
 * File         : app/controllers/ActionRepository.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class ActionRepository
{

    /**
     * Persist an action.
     * 
     * @param Action $action
     * @return type
     */
    public function save(Action $action)
    {
        return $action->save();
    }

    function setVisitor($visitor_data)
    {
        $visitor             = new Visitor();
        $visitor->identifier = $visitor_data['user_id'];
        $visitor->email      = isset($visitor_data['email']) ? $visitor_data['email'] : '';
        $visitor->save();

        return $visitor;
    }

    function setBrowseSession($browser_id, $session_id)
    {
        $browser_session = BrowserSession::firstOrCreate(array("browser_id" => $browser_id, "session_id" => $session_id));
        return $browser_session->id;
    }

    function setAction($action_data, $site_id)
    {
        $action              = new Action();
        $action->name        = $action_data['name'];
        $action->description = (isset($action_data['description'])) ? $action_data['description'] : '';
        $action->site_id     = $site_id;
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

    function setActionMeta($action_instance_id, $properties)
    {
        $action_instance_meta_data = [];
        if (is_array($properties) && count($properties) > 0) {
            foreach ($properties as $key => $val) {
                if ($val !== "" || (is_array($val) && count($val) > 0 )) {
                    $new_meta = [
                        'key'                => $key,
                        'value'              => is_array($val) ? json_encode($val) : $val,
                        'action_instance_id' => $action_instance_id
                    ];
                    array_push($action_instance_meta_data, $new_meta);
                }
            }

            if (count($action_instance_meta_data) > 0)
                ActionInstanceMeta::insert($action_instance_meta_data);
        }
    }

    function getActionInstance($action_id, $item_id, $session_id, $created = false)
    {
        $action_instance             = new ActionInstance();
        $action_instance->action_id  = $action_id;
        $action_instance->session_id = $session_id;
        $action_instance->item_id    = isset($item_id) ? $item_id : 0;
        $action_instance->created    = (!$created) ? new Carbon("now") : $created;
        $action_instance->save();

        return $action_instance;
    }

    function getBrowserID($browser_id, &$is_new_browser = false)
    {
        $browser = Browser::firstOrNew(array("identifier" => $browser_id));
        if (!isset($browser->id)) {
            $browser->save();
            $is_new_browser = true;
        }
        return $browser->id;
    }

    function getVisitorID($data, $session, $site_id)
    {
        //since the email is optional. Please assign empty if not set
        $visitor_id  = false;
        $user_data   = array_add($data, "email", isset($data['email']) ? $data['email'] : "" );
        $obj_session = Session::firstOrNew(array("session" => $session));

        if ($obj_session) { // if session exists, update visitor data
            $visitor_id = !is_null($obj_session->visitor_id) ? $obj_session->visitor_id : false;
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
                $obj_session = Session::where("site_id", $site_id)->where("visitor_id", $v['id'])->first();
                if ($obj_session) {
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

        return $visitor_id;
    }

    function getActionIDFromCache($action_name, $site_id)
    {
        $val = \Cache::get("actions_{$site_id}_{$action_name}");
        return isset($val) ? $val : null;
    }

    function getActionID($action_data, $site_id, &$is_new_action = false)
    {
        $cache_action_id = $this->getActionIDFromCache($action_data['name'], $site_id);

        if (!is_null($cache_action_id))
            return $cache_action_id;

        $action = Action::where("name", $action_data['name'])->where("site_id", $site_id)->first();
        if (!$action) {
            $action_id     = $this->setAction($action_data, $site_id);
            $is_new_action = true;
        }
        else
            $action_id = $action->id;

        \Cache::add("action_{$site_id}_{$action_data['name']}", $action_id, 20160); //store for 14 days
        return $action_id;
    }

    function getSessionID($session, $site_id, $browser_id, $visitor_id = null, &$is_new_session = false)
    {
        $session_visitor_id = 0;
        //start processing visitor
        $visitor_session    = Session::firstOrNew(array("session" => $session, "site_id" => $site_id, "visitor_id" => $visitor_id));

        if (!isset($visitor_session->id)) {
            $visitor_session->save();

            $is_new_session     = $session_visitor_id = $visitor_session->id;
            $this->setBrowseSession($browser_id, $visitor_session->id); //set browser session
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

    function getItemIDFromCache($identifier, $site_id)
    {
        $val = \Cache::get("items_{$site_id}_{$identifier}");
        return isset($val) ? $val : null;
    }

    function getItemID($item_data, $site_id, &$is_new_item = false)
    {
        $cache_item = $this->getItemIDFromCache($item_data['item_id'], $site_id);
        $item       = !is_null($cache_item) ? $cache_item : Item::where("identifier", $item_data['item_id'])->where("site_id", $site_id)->first();

        if ($item) {
            if (isset($item_data['name']) && $item_data['name'] !== "" && ($item->name !== $item_data['name'])) {
                $item->name = $item_data['name'];
                $item->update();
            }

            $this->compareAndUpdateItemMetas($item->id, $item_data);
            return $item->id;
        }
        else {
            $item = Item::firstOrCreate([
                        'identifier' => $item_data['item_id'],
                        'name'       => $item_data['name'],
                        'site_id'    => $site_id
            ]);

            if (isset($item->id)) {
                $now            = Carbon::now();
                $item_meta_data = [];
                foreach ($item_data as $key => $value) {

                    if (is_array($value))
                        $value = json_encode($value);

                    $new_meta = [
                        'item_id'    => $item->id,
                        'key'        => $key,
                        'value'      => $value,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];

                    array_push($item_meta_data, $new_meta);
                }

                ItemMeta::insert($item_meta_data); //bulk insert

                $is_new_item   = $this->item_id = $item->id;
                \Cache::add("items_{$site_id}_{$item_data['item_id']}", $item, 20160);
                return $item->id;
            }
            else
                return false;
        }
    }

    function getCartID($session_id)
    {
        $cart_id           = $count_used_before = -1;
        $cart              = Cart::where("session_id", $session_id)->get()->last();

        if ($cart) {
            $count_used_before = ActionInstanceMeta::where("key", "cart_id")->where("value", $cart->id)->get()->count();
        }

        if ($count_used_before > 0 || is_null($cart)) {
            $new_cart             = new Cart();
            $new_cart->session_id = $session_id;
            $new_cart->save();

            $cart_id = ($new_cart->id) ? $new_cart->id : -1;
        }
        else
            $cart_id = $cart->id;

        return $cart_id;
    }

    function getItemMetasFromCache($item_id)
    {
        $val = \Cache::get("item_metas_{$item_id}");
        return isset($val) ? $val : null;
    }

    function compareAndUpdateItemMetas($item_id, $item_data)
    {
        $properties_keys = array_keys($item_data);
        unset($properties_keys['item_id']);

        $cache_item_metas = $this->getItemMetasFromCache($item_id);
        if (!is_null($cache_item_metas)) {
            $item_metas = $cache_item_metas;
        }
        else {
            $item_metas = ItemMeta::where("item_id", $item_id)->get();
            \Cache::add("item_metas_{$item_id}", $item_metas, 20160);
        }
        $item_metas = !is_null($cache_item_metas) ? $cache_item_metas : ItemMeta::where("item_id", $item_id)->get();

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
            $now            = Carbon::now();
            $item_meta_data = [];
            foreach ($properties_keys as $key) {
                $new_meta = [
                    'item_id'    => $item_id,
                    'key'        => $key,
                    'value'      => is_array($item_data[$key]) ? json_encode($item_data[$key]) : $item_data[$key],
                    'created_at' => $now,
                    'updated_at' => $now
                ];

                array_push($item_meta_data, $new_meta);
            }

            ItemMeta::insert($item_meta_data); //bulk insert
        }

        return;
    }

    function isLogContainDateTime($input)
    {
        if (isset($input['log_date_created_at']) && $input['log_time_created_at']) {
            return Carbon::parse("{$input['log_date_created_at']} {$input['log_time_created_at']}");
        }

        return false;
    }

}

/* End of file ActionRepository.php */
/* Location: ./application/controllers/ActionRepository.php */
    