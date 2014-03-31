<?php

class UserController extends BaseController
{

	public function getDashboard()
	{

		return "Dashboard <a href='" . URL::to('user/logout') . "'>Logout</a>";
	}

	public function logout()
	{
		Auth::logout();
		return Redirect::to('/');
	}

}
