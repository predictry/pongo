<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 17, 2014 11:06:40 AM
 * File         : app/models/Widget.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

class Widget extends \Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table			 = 'widgets';
	public $rules				 = array(
		"name" => "required|max:64"
	);
	public $manage_table_header	 = array(
		"id"				 => "Widget ID",
		"name"				 => "Name",
		"created_at"		 => "Date Created",
		"number_of_rulesets" => "Number of Ruleset(s)"
	);

}

/* End of file Widget.php */
/* Location: ./app/models/Widget.php */
