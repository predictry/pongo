<?php

namespace user;

class ItemsController extends \BaseController
{

	public function __construct()
	{
		parent::__construct();
		\View::share(array("ca" => get_class(), "moduleName" => "Item", "create" => false));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->model = new \Item();
		$page		 = \Input::get('page', 1);
		$data		 = $this->getByPage($page, $this->manageViewConfig['limit_per_page'], "site_id", $this->active_site_id);
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
			"str_message"	 => $message,
			"pageTitle"		 => "Manage Items",
			"table_header"	 => $this->model->manage_table_header,
			"page"			 => $page
		);
		return \View::make("frontend.panels.manage", $output);
	}

	public function getEdit($id)
	{
		$item		 = \Item::find($id)->first();
		$activated	 = ($item->active) ? true : false;
		return \View::make("frontend.panels.items.form", array("item" => $item, "type" => "edit", 'pageTitle' => "Edit Item", "activated" => $activated));
	}

	public function postEdit($id)
	{
		$item	 = \Item::find($id);
		$input	 = \Input::only("name", "item_url", "img_url", "active");
		$rules	 = array(
			'name' => $item->rules['name']
		);

		$validator = \Validator::make($input, $rules);

		if ($validator->passes()) // validator for name and email
		{
			$item->name		 = \Input::get("name");
			$item->active	 = \Input::get("active");
			$item->update();
			return \Redirect::route("items")->with("flash_message", "Data successfully updated.");
		}
		else
		{
			return \Redirect::back()->withInput()->withErrors($validator);
		}
	}

	public function getView($id)
	{
		$item		 = \Item::find($id);
		$activated	 = ($item->activte) ? true : false;
		return \View::make("frontend.panels.items.viewmodalcontent", array("item" => $item, "type" => "view", 'pageTitle' => "View Item", "columns" => $item->manage_table_header, "activated" => $activated));
	}

}
