<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 18, 2014 5:10:42 PM
 * File         : app/controllers/RulesController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     :
 */

namespace App\Controllers\User;

use View,
	Input,
	Paginator;

class RulesController extends \App\Controllers\BaseController
{

	public function __construct()
	{
		parent::__construct();
		View::share(array("ca" => get_class(), "moduleName" => "Ruleset", "view" => false));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->model = new \App\Models\Ruleset();
		$page		 = Input::get('page', 1);

		$data	 = $this->getByPage($page, $this->manageViewConfig['limit_per_page'], "site_id", $this->active_site_id, 'id', 'ASC');
		$message = '';

		if (!is_array($data) && !is_object($data))
		{
			$message	 = $data;
			$paginator	 = null;
		}
		else
		{
			$paginator = Paginator::make($data->items, $data->totalItems, $data->limit);
		}

		$output = array(
			'paginator'		 => $paginator,
			'str_message'	 => $message,
			"pageTitle"		 => "Manage Ruleset",
			"table_header"	 => $this->model->manage_table_header,
			"page"			 => $page
		);

		return View::make("frontend.panels.manage", $output);
	}

	public function getCreate()
	{

		$enum_types = array(
			"excluded"	 => "Excluded",
			"included"	 => "Included"
		);

		$enum_expiry_types = array(
			"no_expiry"	 => "No Expiry",
			"pageviews"	 => "Page Views",
			"date/time"	 => "Date & Time",
			"clicks"	 => "Clicks"
		);

		$items			 = \App\Models\Item::where("site_id", $this->active_site_id)->where("name", "!=", "")->lists("name", "id");
		$custom_script	 = "<script type='text/javascript'>";
		$custom_script .= "var site_url = '" . \URL::to('/') . "';";
		$custom_script .= "</script>";

		return View::make("frontend.panels.rules.form", array(
					"type"				 => "create",
					'pageTitle'			 => "Add New Ruleset",
					'enum_types'		 => $enum_types,
					'enum_expiry_types'	 => $enum_expiry_types,
					"items"				 => $items,
					"index_item_rule"	 => 1,
					"custom_script"		 => $custom_script
		));
	}

	public function postCreate()
	{
		$input		 = Input::only("name", "description", "expiry_type", "expiry_value", "expiry_value_dt");
		$items		 = Input::get("item_id");
		$types		 = Input::get("type");
		$likelihoods = Input::get("likelihood");

		$rule_model		 = new \App\Models\Rule();
		$ruleset_model	 = new \App\Models\Ruleset();

		if ($input['expiry_type'] !== "no_expiry")
		{
			switch ($input['expiry_type'])
			{
				case "pageviews":
				case "clicks":
					$ruleset_model->rules['expiry_value']	 = "required|numeric";
					$ruleset_model->expiry_value			 = $input['expiry_value'];
					break;
				case "date/time":
					$ruleset_model->rules['expiry_value']	 = "required|date:YY-mm-dd H:i:s A";
					$ruleset_model->expiry_value			 = 0;
					break;
				default:
					$ruleset_model->rules['expiry_value']	 = "required";
					break;
			}
			$ruleset_validator = \Validator::make($input, $ruleset_model->rules);
		}
		else
		{
			$ruleset_model->expiry_value = 0;
			$ruleset_validator			 = \Validator::make($input, $ruleset_model->rules);
		}

		$rule_item_validations	 = array();
		$item_rules				 = array();

		if ($ruleset_validator->passes())
		{
			for ($i = 0; $i < count($items); $i++)
			{
				$item_rule = array(
					"item_id"	 => $items[$i],
					"type"		 => $types[$i],
					"likelihood" => ($likelihoods[$i] !== "") ? ($likelihoods[$i] / 100) : 0.0
				);

				$validator = \Validator::make($item_rule, $rule_model->rules);

				if (!$validator->passes())
					array_pull($rule_item_validations, $validator);

				array_push($item_rules, $item_rule);
			}

			if (count($rule_item_validations) > 0) //if any validation error detected
			{
				//throwing error and resolve the form
				return \Redirect::back()->withErrors($rule_item_validations)->with("flash_error", "Inserting problem. Please check your inputs.");
			}
			else
			{
				//prepare for storing data
				$ruleset_model->name			 = $input['name'];
				$ruleset_model->description		 = ($input['description'] !== "") ? $input['description'] : null;
				$ruleset_model->expiry_type		 = $input['expiry_type'];
				$ruleset_model->site_id			 = $this->active_site_id;
				$ruleset_model->combination_id	 = 1; // so far will only have 1 combination

				if ($input['expiry_type'] === "date/time")
				{
					$obj_expiry_date				 = new \Carbon\Carbon();
					$str_expiry_date				 = $obj_expiry_date->createFromFormat("Y-m-d H:i:s A", $input['expiry_value'])->second(0)->toDateTimeString();
					$ruleset_model->expiry_datetime	 = $str_expiry_date;
				}
				$ruleset_model->save();

				if ($ruleset_model->id)
				{
					foreach ($item_rules as $item)
					{
						$rule				 = new \App\Models\Rule();
						$rule->type			 = $item['type'];
						$rule->item_id		 = $item['item_id'];
						$rule->ruleset_id	 = $ruleset_model->id;
						$rule->likelihood	 = $item['likelihood'];
						$rule->save();
					}
				}
			}

			return \Redirect::route('rules')->with("flash_message", "Successfully added new rule.");
		}
		else
		{
			return \Redirect::back()->withErrors($ruleset_validator)->with("flash_error", "Inserting problem. Please check your inputs.");
		}
	}

