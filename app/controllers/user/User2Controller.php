<?php

namespace App\Controllers\User;

use App\Controllers\BaseController,
    View;

/**
 * Author       : Rifki Yandhi
 * Date Created : Jan 9, 2015 2:38:18 PM
 * File         : app/controllers/User2Controller.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class User2Controller extends BaseController
{

    function __construct()
    {
        parent::__construct();
    }

    public function getProfile()
    {
        return View::make(getenv('FRONTEND_SKINS') . $this->theme . '.users.profile');
    }

    public function getPassword()
    {
        return View::make(getenv('FRONTEND_SKINS') . $this->theme . '.users.password');
    }

}

/* End of file User2Controller.php */
/* Location: ./application/controllers/User2Controller.php */
