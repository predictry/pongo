<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 8, 2015 5:51:23 PM
 * File         : Role.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

use Eloquent;

class AssignedRole extends Eloquent
{

    protected $table     = 'assigned_roles';
    protected $fillable  = ['user_id', 'role_id'];
    public $timestamps   = false;
    public static $rules = array(
        "user_id" => "required|exists:accounts,id",
        "role_id" => '"required|exists:roles,id'
    );

}

/* End of file Role.php */