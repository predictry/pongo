<?php

namespace App\Controllers\User;

use View,
	Input,
	Auth,
	Redirect,
	Session,
	Validator,
	Paginator;

class SitesController extends \App\Controllers\BaseController
{

	public function __construct()
	{
		parent::__construct();
		View::share(array("ca" => get_class(), "moduleName" => "Site", "view" => false, "custom_action" => "true"));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->model = new \App\Models\Site();
		$page		 = Input::get('page', 1);
		$data		 = $this->getByPage($page, $this->manageViewConfig['limit_per_page'], "account_id", Auth::user()->id, 'id', 'ASC');
		$message	 = '';

		if (!is_array($data) && !is_object($data))
		{
			$message	 = $data;
			$paginator	 = null;
		}
		else
		{
			$paginator = Paginator::make($data->items, $data->totalItems, $data->limit);
		}

		$output = array(
			'paginator'			 => $paginator,
			'str_message'		 => $message,
			"pageTitle"			 => "Manage Members",
			"table_header"		 => $this->model->manage_table_header,
			"page"				 => $page,
			"custom_action_view" => "frontend.panels.sites.customactionview"
		);

		return View::make("frontend.panels.manage", $output);
	}

	public function getCreate()
	{
		return View::make("frontend.panels.sites.form", array("type" => "create", 'pageTitle' => "Add New Site"));
	}

	public function postCreate()
	{
		$input				 = Input::only("name", "url");
		$input['account_id'] = Auth::user()->id;

		$site		 = new \App\Models\Site();
		$validator	 = Validator::make($input, $site->rules);

		if ($validator->passes())
		{
			$salt = uniqid(mt_rand(), true);

			$site->name			 = $input['name'];
			$site->api_key		 = md5($input['url']);
			$site->api_secret	 = md5($input['url'] . $salt);
			$site->account_id	 = $input['account_id'];
			$site->url			 = $input['url'];
			$id					 = $site->save();

			//can be migrate to table
			$default_actions = array(
				"view"			 => array("score" => 1),
				"rate"			 => array("score" => 2),
				"add_to_cart"	 => array("score" => 3),
				"buy"			 => array("score" => 4)
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

			if ($id)
				return \Redirect::route('sites')->with("flash_message", "Successfully added new site.");
			else
				return \Redirect::back()->with("flash_error", "Inserting problem. Please try again.");
		}
		else
			return \Redirect::back()->withInput()->withErrors($validator);
	}

	public function getEdit($id)
	{
		$site = \App\Models\Site::find($id);
		return \View::make("frontend.panels.sites.form", array("site" => $site, "type" => "edit", 'pageTitle' => "Edit Site"));
	}

	public function postEdit($id)
	{
		$site		 = \App\Models\Site::find($id);
		$input		 = Input::only("name", "url");
		$validator	 = Validator::make($input, $site->rules);
		if ($validator->passes())
		{
			$site->name	 = $input['name'];
			$site->url	 = $input['url'];
			$site->update();

			return Redirect::back()->with("flash_message", "Data successfully updated.");
		}
		else
		{
			return Redirect::back()->withInput()->withErrors($validator);
		}
	}

	public function postDelete($id)
	{
		\App\Models\Site::find($id)->delete();
		return Redirect::back()->with("flash_message", "Site data has been removed.");
	}

	public function getDefault($id)
	{
		$site = \App\Models\Site::where("id", $id)->where("account_id", Auth::user()->id)->first();
		if ($site->id)
		{
			Session::set("active_site_id", $site->id);
			Session::set("active_site_name", $site->name);
			Session::remove("default_action_view");
		}
		return Redirect::route("dashboard");
	}

}
