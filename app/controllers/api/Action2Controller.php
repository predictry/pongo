<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Aug 14, 2014 4:21:58 PM
 * File         : app/controllers/Action2Controller.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Controllers\Api;

use Carbon\Carbon;
use Input,
	Response,
	Request,
	Validator;

class Action2Controller extends \App\Controllers\ApiBaseController
{

	protected $response		 = array();
	protected $curl			 = null;
	protected $is_new_item, $is_new_visitor, $is_new_action, $is_new_session, $is_new_browser;
	protected $action_id, $item_id, $visitor_id, $action_instance_id, $session_id, $browser_id;
	protected $action_type	 = "single";
	protected $action_data	 = array();

	public function __construct()
	{
		parent::__construct();
		$this->is_new_item					 = $this->is_new_visitor				 = $this->is_new_action				 = $this->is_new_session				 = $this->is_new_browser				 = false;
		$this->action_id					 = $this->item_id						 = $this->visitor_id					 = $this->action_instance_id			 = $this->session_id					 = $this->browser_id					 = 0;
		$this->predictry_server_api_key		 = Request::header("X-Predictry-Server-Api-Key");
		$this->predictry_server_tenant_id	 = \Request::header("X-Predictry-Server-Tenant-ID");

		$this->response = array(
			"error"			 => false,
			"status"		 => 200,
			"message"		 => "",
			"client_message" => ""
		);

		$this->gui_domain_auth = array(
			'appid'	 => $this->predictry_server_api_key,
			'domain' => $this->predictry_server_tenant_id
		);
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
		$action_validator = \Validator::make(Input::only("action"), array("action" => "required"));
		if ($action_validator->passes())
		{
			$browser_cookie_inputs	 = Input::only("session_id", "browser_id", "user_id");
			$browser_rules			 = array(
				"session_id" => "required",
				"browser_id" => "",
				"user_id"	 => ""
			);

			$rules			 = array_merge($browser_rules, array('items' => 'required|array'));
			$inputs			 = array_merge($browser_cookie_inputs, Input::only("action", "user", "items"));
			$input_validator = \Validator::make($inputs, $rules);
			if ($input_validator->passes())
			{
				//validating user data
				$user_property_rules = array(
					"user_id"	 => "required|alpha_num",
					"email"		 => (isset($inputs['user']['email']) && $inputs['user']['email'] !== "") ? "required|email" : ""
				);

				$user_validator = \Validator::make($inputs['user'], $user_property_rules);
				if ($user_validator->passes())
				{
					$this->browser_id	 = $this->_getBrowserID($inputs['browser_id'], $this->is_new_browser); //get browser_id
					$this->visitor_id	 = $this->_getVisitorID($inputs['user'], $this->is_new_visitor);
					$this->session_id	 = $this->_getSessionID($inputs['session_id'], $this->visitor_id, $this->is_new_session); //get session_id
					$this->_setBrowseSession($this->browser_id, $this->session_id); //set browser session

					if ($inputs['action']['name'] === 'view')
					{
						$items = $inputs['items'];
						foreach ($items as $item)
						{
							$inputs['item'] = $item;
							$this->_proceedSingleAction($inputs['action']['name'], $inputs);
							//@todo send to gui, since we know if the item, user new or not
							//@todo send action to gui
						}
						$response = $this->response;
					}
					else if ($inputs['action']['name'] === 'buy')
					{
						if ($this->_proceedBuyAction($inputs['action']['name'], $inputs))
							$this->http_status = 200;

						$response = $this->response;
					}
				}
				else
					$response = $this->getErrorResponse("errorValidator", "200", "", $user_validator->errors()->first());
			}
			else
				$response = $this->getErrorResponse("errorValidator", "200", "", $input_validator->errors()->first());
		}
		else
			$response = $this->getErrorResponse("errorValidator", "200", "", $action_validator->errors()->first());

		return Response::json($response, $this->http_status);
	}

	function _validateProceedAction($action_type, $action_name, $user_identifier_id, $email, $session_id, $action_data)
	{
		$rules = array(
			"action"		 => "required",
			"user_id"		 => "required",
			"item_id"		 => "required",
			"session_id"	 => "required",
			"description"	 => "required",
			"email"			 => ($email !== "") ? "required|email" : ""
		);

		if ($action_type === "bulk")
			unset($rules['description']);

		$validator = Validator::make(array(
					"action"		 => $action_name,
					"user_id"		 => $user_identifier_id,
					"item_id"		 => $action_data['item_id'],
					"email"			 => $action_data['email'],
					"session_id"	 => $session_id,
					"description"	 => $action_data['description']), $rules);
		return $validator;
	}

