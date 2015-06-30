<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : May 28, 2014 2:32:31 PM
 * File         : app/models/Filter.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

class Filter extends \Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table            = 'filters';
    public $manage_table_header = array(
        "name"       => "Filter Name",
        "properties" => "Selected Properties"
    );
    public $filter_data_type    = [
        'str'      => 'text',
        'num'      => 'numeric',
        'date'     => 'date',
        'list'     => 'list',
        'location' => 'location'
    ];

    public function metas()
    {
        return $this->hasMany("App\Models\FilterMeta");
    }

}

/* End of file Filter.php */
/* Location: ./app/models/Filter.php */
