<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 7, 2014 4:58:12 PM
 * File         : app/models/ItemMeta.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

class Item extends \Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table            = 'items';
    public $rules               = array(
        'identifier' => 'required',
        'name'       => 'required'
    );
    public $manage_table_header = array(
        "identifier" => "Item ID",
        "name"       => "Name",
        "type"       => "Type",
//		"created_at" => "Date Created",
//		"active"	 => "Activated"
    );

    public function item_metas()
    {
        return $this->hasMany("App\Models\ItemMeta");
    }

}

/* End of file Item.php */
/* Location: ./app/models/Item.php */
