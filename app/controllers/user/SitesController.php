<?php

namespace user;

class SitesController extends \BaseController
{

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function __construct()
	{
		parent::__construct();
		\View::share(array("ca" => get_class(), "moduleName" => "Site"));
	}

	public function index()
	{
		$this->model = new \Site();
		$page		 = \Input::get('page', 1);
		$data		 = $this->getByPage($page);
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
			'paginator'		 => $paginator,
			'str_message'	 => $message,
			"pageTitle"		 => "Manage Members",
			"table_header"	 => $this->model->manage_table_header,
			"page"			 => $page
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

	/**
	 * Get results by page
	 *
	 * @param int $page
	 * @param int $limit
	 * @return StdClass
	 */
	protected function getByPage($page = 1, $limit = 5)
	{
		$results			 = new \stdClass;
		$results->page		 = $page;
		$results->limit		 = $limit;
		$results->totalItems = 0;
		$results->items		 = array();

		$this->model = new \Site();

		$rows = $this->model->where("account_id", \Auth::user()->id)->skip($limit * ($page - 1))
				->take($limit)
				->get();

		$results->totalItems = $this->model->where("account_id", \Auth::user()->id)->count();

		foreach ($rows as $row)
		{
			array_push($results->items, $row);
		}

		if (count($results->items) === 0)
		{
			return "No member records found.";
		}

		return $results;
	}

}
