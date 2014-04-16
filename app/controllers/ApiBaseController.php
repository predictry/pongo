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
	public $site_id					 = null;

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
			"api_key"	 => "",
			"secret_key" => ""
		);

		if (!empty(\Request::header("X-Predictry-Server-Api-Key")) && !empty(\Request::header("X-Predictry-Server-Secret-Key")))
		{
			$api_credential['api_key']		 = \Request::header("X-Predictry-Server-Api-Key");
			$api_credential['secret_key']	 = \Request::header("X-Predictry-Server-Secret-Key");
			$this->site_id					 = $this->validateApiKey($api_credential);
		}

		if (!$this->site_id)
			return \Response::json(array("message" => "Auth failed", "status" => "401"), "401");
	}

	public function validateApiKey($api_credential)
	{
		$site = \App\Models\Site::where("api_key", "=", $api_credential['api_key'])
						->where("api_secret", "=", $api_credential['secret_key'])
						->get()->first();

		if (is_object($site))
			$site = $site->toArray();

		if (count($site) > 0 && !empty($site['url']) && (($site['url'] === "http://" . Request::getHost()) || $site['url'] === Request::getHost()))
		{
			return $site['id'];
		}

		return false;
	}

}
