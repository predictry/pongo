<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Jun 4, 2014 3:51:16 PM
 * File         : app/controllers/api/WidgetInstanceController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Controllers\Api;

class WidgetInstanceController extends \App\Controllers\ApiBaseController
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
		$widget_id	 = \Input::get("widget_id");
		$properties	 = \Input::get("properties");
		$session	 = \Input::get("session");
		$rec_items	 = \Input::get("rec_items");
		$rules		 = array(
			'widget_id'	 => 'required',
			'rec_items'	 => 'required',
			'properties' => 'required',
			'sessions'	 => 'required|exists:sessions,session'
		);

		$validator = \Validator::make(array('widget_id' => $widget_id, 'rec_items' => $rec_items, 'properties' => $properties, 'sessions' => $session), $rules);
		if ($validator->passes())
		{
			$widget_instance			 = new \App\Models\WidgetInstance();
			$widget_instance->widget_id	 = $widget_id;

			$obj_session				 = \App\Models\Session::where("session", $session)->get()->first();
			$widget_instance->session_id = ($obj_session) ? $obj_session->id : 1;
			$widget_instance->save();

			if ($widget_instance->id)
			{
				//Added Widget Instance Metas
				foreach ($rec_items as $item_id)
				{
					$item = \App\Models\Item::where("identifier", $item_id)->where("site_id", $this->site_id)->get()->first();
					if ($item)
					{
						$widget_instance_item						 = new \App\Models\WidgetInstanceItem();
						$widget_instance_item->widget_instance_id	 = $widget_instance->id;
						$widget_instance_item->item_id				 = $item->id;
						$widget_instance_item->save();
					}
				}

				//Add Metas
				foreach ($properties as $key => $val)
				{
					$widget_instance_meta						 = new \App\Models\WidgetInstanceMeta();
					$widget_instance_meta->widget_instance_id	 = $widget_instance->id;
					$widget_instance_meta->key					 = $key;
					$widget_instance_meta->value				 = $val;
					$widget_instance_meta->save();
				}
			}

			return \Response::json(array("message" => "", "status" => "success", "response" => array("widget_instance_id" => $widget_instance->id)), "200");
		}
		else
		{
			return \Response::json(array("message" => $validator->errors()->first(), "status" => "failed"), "200");
		}
	}

}

/* End of file WidgetInstanceController.php */
/* Location: ./application/controllers/api/WidgetInstanceController.php */
