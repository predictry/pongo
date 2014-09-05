<?php

use App\Models\Action;

/**
 * Author       : Rifki Yandhi
 * Date Created : Aug 27, 2014 10:21:49 AM
 * File         : app/controllers/ActionRepository.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class ActionRepository
{

	/**
	 * Persist an action.
	 * 
	 * @param Action $action
	 * @return type
	 */
	public function save(Action $action)
	{
		return $action->save();
	}

}

/* End of file ActionRepository.php */
/* Location: ./application/controllers/ActionRepository.php */
