<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 2, 2014 11:22:49 AM
 * File         : app/models/AccountMember.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class AccountMember extends Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $primaryKey	 = 'member_id';
	protected $table		 = 'accounts_members';
	public $timestamps		 = false;
	public $rules			 = array(
		"account_id" => "required|exists:accounts",
		"member_id"	 => "required"
	);

	public function profile()
	{
		return $this->hasOne("Member", "id");
	}

}

/* End of file AccountMember.php */
/* Location: ./app/models/AccountMember.php */
