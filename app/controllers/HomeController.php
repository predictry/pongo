<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 18, 2014 5:10:42 PM
 * File         : app/controllers/HomeController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     :
 */

namespace App\Controllers;

use View,
	Auth,
	Input,
	Validator,
	Redirect,
	Hash,
	Config,
	Password,
	App;

class HomeController extends BaseController
{

	protected $layout = 'frontend.layouts.basic';

	/**
	 * Display home view.
	 * 
	 * @return Response
	 */
	public function getHome($id, $str)
	{
		echo '<pre>';
		echo "id=>";
		print_r($id);
		echo "<br/>----<br/>";
		echo "str=>";
		print_r($str);
		echo '</pre>';
		die;

		return View::make('hello');
	}

	/**
	 * Display login view.
	 * 
	 * @return Response
	 */
	public function getLogin()
	{
		$user = Auth::user();
		if (!empty($user->id))
		{
			return Redirect::to('dashboard');
		}

		return View::make('frontend.common.login', array("pageTitle" => "Login"));
	}

	/**
	 * Handle a POST request to logged in.
	 * 
	 * @return Response
	 */
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
			$account_id = \App\Models\Account::where("email", $input['email'])->get(array('id'))->first();

			if (isset($account_id))
			{
				if (Auth::attempt(array('email' => $input['email'], 'password' => $input['password']), ($input['remember'])))
				{
					//validate if member or not
					$member = App\Models\Member::where("account_id", Auth::user()->id)->get()->first();
					if ($member)
					{
						$site_member = App\Models\SiteMember::where("member_id", $member->id)->get()->first();
						$site		 = \App\Models\Site::find($site_member->site_id);
						\Session::set("active_site_id", $site_member->site_id);
						\Session::set("active_site_name", $site->name);
						\Session::set("role", "member");
					}
					else
						\Session::set("role", "admin");

					return Redirect::to('dashboard');
				}

				$flash_error = 'error.login.failed';
			}
			else
			{
				$flash_error = "error.email.doesnt.exists";
			}
			return Redirect::to('login')->with('flash_error', $flash_error)->withInput();
		}
		else
		{
			return Redirect::to('login')->withInput()->withErrors($validator);
		}
	}

	/**
	 * Display register view.
	 * 
	 * @return Response
	 */
	public function getRegister()
	{
		$this->siteInfo['pageTitle'] = "signup.now";
		return View::make('frontend.common.register');
	}

	/**
	 * Handle a POST request to register new account
	 * 
	 * @return Response
	 */
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
			$account = new App\Models\Account;

			$account->name				 = $input['name'];
			$account->email				 = $input['email'];
			$account->password			 = Hash::make($input['password']);
			$account->plan_id			 = 1;
			$account->confirmed			 = 1;
			$account->confirmation_code	 = md5(microtime() . Config::get('app.key'));

			$account->save();

			//SEND VERIFICATION EMAIL
			$email_data = array("fullname" => ucwords($input['name']));
			\Mail::send('emails.auth.accountconfirmation', $email_data, function($message) use ($input) {
				$message->to($input['email'], ucwords($input['name']))->subject('Welcome!');
			});

			return Redirect::to('login')->with('flash_message', "home.success.register");
		}
		else
		{
			return Redirect::to('register')->withInput()->withErrors($validator);
		}
	}

	/**
	 * Display forgot password view.
	 * 
	 * @return Response
	 */
	public function getForgotPassword()
	{
		return View::make("frontend.common.forgot");
	}

	/**
	 * Hanle a POST request to send forgot password email confirmation.
	 * 
	 * @return Response
	 */
	public function postForgotPassword()
	{
		$input = array(
			'email' => Input::get('email')
		);

		$rules = array(
			'email' => 'required|email'
		);

		$validator = Validator::make($input, $rules);
		if ($validator->passes())
		{
			$user_id = \App\Models\Account::where("email", $input['email'])->get(array('id'))->first();
			if (!$user_id)
				return Redirect::to('forgot')->with('flash_error', "error.email.doesnt.exists");
			else
			{
				$response = Password::remind(Input::only('email'), function($message) {
							$message->subject = "subject.password.reminder";
						});
				switch ($response)
				{
					case Password::INVALID_USER:
						return Redirect::back()->with('flash_message', \Lang::get($response));

					case Password::REMINDER_SENT:
						return Redirect::back()->with('flash_message', \Lang::get($response));
				}
			}
		}
		else
		{
			return Redirect::back()->withInput()->withErrors($validator);
		}
	}

	/**
	 * Display the password reset view for the given token.
	 *
	 * @param  string  $token
	 * @return Response
	 */
	public function getReset($token = null)
	{
		if (is_null($token))
			App::abort(404);

		return View::make('frontend.common.reset')->with('token', $token);
	}

	/**
	 * Handle a POST request to reset a user's password.
	 *
	 * @return Response
	 */
	public function postReset()
	{
		$credentials = Input::only('email', 'password', 'password_confirmation', 'token');

		$rules = array(
			'email'					 => 'required|email',
			'password'				 => 'required|min:8|confirmed',
			'password_confirmation'	 => 'required',
			'token'					 => 'required'
		);

		$validator = Validator::make($credentials, $rules);

		if ($validator->passes())
		{
			$response = Password::reset($credentials, function($user, $password) {
						$user->password = Hash::make($password);
						$user->save();
					});

			switch ($response)
			{
				case Password::INVALID_PASSWORD:
				case Password::INVALID_TOKEN:
				case Password::INVALID_USER:
					return Redirect::back()->with('flash_error', Lang::get($response));
				case Password::PASSWORD_RESET:
					return Redirect::to('login')->with('flash_message', "success.password.changed");
			}
		}

		$token = Input::get('token');
		return Redirect::to('reset/' . $token)->withInput()->withErrors($validator);
	}

}
