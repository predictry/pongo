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

	static function getNumberOfUsers($site_id)
	{
		$query			 = "SELECT COUNT(*) FROM "
				. "(SELECT count(distinct visitor_id) FROM sessions WHERE site_id = {$site_id} GROUP BY visitor_id) AS tbl";
		$total_actions	 = \DB::select($query);
		return $total_actions[0]->count;
	}

}

/* End of file Session.php */
/* Location: ./app/models/Session.php */
