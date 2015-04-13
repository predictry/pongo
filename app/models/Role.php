<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 8, 2015 5:51:23 PM
 * File         : Role.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{

    protected $table     = 'roles';
    protected $fillable  = ['name'];
    public static $rules = array(
        "name" => "required"
    );

}

/* End of file Role.php */