	function _proceedSingleAction($action_name, $action_data)
	{
		$items_data = $action_data['item'];

		$item_property_rules = array(
			"item_id"	 => "required|alpha_num",
			"name"		 => "required",
			"price"		 => "required|numeric",
			"img_url"	 => "required|url",
			"item_url"	 => "required|url"
		);

		$item_validator = \Validator::make($items_data, $item_property_rules);

		//validating the item first is important since 
		if ($item_validator->passes())
			$this->item_id	 = $this->_getItemID($items_data, $this->is_new_item);
		else
			$this->response	 = $this->getErrorResponse("errorValidator", "200", "", $item_validator->errors()->first());

		if ($this->item_id)
		{
			$this->action_id = $this->_getActionID(array("name" => $action_name), $this->is_new_action);
			$action_instance = new \App\Models\ActionInstance();

			//process action instance
			$action_instance->action_id	 = $this->action_id;
			$action_instance->item_id	 = $this->item_id;
			$action_instance->session_id = $this->session_id;
			$action_instance->created	 = new Carbon("now");
			$action_instance->save();

			$this->action_instance_id = $action_instance->id;
			$this->_setActionMeta($action_instance->id, $action_data['action']);
		}

		if ($this->response['error'])
			return false;


		$this->action_data = $action_data;
		return true;
	}

	function _proceedBuyAction($action_name, $action_data)
	{
		$items			 = $action_data['items'];
		$this->action_id = $this->_getActionID(array("name" => $action_name), $this->is_new_action);

		$action_properties_without_name = $action_data['action'];
		unset($action_properties_without_name['name']);

		$i = 0;
		foreach ($items as $item)
		{
			$action_properties = array_merge($action_properties_without_name, $item);
			unset($action_properties['item_id']);

			$item_model = \App\Models\Item::where("identifier", $item['item_id'])->where("site_id", $this->site_id)->get()->first();
			if ($item_model)
			{
				$action_instance = new \App\Models\ActionInstance();

				//process action instance
				$action_instance->action_id	 = $this->action_id;
				$action_instance->item_id	 = $item['item_id'];
				$action_instance->session_id = $this->session_id;
				$action_instance->created	 = new Carbon("now");
				$action_instance->save();

				$this->_setActionMeta($action_instance->id, $action_properties);
			}
			else
			{
				$this->response = $this->getErrorResponse("errorValidator", "200", "", "Item not found.");
				break;
			}

			$i++;
		}

		if ($this->response['error'])
			return false;

		return true;
	}

