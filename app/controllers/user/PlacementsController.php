<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 10, 2014 15:05:02PM
 * File         : app/controllers/PlacementsController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     :
 */

namespace App\Controllers\User;

class PlacementsController extends \App\Controllers\BaseController
{

	public function __construct()
	{
		parent::__construct();
		\View::share(array("ca" => get_class(), "moduleName" => "Placement", "view" => true));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->model = new \App\Models\Placement();
		$page		 = \Input::get("page", 1);

		$data	 = $this->getByPage($page, $this->manageViewConfig['limit_per_page'], "site_id", $this->active_site_id, 'id', 'ASC');
		$message = '';

		if (!is_array($data) && !is_object($data))
		{
			$message	 = $data;
			$paginator	 = null;
		}
		else
		{
			//adding more extra informations
			foreach ($data->items as $item)
			{
				$number_of_rulesets			 = \App\Models\PlacementRuleSet::where("placement_id", $item->id)->count();
				$item->number_of_rulesets	 = $number_of_rulesets;
			}


			$paginator = \Paginator::make($data->items, $data->totalItems, $data->limit);
		}

		$output = array(
			'paginator'		 => $paginator,
			'str_message'	 => $message,
			"pageTitle"		 => "Manage Placements",
			"table_header"	 => $this->model->manage_table_header,
			"page"			 => $page,
			"modalTitle"	 => "JS Placement Code"
		);

		return \View::make("frontend.panels.manage", $output);
	}

	public function getView($id)
	{
		$placement	 = \App\Models\Placement::find($id);
		$site		 = \App\Models\Site::find($this->active_site_id);
		return \View::make("frontend.panels.placements.viewembedjs", array("placement" => $placement, "site" => $site, 'modalTitle' => "Recommendation JS Code"));
	}

	public function getCreate()
	{
		$ruleset_list = \App\Models\Ruleset::where("site_id", $this->active_site_id)->lists("name", "id");

		$custom_script = "<script type='text/javascript'>";
		$custom_script .= "var site_url = '" . \URL::to('/') . "';";
		$custom_script .= "</script>";

		$output = array(
			"type"							 => "create",
			'ruleset_list'					 => $ruleset_list,
			"index_item_placement_ruleset"	 => 1,
			"custom_script"					 => $custom_script,
			"pageTitle"						 => "Add New Placement"
		);
		return \View::make("frontend.panels.placements.form", $output);
	}

	public function postCreate()
	{
		$input			 = \Input::only("name", "description");
		$ruleset_ids	 = \Input::get("item_id");
		$ruleset_actives = array();

		for ($i = 1; $i <= count($ruleset_ids); $i++)
		{
			array_push($ruleset_actives, \Input::get("active{$i}"));
		}

		$placement_model				 = new \App\Models\Placement();
		$placement_validator			 = \Validator::make($input, $placement_model->rules);
		$placement_ruleset_validators	 = array();
		$placement_ruleset_objs			 = array();

		if ($placement_validator->passes())
		{
			$placement_model->name			 = $input['name'];
			$placement_model->site_id		 = $this->active_site_id;
			$placement_model->description	 = ($input['description'] !== "") ? $input['description'] : null;
			$placement_model->save();

			if ($placement_model->id)
			{
				for ($i = 0; $i < count($ruleset_ids); $i++)
				{
					//loop through ruleset ids
					$ruleset_validator = \Validator::make(
									array(
								"ruleset_id" => $ruleset_ids[$i],
								"active"	 => $ruleset_actives[$i]
									), array(
								"ruleset_id" => "required",
								"active"	 => "required"));

					$placement_rule_sets				 = new \App\Models\PlacementRuleSet();
					$placement_rule_sets->placement_id	 = $placement_model->id;
					$placement_rule_sets->ruleset_id	 = $ruleset_ids[$i];
					$placement_rule_sets->active		 = $ruleset_actives[$i];
					array_push($placement_ruleset_objs, $placement_rule_sets);

					//validate then insert
					if (!$ruleset_validator->passes())
						array_push($placement_ruleset_validators, $ruleset_validator);
				}
			}
		}

		if ($placement_validator->passes() && count($placement_ruleset_validators) === 0)
		{
			foreach ($placement_ruleset_objs as $obj)
				$obj->save(); //save it

			return \Redirect::route('placements')->with("flash_message", "Successfully added new rule.");
		}

		return \Redirect::back()->withErrors($placement_validator)->with("flash_error", "Inserting problem. Please try again.");
	}

