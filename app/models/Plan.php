<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 18, 2014 5:19:50 PM
 * File         : app/models/Plan.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Plan extends \Eloquent
{

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'plans';

}

/* End of file Plan.php */
/* Location: ./app/models/Plan.php */
