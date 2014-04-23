<?php

namespace App\Controllers\Api;

define('LOKE_RESTAPI_URL', 'http://95.85.48.155:8080/'); //movie db

/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 19, 2014 12:31:53 PM
 * File         : api\RecommendationController.php
 * Function     : 
 */
class RecommendationController extends \App\Controllers\ApiBaseController
{

	private $curl = null;

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

		$item_id	 = \Input::get("item_id");
//		$user_id			 = \Input::get("user_id");
//		$session_id			 = \Input::get("session_id");
//		$number_of_results	 = \Input::get("number_of_results");
		$this->curl	 = new \Curl();
		$response	 = $this->curl->_simple_call("get", LOKE_RESTAPI_URL, array("ii" => "getSimilar", "productSimilar" => $item_id));
		return \Response::json(json_decode($response));
	}

}
