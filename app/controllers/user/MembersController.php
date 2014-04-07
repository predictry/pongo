<?php

namespace user;

class MembersController extends \BaseController
{

	protected $table_header = array();

	public function __construct()
	{
		parent::__construct();
		\View::share(array("ca" => get_class(), "moduleName" => "Member"));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
//		$account_members = \AccountMember::where("account_id", \Auth::user()->id)->get();
//		$queries		 = \DB::getQueryLog();

		$page	 = \Input::get('page', 1);
		$data	 = $this->getByPage($page, 5);
		$items	 = array();

		foreach ($data->items as $member)
		{
			$memberWithProfile = \AccountMember::find($member->member_id)->profile;
			array_push($items, $memberWithProfile);
		}

		$paginator	 = \Paginator::make($items, $data->totalItems, 5);
		$member		 = new \Member();
		$message	 = '';

		if (count($items) === 0)
		{
			$message = "No member records found.";
		}

		$output = array(
			'paginator'		 => $paginator,
			"message"		 => $message,
			"pageTitle"		 => "Manage Members",
			"table_header"	 => $member->manage_table_header,
			"page"			 => $page
		);

		return \View::make("frontend.panels.manage", $output);
	}

	/**
	 * Get results by page
	 *
	 * @param int $page
	 * @param int $limit
	 * @return StdClass
	 */
	public function getByPage($page = 1, $limit = 10)
	{
		$results			 = new \stdClass;
		$results->page		 = $page;
		$results->limit		 = $limit;
		$results->totalItems = 0;
		$results->items		 = array();

		$rows = \Accountmember::where("account_id", \Auth::user()->id)->skip($limit * ($page - 1))
				->take($limit)
				->get();

		$results->totalItems = \Accountmember::count();
		foreach ($rows as $row)
		{
			array_push($results->items, $row);
		}

		return $results;
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreate()
	{
		$this->customShare(array('pageTitle' => "Add New Member"));
		return \View::make("frontend.panels.members.form", array("type" => "create"));
	}

	public function postCreate()
	{
		$input		 = \Input::only("name", "email", "password", "password_confirmation", "notify");
		$member		 = new \Member();
		$validator	 = \Validator::make($input, $member->rules);

		if ($validator->passes())
		{
			$member->name			 = $input['name'];
			$member->email			 = $input['email'];
			$member->password_hash	 = \Hash::make($input['password']);
			$member->password_salt	 = "TEST";
			$id						 = $member->save();

			if ($id)
			{
				$account_member				 = new \AccountMember();
				$account_member->account_id	 = \Auth::user()->id;
				$account_member->member_id	 = $member->id;

				$account_member->save();
				return \Redirect::back()->with("flash_message", "Successfully added new member.");
			}
			else
				return \Redirect::back()->with("flash_error", "Inserting problem. Please try again.");
		}
		else
		{
			return \Redirect::back()->withInput()->withErrors($validator);
		}
	}

	public function getEdit($id)
	{
		$member = \AccountMember::find($id)->profile;
		$this->customShare(array('pageTitle' => "Edit Member"));
		return \View::make("frontend.panels.members.form", array("member" => $member, "type" => "edit"));
	}

	public function postEdit($id)
	{
		$member	 = \Member::find($id);
		$input	 = \Input::only("name", "email", "password", "password_confirmation");

		$existing_email = $member->email;

		$rules = array(
			'name'	 => $member->rules['name'],
			"email"	 => ($input['email'] !== $existing_email) ? 'required|email|unique:accounts|unique:members' : 'required|email'
		);

		$validator = \Validator::make($input, $rules);

		if ($validator->passes()) // validator for name and email
		{
			if (isset($input['password']) && $input['password'] !== '')
			{
				$validator = \Validator::make($input, array('password' => $member->rules['password']));

				if ($validator->passes()) // if password is not empty then validate
				{
					$member->name			 = $input['name'];
					$member->email			 = $input['email'];
					$member->password_hash	 = \Hash::make($input['password']);
					$member->password_salt	 = "TEST";
				}
				else
				{
					return \Redirect::back()->withInput()->withErrors($validator);
				}
			}
			else
			{
				$member->name	 = $input['name'];
				$member->email	 = $input['email'];
			}

			$member->update();
			return \Redirect::back()->with("flash_message", "Data successfully updated.");
		}
		else
		{
			return \Redirect::back()->withInput()->withErrors($validator);
		}
	}

	public function postDelete($id)
	{
		\Member::find($id)->delete();
		\AccountMember::where("account_id", \Auth::user()->id)->where("member_id", $id)->delete();
		return \Redirect::back()->with("flash_message", "Member data has been removed.");
	}

}
