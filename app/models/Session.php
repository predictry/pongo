<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 10, 2014 10:17:57 AM
 * File         : app/models/Session.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

class Session extends \Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'sessions';

	public function visitor()
	{
		return $this->belongsTo("App\Models\Visitor");
	}

}

/* End of file Session.php */
/* Location: ./app/models/Session.php */
