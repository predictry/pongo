<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Jul 1, 2014 10:24:02 AM
 * File         : app/controllers/TokenAuthenticationController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Controllers;

class TokenAuthenticationController extends BaseController
{

	private $curl					 = null;
	private $tictail_client_id		 = "clientid_uoXdug21LOW8yEOqa99I76L56E7SDh";
	private $tictail_client_secret	 = "clientsecret_38pUs0emy77G1LrkbrVl7Yu4L0glk45I3tkXDXPX";
	private $tictail_auth_url		 = "https://tictail.com/oauth/token";
	private $access_token			 = "";
	private $token_expire			 = 0;

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$code = \Input::get("code");

		if ($code)
		{
			$this->curl	 = new \Curl();
			$authdata	 = array(
				'client_id'		 => $this->tictail_client_id,
				'client_secret'	 => $this->tictail_client_secret,
				'code'			 => $code,
				'grant_type'	 => 'authorization_code'
			);

			$response = $this->curl->_simple_call("post", $this->tictail_auth_url, $authdata);

			if ($response)
			{
				$response = json_decode($response);

				$this->access_token	 = $response->access_token;
				$this->token_expire	 = $response->expires_in;

				$acc = \App\Models\Account::where("email", $response->store->storekeeper_email)->get()->first();
				if (!$acc)
					$acc = $this->_getAccountStatus($response);

				$site = $this->_getSiteStatus($response->store->url, $acc->id);

				\Auth::loginUsingId($acc->id);
				\Session::set("role", "admin");
				\Session::set("active_site_name", $site->name);
				\Session::set("role", "admin");
				\Session::set("client", "tictail");

				return \Redirect::to("home");
			}else
			{
				return \Redirect::to("https://tictail.com/oauth/authorize?response_type={$code}&client_id={$this->tictail_client_id}&redirect_uri=http://dashboard.predictry.dev/tokenauth/");
			}
		}
	}

	function _getAccountStatus($obj)
	{
		$input = array(
			'name'					 => str_replace(strrchr($obj->store->storekeeper_email, '@'), '', $obj->store->storekeeper_email),
			'email'					 => $obj->store->storekeeper_email,
			'password'				 => $this->access_token,
			'password_confirmation'	 => $this->access_token
		);

		$rules = array(
			'name'					 => 'required',
			'email'					 => 'required|email|unique:accounts',
			'password'				 => 'required|min:8|confirmed',
			'password_confirmation'	 => 'required|min:8'
		);

		$validator = \Validator::make($input, $rules);
		if ($validator->passes())
		{
			$account = new \App\Models\Account();

			$account->name				 = $input['name'];
			$account->email				 = $input['email'];
			$account->password			 = \Hash::make($input['password']);
			$account->plan_id			 = 1;
			$account->confirmed			 = 1;
			$account->confirmation_code	 = md5(microtime() . \Config::get('app.key'));

			$account->save();

			//SEND VERIFICATION EMAIL
			$email_data = array("fullname" => ucwords($input['name']));
			\Mail::send('emails.auth.accountconfirmation', $email_data, function($message) use ($input) {
				$message->to($input['email'], ucwords($input['name']))->subject('Welcome!');
			});

			return $account;
		}
		else
			return \Redirect::to('register')->withInput()->withErrors($validator);
	}

	function _getSiteStatus($url, $account_id)
	{
		//check site
		$site = \App\Models\Site::where("url", $url)->where("account_id", $account_id)->get()->first();
		if (!$site)
		{
			$site = new \App\Models\Site();

			$parsedUrl	 = parse_url($url);
			$host		 = explode('.', $parsedUrl['host']);
			$subdomain	 = $host[0];

			$site->name			 = strtoupper($subdomain);
			$site->api_key		 = md5($url . uniqid(mt_rand(), true));
			$site->api_secret	 = md5($url . uniqid(mt_rand(), true));
			$site->account_id	 = $account_id;
			$site->url			 = $url;
			$site->save();

			//can be migrate to table
			$default_actions = array(
				"view"				 => array("score" => 1),
				"rate"				 => array("score" => 2),
				"add_to_cart"		 => array("score" => 3),
				"buy"				 => array("score" => 4),
				"started_checkout"	 => array("score" => 5),
				"started_payment"	 => array("score" => 6),
				"complete_purchase"	 => array("score" => 7)
			);

			//set default action types for the site
			foreach ($default_actions as $key => $arr)
			{
				$action				 = new \App\Models\Action();
				$action->name		 = $key;
				$action->description = null;
				$action->site_id	 = $site->id;
				$action->save();

				foreach ($arr as $key2 => $val)
				{
					$action_meta			 = new \App\Models\ActionMeta();
					$action_meta->key		 = $key2;
					$action_meta->value		 = $val;
					$action_meta->action_id	 = $action->id;
					$action_meta->save();
				}
			}

			$default_funel_preference				 = new \App\Models\FunelPreference();
			$default_funel_preference->site_id		 = $site->id;
			$default_funel_preference->name			 = "Default";
			$default_funel_preference->is_default	 = true;
			$default_funel_preference->save();
		}

		return $site;
	}

}

/* End of file TokenAuthenticationController.php */
/* Location: ./application/controllers/TokenAuthenticationController.php */
