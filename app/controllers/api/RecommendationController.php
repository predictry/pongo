<?php

namespace App\Controllers\Api;

define('LOKE_RESTAPI_URL', 'http://95.85.48.155:8080/');
define('EASYREC_RESTAPI_URL', 'http://demo.easyrec.org:8080/api/1.0/json/');

/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 19, 2014 12:31:53 PM
 * File         : api\RecommendationController.php
 * Function     : 
 */
class RecommendationController extends \App\Controllers\ApiBaseController
{

	private $curl				 = null;
	private $placement_id		 = null;
	private $operator_types		 = array();
	private $number_of_results	 = 10;

	function __construct()
	{
		parent::__construct();
		$this->operator_types = array(
			'contain'			 => 'Contains',
			'equal'				 => 'Equals',
			'not_equal'			 => 'Not Equals',
			'is_set'			 => 'Is Set',
			'is_not_set'		 => 'Is Not Set',
			'greater_than'		 => 'Greater Than',
			'greater_than_equal' => 'Greater Than or Equal',
			'less_than'			 => 'Less Than',
			'less_than_equal'	 => 'Less Than or Equal'
		);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$inputs				 = \Input::only("item_id", "user_id", "session_id", "algo", "placement_id");
		$this->placement_id	 = $inputs['placement_id'];

		if (!isset($inputs['algo']))
			$inputs['algo'] = "otherusersalsoviewed";

		if ($this->_isAlgoExists($inputs['algo']))
		{
			$method_algo = $inputs['algo'];
			unset($inputs['algo']);

			$input_reco_data		 = $this->_getEasyrecRecoOption($inputs);
			$easyrec_url_with_method = EASYREC_RESTAPI_URL . $method_algo;

			if (!is_object($input_reco_data))
			{
				$this->curl			 = new \Curl();
				$response			 = $this->curl->_simple_call("get", $easyrec_url_with_method, $input_reco_data);
				$recommended_items	 = $this->_extractEasyRecResult(json_decode($response));
				if ($recommended_items)
				{
					$rec_items = array_fetch($recommended_items, 'alias_id'); // item_id = alias_id
					return \Response::json(array("status" => "success", "recomm" => $recommended_items, "placement_instance_id" => $this->_setPlacementInstance($rec_items, $inputs['session_id'])));
				}
			}
			else
				return \Response::json(array("status" => "failed", "message" => $input_reco_data->errors()->first()), "400");

			return \Response::json(array('status' => 'success', 'message' => 'no results'), "200");
		}
		return \Response::json(array('status' => 'failed', 'message' => 'something wrong'), "400");
	}

	function _setPlacementInstance($rec_items, $session_id)
	{
		$placement_instance					 = new \App\Models\PlacementInstance();
		$placement_instance->placement_id	 = $this->placement_id;

		$obj_session					 = \App\Models\Session::where("session", $session_id)->get()->first();
		$placement_instance->session_id	 = ($obj_session) ? $obj_session->id : 1;
		$placement_instance->save();

		if ($placement_instance->id)
		{
			//Added Placement Instance Metas
			foreach ($rec_items as $item_id)
			{
				$placement_instance_item						 = new \App\Models\PlacementInstanceItem();
				$placement_instance_item->placement_instance_id	 = $placement_instance->id;
				$placement_instance_item->item_id				 = $item_id;
				$placement_instance_item->save();
			}
		}

		return $placement_instance->id;
	}

	function _isAlgoExists($algo)
	{
		$available_algos = array("otherusersalsoviewed", "otherusersalsobought", "itemsratedgoodbyotherusers", "recommendationsforuser", "relateditems");
		return in_array($algo, $available_algos) ? $algo : $available_algos[0];
	}

	function _getEasyrecRecoOption($inputs, $options = false)
	{
		$rules = array(
			"item_id"	 => "required",
			"user_id"	 => "required",
			"session_id" => "required"
		);

		$api_credential['tenantid']	 = \Request::header("X-Predictry-Server-Tenant-ID");
		$api_credential['apikey']	 = \Request::header("X-Predictry-Server-Api-Key");

		$validator = \Validator::make($inputs, $rules);

		if ($validator->passes())
		{
			$easyrec_inputs = array(
				"itemid"			 => $inputs['item_id'],
				"userid"			 => $inputs['user_id'],
				"numberOfResults"	 => $this->number_of_results
			);

			if ($options)
				return array_merge($api_credential, $easyrec_inputs, $options);
			else
				return array_merge($api_credential, $easyrec_inputs);
		}
		else
			return $validator;
	}

