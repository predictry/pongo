<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Jun 4, 2014 4:21:42 PM
 * File         : app/controllers/CartLog.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Controllers\Api;

class CartLogController extends \App\Controllers\ApiBaseController
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
		return "are you looking for something buddy? Better go get your mommy";
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$inputs	 = \Input::only("cart_id", "item_id", "qty", "event");
		$rules	 = array(
			'cart_id'	 => 'required|integer|exists:cart,id',
			'item_id'	 => 'required|exists:items,identifier,site_id,' . $this->site_id,
			'qty'		 => 'required|integer'
		);

		$validator = \Validator::make($inputs, $rules);

		if ($validator->passes())
		{
			$item = \App\Models\Item::where("identifier", $inputs['item_id'])->where("site_id", $this->site_id)->get()->first();

			$cart_log			 = new \App\Models\CartLog();
			$cart_log->cart_id	 = $inputs['cart_id'];
			$cart_log->item_id	 = $item->id;
			$cart_log->qty		 = $inputs['qty'];
			$cart_log->event	 = $inputs['event'];
			$cart_log->save();

			return \Response::json(array("message" => "log successfully added", "status" => "success"), "200");
		}
		else
			return \Response::json(array("message" => $validator->errors()->first(), "status" => "failed"), "200");
	}

}

/* End of file CartLog.php */
/* Location: ./application/controllers/CartLog.php */
