<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 18, 2014 5:35:03 PM
 * File         : app/models/Site.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

class Site extends \Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table			 = 'sites';
	public $manage_table_header	 = array(
		"name"		 => "Tenant ID",
		"url"		 => "URL Address",
		"api_key"	 => "API Key",
		"api_secret" => "API Secret Key"
	);
	public $rules				 = array(
		"name"	 => "required|regex:/^[a-zA-Z]{1}/|alpha_num|max:32",
		"url"	 => "required|active_url",
	);

	public function actions()
	{
		return $this->hasMany("Action");
	}

}

/* End of file Site.php */
/* Location: ./app/models/Site.php */