	function _extractEasyRecResult($response)
	{
		$recommended_items	 = array();
		$placement			 = null;

		if (isset($this->placement_id))
			$placement = \App\Models\Placement::where("id", $this->placement_id)->where("site_id", $this->site_id)->get()->first();

		if (isset($response->recommendeditems) && $response->recommendeditems !== null)
		{
			foreach ($response->recommendeditems as $items)
			{
				if (!is_object($items))
				{
					foreach ($items as $item_result)
					{
						$item = \App\Models\Item::where("identifier", $item_result->id)->get()->first();

						if ($item && $item->active && (!isset($placement) || $this->_isAllowedBasedOnPropertiesFilter($item)))
						{
							$item_reco = array(
								"id"				 => $item->identifier,
								"alias_id"			 => $item->id,
								"description"		 => $item->name,
								"created_at"		 => $item->created_at->toDateTimeString(),
								"item_properties"	 => $this->_getRecoItemProperties($item)
							);

							array_push($recommended_items, $item_reco);
						}
					}
				}
				else
				{
					$item_result = $items;
					$item		 = \App\Models\Item::where("identifier", $item_result->id)->get()->first();
					if ($item && ($this->_isAllowedBasedOnPropertiesFilter($item) || $this->placement_id === null))
					{
						$item_reco = array(
							"id"				 => $item->identifier,
							"description"		 => $item->name,
							"created_at"		 => $item->created_at->toDateTimeString(),
							"item_properties"	 => $this->_getRecoItemProperties($item)
						);

						array_push($recommended_items, $item_reco);
					}
				}
			}
		}
		return $recommended_items;
	}

	function _getRecoItemProperties($item)
	{
		$item_properties = array();
		$item_metas		 = \App\Models\Itemmeta::where("item_id", $item->id)->get();
		if ($item_metas)
		{
			foreach ($item_metas as $item_meta)
			{
				if ($item_meta->key === "img_url")
				{
					$imageUrl	 = $item_meta->value;
					if (!(strpos($imageUrl, "http") !== false))
						$imageUrl	 = 'https:' . $item_meta->value;

					if (!$this->_is_url_exist($imageUrl))
					{
						//$item_meta->value	 = 'holder.js/160x180/#EE4054:#FFF';
						$item_meta->value = "https://s3-ap-southeast-1.amazonaws.com/media.redmart.com/newmedia/460x/coming_soon.jpg";
					}
				}

				$item_properties[$item_meta->key] = $item_meta->value;
			}
		}

		return $item_properties;
	}

	function _is_url_exist($url)
	{
		$ch		 = curl_init($url);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_exec($ch);
		$code	 = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($code == 200)
		{
			$status = true;
		}
		else
		{
			$status = false;
		}
		curl_close($ch);
		return $status;
	}

	function _isAllowedBasedOnPropertiesFilter($item)
	{
		$placement_filters	 = \App\Models\PlacementFilter::where("placement_id", $this->placement_id)->get()->first();
		$item_metas			 = \App\Models\Itemmeta::where("item_id", $item->id)->get()->lists("value", "key");
		$bool				 = true;

		if (!(is_array($item_metas) && count($item_metas) > 0))
			return false;

		if ($placement_filters && $placement_filters->active === 'activated')
		{
			$filter_metas = \App\Models\Filtermeta::where("filter_id", $placement_filters->filter_id)->get();
			foreach ($filter_metas as $meta)
			{
				if (isset($item_metas["{$meta->property}"]) && array_key_exists($meta->operator, $this->operator_types))
				{
					$item_property_value = $item_metas["{$meta->property}"];

					switch ($meta->operator)
					{
						case "contain":
							$bool = \Str::contains($meta->value, $item_property_value);
							break;

						case "equal":
							$bool = ($meta->value === $item_property_value) ? true : false;
							break;

						case "not_equal":
							$bool = ($meta->value !== $item_property_value) ? true : false;
							break;

						case "greater_than":
							$bool = (is_numeric($item_property_value)) ? ($item_property_value > $meta->value) : false;
							break;

						case "greater_than_equal":
							$bool = (is_numeric($item_property_value)) ? ($item_property_value >= $meta->value) : false;
							break;

						case "less_than":
							$bool = (is_numeric($item_property_value)) ? ($item_property_value < $meta->value) : false;
							break;

						case "less_than_equal":
							$bool = (is_numeric($item_property_value)) ? ($item_property_value <= $meta->value) : false;
							break;

						default:
							$bool = true;
							break;
					}
				}

				if (!$bool)
					return false;
			}
		}
		return $bool;
	}

}