	public function postFetchItems()
	{
		$exclude_item_ids = Input::get("excludeItemIDs");

		if (isset($exclude_item_ids) && count($exclude_item_ids) > 0)
		{
			$item_list	 = \App\Models\Item::where("site_id", $this->active_site_id)
					->whereIn("id", $exclude_item_ids, "and", true)
					->lists("name", "id");
			$response	 = array(
				"status"	 => "success",
				"response"	 => $item_list
			);
		}
		else
		{
			$response = array(
				"status"	 => "failed",
				"response"	 => ""
			);
		}
		return \Response::json($response);
	}

	public function getEdit($id)
	{
		$is_exists = \App\Models\Ruleset::where("id", $id)->where("site_id", $this->active_site_id)->count();

		if ($is_exists)
		{
			$item_rules = \App\Models\Rule::where("ruleset_id", $id)->get()->toArray();
		}

		$enum_types = array(
			"excluded"	 => "Excluded",
			"included"	 => "Included"
		);

		$enum_expiry_types = array(
			"no_expiry"	 => "No Expiry",
			"pageviews"	 => "Page Views",
			"date/time"	 => "Date & Time",
			"clicks"	 => "Clicks"
		);

		$items	 = \App\Models\Item::where("site_id", $this->active_site_id)->where("name", "!=", "")->lists("name", "id");
		$ruleset = \App\Models\Ruleset::where("id", $id)->where("site_id", $this->active_site_id)->first();

		$custom_script = "<script type='text/javascript'>";
		$custom_script .= "var site_url = '" . \URL::to('/') . "';";

		$index_item_rule = 1;
		foreach ($item_rules as $obj)
		{
			$obj['last_index']	 = count($item_rules);
			$json_obj			 = json_encode($obj);
			$custom_script .= "editItemRule({$json_obj}, {$index_item_rule});"; //js func to make add itemruleedit
			$index_item_rule+=1;
		}
		$custom_script.= "</script>";


		return View::make("frontend.panels.rules.form", array(
					"type"				 => "edit",
					'pageTitle'			 => "Add New Ruleset",
					'enum_types'		 => $enum_types,
					'enum_expiry_types'	 => $enum_expiry_types,
					"items"				 => $items,
					"item_rules"		 => $item_rules,
					"ruleset"			 => $ruleset,
					"index_item_rule"	 => 1,
					"custom_script"		 => $custom_script
		));
	}

