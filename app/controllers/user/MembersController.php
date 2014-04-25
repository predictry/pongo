<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 18, 2014 5:10:42 PM
 * File         : app/controllers/MembersController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     :
 */

namespace App\Controllers\User;

use View,
	Input,
	Auth,
	Redirect,
	Hash,
	Validator,
	Paginator;

class MembersController extends \App\Controllers\BaseController
{

	public function __construct()
	{
		parent::__construct();
		View::share(array("ca" => get_class(), "moduleName" => "Member", "view" => false));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->model = new \App\Models\SiteMember();
		$page		 = Input::get('page', 1);
		$data		 = $this->getByPage($page, $this->manageViewConfig['limit_per_page'], "site_id", $this->active_site_id);
		$items		 = array();
		$message	 = '';

		if (is_object($data))
		{
			foreach ($data->items as $member)
			{
				$memberWithProfile		 = \App\Models\Member::find($member->member_id)->detail;
				$memberWithProfile->id	 = $member->member_id;
				array_push($items, $memberWithProfile);
			}

			$paginator = Paginator::make($items, $data->totalItems, $data->limit);
		}
		else
		{
			$message	 = $data;
			$paginator	 = null;
		}

		$member = new \App\Models\Member();

		$output = array(
			'paginator'		 => $paginator,
			"str_message"	 => $message,
			"pageTitle"		 => "Manage Members",
			"table_header"	 => $member->manage_table_header,
			"page"			 => $page
		);

		return View::make("frontend.panels.manage", $output);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreate()
	{
		$this->customShare(array('pageTitle' => "Add New Member"));
		return View::make("frontend.panels.members.form", array("type" => "create"));
	}

	public function postCreate()
	{
		$input = array(
			'name'					 => \Input::get('name'),
			'email'					 => \Input::get('email'),
			'password'				 => \Input::get('password'),
			'password_confirmation'	 => \Input::get('password_confirmation')
		);

		$rules = array(
			'name'					 => 'required',
			'password'				 => 'required|min:8|confirmed',
			'password_confirmation'	 => 'required|min:8',
			'email'					 => 'required|email|unique:accounts'
		);

		$validator = \Validator::make($input, $rules);
		if ($validator->passes())
		{
			$account = new \App\Models\Account;

			$account->name				 = $input['name'];
			$account->email				 = $input['email'];
			$account->password			 = Hash::make($input['password']);
			$account->plan_id			 = 1;
			$account->confirmed			 = 1;
			$account->confirmation_code	 = md5(microtime() . \Config::get('app.key'));
			$account->save();

			if ($account->id)
			{

				$member				 = new \App\Models\Member();
				$member->account_id	 = $account->id;
				$member->save();

				$account_member				 = new \App\Models\SiteMember();
				$account_member->site_id	 = $this->active_site_id;
				$account_member->member_id	 = $member->id;
				$account_member->access		 = "view";

				$account_member->save();

				$notify = \Input::get("notify");

				if ($notify)
				{
					//SEND INVITE MEMBER EMAIL NOTIFICATION
					$email_data = array(
						"fullname"	 => ucwords($input['name']),
						"friendname" => ucwords(Auth::user()->fullname),
						"user_email" => $input['email'],
						"password"	 => $input['password']
					);

					\Mail::send('emails.members.invitenotification', $email_data, function($message) use ($input) {

						$message->to($input['email'], ucwords($input['name']))->subject('Hi, you\'ve just invited!');
					});
				}

				return Redirect::to("members")->with("flash_message", "Successfully added new member.");
			}
			else
				return Redirect::back()->with("flash_error", "Inserting problem. Please try again.");
		}
		else
		{
			return Redirect::back()->withInput()->withErrors($validator);
		}
	}

	public function getEdit($id)
	{
		$member		 = \App\Models\Member::find($id)->detail;
		$member->id	 = $id;
		$this->customShare(array('pageTitle' => "Edit Member"));
		return View::make("frontend.panels.members.form", array("member" => $member, "type" => "edit"));
	}

	public function postEdit($id)
	{
		$member	 = \App\Models\Member::find($id)->detail;
		$input	 = Input::only("name", "email", "password", "password_confirmation");

		$existing_email = $member->email;

		$member_model = new \App\Models\Member();

		$rules = array(
			'name'	 => $member_model->rules['name'],
			"email"	 => ($input['email'] !== $existing_email) ? 'required|email|unique:accounts' : 'required|email'
		);

		$validator = Validator::make($input, $rules);

		if ($validator->passes()) // validator for name and email
		{
			if (isset($input['password']) && $input['password'] !== '') // i think can be change to Input::has('password') guess so
			{
				$input_password	 = Input::only("password", "password_confirmation");
				$rules			 = array(
					'password' => 'required|min:8|confirmed'
				);



				$validator = Validator::make($input_password, $rules);
				if ($validator->passes())
				{
					$member->password	 = Hash::make($input['password']);
					$member->updated_at	 = new \DateTime;
					$member->update();
				}
				else
				{
					return Redirect::back()->withErrors($validator);
				}
			}
			else
			{
				$member->name	 = $input['name'];
				$member->email	 = $input['email'];
			}

			$member->update();
			return Redirect::to("members")->with("flash_message", "Data successfully updated.");
		}
		else
		{
			return Redirect::back()->withInput()->withErrors($validator);
		}
	}

	public function postDelete($id)
	{
		$member = \App\Models\Member::find($id);
		if ($member)
		{
			$account = \App\Models\Account::find($member->account_id);
			\App\Models\SiteMember::where("site_id", $this->active_site_id)->where("member_id", $id)->delete();
			$member->delete();
			$account->delete();
		}
		return Redirect::back()->with("flash_message", "Member data has been removed.");
	}

}
