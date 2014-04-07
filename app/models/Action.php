<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 4, 2014 10:24:02 AM
 * File         : app/models/Action.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class Action extends Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table	 = 'actions';

	public function metas()
	{
		return $this->hasMany("ActionMeta");
	}

}

/* End of file Action.php */
/* Location: ./app/models/Action.php */
