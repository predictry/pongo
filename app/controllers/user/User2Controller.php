<?php

namespace App\Controllers\User;

use App\Controllers\BaseController,
    Auth,
    Redirect,
    Session,
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
        $current_site = \Session::get("active_site_name");
        return View::make(getenv('FRONTEND_SKINS') . $this->theme . '.panels.users.profile', ['pageTitle' => 'Edit Profile', 'current_site' => $current_site]);
    }

    public function getPassword()
    {
        $current_site = \Session::get("active_site_name");
        return View::make(getenv('FRONTEND_SKINS') . $this->theme . '.panels.users.password', ['pageTitle' => 'Edit Password', 'current_site' => $current_site]);
    }

    public function logout()
    {
        Auth::logout();
        Session::clear();
        return Redirect::to('v2/login');
    }

}

/* End of file User2Controller.php */
/* Location: ./application/controllers/User2Controller.php */