	public function getEdit($id)
	{
		$is_exists = \App\Models\Placement::where("id", $id)->where("site_id", $this->active_site_id)->count();
		if ($is_exists)
		{
			$placement				 = \App\Models\Placement::where("id", $id)->where("site_id", $this->active_site_id)->first();
			$placement_item_rulesets = \App\Models\PlacementRuleSet::where("placement_id", $id)->get()->toArray();
			$ruleset_list			 = \App\Models\Ruleset::where("site_id", $this->active_site_id)->lists("name", "id");

			$custom_script = "<script type='text/javascript'>";
			$custom_script .= "var site_url = '" . \URL::to('/') . "';";

			$index_item_rule = 1;
			foreach ($placement_item_rulesets as $obj)
			{
				$obj['last_index']	 = count($placement_item_rulesets);
				$json_obj			 = json_encode($obj);
				$custom_script .= "editItemPlacementRuleset({$json_obj}, {$index_item_rule});"; //js func to make add itemruleedit
				$index_item_rule+=1;
			}
			$custom_script.= "</script>";
			$numberOfItems = ( $index_item_rule > 1) ? $index_item_rule : 1;

			return \View::make("frontend.panels.placements.form", array(
						"type"							 => "edit",
						'pageTitle'						 => "Edit Placement",
						"placement"						 => $placement,
						"placement_item_rulesets"		 => $placement_item_rulesets,
						"ruleset_list"					 => $ruleset_list,
						"custom_script"					 => $custom_script,
						"number_of_items"				 => $numberOfItems,
						"index_item_placement_ruleset"	 => 1
			));
		}
	}

	public function postEdit($id)
	{
		$input						 = \Input::only("name", "description");
		$placement_item_ruleset_ids	 = \Input::get("item_id");
		$edit_placement_ruleset_ids	 = \Input::get("item_ruleset_id");
		$ruleset_actives			 = array();

		for ($i = 1; $i <= count($placement_item_ruleset_ids); $i++)
		{
			array_push($ruleset_actives, \Input::get("active{$i}"));
		}

		$placement			 = \App\Models\Placement::where('id', $id)->where("site_id", $this->active_site_id)->first();
		$placement_validator = \Validator::make(array("name" => $input['name'], "description" => $input['description']), array("name" => "required|max:64"));

		if (isset($placement) && $placement_validator->passes())
		{
			$placement->name		 = $input["name"];
			$placement->description	 = $input["description"];
			$placement->update();

			$placement_item_rules	 = \App\Models\PlacementRuleSet::where("placement_id", $id)->get()->toArray();
			$placement_item_ids		 = array_fetch($placement_item_rules, "id");

			$result_array_diff		 = array_diff($edit_placement_ruleset_ids, $placement_item_ids); //possible to add
			$result_array_diff_round = array_diff($placement_item_ids, $edit_placement_ruleset_ids); // ids that possible to remove

			$combine_both_new_and_remove = array_merge($result_array_diff, $result_array_diff_round);
			$result_array_diff_updated	 = array_diff($placement_item_ids, $combine_both_new_and_remove); // possible to update (combine new and remove) then compare to existing
//			echo '<pre>';
//			print_r($placement_item_rules);
//			print_r($placement_item_ids);
//			print_r($edit_placement_ruleset_ids);
//			print_r($result_array_diff);
//			print_r($result_array_diff_round);
//			print_r($result_array_diff_updated);
//			echo '</pre>';
//			die;

			foreach ($result_array_diff_round as $value)
			{
				$placement_ruleset = \App\Models\PlacementRuleSet::find($value);
				$placement_ruleset->delete();
			}

			//new
			foreach ($result_array_diff as $i => $value)
			{
				if ($i < count($placement_item_ruleset_ids))
				{
					$placement_ruleset				 = new \App\Models\PlacementRuleSet();
					$placement_ruleset->ruleset_id	 = $placement_item_ruleset_ids[$i];
					$placement_ruleset->placement_id = $placement->id;
					$placement_ruleset->active		 = $ruleset_actives[$i];
					$placement_ruleset->save();
				}
			}

			//update
			foreach ($result_array_diff_updated as $value)
			{
				$i = array_search($value, $edit_placement_ruleset_ids);

				if ($i > -1 && ($i <= count($placement_item_ruleset_ids)))
				{
					$placement_ruleset				 = \App\Models\PlacementRuleSet::find($value);
					$placement_ruleset->ruleset_id	 = $placement_item_ruleset_ids[$i];
					$placement_ruleset->active		 = $ruleset_actives[$i];
					$placement_ruleset->update();
				}
			}
		}
		return \Redirect::route('placements')->with("flash_message", "Successfully added new rule.");
	}

