<?php

class HomeController extends BaseController
{

	protected $layout = 'frontend.layouts.basic';

	public function getHome()
	{
		return View::make('hello');
	}

	public function getLogin()
	{
		$user = Auth::user();
		if (!empty($user->id))
		{
			return Redirect::to('users/dashboard');
		}

		return View::make('frontend.site.login');
	}

	public function postLogin()
	{
		$input = array(
			'email'		 => Input::get('email'),
			'username'	 => Input::get('email'),
			'password'	 => Input::get('password'),
			'remember'	 => Input::get('remember')
		);

		$rules = array(
			'email'		 => 'required|email',
			'password'	 => 'required|min:8'
		);

		$validator	 = Validator::make($input, $rules);
		$flash_error = '';
		if ($validator->passes())
		{
			$account_id = Account::where("email", $input['email'])->get(array('id'))->first();
			if (isset($account_id))
			{
				if (Auth::attempt(array('email' => $input['email'], 'password' => $input['password'])))
				{
					return Redirect::to('user/dashboard');
				}

				$flash_error = 'Your email/password combination was incorrect.';
			}
			else
			{
				$flash_error = "Email/password doesn't exists.";
			}
			return Redirect::to('login')->with('flash_error', $flash_error)->withInput();
		}
		else
		{
			return Redirect::to('login')->withInput()->withErrors($validator);
		}
	}

	public function getRegister()
	{
		return View::make('frontend.site.register');
	}

	public function postRegister()
	{
		$input = array(
			'name'					 => Input::get('name'),
			'email'					 => Input::get('email'),
			'password'				 => Input::get('password'),
			'password_confirmation'	 => Input::get('password_confirmation')
		);

		$rules = array(
			'name'					 => 'required',
			'password'				 => 'required|min:8|confirmed',
			'password_confirmation'	 => 'required|min:8',
			'email'					 => 'required|email|unique:accounts'
		);

		$validator = Validator::make($input, $rules);
		if ($validator->passes())
		{
			$account = new Account;

			$account->name				 = $input['name'];
			$account->email				 = $input['email'];
			$account->password			 = Hash::make($input['password']);
			$account->plan_id			 = 1;
			$account->confirmed			 = 1;
			$account->confirmation_code	 = md5(microtime() . Config::get('app.key'));

			$account->save();
			return Redirect::to('login')->with('flash_message', "You have sucessfully registered. Please login.");
		}
		else
		{
			return Redirect::to('register')->withInput()->withErrors($validator);
		}
	}

	public function getForgotPassword()
	{
		
	}

}
