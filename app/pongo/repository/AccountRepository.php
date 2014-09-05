<?php

namespace App\Pongo\Repository;

use App\Models\Account;
use App\Models\Member;
use App\Models\Site;
use App\Models\SiteMember;
use Illuminate\Support\Facades\Auth;
use Session;
use Validator;

/**
 * Author       : Rifki Yandhi
 * Date Created : Aug 27, 2014 10:28:31 AM
 * File         : AccountRepository.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
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
	 * @param \App\Models\Account $account
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
		if ($member)
		{
			$site_member = SiteMember::where("member_id", $member->id)->get()->first();
			$site		 = Site::find($site_member->site_id);

			$this->applyRoleSession("member");
			$this->applySiteSession($site);
			return $member->id;
		}

		return false;
	}

}

/* End of file AccountRepository.php */	