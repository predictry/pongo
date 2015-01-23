<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : May 21, 2014 4:24:51 PM
 * File         : app/models/Combination.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Combination extends \Eloquent
{

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'combinations';

}

/* End of file Combination.php */
/* Location: ./app/models/Combination.php */
