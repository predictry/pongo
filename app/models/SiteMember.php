<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 11, 2014 11:41:14 AM
 * File         : app/models/SiteMember.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

class SiteMember extends \Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	public $primaryKey	 = "member_id";
	public $timestamps	 = false;
	protected $table	 = 'sites_members';

	public function profile()
	{
		return $this->belongsTo("App\Models\Member");
	}

}

/* End of file SiteMember.php */
/* Location: ./app/models/SiteMember.php */
