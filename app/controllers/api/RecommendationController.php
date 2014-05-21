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

	private $curl = null;

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$inputs = \Input::only("item_id", "user_id", "session_id", "number_of_results", "algo");

		if (!isset($inputs['algo']))
			$inputs['algo'] = "otherusersalsoviewed";

		if ($this->_isAlgoExists($inputs['algo']))
		{
			$method_algo = $inputs['algo'];
			unset($inputs['algo']);

			$input_reco_data		 = $this->_getEasyrecRecoOption($inputs);
			$easyrec_url_with_method = EASYREC_RESTAPI_URL . $method_algo;

			if ($input_reco_data)
			{
				$this->curl			 = new \Curl();
				$response			 = $this->curl->_simple_call("get", $easyrec_url_with_method, $input_reco_data);
				$recommended_items	 = $this->_extractEasyRecResult(json_decode($response));
				if ($recommended_items)
				{
					return \Response::json(array("recomm" => $recommended_items));
				}
			}
		}
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
				"numberOfResults"	 => $inputs['number_of_results']
			);

			if ($options)
				return array_merge($api_credential, $easyrec_inputs, $options);
			else
				return array_merge($api_credential, $easyrec_inputs);
		}
		else
			return false;
	}

	function _extractEasyRecResult($response)
	{
		$recommended_items = array();
		if (isset($response->recommendeditems) && $response->recommendeditems !== null)
		{
			foreach ($response->recommendeditems as $items)
			{
				if (!is_object($items))
				{

					foreach ($items as $item_result)
					{
						$item = \App\Models\Item::where("identifier", $item_result->id)->get()->first();

						if ($item)
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
				else
				{
					$item_result = $items;
					$item		 = \App\Models\Item::where("identifier", $item_result->id)->get()->first();
					if ($item)
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
//						$item_meta->value	 = 'holder.js/160x180/#EE4054:#FFF';
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

}
