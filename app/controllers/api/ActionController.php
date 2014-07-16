<?php

namespace App\Controllers\Api;

use Carbon\Carbon;
use Input,
	Response,
	Request,
	Validator;

define('EASYREC_RESTAPI_URL', 'http://demo.easyrec.org:8080/api/1.0/json/');

class ActionController extends \App\Controllers\ApiBaseController
{

	private $curl = null;

	public function __construct()
	{
		parent::__construct();
		$this->predictry_server_api_key = Request::header("X-Predictry-Server-Api-Key");
	}

	public function index()
	{
		$name = Input::get("name");
		return "gotcha " . $name;
	}

	public function missingMethod($parameters = array())
	{
		return "Missing methods";
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$action_type = \Input::get('action_type');

		if (isset($action_type))
		{
			$action_name		 = Input::get("action");
			$user_identifier_id	 = Input::get("user_id");
			$session_id			 = Input::get("session_id");

			if ($action_type === "single")
			{
				$action_data['item_id']				 = Input::get("item_id");
				$action_data['description']			 = Input::get("description");
				$action_data['action_properties']	 = Input::get("action_properties");
				$action_data['item_properties']		 = Input::get("item_properties");
				$validator							 = $this->_validateProceedAction($action_name, $user_identifier_id, $session_id, $action_data);
				if ($validator->passes())
				{
					$response = $this->_proceedAction($action_name, $user_identifier_id, $session_id, $action_data);
				}
				else
				{
					$response['status']	 = "failed";
					$response['message'] = $validator->errors()->first();
					return Response::json($response, "200");
				}
			}
			else if ($action_type === "bulk")
			{
				$actions = \Input::get("actions");

				foreach ($actions as $act)
				{
					$action_data['item_id']				 = $act['item_id'];
					$action_data['description']			 = $act['description'];
					$action_data['action_properties']	 = isset($act['action_properties']) ? $act['action_properties'] : array();
					$action_data['item_properties']		 = isset($act['item_properties']) ? $act['item_properties'] : array();
					$validator							 = $this->_validateProceedAction($action_name, $user_identifier_id, $session_id, $action_data);
					if ($validator->passes())
					{
						$response = $this->_proceedAction($action_name, $user_identifier_id, $session_id, $action_data);
					}
				}
			}

			$response = array(
				"status"	 => "success",
				"message"	 => ""
			);
		}
		else
		{
			$response['status']	 = "failed";
			$response['message'] = "action type unknown";
		}
		return Response::json($response, "200");
	}

	function _validateProceedAction($action_name, $user_identifier_id, $session_id, $action_data)
	{
		$rules = array(
			"action"	 => "required",
			"user_id"	 => "required",
			"item_id"	 => "required",
			"session_id" => "required"
		);

		$validator = Validator::make(array("action" => $action_name, "user_id" => $user_identifier_id, "item_id" => $action_data['item_id'], "session_id" => $session_id), $rules);
		return $validator;
	}

	function _proceedAction($action_name, $user_identifier_id, $session_id, $action_data)
	{
		$item_identifier_id	 = $action_data['item_id'];
		$session			 = $session_id;
		$item_name			 = $action_data['description'];
		$action_properties	 = $action_data['action_properties'];
		$item_properties	 = $action_data['item_properties'];

		$action_properties	 = isset($action_properties) && ($action_properties !== "null") ? $action_properties : array();
		$item_properties	 = isset($item_properties) && ($item_properties !== "null") ? $item_properties : array();

		$action_data = array(
			"name"			 => $this->_getBuyAlias($action_name),
			"description"	 => null,
			"score"			 => Input::get("score"),
		);

		$item_data = array(
			"name"		 => $item_name,
			"identifier" => $item_identifier_id,
			"properties" => $item_properties
		);

		$action_id			 = $this->_getActionID($action_data);
		$visitor_id			 = $this->_getVisitorID($user_identifier_id);
		$visitor_session_id	 = $this->_getSessionID($visitor_id, $session);
		$item_id			 = $this->_getItemID($item_data);
		if ($item_id)
		{
			//process action instance
			$action_instance			 = new \App\Models\ActionInstance();
			$action_instance->action_id	 = $action_id;
			$action_instance->item_id	 = $item_id;
			$action_instance->session_id = $visitor_session_id;
			$action_instance->created	 = new Carbon("now");
			$action_instance->save();

			$this->_setActionMeta($action_instance->id, $action_properties);
		}

		$call_action_data = array(
			'action'			 => $this->_getBuyAlias($action_name),
			'userid'			 => $user_identifier_id,
			'sessionid'			 => $session,
			'itemid'			 => $item_identifier_id,
			'itemdescription'	 => $item_name,
			'itemurl'			 => isset($item_properties) && ($item_properties !== "null") && isset($item_properties['item_url']) ? $item_properties['item_url'] : '',
			'itemimageurl'		 => isset($item_properties) && ($item_properties !== "null") && isset($item_properties['img_url']) ? $item_properties['img_url'] : ''
		);

//		$this->_callEngine($call_action_data, $this->site['tenant_engine_url'], 'loke_engine');
//		$this->_callEngine($call_action_data, EASYREC_RESTAPI_URL);

		return true;
	}

	function _getActionID($action_data)
	{
		$action_id	 = 0;
		//start processing action
		$action		 = \App\Models\Action::where("name", $action_data['name'])->where("site_id", $this->site_id)->first();

		if (!$action)
		{
			$action_id = $this->_setAction($action_data);
		}
		else
		{
			$action_id = $action->id;
		}

		return $action_id;
	}

