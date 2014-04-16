<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 14, 2014 10:58:54 AM
 * File         : app/models/RuleSet.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

class Ruleset extends \Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table			 = 'rule_sets';
	public $rules				 = array(
		"name"			 => "required",
		"expiry_type"	 => "required"
	);
	public $manage_table_header	 = array(
		"name"				 => "Name",
		"description"		 => "Description",
		"expiry_type"		 => "Expiry Type",
		"expiry_datetime"	 => "Expiry Date",
		"expiry_value"		 => "Expiry Value"
	);

	public function item_rules()
	{
		return $this->hasMany("App\Models\Rule", "ruleset_id");
	}

}

/* End of file RuleSet.php */
/* Location: ./app/models/RuleSet.php */
