<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 7, 2014 4:58:12 PM
 * File         : app/models/Item.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class Item extends Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table			 = 'items';
	public $rules				 = array();
	public $table_manage_header	 = array();

}

/* End of file Item.php */
/* Location: ./app/models/Item.php */