	public function getItemPlacementRuleset()
	{
		$index_item_placement_ruleset = \Input::get("index");

		$ruleset_list = \App\Models\Ruleset::where("site_id", $this->active_site_id)->lists("name", "id");

		return \Response::json(
						array("status"	 => "success",
							"response"	 => \View::make("frontend.panels.placements.itemruleset", array(
								'ruleset_list'					 => $ruleset_list,
								'type'							 => 'create',
								"index_item_placement_ruleset"	 => $index_item_placement_ruleset))->render()
		));
	}

	public function getItemEditPlacementRuleset()
	{
		$obj			 = \Input::get("obj");
		$index_item_rule = \Input::get("index");
		$ruleset_list	 = \App\Models\Ruleset::where("site_id", $this->active_site_id)->lists("name", "id");

		$custom_script = "<script type='text/javascript'>";
		$custom_script .= "var site_url = '" . \URL::to('/') . "';";
		$custom_script .= "</script>";

		$output = array(
			"type"							 => "create",
			'ruleset_list'					 => $ruleset_list,
			"custom_script"					 => $custom_script,
			"obj"							 => $obj,
			"index_item_placement_ruleset"	 => $index_item_rule,
		);

		return \Response::json(
						array("status"	 => "success",
							"response"	 => \View::make("frontend.panels.placements.itemrulesetedit", $output)->render()));
	}

	public function postDelete($id)
	{
		$is_exists = \App\Models\Placement::where("id", $id)->where("site_id", $this->active_site_id)->count();
		if ($is_exists)
		{
			$ruleset = \App\Models\Placement::find($id);
			$ruleset->delete();
		}
		return \Redirect::route('placements')->with("flash_message", "Placement has been successfully removed.");
	}

	/*
	 * Wizard
	 */

	public function getWizard()
	{
		$ruleset_list = \App\Models\Ruleset::where("site_id", $this->active_site_id)->lists("name", "id");

		$custom_script = "<script type='text/javascript'>";
		$custom_script .= "var site_url = '" . \URL::to('/') . "';";
		$custom_script .= "</script>";

		$output = array(
			"type"							 => "wizard_create",
			'ruleset_list'					 => $ruleset_list,
			"custom_script"					 => $custom_script,
			"index_item_placement_ruleset"	 => 1,
			"pageTitle"						 => "Add New Placement",
			"modalTitle"					 => "Add new ruleset"
		);
		return \View::make("frontend.panels.placements.wizardforms", $output);
	}

