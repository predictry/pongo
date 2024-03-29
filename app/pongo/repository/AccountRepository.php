<?php

namespace App\Pongo\Repository;

use App\Models\Account,
    App\Models\AccountMeta,
    App\Models\Member,
    App\Models\Site,
    App\Models\SiteMember,
    Config,
    Illuminate\Support\Facades\Auth,
    Session,
    Validator;

class AccountRepository
{

    public function newInstance($attributes = array())
    {
        return new Account($attributes);
    }

    public function validate($input, $rules = array())
    {
        $rules = count($rules) > 0 ? $rules : Account::$rules;
        return Validator::make($input, $rules);
    }

    /**
     * Persist Account.
     * 
     * @param Account $account
     * @return boolean|object
     */
    public function saveAccount(Account $account)
    {
        return $account->save();
    }

    /**
     * Apply Role Session
     * 
     * @param string $role
     */
    public function applyRoleSession($role)
    {
        Session::set("role", $role);
    }

    /**
     * Apply Site Session.
     * 
     * @param object $site
     */
    public function applySiteSession($site)
    {
        Session::set("active_site_id", $site->id);
        Session::set("active_site_name", $site->name);
    }

    /**
     * Check if the current use is member or admin. If yes, then set role as a member.
     * 
     * @return boolean
     */
    public function isMember()
    {
        //validate if member or not
        $member = Member::where("account_id", Auth::user()->id)->get()->first();
        if ($member) {
            $site_member = SiteMember::where("member_id", $member->id)->get()->first();
            $site        = Site::find($site_member->site_id);

            $this->applyRoleSession("member");
            $this->applySiteSession($site);
            return $member->id;
        }

        return false;
    }

    public function isNewAccount()
    {
        $account_meta = AccountMeta::where('account_id', \Auth::user()->id)->where('key', 'is_new_account')->first();

        if ($account_meta && $account_meta->value) {
            return true;
        }

        return false;
    }

    public function assignConfirmation($account, $is_confirmed = false)
    {
        if (is_object($account)) {
            $account->confirmed = ($is_confirmed) ? 1 : 0;
            $account            = $this->setConfirmationCode($account);

            return $account;
        }
        return false;
    }

    public function setConfirmationCode($account)
    {
        if (is_object($account)) {
            $account->confirmation_code = md5(microtime() . Config::get('app.key'));
            return $account;
        }

        return false;
    }


    public function siteValidate($input, $rules = array()) {
      $rules = count($rules) > 0 ? $rules : Site::$rules;
      return Validator::make($input, $rules);
    }

    public function assignUserRoleByEmail($email)
    {
        $user_role = \App\Models\Role::where('name', 'User')->first();

        if ($user_role) {
            $account = Account::where('email', $email)->first();
            if ($account)
                $account->roles()->attach($user_role->id);
        }

        return;
    }
}

/* End of file AccountRepository.php */	