	function _proceedAction($action_name, $user_identifier_id, $email, $session_id, $action_data)
	{
		$item_identifier_id	 = $action_data['item_id'];
		$session			 = $session_id;
		$item_name			 = isset($action_data['description']) ? $action_data['description'] : '';
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

		$action_id = $this->_getActionID($action_data);

		$is_new_user = false;
		$visitor_id	 = $this->_getVisitorID($user_identifier_id, $email, $is_new_user);

		$is_new_item = false;
		$item_id	 = $this->_getItemID($item_data, $is_new_item);

		$is_new_session		 = false;
		$visitor_session_id	 = $this->_getSessionID($visitor_id, $session, $is_new_session);

		$action_instance = new \App\Models\ActionInstance();
		if ($item_id)
		{
			//process action instance
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

//		$this->_sendActionToEngine($call_action_data, $this->site['tenant_engine_url'], 'loke_engine');
		$this->_sendActionToEngine($call_action_data, EASYREC_RESTAPI_URL);
//		
		//GUI RESOURCES RELATED
		$gui_org_credential_access = array(
			'appid'			 => 'LjlLfujcZ1Xwol9RIrdUBA5IJP2byk5e1irzjdEk',
			'organization'	 => 'redmart'
		);

		if ($is_new_user)
		{
			$user_resources_uri					 = GUI_RESTAPI_URL . "users/";
			$user_resources_uri_with_credential	 = $user_resources_uri . '?' . http_build_query($gui_org_credential_access);

			$this->curl->http_login(GUI_HTTP_USERNAME, GUI_HTTP_PASSWORD);
			$response = $this->curl->_simple_call("post", $user_resources_uri_with_credential, array('id' => $user_identifier_id));
		}

		if ($is_new_item)
		{
			$item_resources_uri					 = GUI_RESTAPI_URL . "items/";
			$item_resources_uri_with_credential	 = $item_resources_uri . '?' . http_build_query($gui_org_credential_access);
		}

		$action_resources_uri					 = GUI_RESTAPI_URL . "actions/";
		$action_resources_uri_with_credential	 = $action_resources_uri . '?' . http_build_query($gui_org_credential_access);
		$call_action_data						 = array_merge($call_action_data, array('actionid' => ($action_instance && $action_instance->id) ? $action_instance->id : 0));

		//SEND TO GUI ENGINE
		$response = $this->_sendActionToEngine($call_action_data, $action_resources_uri_with_credential, "gui");

		if (is_object($response) && $response->error !== "")
		{
			return $response;
		}

		return true;
	}

	function _getActionID($action_data, &$is_new)
	{
		$action_id	 = 0;
		//start processing action
		$action		 = \App\Models\Action::where("name", $action_data['name'])->where("site_id", $this->site_id)->first();

		if (!$action)
		{
			$action_id	 = $this->_setAction($action_data);
			$is_new		 = true;
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
		if (is_array($properties) && count($properties) > 0)
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
	}

	function _setBrowseSession($browser_id, $session_id)
	{
		$browser_session = \App\Models\BrowserSession::where("browser_id", $browser_id)->where("session_id", $session_id)->get()->first();
		if ($browser_session)
		{
			$browser_session			 = new \App\Models\BrowserSession();
			$browser_session->browser_id = $browser_id;
			$browser_session->session_id = $session_id;
			$browser_session->save();
		}

		return;
	}

	function _setVisitor($visitor_data)
	{
		$visitor			 = new \App\Models\Visitor();
		$visitor->identifier = $visitor_data['user_id'];
		$visitor->email		 = $visitor_data['email'];
		$visitor->save();

		return $visitor->id;
	}

	function _getVisitorID($user_data, &$is_new = false)
	{
		if (isset($user_data['email']) && $user_data['email'] !== "")
		{
			$visitor = \App\Models\Visitor::where("email", $user_data['email'])->get()->toArray();
			if (!$visitor)
			{
				$is_new = true;
				return $this->_setVisitor($user_data);
			}
			else
			{
				if (count($visitor) > 0)
				{
					//check if the email of visitor match one of the session from spesific site
					//if not means the email is unique for different site
					$temp_visitor_id = 0;
					foreach ($visitor as $v)
					{
						$session = \App\Models\Session::where("site_id", $this->site_id)->where("visitor_id", $v['id'])->get()->first();
						if ($session)
						{
							$temp_visitor_id = $v['id'];
							break;
						}
					}

					$is_new = (!$temp_visitor_id) ? true : false;
					return (!$temp_visitor_id) ? $this->_setVisitor($user_data) : $temp_visitor_id;
				}
			}
			return $visitor->id;
		}
		else
		{
			$visitor = \App\Models\Visitor::where("identifier", $user_data['user_id'])->get()->first();
			if (!$visitor)
				return $this->_setVisitor($user_data);
			return $visitor->id;
		}
	}

	function _getItemID($item_data, &$is_new = false)
	{
		$identifier	 = $item_data['item_id'];
		$item		 = \App\Models\Item::where("identifier", $identifier)->where("site_id", $this->site_id)->first();

		if ($item)
		{
			if (isset($item_data['name']) && $item_data['name'] !== "" && ($item->name !== $item_data['name']))
			{
				$item->name = $item_data['name'];
				$item->update();
			}

			$properties_keys = array_keys($item_data);
			unset($properties_keys['item_id']);

			$item_metas = \App\Models\ItemMeta::where("item_id", $item->id)->get();
			foreach ($item_metas as $meta)
			{
				if (in_array($meta->key, $properties_keys))
				{
					$value	 = $item_data[$meta->key];
					if (is_array($value))
						$value	 = json_encode($item_data[$meta->key]);

					if ($meta->value !== trim($value))
					{
						$meta->value = trim($value);
						$meta->update();
					}
					$index = array_search($meta->key, $properties_keys);
					unset($properties_keys[$index]);
				}
			}

			if (count($properties_keys) > 0)
			{//means have new additional properties
				foreach ($properties_keys as $key)
				{
					$item_meta			 = new \App\Models\ItemMeta();
					$item_meta->key		 = $key;
					$item_meta->value	 = is_array($item_data[$key]) ? json_encode($item_data[$key]) : $item_data[$key];
					$item_meta->item_id	 = $item->id;
					$item_meta->save();
				}
			}

			return $item->id;
		}
		else
		{
			$item				 = new \App\Models\Item();
			$item->identifier	 = $item_data['item_id'];
			$item->name			 = $item_data['name'];
			$item->site_id		 = $this->site_id;
			$item->save();

			if ($item->id)
			{
				foreach ($item_data as $key => $value)
				{
					$item_meta			 = new \App\Models\ItemMeta();
					$item_meta->item_id	 = $item->id;
					$item_meta->key		 = $key;

					if (is_array($value))
						$value = json_encode($value);

					$item_meta->value = $value;
					$item_meta->save();
				}

				$this->is_new_item	 = true;
				$this->item_id		 = $item->id;
				return $item->id;
			}
			else
				return false;
		}
	}

	function _getSessionID($session, $visitor_id = null, &$is_new = false)
	{
		$session_visitor_id	 = 0;
		//start processing visitor
		$visitor_session	 = \App\Models\Session::where("session", $session)->where("site_id", $this->site_id)->get()->first();

		if (!$visitor_session)
		{
			$visitor_session			 = new \App\Models\Session();
			$visitor_session->visitor_id = $visitor_id;
			$visitor_session->site_id	 = $this->site_id;
			$visitor_session->session	 = $session;
			$visitor_session->save();
			$is_new						 = true;

			$session_visitor_id = $visitor_session->id;
		}
		else
			$session_visitor_id = $visitor_session->id;

		return $session_visitor_id;
	}

	function _getBrowserID($browser_id, &$is_new = false)
	{
		$browser = \App\Models\Browser::where("identifier", $browser_id)->get()->first();
		if ($browser)
			return $browser->id;
		else
		{
			$browser			 = new \App\Models\Browser();
			$browser->identifier = $browser_id;
			$browser->save();
			return $browser->id;
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

	private function _sendActionToEngine($action_data, $engine_url, $engine = 'easyrec')
	{
		$this->curl = new \Curl();

		if ($engine === 'loke')
		{
			$new_action_data = array(
				'ii'		 => "setRatings",
				"user"		 => $action_data['userid'],
				"score"		 => $this->_translateActionToRating($action_data['action']),
				"product"	 => $action_data['itemid']
			);
			$response		 = $this->curl->_simple_call("get", $engine_url, $new_action_data);
		}
		else if ($engine === "easyrec")
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
			$response = $this->curl->_simple_call("get", $engine_url, $new_action_data);
		}
		else if ($engine === 'gui')
		{
			$new_action_data = array(
				'type'		 => $action_data['action'],
				'id'		 => $action_data['actionid'],
				'userId'	 => $action_data['userid'],
				'itemId'	 => $action_data['itemid'],
				'sessionId'	 => $action_data['sessionid'],
				'timestamp'	 => time(),
			);

			$this->curl->http_login(GUI_HTTP_USERNAME, GUI_HTTP_PASSWORD);
			$response = $this->curl->_simple_call("post", $engine_url, $new_action_data);
		}

		return $response;
	}

	//@todo POST ITEM TO GUI, WITH ALL PROPERTIES THAT ALREADY DEFINED
	function _postItemToGui($item_data)
	{
		$this->curl->http_login(GUI_HTTP_USERNAME, GUI_HTTP_PASSWORD);
		$gui_item_params = array(
			"name", "brand", "model", "description", "tags", "price", "category",
			"subCategory", "dateAdded", "itemURL", "imageURL", "startDate", "endDate", "locations"
		);

		$gui_item_data = array();

		foreach ($item_data as $key => $val)
		{
			if (in_array(\Str::camel($key), $gui_item_params))
				array_push($gui_item_data, array(\Str::camel($key) => $val));
		}

		$item_resources_uri					 = GUI_RESTAPI_URL . "items/";
		$item_resources_uri_with_credential	 = $item_resources_uri . '?' . http_build_query($this->gui_domain_auth);
		$response							 = $this->curl->_simple_call("post", $item_resources_uri_with_credential, $item_data);
		return $response;
	}

}

/* End of file Action2Controller.php */
/* Location: ./application/controllers/Action2Controller.php */
