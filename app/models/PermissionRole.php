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

class PermissionRole extends Eloquent
{

    protected $table     = 'permission_role';
    protected $fillable  = ['permission_id', 'role_id'];
    public $timestamps   = false;
    public static $rules = array(
        "permission_id" => "required|exists:permissions,id",
        "role_id"       => '"required|exists:roles,id'
    );

}

/* End of file Role.php */