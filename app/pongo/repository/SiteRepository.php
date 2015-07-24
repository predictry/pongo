<?php

namespace App\Pongo\Repository;

use App\Models\Account,
    App\Models\Action,
    App\Models\ActionInstance,
    App\Models\ActionMeta,
    App\Models\FunelPreference,
    App\Models\Member,
    App\Models\Site,
    App\Models\SiteMember,
    Auth,
    Validator;

/**
 * Author       : Rifki Yandhi
 * Date Created : Sep 26, 2014 10:18:15 AM
 * File         : SiteRepository.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class SiteRepository {

    protected $excluded_properties = null;

    public function createNewSite($account_id, $site_name, $site_url) {
        $site = new Site();
        $site->name = $site_name;
        $site->url = $site->api_key = $site->api_secret = $site_url;
        $site->account_id = $account_id;
        $site->save();
        return $site;
    }

    public function createDefaultActions($site_id) {
        //can be migrate to table
        $default_actions = array(
            "view" => array("score" => 1),
            "rate" => array("score" => 2),
            "add_to_cart" => array("score" => 3),
            "buy" => array("score" => 4),
            "started_checkout" => array("score" => 5),
            "started_payment" => array("score" => 6),
            "complete_purchase" => array("score" => 7)
        );

        //set default action types for the site
        foreach ($default_actions as $key => $arr) {
            $action = new Action();
            $action->name = $key;
            $action->description = null;
            $action->site_id = $site_id;
            $action->save();

            foreach ($arr as $key2 => $val) {
                $action_meta = new ActionMeta();
                $action_meta->key = $key2;
                $action_meta->value = $val;
                $action_meta->action_id = $action->id;
                $action_meta->save();
            }
        }
    }

    public function createDefaultFunnelPreferences($site_id) {
        $default_funel_preference = new FunelPreference();
        $default_funel_preference->site_id = $site_id;
        $default_funel_preference->name = "Default";
        $default_funel_preference->is_default = true;
        $default_funel_preference->save();
    }

    //REMOVE SITE_MEMBERS
    public function removeSiteMembers($site_id) {
        $site_members = SiteMember::where("site_id", $site_id)->get();

        //soft deletes members account.
        foreach ($site_members as $site_member) {
            $member = Member::find($site_member->member_id);
            $member->delete();
            Account::find($member->account_id)->delete();
        }

        SiteMember::where("site_id", $site_id)->delete();
    }

    public function removeActions($site_id) {
        $actions = Action::where("site_id", $site_id)->get();
        foreach ($actions as $action) {
            ActionInstance::where("action_id", $action->id)->delete();
            ActionMeta::where("action_id", $action->id)->delete();
        }
        Action::where("site_id", $site_id)->delete();
    }

    public function isBelongToHim($tenant_id) {
        $site = Site::where('name', $tenant_id)->where('account_id', Auth::user()->id)->first();
        return ($site) ? $site : false;
    }

    public function validateUniqueUrl($url) {
        $url_validator = Validator::make(['url' => $url], ['url' => 'unique:sites,url']);

        if ($url_validator->passes()) { 
          return true;
        } else
            return $url_validator;
    }

    public function buildSnippedJSData($action_name, $action, $common = null, $excluded_properties = null) {
        $this->excluded_properties = $excluded_properties;

        $is_common_shared = $this->isCommonSharedTo($common, $action_name);

        if ($is_common_shared && isset($common->actions->entities)) {
            $common_entities = $common->actions->entities;
            $common_snipped_data = $this->extractEntities($common_entities);
        }

        $entities = $action->entities;
        $snipped_data = (object) array_merge((array) $this->extractEntities($entities), (array) $common_snipped_data);
        return $snipped_data;
    }

    public function getSelectedActionFromJson($json_data, $action_name) {
        $selected_action = null;

        foreach ($json_data->sections as $section) {
            if (!$section->disabled) {
                $actions = $section->actions;
                foreach ($actions as $action) {
                    if ($action->name === $action_name) {
                        $selected_action = $action;
                        break;
                    }
                }
            }

            if (!is_null($selected_action))
                break;
        }

        return $selected_action;
    }

    public function isCommonSharedTo($common, $action_name) {
        if (isset($common->actions) && isset($common->actions->shared)) {
            return in_array($action_name, $common->actions->shared);
        }

        return false;
    }

    public function extractEntities($entities) {
        $snipped_data = (object) [];

        foreach ($entities as $entity) {
            if ($entity->type === "object") {
                $snipped_data->{$entity->entity_name} = (object) [];
            } else if ($entity->type === "array") {
                $snipped_data->{$entity->entity_name} = array();
            }

            $snipped_data->{$entity->entity_name} = $this->extractProperties($snipped_data->{$entity->entity_name}, $entity->entity_name, $entity->type, $entity->properties);
        }

        return $snipped_data;
    }

    public function extractProperties($snipped_entity, $entity_name, $entity_type, $properties) {
        $snipped_properties = (object) [];
        foreach ($properties as $property) {

            if (is_null($this->excluded_properties) || !in_array($property->field, $this->excluded_properties)) {
                if (!isset($property->hidden)) {

                    if ($entity_type === "object") {
                        if ($entity_name === "action" && $property->field === "name")
                            $snipped_entity->{$property->field} = "{$property->default_value}";
                        else
                            $snipped_entity->{$property->field} = "<%= {$property->field} %>";
                    }
                    else if ($entity_type === "array") {
                        $snipped_properties->{$property->field} = "<%= {$property->field} %>";
                    }
                }
            }
        }

        if ($entity_type === "array")
            array_push($snipped_entity, $snipped_properties);

        return $snipped_entity;
    }

    public function getExcludedProperties($site_id, $action_name) {
        $action = Action::where('site_id', $site_id)->where('name', $action_name)->first();
        if ($action) {
            return ActionMeta::where('key', 'excluded_properties')->where('action_id', $action->id)->first();
        }

        return [];
    }

    public function createSiteBasedOnURL($account, $url, $site_category_id = 1) {
        if(mb_substr($url, 0, 4) !== 'http') 
          $url_tp = 'http://' . $url;   
        else
          $url_tp = $url;

        $parse_url = parse_url($url_tp);

        if (!empty($parse_url['host'])) {
            $host = $parse_url['host'];

            $site = new Site();
            $site->url = $url_tp;
            $site->api_key = $url;
            $site->api_secret = $url;
            $site->name = strtoupper(str_replace('.', '', $host));
            $site->account_id = $account->id;
            $site->site_category_id = $site_category_id;
            $site->save();

            return isset($site->id) ? $site->id : 0;
        }
        return false;
    }
  
}

/* End of file SiteRepository.php */
