<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 10, 2014 15:05:02PM
 * File         : app/controllers/widgetsController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     :
 */

namespace App\Controllers\User;

class WidgetsController extends \App\Controllers\BaseController
{

	public function __construct()
	{
		parent::__construct();
		\View::share(array("ca" => get_class(), "moduleName" => "widget", "view" => true));
		if (\Auth::user()->plan_id === 3) //redmart
		{
			\View::share(array("create" => false, "edit" => false, "delete" => false));
		}
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->model = new \App\Models\Widget();
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
				$number_of_rulesets			 = \App\Models\WidgetRuleSet::where("widget_id", $item->id)->count();
				$item->number_of_rulesets	 = $number_of_rulesets;
			}


			$paginator = \Paginator::make($data->items, $data->totalItems, $data->limit);
		}

		$output = array(
			'paginator'		 => $paginator,
			'str_message'	 => $message,
			"pageTitle"		 => "Manage widgets",
			"table_header"	 => $this->model->manage_table_header,
			"page"			 => $page,
			"modalTitle"	 => "JS widget Code"
		);

		return \View::make("frontend.panels.manage", $output);
	}

	public function getView($id)
	{
		$widget	 = \App\Models\Widget::find($id);
		$site	 = \App\Models\Site::find($this->active_site_id);
		return \View::make("frontend.panels.widgets.viewembedjs", array("widget" => $widget, "site" => $site, 'modalTitle' => "Recommendation JS Code"));
	}

	public function getCreate()
	{
		$ruleset_list	 = \App\Models\Ruleset::where("site_id", $this->active_site_id)->lists("name", "id");
		$filter_list	 = \App\Models\Filter::where("site_id", $this->active_site_id)->lists("name", "id");


		$custom_script = "<script type='text/javascript'>";
		$custom_script .= "var site_url = '" . \URL::to('/') . "';";
		$custom_script .= "</script>";

		if (count($ruleset_list) <= 0)
		{
			return \Redirect::to("widgets")->with("flash_error", "Currently, you don't have any ruleset to set into widget.");
		}

		$output = array(
			"type"						 => "create",
			'ruleset_list'				 => $ruleset_list,
			'filter_list'				 => $filter_list,
			"index_item_widget_ruleset"	 => 1,
			"index_item_widget_filter"	 => 1,
			"custom_script"				 => $custom_script,
			"pageTitle"					 => "Add New widget"
		);
		return \View::make("frontend.panels.widgets.form", $output);
	}

	public function postCreate()
	{
		$inputs			 = \Input::only("name", "description");
		$ruleset_ids	 = \Input::get("item_id");
		$filter_ids		 = \Input::get("filter_id");
		$ruleset_actives = array();
		$filter_actives	 = array();

		for ($i = 1; $i <= count($ruleset_ids); $i++)
		{
			array_push($ruleset_actives, \Input::get("active{$i}"));
			array_push($filter_actives, \Input::get("filter_active{$i}"));
		}

		$widget				 = new \App\Models\Widget();
		$widget_validator	 = \Validator::make($inputs, $widget->rules);

		$widget_ruleset_validators	 = $widget_filter_validators	 = $widget_ruleset_objs		 = $widget_filter_objs			 = array();
		if ($widget_validator->passes())
		{
			$widget->name		 = $inputs['name'];
			$widget->site_id	 = $this->active_site_id;
			$widget->description = ($inputs['description'] !== "") ? $inputs['description'] : null;
			$widget->save();

			if ($widget->id)
			{
				for ($i = 0; $i < count($ruleset_ids); $i++)
				{
					//loop through ruleset ids
					$ruleset_validator = \Validator::make(array("ruleset_id" => $ruleset_ids[$i], "active" => $ruleset_actives[$i]), array("ruleset_id" => "required", "active" => "required"));

					//validate then insert
					if ($ruleset_validator->passes())
					{
						$widget_rule_sets				 = new \App\Models\WidgetRuleSet();
						$widget_rule_sets->widget_id	 = $widget->id;
						$widget_rule_sets->ruleset_id	 = $ruleset_ids[$i];
						$widget_rule_sets->active		 = $ruleset_actives[$i];
						array_push($widget_ruleset_objs, $widget_rule_sets);
					}
					else
						array_push($widget_ruleset_validators, $ruleset_validator);
				}

				for ($i = 0; $i < count($filter_ids); $i++)
				{
					$filter_validator = \Validator::make(array('filter_id' => $filter_ids[$i], "active" => $filter_actives[$i]), array("filter_id" => "required", "active" => "required"));
					if ($filter_validator->passes())
					{
						$widget_filter				 = new \App\Models\WidgetFilter();
						$widget_filter->widget_id	 = $widget->id;
						$widget_filter->filter_id	 = $filter_ids[$i];
						$widget_filter->active		 = $filter_actives[$i];
						array_push($widget_filter_objs, $widget_filter);
					}
					else
						array_push($widget_filter_validators, $filter_validator);
				}
			}
		}

		if ($widget_validator->passes() && count($widget_ruleset_validators) === 0 && count($widget_filter_validators) === 0)
		{
			foreach ($widget_ruleset_objs as $obj)
				$obj->save(); //save it

			foreach ($widget_filter_objs as $obj)
				$obj->save();

			return \Redirect::route('widgets')->with("flash_message", "Successfully added new widget.");
		}
		else
			$widget->delete();

		return \Redirect::back()->withErrors($widget_validator)->with("flash_error", "Inserting problem. Please try again.");
	}

	public function getEdit($id)
	{
		$is_exists = \App\Models\Widget::where("id", $id)->where("site_id", $this->active_site_id)->count();
		if ($is_exists)
		{
			$widget					 = \App\Models\Widget::where("id", $id)->where("site_id", $this->active_site_id)->first();
			$widget_item_rulesets	 = \App\Models\WidgetRuleSet::where("widget_id", $id)->get()->toArray();
			$widget_item_filters	 = \App\Models\WidgetFilter::where("widget_id", $id)->get()->toArray();
			$ruleset_list			 = \App\Models\Ruleset::where("site_id", $this->active_site_id)->lists("name", "id");
			$filter_list			 = \App\Models\Filter::where("site_id", $this->active_site_id)->lists("name", "id");

			$custom_script = "<script type='text/javascript'>";
			$custom_script .= "var site_url = '" . \URL::to('/') . "';";

			$index_item_rule = 1;
			foreach ($widget_item_rulesets as $obj)
			{
				$obj['last_index']	 = count($widget_item_rulesets);
				$json_obj			 = json_encode($obj);
				$custom_script .= "editItemWidgetRuleset({$json_obj}, {$index_item_rule});"; //js func to make add itemruleedit
				$index_item_rule+=1;
			}

			$index_item_filter = 1;
			foreach ($widget_item_filters as $obj)
			{
				$obj['last_index']	 = count($widget_item_filters);
				$json_obj			 = json_encode($obj);
				$custom_script .= "editItemWidgetFilter({$json_obj}, {$index_item_filter});"; //js func to make add itemruleedit
				$index_item_filter+=1;
			}
			$custom_script.= "</script>";
			$numberOfItems		 = ( $index_item_rule > 1) ? $index_item_rule : 1;
			$numberOfFilterItems = ($index_item_filter > 1) ? $index_item_filter : 1;

			return \View::make("frontend.panels.widgets.form", array(
						"type"						 => "edit",
						'pageTitle'					 => "Edit widget",
						"widget"					 => $widget,
						"widget_item_rulesets"		 => $widget_item_rulesets,
						"ruleset_list"				 => $ruleset_list,
						"filter_list"				 => $filter_list,
						"custom_script"				 => $custom_script,
						"number_of_items"			 => $numberOfItems,
						"number_of_filter_items"	 => $numberOfFilterItems,
						"index_item_widget_ruleset"	 => 1,
						"index_item_widget_filter"	 => 1
			));
		}
	}

	public function postEdit($id)
	{
		$input					 = \Input::only("name", "description");
		$widget_item_ruleset_ids = \Input::get("item_id");
		$edit_widget_ruleset_ids = \Input::get("item_ruleset_id");
		$widget_item_filter_ids	 = \Input::get("filter_id");
		$edit_widget_filter_ids	 = \Input::get("item_filter_id");
		$ruleset_actives		 = $filter_actives			 = array();

		for ($i = 1; $i <= count($widget_item_ruleset_ids); $i++)
		{
			array_push($ruleset_actives, \Input::get("active{$i}"));
			array_push($filter_actives, \Input::get("filter_active{$i}"));
		}

		$widget				 = \App\Models\Widget::where('id', $id)->where("site_id", $this->active_site_id)->first();
		$widget_validator	 = \Validator::make(array("name" => $input['name'], "description" => $input['description']), array("name" => "required|max:64"));

		if (isset($widget) && $widget_validator->passes())
		{
			$widget->name		 = $input["name"];
			$widget->description = $input["description"];
			$widget->update();

			$widget_item_rules	 = \App\Models\WidgetRuleSet::where("widget_id", $id)->get()->toArray();
			$widget_item_ids	 = array_fetch($widget_item_rules, "id");

			$result_array_diff		 = array_diff($edit_widget_ruleset_ids, $widget_item_ids); //possible to add
			$result_array_diff_round = array_diff($widget_item_ids, $edit_widget_ruleset_ids); // ids that possible to remove

			$combine_both_new_and_remove = array_merge($result_array_diff, $result_array_diff_round);
			$result_array_diff_updated	 = array_diff($widget_item_ids, $combine_both_new_and_remove); // possible to update (combine new and remove) then compare to existing

			$result_filter_array_diff_updated	 = $result_filter_array_diff			 = $result_filter_array_diff_round		 = array();

			if ($edit_widget_filter_ids)
			{
				$widget_filter_ids					 = \App\Models\WidgetFilter::where("widget_id", $id)->get()->lists("id");
				$result_filter_array_diff			 = array_diff($edit_widget_filter_ids, $widget_filter_ids);
				$result_filter_array_diff_round		 = array_diff($widget_filter_ids, $edit_widget_filter_ids);
				$combine_filter_both_new_and_remove	 = array_merge($result_filter_array_diff, $result_filter_array_diff_round);
				$result_filter_array_diff_updated	 = array_diff($widget_filter_ids, $combine_filter_both_new_and_remove); // possible to update (combine new and remove) then compare to existing
			}

			foreach ($result_array_diff_round as $value)
			{
				$widget_ruleset = \App\Models\WidgetRuleSet::find($value);
				$widget_ruleset->delete();
			}

			if (count($result_filter_array_diff_round) > 0)
				foreach ($result_filter_array_diff_round as $value)
				{
					$widget_filter = \App\Models\WidgetFilter::find($value);
					$widget_filter->delete();
				}

			//new
			foreach ($result_array_diff as $i => $value)
			{
				if ($i < count($widget_item_ruleset_ids))
				{
					$widget_ruleset				 = new \App\Models\WidgetRuleSet();
					$widget_ruleset->ruleset_id	 = $widget_item_ruleset_ids[$i];
					$widget_ruleset->widget_id	 = $widget->id;
					$widget_ruleset->active		 = $ruleset_actives[$i];
					$widget_ruleset->save();
				}
			}

			//new
			if (count($result_filter_array_diff) > 0)
				foreach ($result_filter_array_diff as $i => $value)
				{
					if ($i < count($widget_item_filter_ids))
					{
						$widget_filter				 = new \App\Models\WidgetFilter();
						$widget_filter->filter_id	 = $widget_item_filter_ids[$i];
						$widget_filter->widget_id	 = $widget->id;
						$widget_filter->active		 = $filter_actives[$i];
						$widget_filter->save();
					}
				}

			//update
			foreach ($result_array_diff_updated as $value)
			{
				$i = array_search($value, $edit_widget_ruleset_ids);

				if ($i > -1 && ($i <= count($widget_item_ruleset_ids)))
				{
					$widget_ruleset				 = \App\Models\WidgetRuleSet::find($value);
					$widget_ruleset->ruleset_id	 = $widget_item_ruleset_ids[$i];
					$widget_ruleset->active		 = $ruleset_actives[$i];
					$widget_ruleset->update();
				}
			}


			//update
			if (count($result_filter_array_diff_updated) > 0)
				foreach ($result_filter_array_diff_updated as $value)
				{
					$i = array_search($value, $edit_widget_filter_ids);

					if ($i > -1 && ($i <= count($widget_item_filter_ids)))
					{
						$widget_filter				 = \App\Models\WidgetFilter::find($value);
						$widget_filter->filter_id	 = $widget_item_filter_ids[$i];
						$widget_filter->active		 = $filter_actives[$i];
						$widget_filter->update();
					}
				}
		}
		return \Redirect::route('widgets')->with("flash_message", "Successfully added new rule.");
	}

	public function getItemwidgetRuleset()
	{
		$index_item_widget_ruleset = \Input::get("index");

		$ruleset_list = \App\Models\Ruleset::where("site_id", $this->active_site_id)->lists("name", "id");

		return \Response::json(
						array("status"	 => "success",
							"response"	 => \View::make("frontend.panels.widgets.itemruleset", array(
								'ruleset_list'				 => $ruleset_list,
								'type'						 => 'create',
								"index_item_widget_ruleset"	 => $index_item_widget_ruleset))->render()
		));
	}

	public function getItemEditwidgetRuleset()
	{
		$obj			 = \Input::get("obj");
		$index_item_rule = \Input::get("index");
		$ruleset_list	 = \App\Models\Ruleset::where("site_id", $this->active_site_id)->lists("name", "id");

		$custom_script = "<script type='text/javascript'>";
		$custom_script .= "var site_url = '" . \URL::to('/') . "';";
		$custom_script .= "</script>";

		$output = array(
			"type"						 => "create",
			'ruleset_list'				 => $ruleset_list,
			"custom_script"				 => $custom_script,
			"obj"						 => $obj,
			"index_item_widget_ruleset"	 => $index_item_rule,
		);

		return \Response::json(
						array("status"	 => "success",
							"response"	 => \View::make("frontend.panels.widgets.itemrulesetedit", $output)->render()));
	}

	public function getItemEditwidgetFilter()
	{
		$obj			 = \Input::get("obj");
		$index_item_rule = \Input::get("index");
		$filter_list	 = \App\Models\Filter::where("site_id", $this->active_site_id)->lists("name", "id");

		$custom_script = "<script type='text/javascript'>";
		$custom_script .= "var site_url = '" . \URL::to('/') . "';";
		$custom_script .= "</script>";

		$output = array(
			"type"						 => "create",
			'filter_list'				 => $filter_list,
			"custom_script"				 => $custom_script,
			"obj"						 => $obj,
			"index_item_widget_filter"	 => $index_item_rule,
		);

		return \Response::json(
						array("status"	 => "success",
							"response"	 => \View::make("frontend.panels.widgets.itemfilteredit", $output)->render()));
	}

	public function postDelete($id)
	{
		$is_exists = \App\Models\Widget::where("id", $id)->where("site_id", $this->active_site_id)->count();
		if ($is_exists)
		{

			\App\Models\WidgetRuleSet::where("widget_id", $id)->delete();
			\App\Models\WidgetFilter::where("widget_id", $id)->delete();

			$ruleset = \App\Models\Widget::find($id);
			$ruleset->delete();
		}
		return \Redirect::route('widgets')->with("flash_message", "widget has been successfully removed.");
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

		if (count($ruleset_list) <= 0)
		{
			return \Redirect::to("widgets")->with("flash_error", "Currently, you don't have any ruleset to set into widget.");
		}

		$output = array(
			"type"						 => "wizard_create",
			'ruleset_list'				 => $ruleset_list,
			"custom_script"				 => $custom_script,
			"index_item_widget_ruleset"	 => 1,
			"pageTitle"					 => "Add New widget",
			"modalTitle"				 => "Add new ruleset"
		);

		return \View::make("frontend.panels.widgets.wizardforms", $output);
	}

	public function postAjaxWizardWidget()
	{
		$input				 = \Input::only("name", "description");
		$widget_model		 = new \App\Models\Widget();
		$widget_validator	 = \Validator::make($input, $widget_model->rules);

		if (!$widget_validator->passes())
		{
			return \Response::json(
							array("status"	 => "error",
								"response"	 => \View::make("frontend.panels.widgets.wizardformwidget")->withErrors($widget_validator)->render()
			));
		}

		//save in session
		\Session::set("input_widget", $input);
		return \Response::json(array("status" => "success"));
	}

	public function postAjaxWizardCompleteWidget()
	{
		$input_widget	 = \Session::get("input_widget");
		$ruleset_ids	 = \Input::get("item_id");
		$ruleset_actives = array();
		for ($i = 1; $i <= count($ruleset_ids); $i++)
		{
			array_push($ruleset_actives, \Input::get("active{$i}"));
		}

		$widget_model				 = new \App\Models\Widget();
		$widget_model->name			 = $input_widget['name'];
		$widget_model->site_id		 = $this->active_site_id;
		$widget_model->description	 = ($input_widget['description'] !== "") ? $input_widget['description'] : null;
		$widget_model->save();

		$widget_ruleset_validators	 = array();
		$widget_ruleset_objs		 = array();

		if ($widget_model->id)
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

				$widget_rule_sets				 = new \App\Models\WidgetRuleSet();
				$widget_rule_sets->widget_id	 = $widget_model->id;
				$widget_rule_sets->ruleset_id	 = $ruleset_ids[$i];
				$widget_rule_sets->active		 = $ruleset_actives[$i];
				array_push($widget_ruleset_objs, $widget_rule_sets);

				//validate then insert
				if (!$ruleset_validator->passes())
					array_push($widget_ruleset_validators, $ruleset_validator);
			}
		}

		if (count($widget_ruleset_validators) === 0)
		{
			foreach ($widget_ruleset_objs as $obj)
				$obj->save(); //save it

			$widget	 = \App\Models\Widget::find($widget_model->id);
			$site	 = \App\Models\Site::find($this->active_site_id);
			return \Response::json(
							array("status"	 => "success",
								"response"	 => \View::make("frontend.panels.widgets.viewembedjs", array("widget" => $widget, "site" => $site))->render()
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
