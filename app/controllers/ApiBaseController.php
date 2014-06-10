<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 18, 2014 5:10:42 PM
 * File         : app/controllers/ApiBaseController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     :
 */

namespace App\Controllers;

use Request,
	App\Models;

class ApiBaseController extends \Controller
{

	public $predictry_server_api_key = null;
	public $site					 = null;
	public $site_id					 = null;
	private $message				 = "";

	public function __construct()
	{
		$this->beforeFilter('@filterRequests');
	}

	/**
	 * Filter the incoming requests.
	 */
	public function filterRequests($route, $request)
	{
		$this->site_id	 = false;
		$api_credential	 = array(
			"tenant_id"	 => "",
			"api_key"	 => "",
			"secret_key" => ""
		);

		if (!empty(\Request::header("X-Predictry-Server-Api-Key")) && !empty(\Request::header("X-Predictry-Server-Tenant-ID")))
		{
			$api_credential['tenant_id'] = \Request::header("X-Predictry-Server-Tenant-ID");
			$api_credential['api_key']	 = \Request::header("X-Predictry-Server-Api-Key");
//			$api_credential['secret_key']	 = \Request::header("X-Predictry-Server-Secret-Key");

			$this->site_id = $this->validateApiKey($api_credential);
		}
		else
		{
			return \Response::json(array("message" => "Auth failed " . $this->message, "status" => "401"), "401");
		}

		if (!$this->site_id)
			return \Response::json(array("message" => "Auth failed " . $this->message, "status" => "401"), "401");
	}

	public function validateApiKey($api_credential)
	{
		$site = \App\Models\Site::where("api_key", $api_credential['api_key'])
						->where("name", $api_credential['tenant_id'])
						->get()->first();

		if (is_object($site))
			$site = $site->toArray();
		else
		{
			$this->message = "[credential hasn't assigned or wrong]";
			return false;
		}

		if (count($site) > 0 && !empty($site['url']))// && (($site['url'] === "http://" . \Request::server("HTTP_ORIGIN")) || $site['url'] === \Request::server("HTTP_ORIGIN")))
		{
			$this->site = $site;
			return $site['id'];
		}
		else
			$this->message = "[unknown site]";

		return false;
	}

	public function getTesting()
	{
		return \Response::json($_SERVER);
	}

}
