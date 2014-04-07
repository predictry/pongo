
<?php

class UserController extends BaseController
{

	protected $layout = 'frontend.layouts.dashboard';

	public function getDashboard()
	{
		\View::share(array("ca" => get_class()));
		return View::make('frontend.panels.dashboard');
	}

	public function getProfile()
	{
		return View::make('frontend.panels.users.profile');
	}

	public function postProfile()
	{
		$input			 = Input::only('name', 'email');
		$existing_email	 = Auth::user()->email;

		$rules = array(
			'name'	 => 'required',
			'email'	 => ($input['email'] !== $existing_email) ? 'required|email|unique:accounts' : 'required|email'
		);

		$validator = Validator::make($input, $rules);
		if ($validator->passes())
		{
			$account = Auth::user();

			$account->name		 = $input['name'];
			$account->email		 = ($input['email'] !== $existing_email) ? $input['email'] : $existing_email;
			$account->updated_at = new DateTime;

			$account->update();

			return Redirect::back()->with("flash_message", "Profile has been updated.");
		}
		else
		{
			return Redirect::back()->withInput()->withErrors($validator);
		}
	}

	public function getPassword()
	{
		return View::make('frontend.panels.users.password');
	}

	public function postPassword()
	{
		$input = Input::only('password', 'new_password', 'new_password_confirmation');

		$rules = array(
			'password'		 => 'required|min:8',
			'new_password'	 => 'required|min:8|confirmed'
		);

		$validator = Validator::make($input, $rules);

		if ($validator->passes())
		{
			if (Auth::attempt(array('email' => Auth::user()->email, 'password' => $input['password'])))
			{
				$account			 = Auth::user();
				$account->password	 = Hash::make($input['new_password']);
				$account->updated_at = new DateTime;
				$account->update();

				Auth::logout();
				return Redirect::to('login')->with('flash_message', "Password has been changed. Please login using new details.");
			}
			else
			{
				return Redirect::back()->with("flash_error", "Current password is invalid. Please try again.");
			}
		}
		else
		{
			return Redirect::back()->withErrors($validator);
		}
	}

	public function logout()
	{
		Auth::logout();
		return Redirect::to('/');
	}

}