	public function postEdit($id)
	{
		$input				 = Input::only("name", "description", "expiry_type", "expiry_value", "expiry_value_dt");
		$items				 = Input::get("item_id");
		$types				 = Input::get("type");
		$edit_item_rule_ids	 = Input::get("item_rule_id");
		$likelihoods		 = Input::get("likelihood");

		$rule_model		 = new \App\Models\Rule();
		$ruleset_model	 = new \App\Models\Ruleset();
		$ruleset		 = \App\Models\Ruleset::find($id);

		if ($input['expiry_type'] !== "no_expiry")
		{
			switch ($input['expiry_type'])
			{
				case "pageviews":
				case "clicks":
					$ruleset_model->rules['expiry_value']	 = "required|numeric";
					$ruleset->expiry_value					 = $input['expiry_value'];
					break;
				case "date/time":
					$ruleset_model->rules['expiry_value']	 = "required|date:YY-mm-dd H:i:s A";
					$ruleset->expiry_value					 = 0;
					break;
				default:
					$ruleset_model->rules['expiry_value']	 = "required";
					break;
			}
			$ruleset_validator = \Validator::make($input, $ruleset_model->rules);
		}
		else
		{
			$ruleset->expiry_value	 = 0;
			$ruleset_validator		 = \Validator::make($input, $ruleset_model->rules);
		}

		$rule_item_validations	 = array();
		$item_rules				 = array();

		if ($ruleset_validator->passes())
		{
			for ($i = 0; $i < count($items); $i++)
			{
				$item_rule = array(
					"item_id"	 => $items[$i],
					"type"		 => $types[$i],
					"likelihood" => ($likelihoods[$i] !== "") ? ($likelihoods[$i] / 100) : 0.0
				);

				$validator = \Validator::make($item_rule, $rule_model->rules);

				if (!$validator->passes())
					array_pull($rule_item_validations, $validator);

				array_push($item_rules, $item_rule);
			}

			if (count($rule_item_validations) > 0) //if any validation error detected
			{
				//throwing error and resolve the form
				return \Redirect::back()->withErrors($rule_item_validations)->with("flash_error", "Inserting problem. Please check your inputs.");
			}
			else
			{
				//prepare for storing data
				$ruleset->name			 = $input['name'];
				$ruleset->description	 = ($input['description'] !== "") ? $input['description'] : null;
				$ruleset->expiry_type	 = $input['expiry_type'];
				$ruleset->site_id		 = $this->active_site_id;
				$ruleset->combination_id = 1; // so far will only have 1 combination

				if ($input['expiry_type'] === "date/time")
				{
					$obj_expiry_date			 = new \Carbon\Carbon();
					$str_expiry_date			 = $obj_expiry_date->createFromFormat("Y-m-d H:i:s A", $input['expiry_value'])->second(0)->toDateTimeString();
					$ruleset->expiry_datetime	 = $str_expiry_date;
					$ruleset->expiry_value		 = 0;
				}
				else
				{
					$ruleset->expiry_datetime = null;
				}
				$ruleset->update();

				$item_rules		 = \App\Models\Rule::where("ruleset_id", $id)->get()->toArray();
				$item_rule_ids	 = array_fetch($item_rules, "id");

				$result_array_diff		 = array_diff($edit_item_rule_ids, $item_rule_ids); //possible to add
				$result_array_diff_round = array_diff($item_rule_ids, $edit_item_rule_ids); // ids that possible to remove

				$combine_both_new_and_remove = array_merge($result_array_diff, $result_array_diff_round);
				$result_array_diff_updated	 = array_diff($item_rule_ids, $combine_both_new_and_remove); // possible to update (combine new and remove) then compare to existing
				//remove
				foreach ($result_array_diff_round as $value)
				{
					$item_rule = \App\Models\Rule::find($value);
					$item_rule->delete();
				}

				//new
				foreach ($result_array_diff as $i => $value)
				{
					if ($i < count($items))
					{
						$rule				 = new \App\Models\Rule();
						$rule->type			 = $types[$i];
						$rule->item_id		 = $items[$i];
						$rule->ruleset_id	 = $ruleset->id;
						$rule->likelihood	 = ($likelihoods[$i] !== "") ? ($likelihoods[$i] / 1000) : 0.0;
						$rule->save();
					}
				}

				//update
				foreach ($result_array_diff_updated as $value)
				{
					$i = array_search($value, $edit_item_rule_ids);

					if ($i > -1 && ($i <= count($items)))
					{
						$item_rule				 = \App\Models\Rule::find($value);
						$item_rule->type		 = $types[$i];
						$item_rule->item_id		 = $items[$i];
						$item_rule->ruleset_id	 = $ruleset->id;
						$item_rule->likelihood	 = ($likelihoods[$i] !== "") ? ($likelihoods[$i] / 1000) : 0.0;
						$item_rule->update();
					}
				}
			}

			return \Redirect::route('rules')->with("flash_message", "Ruleset has been successfully updated.");
		}
		else
		{
			return \Redirect::back()->withErrors($ruleset_validator)->with("flash_error", "Inserting problem. Please check your inputs.");
		}
	}

	public function postDelete($id)
	{
		$is_exists = \App\Models\Ruleset::where("id", $id)->where("site_id", $this->active_site_id)->count();
		if ($is_exists)
		{
			$ruleset = \App\Models\Ruleset::find($id);
			$ruleset->delete();
		}
		return \Redirect::route('rules')->with("flash_message", "Ruleset has been successfully removed.");
	}

	public function getItemRule()
	{
		$index_item_rule = Input::get("index");

		$enum_types = array(
			"excluded"	 => "Excluded",
			"included"	 => "Included"
		);

		$enum_expiry_types = array(
			"no_expiry"	 => "No Expiry",
			"pageviews"	 => "Page Views",
			"date/time"	 => "Date & Time",
			"clicks"	 => "Clicks"
		);

		$items = \App\Models\Item::where("site_id", $this->active_site_id)
				->where("name", "!=", "")
				->lists("name", "id");

		return \Response::json(
						array("status"	 => "success",
							"response"	 => View::make("frontend.panels.rules.itemrule", array(
								'enum_types'		 => $enum_types,
								'enum_expiry_types'	 => $enum_expiry_types,
								"items"				 => $items,
								"index_item_rule"	 => $index_item_rule)
							)->render()
		));
	}

	public function getItemEditRule()
	{
		$obj			 = Input::get("obj");
		$index_item_rule = Input::get("index");

		$enum_types = array(
			"excluded"	 => "Excluded",
			"included"	 => "Included"
		);

		$enum_expiry_types = array(
			"no_expiry"	 => "No Expiry",
			"pageviews"	 => "Page Views",
			"date/time"	 => "Date & Time",
			"clicks"	 => "Clicks"
		);

		$items = \App\Models\Item::where("site_id", $this->active_site_id)
				->where("name", "!=", "")
				->lists("name", "id");

		return \Response::json(
						array("status"	 => "success",
							"response"	 => View::make("frontend.panels.rules.itemruleedit", array(
								'enum_types'		 => $enum_types,
								'enum_expiry_types'	 => $enum_expiry_types,
								"items"				 => $items,
								"index_item_rule"	 => $index_item_rule,
								"obj"				 => $obj
							))->render()
		));
	}

}
