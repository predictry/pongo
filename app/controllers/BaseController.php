<?php

class BaseController extends Controller
{

	public $siteInfo		 = array();
	public $manageViewConfig = array();
	public $model			 = null;
	public $active_site_id	 = 0;

	public function __construct()
	{
		$this->siteInfo['siteName']	 = 'Predictry';
		$this->siteInfo['pageTitle'] = '';
		$this->siteInfo['metaDesc']	 = 'Predictry website description';
		$this->siteInfo['metaKeys']	 = 'predictry, recommendation, engine';
		$this->siteInfo['styles']	 = array();
		$this->siteInfo['scripts']	 = array();
		$this->siteInfo['ca']		 = '';

		$this->manageViewConfig['create']			 = true;
		$this->manageViewConfig['edit']				 = true;
		$this->manageViewConfig['view']				 = true;
		$this->manageViewConfig['delete']			 = true;
		$this->manageViewConfig['custom_action']	 = false;
		$this->manageViewConfig['limit_per_page']	 = 5;

		View::share($this->siteInfo);
		View::share($this->manageViewConfig);

		//set default active site id
		if (Session::get("active_site_id") !== null)
		{
			$this->active_site_id = Session::get("active_site_id");
			View::share(array("activeSiteName" => Session::get("active_site_name")));
		}
		else
		{
			$site = Site::where("account_id", Auth::user()->id)->get(array('id', 'name'))->first();

			if ($site->id)
			{
				$this->active_site_id = $site->id;
				Session::set("active_site_id", $site->id);
				Session::set("active_site_name", $site->name);
				View::share(array("activeSiteName" => Session::get("active_site_name")));
			}
			else
				return Redirect::to('logout');
		}
	}

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if (!is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	public function customShare($data)
	{
		foreach ($data as $key => $value)
		{
			$this->siteInfo[$key] = $value;
		}
		View::share($this->siteInfo);
	}

	public function validateApiKey($api_credential)
	{
//		$site = Site::where("api_key", $api_credential['api_key'])->where("secret_key", $api_credential['secret_key'])->get();
		$site = Site::where("api_key", "=", $api_credential['api_key'])
						->where("api_secret", "=", $api_credential['secret_key'])
						->get()->first();

		if (is_object($site))
			$site = $site->toArray();

		if (count($site) > 0 && !empty($site['url']) && ($site['url'] === "http://www.rifkiyandhi.com"))
		{
			return $site['id'];
		}

		return false;
	}

	/**
	 * Get results by page
	 *
	 * @param int $page
	 * @param int $limit
	 * @return StdClass
	 */
	public function getByPage($page = 1, $limit = 10, $column = false, $value = false)
	{
		$results			 = new \stdClass;
		$results->page		 = $page;
		$results->limit		 = $limit;
		$results->totalItems = 0;
		$results->items		 = array();

		if ($column && $value)
		{
			$rows = $this->model->where($column, $value)->skip($limit * ($page - 1))
					->take($limit)
					->get();

			$results->totalItems = $this->model->where($column, $value)->count();
		}
		else
		{
			$rows				 = $this->model->skip($limit * ($page - 1))
					->take($limit)
					->get();
			$results->totalItems = $this->model->count();
		}

		foreach ($rows as $row)
		{
			array_push($results->items, $row);
		}

		if (count($results->items) === 0)
		{
			return "No records found.";
		}

		return $results;
	}

}
