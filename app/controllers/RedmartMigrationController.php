<?php

namespace App\Controllers;

// for set memory limit & execution time
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '0');
set_time_limit(0);

class RedmartMigrationController extends BaseController
{

	private $migration_site_id = 0;

	public function __construct()
	{
		$this->migration_site_id = 10; //redmart site id
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$action_ids						 = \App\Models\Action::where("site_id", $this->migration_site_id)->get(array("id"))->toArray();
		$migration_action_file_dump_path = public_path() . "/vv_reco_redmart/tbl_actions.csv";
		$migration_item_file_dump_path	 = public_path() . "/vv_reco_redmart/tbl_items.csv";
		$migration_outbox_file_dump_path = public_path() . "/vv_reco_redmart/tbl_outbox.csv";
//		$migration_hello_file_dump_path	 = public_path() . "/vv_reco_redmart/hello.csv";
//		$migration_outbox_header = array(
//			"id", "action_guid", "dt_added", "dt_proceed", "dt_sent", "executed"
//		);

		$migration_action_header = array(
//			"id", "user_id", "action_type", "item_id", "ip", "browser", "dt_added", "sessionid", "guid"
			"id", "user_id", "action_type", "item_id", "ip", "dt_added", "sessionid", "guid"
		);
		$migration_item_header	 = array(
			"id", "description", "item_url", "img_url", "price", "dt_added"
		);

		//migrate items
		$migration_items = $this->_csvToArray($migration_item_header, $migration_item_file_dump_path);

		$i = 0;
		foreach ($migration_items as $row)
		{
			if ($i === 0)
			{
				$i++;
				continue;
			}

			$new_item = array(
				"item_id"		 => $row['id'],
				"description"	 => $row['description']
			);

			$new_item_id = $this->_setItem($new_item);

			if ($new_item_id)
			{
				$new_item_metas	 = array();
				$new_item_metas	 = ($row['item_url'] !== "") ? array_merge($new_item_metas, array("item_url" => $row['item_url'])) : $new_item_metas;
				$new_item_metas	 = ($row['img_url'] !== "") ? array_merge($new_item_metas, array("img_url" => $row['img_url'])) : $new_item_metas;
				$new_item_metas	 = ($row['price'] !== "") ? array_merge($new_item_metas, array("price" => $row['price'])) : $new_item_metas;
				$this->_setItemMeta($new_item_id, $new_item_metas);
			}
		}

		$migration_actions = $this->_csvToArray($migration_action_header, $migration_action_file_dump_path);

		$i = 0;
		foreach ($migration_actions as $row)
		{
			if ($i === 0)
			{
				$i++;
				continue;
			}

			//ADD VISITOR AND SESSIONS
			$new_visitor = array(
				"user_id" => $row['user_id']
			);

			$new_visitor_id = $this->_setVisitor($new_visitor);
			if ($new_visitor_id)
			{
				$this->_setSession($new_visitor_id, $row['sessionid']);
			}

			//ADD ACTIONS
			$visitor = \App\Models\Visitor::where("identifier", $row['user_id'])->get()->first();
			if ($visitor)
			{
				$session = \App\Models\Session::where("visitor_id", $visitor->id)->where("session", $row['sessionid'])->get()->first();
				if ($session)
				{
					$action = \App\Models\Action::where("name", $row['action_type'])->where("site_id", $this->migration_site_id)->get(array("id"))->first();

					$item = \App\Models\Item::where("identifier", $row['item_id'])->get()->first();

					if ($item)
					{
						$new_action_instance = array(
							'id'		 => $row['id'],
							'item_id'	 => $item->id,
							'session_id' => $session->id,
							'dt_added'	 => $row['dt_added']
						);

						$new_action_instance_id = $this->_setActionInstance($action->id, $new_action_instance);
						if ($new_action_instance_id)
						{
							$new_action_instance_metas = array(
//								"browser"	 => $row['browser'],
								"guid" => $row['guid']
							);

							$this->_setActionInstanceMeta($new_action_instance_id, $new_action_instance_metas);
						}

						echo $row['id'] . " => " . $new_action_instance_id . "<br/>";
					}
					else
					{
						echo $row['id'] . " => not data item (" . $row['item_id'] . ") <br/>";
					}
				}
			}
		}

		die("done");
	}

	public function _setActionInstance($action_id, $data)
	{
		$action_instance			 = new \App\Models\ActionInstance();
		$action_instance->id		 = $data['id'];
		$action_instance->action_id	 = $action_id;
		$action_instance->item_id	 = $data['item_id'];
		$action_instance->session_id = $data['session_id'];
		$action_instance->created	 = $data['dt_added'];

		if ($action_instance->save())
			return $action_instance->id;
		else
			return false;
	}

	/**
	 * 
	 * @param int $action_instance_id
	 * @param array $keys
	 * @param array $values
	 */
	public function _setActionInstanceMeta($action_instance_id, $metas)
	{
		foreach ($metas as $key => $value)
		{
			$action_instance_meta						 = new \App\Models\ActionInstanceMeta();
			$action_instance_meta->key					 = $key;
			$action_instance_meta->value				 = $value;
			$action_instance_meta->action_instance_id	 = $action_instance_id;
			$action_instance_meta->save();
		}
	}

	public function _setVisitor($data)
	{
		$is_exists = \App\Models\Visitor::where("identifier", $data['user_id'])->get()->first();
		if (!$is_exists)
		{
			$visitor			 = new \App\Models\Visitor();
			$visitor->identifier = $data['user_id'];

			if ($visitor->save())
				return $visitor->id;
			else
				return false;
		}
		else
			return $is_exists->id;
	}

	public function _setSession($visitor_id, $session_value)
	{
		$is_exists = \App\Models\Session::where("site_id", $this->migration_site_id)->where("visitor_id", $visitor_id)->where("session", $session_value)->get()->first();

		if (!$is_exists)
		{
			$session			 = new \App\Models\Session();
			$session->site_id	 = $this->migration_site_id;
			$session->visitor_id = $visitor_id;
			$session->session	 = $session_value;

			if ($session->save())
				return $session->id;
			else
				return false;
		}
		else
			return false;
	}

	public function _setItem($data)
	{
		$is_exists = \App\Models\Item::where("identifier", $data['item_id'])->where("site_id", $this->migration_site_id)->get()->first();

		if (!$is_exists)
		{
			$item				 = new \App\Models\Item();
			$item->identifier	 = $data['item_id'];
			$item->name			 = $data['description'];
			$item->site_id		 = $this->migration_site_id;
			$item->type			 = "product";
			$item->active		 = true;

			if ($item->save())
				return $item->id;
			else
				return false;
		}
		else
			return false;
	}

	public function _setItemMeta($item_id, $metas)
	{
		foreach ($metas as $key => $value)
		{
			$item_meta			 = new \App\Models\ItemMeta();
			$item_meta->key		 = $key;
			$item_meta->value	 = $value;
			$item_meta->item_id	 = $item_id;
			$item_meta->save();
		}
	}

	public function _csvToArray($header, $file_path)
	{
		$contents = array();

		if (($handle = fopen($file_path, 'r')) !== FALSE)
		{
			while (($row = fgetcsv($handle, 1000, ',')) !== FALSE)
			{
				if (count($row) == count($header))
					$contents[] = array_combine($header, $row);
				else
				{
					echo '<pre>';
					print_r($row);
					echo "<br/>----<br/>";
					echo '</pre>';
					die;
				}
			}
		}

		return $contents;
	}

}
