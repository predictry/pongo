<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 8, 2015 5:51:23 PM
 * File         : Role.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{

    protected $table     = 'permissions';
    protected $fillable  = ['name', 'display_name'];
    public static $rules = array(
        "name"         => "required|max:255",
        "display_name" => "required|max:255"
    );

}

/* End of file Role.php */