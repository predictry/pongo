<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Aug 21, 2014 3:01:11 PM
 * File         : Gui.php
 * Function     : 
 */
class Gui
{

	static $username, $password, $uri, $uriDomain, $appid, $domain;

	function __construct()
	{
		self::$username = null;
		self::$password = null;
		self::$uri = null;
		self::$uriDomain = null;
	}

	public static function setCredential($username, $password)
	{
		self::$username = $username;
		self::$password = $password;
	}

	public static function setDomainAuth($appid, $domain)
	{
		self::$appid = $appid;
		self::$domain = $domain;
	}

	public static function setUri($uri)
	{
		self::$uri = $uri;
	}

	public static function setResourcesUri($resources)
	{
		$item_resources_uri	 = self::$uri . "{$resources}";
		$domain_auth		 = array(
			"appid"	 => self::$appid,
			"domain" => self::$domain
		);

		$item_resources_uri_with_credential = $item_resources_uri . '?' . http_build_query($domain_auth);
		self::$uriDomain = $item_resources_uri_with_credential;
	}

	public static function setAccess($uri, $credential, $domain_auth)
	{
		if ($uri !== "")
			self::setUri($uri);

		if (count($credential) > 0 && isset($credential['username']) && isset($credential['password']))
			self::setCredential($credential['username'], $credential['password']);

		if (count($domain_auth) > 0 && isset($domain_auth['appid']) && isset($domain_auth['domain']))
			self::setDomainAuth($domain_auth['appid'], $domain_auth['domain']);
	}

	/**
	 * 
	 * @param array $item_data
	 * @param array $credential
	 */
	public static function postItem($id, $item_data, $uri = "", $domain_auth = array(), $credential = array())
	{
		$gui_item_params = array(
			"name", "brand", "model", "description", "tags", "price", "category",
			"subCategory", "dateAdded", "itemURL", "imageURL", "startDate", "endDate", "locations"
		);

		self::setAccess($uri, $credential, $domain_auth);

		$gui_item_data = self::extractData($gui_item_params, $item_data);
		array_push($gui_item_data, array("id" => $id)); //item id from items table

		self::setResourcesUri("items");
		$response = self::send("post", self::$uriDomain, $gui_item_data);
		return $response;
	}

	public static function postUser($id, $user_data, $uri = "", $domain_auth = array(), $credential = array())
	{
		$gui_user_params = array("email");

		self::setAccess($uri, $credential, $domain_auth);
		$gui_user_data = self::extractData($gui_user_params, $user_data);
		array_push($gui_user_data, array("id" => $id)); //item id from items table

		self::setResourcesUri("users");
		$response = self::send("post", self::$uriDomain, $gui_user_data);
		return $response;
	}

	public static function postAction($id, $action_data, $uri = "", $domain_auth = array(), $credential = array())
	{
		$gui_action_params = array(
			"userId", "itemId", "type", "timestamp", "ipAddress",
			"sessionId", "guid", "agent", "quantum"
		);

		self::setAccess($uri, $credential, $domain_auth);
		$gui_item_data = self::extractData($gui_action_params, $action_data);

		array_push($action_data, array("id" => $id)); //action instance id from action_instance table
		array_push($action_data, array("type" => $action_data['name'])); //type is action name

		self::setResourcesUri("users");
		$response = self::send("post", self::$uriDomain, $gui_item_data);
		return $response;
	}

	public static function extractData($allowed_params, $data)
	{
		$new_data = array();
		foreach ($data as $key => $val)
		{
			if (in_array(\Str::camel($key), $allowed_params))
				array_push($new_data, array(\Str::camel($key) => $val));
		}

		return $new_data;
	}

	public static function send($method, $resources_uri, $data)
	{
		$curl = new \Curl();
		$curl->http_login(self::$username, self::$password);
		return $curl->_simple_call($method, $resources_uri, $data);
	}

	/**
	 * Get Recommended Items
	 * 
	 * @param string $type
	 * @param array $fields
	 * @param array $filters
	 * @param string $uri
	 * @param array $domain_auth
	 * @param array $credential
	 * @return object
	 */
	public static function getRecommended($type, $fields = array(), $filters = array(), $uri = "", $domain_auth = array(), $credential = array())
	{
		$gui_reco_data	 = array();
		$filter_params	 = array("limit", "priceFloor", "priceCeiling", "locations", "tags", "category", "subCategory");

		self::setAccess($uri, $credential, $domain_auth);
		$str_fields = (count($fields) > 0) ? implode(",", $fields) : "";

		if ($str_fields !== "")
			array_push($gui_reco_data, array("fields" => $str_fields));

		array_push($gui_reco_data, array("type" => $type));

		self::setResourcesUri("recommend");
		$response = self::send("post", self::$uriDomain, $gui_reco_data);
		return $response;
	}

	//@todo Build standard query language for filtering purpose to get recommended items or any possible request that requires it.
	public static function buildQuery($filters)
	{
		// Sample Data
//		$filters = array(
//			array(
//				"property"	 => "price",
//				"operator"	 => "greater_than",
//				"type"		 => "int",
//				"value"		 => 100
//			),
//			array(
//				"property"	 => "category",
//				"operator"	 => "contain",
//				"type"		 => "string",
//				"value"		 => "masak"
//			)
//		);

		$operator_string_alias = array(
			"greater_than"		 => "gt",
			"less_than"			 => "lt",
			"greater_than_equal" => "gte",
			"less_than_equal"	 => "lte",
			"not_equal"			 => "n_eq",
			"equal"				 => "eq",
			"contain"			 => "ct",
			"not_contain"		 => "n_ct"
		);

		$divider	 = "$";
		$query_str	 = '';

		foreach ($filters as $filter)
		{
			$query_str .= "{$divider}{$filter['property']}"
					. "{$divider}{$operator_string_alias[$filter['operator']]}"
					. "{$divider}{$filter['type']}"
					. "{$divider}{$filter['value']}"
					. "|";
		}

		return substr($query_str, 0, strlen($query_str) - 1);
		//sample result 
		//$price$gt$int$100|$category$ct$string$masak
	}

}
