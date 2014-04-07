<?php

namespace api;

class ActionController extends \BaseController
{

	protected $predictry_server_api_key	 = null;
	protected $site_id					 = null;

	public function __construct()
	{
		$this->beforeFilter('@filterRequests');
		parent::__construct();
		$this->predictry_server_api_key = \Request::header("X-Predictry-Server-Api-Key");
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
		$input = array(
			"item_id"	 => \Input::get("item_id"),
			"user_id"	 => \Input::get("user_id")
		);
		
		$action				 = new \Action();
		$action->name		 = \Input::get("action");
		$action->site_id	 = $this->site_id;
		$action->description = json_encode(array_merge(array("score" => $this->_translateActionToRating(\Input::get("action"))), $input));
		$action->save();

		if (isset($action->id) && $action->id > 0)
		{
			$input["score"] = $this->_translateActionToRating(\Input::get("action"));

			//storing meta
			foreach ($input as $key => $val)
			{
				$action_meta = new \ActionMeta();

				$action_meta->key		 = $key;
				$action_meta->value		 = $val;
				$action_meta->action_id	 = $action->id;

				$action_meta->save();
			}
		}
		
		$item = new \Item();
		$item->identifier = \Input::get("item_id");
		$item->name = \Input::get("name");

		return \Response::json($input);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function missingMethod($parameters = array())
	{
		return "Missing methods";
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

			$this->site_id = $this->validateApiKey($api_credential);
		}

		if (!$this->site_id)
			return \Response::json(array("message" => "Auth failed", "status" => "401"), "401");
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
