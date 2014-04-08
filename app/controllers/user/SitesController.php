<?php

namespace user;

class SitesController extends \BaseController
{

	public function __construct()
	{
		parent::__construct();
		\View::share(array("ca" => get_class(), "moduleName" => "Site", "view" => false, "custom_action" => "true"));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->model = new \Site();
		$page		 = \Input::get('page', 1);
		$data		 = $this->getByPage($page, $this->manageViewConfig['limit_per_page'], "account_id", \Auth::user()->id);
		$message	 = '';

		if (!is_array($data) && !is_object($data))
		{
			$message	 = $data;
			$paginator	 = null;
		}
		else
		{
			$paginator = \Paginator::make($data->items, $data->totalItems, $data->limit);
		}

		$output = array(
			'paginator'			 => $paginator,
			'str_message'		 => $message,
			"pageTitle"			 => "Manage Members",
			"table_header"		 => $this->model->manage_table_header,
			"page"				 => $page,
			"custom_action_view" => "frontend.panels.sites.customactionview"
		);

		return \View::make("frontend.panels.manage", $output);
	}

	public function getCreate()
	{
		return \View::make("frontend.panels.sites.form", array("type" => "create", 'pageTitle' => "Add New Site"));
	}

	public function postCreate()
	{
		$input				 = Input::only("name", "url");
		$input['account_id'] = \Auth::user()->id;

		$site		 = new \Site();
		$validator	 = \Validator::make($input, $site->rules);

		if ($validator->passes())
		{
			$salt = uniqid(mt_rand(), true);

			$site->name			 = $input['name'];
			$site->api_key		 = md5($input['url']);
			$site->api_secret	 = md5($input['url'] . $salt);
			$site->account_id	 = $input['account_id'];
			$id					 = $site->save();
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
		$site = \Site::find($id);
		return \View::make("frontend.panels.sites.form", array("site" => $site, "type" => "edit", 'pageTitle' => "Edit Site"));
	}

	public function postEdit($id)
	{
		$site		 = \Site::find($id);
		$input		 = \Input::only("name", "url");
		$validator	 = \Validator::make($input, $site->rules);
		if ($validator->passes())
		{
			$site->name	 = $input['name'];
			$site->url	 = $input['url'];
			$site->update();

			return \Redirect::back()->with("flash_message", "Data successfully updated.");
		}
		else
		{
			return \Redirect::back()->withInput()->withErrors($validator);
		}
	}

	public function postDelete($id)
	{
		\Site::find($id)->delete();
		return \Redirect::back()->with("flash_message", "Site data has been removed.");
	}

	public function getDefault($id)
	{
		$site = \Site::where("id", $id)->where("account_id", \Auth::user()->id)->get()->first();
		if ($site->id)
		{
			\Session::set("active_site_id", $id);
			\Session::set("active_site_name", $site->name);
		}
		return \Redirect::route("dashboard");
	}

}