	public function postAjaxWizardPlacement()
	{
		$input				 = \Input::only("name", "description");
		$placement_model	 = new \App\Models\Placement();
		$placement_validator = \Validator::make($input, $placement_model->rules);

		if (!$placement_validator->passes())
		{
			return \Response::json(
							array("status"	 => "error",
								"response"	 => \View::make("frontend.panels.placements.wizardformplacement")->withErrors($placement_validator)->render()
			));
		}

		//save in session
		\Session::set("input_placement", $input);
		return \Response::json(array("status" => "success"));
	}

	public function postAjaxWizardCompletePlacement()
	{
		$input_placement = \Session::get("input_placement");
		$ruleset_ids	 = \Input::get("item_id");
		$ruleset_actives = array();
		for ($i = 1; $i <= count($ruleset_ids); $i++)
		{
			array_push($ruleset_actives, \Input::get("active{$i}"));
		}

		$placement_model				 = new \App\Models\Placement();
		$placement_model->name			 = $input_placement['name'];
		$placement_model->site_id		 = $this->active_site_id;
		$placement_model->description	 = ($input_placement['description'] !== "") ? $input_placement['description'] : null;
		$placement_model->save();

		$placement_ruleset_validators	 = array();
		$placement_ruleset_objs			 = array();

		if ($placement_model->id)
		{
			for ($i = 0; $i < count($ruleset_ids); $i++)
			{
				//loop through ruleset ids
				$ruleset_validator = \Validator::make(
								array(
							"ruleset_id" => $ruleset_ids[$i],
							"active"	 => $ruleset_actives[$i]
								), array(
							"ruleset_id" => "required",
							"active"	 => "required"));

				$placement_rule_sets				 = new \App\Models\PlacementRuleSet();
				$placement_rule_sets->placement_id	 = $placement_model->id;
				$placement_rule_sets->ruleset_id	 = $ruleset_ids[$i];
				$placement_rule_sets->active		 = $ruleset_actives[$i];
				array_push($placement_ruleset_objs, $placement_rule_sets);

				//validate then insert
				if (!$ruleset_validator->passes())
					array_push($placement_ruleset_validators, $ruleset_validator);
			}
		}

		if (count($placement_ruleset_validators) === 0)
		{
			foreach ($placement_ruleset_objs as $obj)
				$obj->save(); //save it

			$placement	 = \App\Models\Placement::find($placement_model->id);
			$site		 = \App\Models\Site::find($this->active_site_id);
			return \Response::json(
							array("status"	 => "success",
								"response"	 => \View::make("frontend.panels.placements.viewembedjs", array("placement" => $placement, "site" => $site))->render()
			));
		}
	}

	//add extra ruleset modal
	public function postAjaxWizardAddRuleset()
	{
		$input		 = \Input::only("name", "description", "expiry_type", "expiry_value", "expiry_value_dt");
		$items		 = \Input::get("item_id");
		$types		 = \Input::get("type");
		$likelihoods = \Input::get("likelihood");

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
					"likelihood" => ($likelihoods[$i] !== "") ? ($likelihoods[$i] / 1000) : 0.0
				);

				$validator = \Validator::make($item_rule, $rule_model->rules);

				if (!$validator->passes())
					array_pull($rule_item_validations, $validator);

				array_push($item_rules, $item_rule);
			}

			if (count($rule_item_validations) === 0)
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
		}

		if (!$ruleset_validator->passes() || count($rule_item_validations) > 0)
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

			$items = \App\Models\Item::where("site_id", $this->active_site_id)->where("name", "!=", "")->lists("name", "id");
			return \Response::json(
							array("status"	 => "error",
								"response"	 => \View::make("frontend.panels.rules.modalform", array(
									"type"				 => "create",
									'enum_types'		 => $enum_types,
									'enum_expiry_types'	 => $enum_expiry_types,
									"items"				 => $items,
									"index_item_rule"	 => 1,
									"flash_error"		 => "Inserting problem. Please check your inputs."
								))->withErrors($ruleset_validator)->render()));
		}

		return \Response::json(array("status" => "success"));
	}

}
