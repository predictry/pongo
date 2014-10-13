<?php

namespace App\Pongo\Repository;

use App\Models\Account,
    App\Models\Action,
    App\Models\ActionMeta,
    App\Models\FunelPreference,
    App\Models\Member,
    App\Models\SiteMember;

/**
 * Author       : Rifki Yandhi
 * Date Created : Sep 26, 2014 10:18:15 AM
 * File         : SiteRepository.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class SiteRepository
{

    public function createNewSite($site_name, $site_url)
    {
        return \App\Models\Site::firstOrCreate([
                    'account_id', \Auth::user()->id,
                    'name'       => $site_name,
                    'url'        => $site_url,
                    'api_key'    => $site_url,
                    'api_secret' => $site_url
        ]);
    }

    public function createDefaultActions($site_id)
    {
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
            $action->site_id     = $site_id;
            $action->save();

            foreach ($arr as $key2 => $val) {
                $action_meta            = new ActionMeta();
                $action_meta->key       = $key2;
                $action_meta->value     = $val;
                $action_meta->action_id = $action->id;
                $action_meta->save();
            }
        }
    }

    public function createDefaultFunnelPreferences($site_id)
    {
        $default_funel_preference             = new FunelPreference();
        $default_funel_preference->site_id    = $site_id;
        $default_funel_preference->name       = "Default";
        $default_funel_preference->is_default = true;
        $default_funel_preference->save();
    }

    //REMOVE SITE_MEMBERS
    public function removeSiteMembers($site_id)
    {
        $site_members = SiteMember::where("site_id", $site_id)->get();

        //soft deletes members account.
        foreach ($site_members as $site_member) {
            $member = Member::find($site_member->member_id);
            $member->delete();
            Account::find($member->account_id)->delete();
        }

        SiteMember::where("site_id", $site_id)->delete();
    }

    public function removeActions($site_id)
    {
        $actions = Action::where("site_id", $site_id)->get();
        foreach ($actions as $action) {
            \App\Models\ActionInstance::where("action_id", $action->id)->delete();
            ActionMeta::where("action_id", $action->id)->delete();
        }
        Action::where("site_id", $site_id)->delete();
    }

}

/* End of file SiteRepository.php */