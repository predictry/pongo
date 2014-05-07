<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : May 6, 2014 4:17:58 PM
 * File         : app/models/FunelPreference.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

class FunelPreference extends \Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'funel_preferences';
	public $rules	 = array(
		"name"		 => "required",
		"action_id"	 => "required"
	);

}

/* End of file FunelPreference.php */
/* Location: ./app/models/FunelPreference.php */
