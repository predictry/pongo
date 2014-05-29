<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : May 27, 2014 10:49:30 AM
 * File         : app/controllers/ItemController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Controllers\Api;

class ItemController extends \App\Controllers\ApiBaseController
{

	function __construct()
	{
		parent::__construct();
	}

	public function store()
	{
		$item_id	 = \Input::get("item_id");
		$description = trim(\Input::get("description"));
		$properties	 = \Input::get("properties");

		$rules = array(
			'item_id' => 'required'
		);

		$validator = \Validator::make(array('item_id' => $item_id, 'description' => $description, 'properties' => $properties), $rules);

		if ($validator->passes())
		{
			$item = \App\Models\Item::where("identifier", $item_id)->get()->first();

			if ($item)
			{
				if (isset($description) && ($item->name !== $description))
				{
					$item->name = $description;
					$item->update();
				}

				$properties_keys = array_keys($properties);

				$item_metas = \App\Models\Itemmeta::where("item_id", $item->id)->get();
				foreach ($item_metas as $meta)
				{
					if (in_array($meta->key, $properties_keys))
					{
						if ($meta->value !== trim($properties[$meta->key]))
						{
							$meta->value = trim($properties[$meta->key]);
							$meta->update();
						}
						$index = array_search($meta->key, $properties_keys);
						unset($properties_keys[$index]);
					}
					else
						$meta->delete();
				}


				if (count($properties_keys) > 0)
				{//means have new additional properties
					foreach ($properties_keys as $key)
					{
						$item_meta			 = new \App\Models\Itemmeta();
						$item_meta->key		 = $key;
						$item_meta->value	 = $properties[$key];
						$item_meta->item_id	 = $item->id;
						$item_meta->save();
					}
				}
				
				return \Response::json(array("status" => "success", "message" => "Item successfully updated"), "200");
			}
			else
				return \Response::json(array('status' => 'failed', 'message' => 'Item not found'), "202");
		}
		else
			return \Response::json(array("status" => "failed", "message" => $validator->errors()->first()), "400");
	}

}

/* End of file ItemController.php */
/* Location: ./application/controllers/ItemController.php */
