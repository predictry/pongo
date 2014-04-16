<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 14, 2014 10:44:30 AM
 * File         : app/models/Rule.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

class Rule extends \Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'rules';
	public $rules	 = array(
		"type"		 => "required",
		"likelihood" => "required|numeric|min:0|max:100",
		"item_id"	 => "required"
	);

	public function ruleset()
	{
		return $this->belongsTo("App\Models\RuleSet", "id");
	}

}

/* End of file Rule.php */
/* Location: ./app/models/Rule.php */