	function _setAction($action_data)
	{
		$action				 = new \App\Models\Action();
		$action->name		 = $action_data['name'];
		$action->description = $action_data['description'];
		$action->site_id	 = $this->site_id;
		$action->save();

		if (isset($action_data['score']) && $action->id)
		{
			$action_meta			 = new \App\Models\ActionMeta();
			$action_meta->key		 = "score";
			$action_meta->value		 = $action_data['score'];
			$action_meta->action_id	 = $action->id;
			$action_meta->save();
		}
		return $action->id;
	}

	function _setActionMeta($action_instance_id, $properties)
	{
		foreach ($properties as $key => $val)
		{
			$action_instance_meta						 = new \App\Models\ActionInstanceMeta();
			$action_instance_meta->key					 = $key;
			$action_instance_meta->value				 = $val;
			$action_instance_meta->action_instance_id	 = $action_instance_id;
			$action_instance_meta->save();
		}
	}

	function _getVisitorID($identifier)
	{
		$visitor_id = 0;

		//start processing visitor
		$visitor_data = \App\Models\Visitor::where("identifier", $identifier)->first();
		if (!$visitor_data)
		{
			$visitor			 = new \App\Models\Visitor();
			$visitor->identifier = $identifier;
			$visitor->save();
			$visitor_id			 = $visitor->id;
		}
		else
		{
			$visitor_id = $visitor_data->id;
		}

		return $visitor_id;
	}

	function _getSessionID($visitor_id, $session)
	{
		$session_visitor_id	 = 0;
		//start processing visitor
		$visitor_session	 = \App\Models\Session::where("visitor_id", $visitor_id)->where("session", $session)->where("site_id", $this->site_id)->get()->first();

		if (!$visitor_session)
		{
			$visitor_session			 = new \App\Models\Session();
			$visitor_session->visitor_id = $visitor_id;
			$visitor_session->site_id	 = $this->site_id;
			$visitor_session->session	 = $session;
			$visitor_session->save();

			$session_visitor_id = $visitor_session->id;
		}
		else
		{
			$session_visitor_id = $visitor_session->id;
		}

		return $session_visitor_id;
	}

	function _getItemID($item_data)
	{
		$item = \App\Models\Item::where("identifier", $item_data['identifier'])->where("site_id", $this->site_id)->first();

		$properties = $item_data['properties'];

		if ($item)
		{
			if (isset($item_data['name']) && ($item->name !== $item_data['name']))
			{
				$item->name = $item_data['name'];
				$item->update();
			}
			$properties_keys = array_keys($properties);

			$item_metas = \App\Models\ItemMeta::where("item_id", $item->id)->get();
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
//				else
//					$meta->delete();
			}


			if (count($properties_keys) > 0)
			{//means have new additional properties
				foreach ($properties_keys as $key)
				{
					$item_meta			 = new \App\Models\ItemMeta();
					$item_meta->key		 = $key;
					$item_meta->value	 = $properties[$key];
					$item_meta->item_id	 = $item->id;
					$item_meta->save();
				}
			}


			return $item->id;
		}
		else
		{
			$item				 = new \App\Models\Item();
			$item->identifier	 = $item_data['identifier'];
			$item->name			 = $item_data['name'];
			$item->site_id		 = $this->site_id;
			$item->save();

			if ($item->id)
			{
				foreach ($item_data['properties'] as $key => $value)
				{
					$item_meta			 = new \App\Models\ItemMeta();
					$item_meta->item_id	 = $item->id;
					$item_meta->key		 = $key;
					$item_meta->value	 = $value;
					$item_meta->save();
				}
				return $item->id;
			}
			else
				return false;
		}
	}

	private function _translateActionToRating($str_action)
	{
		$actions = array(
			"view"			 => 1,
			"rate"			 => 2,
			"add_to_cart"	 => 3,
			"buy"			 => 4
		);

		if (key_exists($str_action, $actions))
		{
			return $actions[$str_action];
		}
		else
			return false;
	}

	private function _getBuyAlias($action_name)
	{
		if ($action_name === 'complete_purchase')
			return 'buy';

		return $action_name;
	}

	private function _callEngine($action_data, $engine_url, $engine = 'easyrec')
	{
		$this->curl = new \Curl();

		if ($engine !== 'easyrec')
		{
			$new_action_data = array(
				'ii'		 => "setRatings",
				"user"		 => $action_data['userid'],
				"score"		 => $this->_translateActionToRating($action_data['action']),
				"product"	 => $action_data['itemid']
			);
		}
		else
		{
			$easyrec_default_actions = array("view", "buy");
			$action_name			 = $action_data['action'];
			unset($action_data['action']);

			$new_action_data = array_merge($action_data, array(
				'actiontime' => date('d_m_Y_H_i_s'),
				'itemtype'	 => 'ITEM',
				'apikey'	 => $this->site['api_key'],
				'tenantid'	 => $this->site['name']
			));

			if (!in_array($action_name, $easyrec_default_actions))
			{
				$custom_action = array(
					'actiontype' => $action_name
				);

				$new_action_data = array_merge($custom_action, $new_action_data);
				$engine_url .= "sendaction";
			}
			else
			{
				$engine_url .= $action_name;
			}
		}

		$response = $this->curl->_simple_call("get", $engine_url, $new_action_data);
		return $response;
	}

}
