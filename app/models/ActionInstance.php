<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 9, 2014 4:29:56 PM
 * File         : app/models/ActionInstance.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

class ActionInstance extends \Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	public $timestamps			 = false;
	protected $table			 = 'action_instances';
	public $manage_table_header	 = array(
		"user_id"			 => "User ID",
		"item_identifier_id" => "Item ID",
		"created"			 => "Date Created"
	);

	public function action()
	{
		return $this->belongsTo("App\Models\Action");
	}

	static function getMostItems($action_id)
	{
		$top_items	 = \App\Models\ActionInstance::select(\DB::raw('count(item_id) as numb, item_id'))->where("action_id", $action_id)->groupBy("item_id")->orderBy("numb", "DESC")->limit(5)->get()->toArray();
		$items		 = array();

		foreach ($top_items as $top)
		{
			$item = Item::find($top['item_id'])->toArray();
			if ($item)
			{
				$item['total']	 = $top['numb'];
				$items[]		 = $item;
			}
		}

		return $items;
	}

	static function getNumberOfSales($add_to_cart_action_id, $buy_action_id, $dt_start, $dt_end)
	{
		$add_to_cart_item_ids	 = ActionInstance::where("action_id", $add_to_cart_action_id)->whereBetween('created', [$dt_start, $dt_end])->get()->lists("item_id");
		if (count($add_to_cart_item_ids) > 0)
			$buy_item_ids			 = ActionInstance::where("action_id", $buy_action_id)->whereIn("item_id", $add_to_cart_item_ids)->whereBetween('created', [$dt_start, $dt_end])->get()->count();
		else
			return 0;
		
		return ($buy_item_ids >= count($add_to_cart_item_ids)) ? count($add_to_cart_item_ids) : $buy_item_ids;
	}

}

/* End of file ActionInstance.php */
/* Location: ./app/models/ActionInstance.php */
