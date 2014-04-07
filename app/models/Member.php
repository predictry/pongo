<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 2, 2014 11:16:13 AM
 * File         : app/models/Member.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class Member extends Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $primaryKey		 = "id";
	protected $table			 = 'members';
	protected $softDelete		 = true;
	public $manage_table_header	 = array(
		"name"		 => "Name",
		"email"		 => "Email",
		"created_at" => "Date Created"
	);
	public $rules = array(
		"name"		 => "required",
		"email"		 => "required|email|unique:accounts|unique:members",
		"password"	 => "required|min:8|confirmed"
	);

}

/* End of file Member.php */
/* Location: ./app/models/Member.php */
