<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 10, 2014 3:54:51 PM
 * File         : app/models/ItemMeta.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

class ItemMeta extends \Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'item_metas';

	public function item()
	{
		return $this->belongsTo("App\Models\Item");
	}

}

/* End of file ItemMeta.php */
	/* Location: ./app/models/ItemMeta.php */	