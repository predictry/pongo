<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : May 26, 2014 4:59:24 PM
 * File         : app/controllers/ItemActivationController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Controllers\Api;

class ItemActivationController extends \App\Controllers\ApiBaseController
{

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return "are you looking for something buddy?";
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$item_id	 = \Input::get("item_id");
		$activated	 = \Input::get("activated");

		$rules = array(
			'item_id'	 => 'required',
			'activated'	 => 'required|in:yes,no'
		);

		$validator = \Validator::make(array('item_id' => $item_id, 'activated' => $activated), $rules, array('in' => 'The :attribute field either yes or no.'));

		if ($validator->passes())
		{
			$item = \App\Models\Item::where("identifier", $item_id)->where("site_id", $this->site_id)->get()->first();
			if ($item)
			{
				$activated		 = ($activated === 'yes') ? true : false;
				$item->active	 = $activated;
				$item->update();

				return \Response::json(array("message" => "Item active status updated", "status" => "success"), "200");
			}
			else
			{
				return \Response::json(array("message" => "Item not found", "status" => "failed"), "202");
			}
		}
		else
		{
			return \Response::json(array("message" => $validator->errors()->first(), "status" => "failed"), "200");
		}
	}

}

/* End of file ItemActivationController.php */
/* Location: ./application/controllers/ItemActivationController.php */
