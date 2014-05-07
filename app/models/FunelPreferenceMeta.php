<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : May 6, 2014 4:18:24 PM
 * File         : app/models/FunelPreferenceMeta.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

class FunelPreferenceMeta extends \Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table	 = 'funel_preference_metas';
	public $timestamps	 = false;

	public function action()
	{
		return $this->belongsTo("App\Model\Action", "action_id");
	}

}

/* End of file FunelPreferenceMeta.php */
/* Location: ./app/models/FunelPreferenceMeta.php */
