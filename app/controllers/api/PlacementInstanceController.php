<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Jun 4, 2014 3:51:16 PM
 * File         : app/controllers/PlacementInstance.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Controllers\Api;

class PlacementInstanceController extends \App\Controllers\ApiBaseController
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
		$placement_id	 = \Input::get("placement_id");
		$properties		 = \Input::get("properties");
		$session		 = \Input::get("session");
		$rec_items		 = \Input::get("rec_items");
		$rules			 = array(
			'placement_id'	 => 'required',
			'rec_items'		 => 'required',
			'properties'	 => 'required',
			'sessions'		 => 'required|exists:sessions,session'
		);

		$validator = \Validator::make(array('placement_id' => $placement_id, 'rec_items' => $rec_items, 'properties' => $properties, 'sessions' => $session), $rules);
		if ($validator->passes())
		{
			$placement_instance					 = new \App\Models\PlacementInstance();
			$placement_instance->placement_id	 = $placement_id;

			$obj_session					 = \App\Models\Session::where("session", $session)->get()->first();
			$placement_instance->session_id	 = ($obj_session) ? $obj_session->id : 1;
			$placement_instance->save();

			if ($placement_instance->id)
			{
				//Added Placement Instance Metas
				foreach ($rec_items as $item_id)
				{
					$item = \App\Models\Item::where("identifier", $item_id)->where("site_id", $this->site_id)->get()->first();
					if ($item)
					{
						$placement_instance_item						 = new \App\Models\PlacementInstanceItem();
						$placement_instance_item->placement_instance_id	 = $placement_instance->id;
						$placement_instance_item->item_id				 = $item->id;
						$placement_instance_item->save();
					}
				}

				//Add Metas
				foreach ($properties as $key => $val)
				{
					$placement_instance_meta						 = new \App\Models\PlacementInstanceMeta();
					$placement_instance_meta->placement_instance_id	 = $placement_instance->id;
					$placement_instance_meta->key					 = $key;
					$placement_instance_meta->value					 = $val;
					$placement_instance_meta->save();
				}
			}

			return \Response::json(array("message" => "", "status" => "success", "response" => array("placement_instance_id" => $placement_instance->id)), "200");
		}
		else
		{
			return \Response::json(array("message" => $validator->errors()->first(), "status" => "failed"), "200");
		}
	}

}

/* End of file PlacementInstance.php */
/* Location: ./application/controllers/PlacementInstance.php */
