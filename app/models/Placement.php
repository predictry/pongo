<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 17, 2014 11:06:40 AM
 * File         : app/models/Placement.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

class Placement extends \Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table			 = 'placements';
	public $rules				 = array(
		"name" => "required|max:64"
	);
	public $manage_table_header	 = array(
		"name"				 => "Name",
		"created_at"		 => "Date Created",
		"number_of_rulesets" => "Number of Ruleset(s)"
	);

}

/* End of file Placement.php */
/* Location: ./app/models/Placement.php */
