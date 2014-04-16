<?php

namespace App\Controllers\Api;

use Carbon\Carbon;
use Input,
	Response,
	Request,
	Validator;

class ActionController extends \App\Controllers\ApiBaseController
{

	public function __construct()
	{
		parent::__construct();
		$this->predictry_server_api_key = Request::header("X-Predictry-Server-Api-Key");
	}

	public function index()
	{
		return "INDEX";
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$response = array(
			"status"	 => "success",
			"message"	 => ""
		);

		$action_name		 = Input::get("action");
		$user_identifier_id	 = Input::get("user_id");
		$item_identifier_id	 = Input::get("item_id");
		$session			 = Input::get("session_id");
		$item_name			 = Input::get("description");
		$action_properties	 = Input::get("action_properties");
		$item_properties	 = Input::get("item_properties");

		$action_properties	 = isset($action_properties) ? $action_properties : array();
		$item_properties	 = isset($item_properties) ? $item_properties : array();

		$rules = array(
			"action"	 => "required",
			"user_id"	 => "required",
			"item_id"	 => "required",
			"session_id" => "required"
		);

		$validator = Validator::make(array("action" => $action_name, "user_id" => $user_identifier_id, "item_id" => $item_identifier_id, "session_id" => $session), $rules);
		if ($validator->passes())
		{
			$action_data = array(
				"name"			 => $action_name,
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
		}
		else
		{
			$response['status']	 = "failed";
			$response['message'] = $validator->errors()->first();
			return Response::json($response);
		}

		return Response::json($response);
	}

	function _getActionID($action_data)
	{
		$action_id	 = 0;
		//start processing action
		$action		 = \App\Models\Action::where("name", strtolower($action_data['name']))->where("site_id", $this->site_id)->first();

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
		$visitor_session_id	 = 0;
		//start processing visitor
		$visitor_data		 = \App\Models\Session::where("visitor_id", $visitor_id)->where("session", $session)->get()->first();
		if (!$visitor_data)
		{
			$visitor_session			 = new \App\Models\Session();
			$visitor_session->visitor_id = $visitor_id;
			$visitor_session->site_id	 = $this->site_id;
			$visitor_session->session	 = $session;
			$visitor_session->save();

			$visitor_session_id = $visitor_session->id;
		}
		else
		{
			$visitor_session_id = $visitor_data->id;
		}
		return $visitor_session_id;
	}

	function _getItemID($item_data)
	{
		$item = \App\Models\Item::where("identifier", $item_data['identifier'])->first();

		if ($item)
			return $item->id;
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
					$item_meta			 = new \App\Models\Itemmeta();
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

	public function missingMethod($parameters = array())
	{
		return "Missing methods";
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

}
