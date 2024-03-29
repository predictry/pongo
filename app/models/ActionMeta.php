<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 4, 2014 3:41:14 PM
 * File         : app/models/ActionMeta.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

use Eloquent;

class ActionMeta extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $guarded = array("id");
    public $timestamps = false;
    protected $table   = 'action_metas';

    public function action()
    {
        return $this->hasMany("App\Models\Action", "action_id");
    }

}

/* End of file ActionMeta.php */
/* Location: ./app/models/ActionMeta.php */
