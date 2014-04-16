<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 9, 2014 4:31:24 PM
 * File         : app/models/Visitor.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

class Visitor extends \Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'visitors';

	public function sessions()
	{
		return $this->hasMany("App\Models\Session");
	}

}

/* End of file Visitor.php */
/* Location: ./app/models/Visitor.php */
