<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Aug 18, 2014 12:53:37 PM
 * File         : app/models/Browser.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Browser extends \Eloquent
{

    use SoftDeletingTrait;

    protected $dates   = ['deleted_at'];
    protected $guarded = array("id");
    protected $table   = 'browsers';

}

/* End of file Browser.php */
/* Location: ./app/models/Browser.php */
