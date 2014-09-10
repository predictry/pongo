<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : May 28, 2014 12:21:07 PM
 * File         : app/controllers/FiltersController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Controllers\User;

use App\Controllers\BaseController,
    App\Models\Filter,
    App\Models\FilterMeta,
    App\Models\Item,
    App\Models\ItemMeta,
    App\Models\WidgetFilter,
    Input,
    Paginator,
    Redirect,
    Response,
    URL,
    Validator,
    View;

class FiltersController extends BaseController
{

    private $operator_types = array();

    function __construct()
    {
        parent::__construct();
        $this->model = new Filter();

        $this->operator_types = array(
            'contain'            => 'Contains',
            'equal'              => 'Equals',
            'not_equal'          => 'Not Equals',
            'greater_than'       => 'Greater Than',
            'greater_than_equal' => 'Greater Than or Equal',
            'less_than'          => 'Less Than',
            'less_than_equal'    => 'Less Than or Equal',
//            'is_set'             => 'Is Set',
//            'is_not_set'         => 'Is Not Set',
        );

        View::share(array("ca" => get_class(), "moduleName" => "Filter", "view" => false, 'operator_types' => $this->operator_types));
    }

    public function index()
    {
        $page = Input::get('page', 1);

        $data    = $this->getByPage($page, $this->manageViewConfig['limit_per_page'], "site_id", $this->active_site_id, 'id', 'ASC');
        $message = '';

        if (!is_array($data) && !is_object($data)) {
            $message   = $data;
            $paginator = null;
        }
        else {
            $items = $data->items;
            foreach ($items as $obj) {
                $filter_properties     = FilterMeta::where("filter_id", $obj->id)->get()->lists("property");
                for ($i = 0; $i < count($filter_properties); $i++)
                    $filter_properties[$i] = '<span class="label label-primary">' . $filter_properties[$i] . '</span>';
                $obj->properties       = implode(" ", $filter_properties);
            }

            $paginator = Paginator::make($items, $data->totalItems, $data->limit);
        }

        $custom_create = '<a href="" class="btn btn-primary btn"><i class="fa fa-plus"></i> Add New Filter</a>';

        $output = array(
            'paginator'     => $paginator,
            'str_message'   => $message,
            "pageTitle"     => "Manage Filters",
            "table_header"  => $this->model->manage_table_header,
            'custom_create' => $custom_create,
            "page"          => $page
        );

        return View::make("frontend.panels.manage", $output);
    }

    public function getCreate()
    {
        $items = Item::where("site_id", $this->active_site_id)->where("name", "!=", "")->lists("name", "id");
        if (count($items) <= 0) {
            return Redirect::to("filters")->with("flash_error", "Currently, you don't any properties from the item yet.");
        }

        $item_ids   = Item::where("site_id", $this->active_site_id)->get()->lists("id");
        $properties = ItemMeta::whereIn("item_id", $item_ids)->distinct("key")->lists("key", "key");
        $types      = $this->model->filter_data_type;

        $custom_script = "<script type='text/javascript'>";
        $custom_script .= "var site_url = '" . URL::to('/') . "';";
        $custom_script .= "</script>";

        return View::make("frontend.panels.filters.form", array(
                    "type"          => "create",
                    'pageTitle'     => "Add New Filters",
                    "properties"    => $properties,
                    "types"         => $types,
                    "index_item"    => 1,
                    "custom_script" => $custom_script
        ));
    }

    public function postCreate()
    {
        $inputs = Input::only("name", "property", "operator_key", "type", "value");
        $rules  = array(
            'name'         => 'required',
            'property'     => 'required|array',
            'operator_key' => 'required|array',
            'type'         => 'required|array',
            'value'        => 'required|array',
        );

        $validator = Validator::make($inputs, $rules);

        if ($validator->passes()) {
            $validator_value = $this->_validateArray('value', $inputs['value'], array('value' => 'required'));
            if (is_object($validator_value))
                return Redirect::back()->withErrors($validator)->with("flash_error", "Inserting problem. Filter value cannot be empty.");

            $filter          = new Filter();
            $filter->site_id = $this->active_site_id;
            $filter->name    = $inputs['name'];
            $filter->save();

            if ($filter->id) {
                for ($i = 0; $i < count($inputs['property']); $i++) {
                    $filter_meta            = new FilterMeta();
                    $filter_meta->filter_id = $filter->id;
                    $filter_meta->property  = $inputs['property'][$i];
                    $filter_meta->operator  = $inputs['operator_key'][$i];
                    $filter_meta->type      = $inputs['type'][$i];
                    $filter_meta->value     = $inputs['value'][$i];
                    $filter_meta->save();
                }
            }

            return Redirect::route('filters')->with("flash_message", "Successfully added filter.");
        }
        else {
            return Redirect::back()->withErrors($validator)->with("flash_error", "Inserting problem. Please check your inputs.");
        }
    }

    public function getItem()
    {
        $index_item = Input::get("index");

        $item_ids   = Item::where("site_id", $this->active_site_id)->get()->lists("id");
        $properties = ItemMeta::whereIn("item_id", $item_ids)->distinct("key")->lists("key", "key");
        $types      = $this->model->filter_data_type;

        return Response::json(
                        array("status"   => "success",
                            "response" => View::make("frontend.panels.filters.itemfilter", array(
                                "properties" => $properties,
                                "types"      => $types,
                                "index_item" => $index_item)
                            )->render()
        ));
    }

    public function getEdit($id)
    {
        $filter = Filter::where("id", $id)->where("site_id", $this->active_site_id)->get()->first();

        if ($filter) {
            $filter_metas = FilterMeta::where("filter_id", $filter->id)->get()->toArray();
        }

        $custom_script   = "<script type='text/javascript'>var site_url = '" . URL::to('/') . "';";
        $index_item_rule = 1;
        foreach ($filter_metas as $obj) {
            $obj['last_index'] = count($filter_metas);
            $json_obj          = json_encode($obj);
            $custom_script .= "editItemFilter({$json_obj}, {$index_item_rule});"; //js func to make add itemruleedit
            $index_item_rule+=1;
        }
        $custom_script.= "</script>";
        $number_of_items = ( $index_item_rule > 1) ? $index_item_rule : 1;

        return View::make("frontend.panels.filters.form", array(
                    "type"            => "edit",
                    'pageTitle'       => "Edit Filter",
                    "filter"          => $filter,
                    "filter_metas"    => $filter_metas,
                    "index_item_rule" => 1,
                    "number_of_items" => $number_of_items,
                    "custom_script"   => $custom_script
        ));
    }

    public function getItemEdit()
    {
        $obj        = Input::get("obj");
        $index_item = Input::get("index");

        $item_ids   = Item::where("site_id", $this->active_site_id)->get()->lists("id");
        $properties = ItemMeta::whereIn("item_id", $item_ids)->distinct("key")->lists("key", "key");

        $custom_script = "<script type='text/javascript'>";
        $custom_script .= "var site_url = '" . URL::to('/') . "';";
        $custom_script .= "</script>";

        $types = $this->model->filter_data_type;

        $output = array(
            "type"          => "create",
            "custom_script" => $custom_script,
            "properties"    => $properties,
            "obj"           => $obj,
            "types"         => $types,
            "index_item"    => $index_item,
        );

        return Response::json(
                        array("status"   => "success",
                            "response" => View::make("frontend.panels.filters.itemfilteredit", $output)->render()));
    }

    public function postEdit($id)
    {
        $inputs = Input::only("name", "property", "operator_key", "value", "filter_meta_id");
        $rules  = array(
            'name'           => 'required',
            'property'       => 'required|array',
            'operator_key'   => 'required|array',
            'value'          => 'required|array',
            'filter_meta_id' => 'required|array',
        );

        $validator = Validator::make($inputs, $rules);

        if ($validator->passes()) {
            $validator_value = $this->_validateArray('value', $inputs['value'], array('value' => 'required'));
            if (is_object($validator_value))
                return Redirect::back()->withErrors($validator)->with("flash_error", "Inserting problem. Filter value cannot be empty.");

            $filter = Filter::where("id", $id)->where("site_id", $this->active_site_id)->get()->first();
            if ($filter->id) {
                $filter->name = Input::get("name");
                $filter->update();

                $filter_meta_ids = array();

                for ($i = 0; $i < count($inputs['filter_meta_id']); $i++) {
                    if ($inputs['filter_meta_id'][$i] > 0) {
                        $filter_meta = FilterMeta::find($inputs['filter_meta_id'][$i]);
                        if ($filter_meta) {
                            $filter_meta->property = $inputs['property'][$i];
                            $filter_meta->operator = $inputs['operator_key'][$i];
                            $filter_meta->value    = $inputs['value'][$i];
                            $filter_meta->update();
                            $filter_meta_ids[]     = $inputs['filter_meta_id'][$i];
                        }
                    }
                    else if ($inputs['filter_meta_id'][$i] == -1) {
                        $filter_meta            = new FilterMeta();
                        $filter_meta->filter_id = $filter->id;
                        $filter_meta->property  = $inputs['property'][$i];
                        $filter_meta->operator  = $inputs['operator_key'][$i];
                        $filter_meta->value     = $inputs['value'][$i];
                        $filter_meta->save();

                        if ($filter_meta->id)
                            $filter_meta_ids[] = $filter_meta->id;
                    }
                }
                FilterMeta::whereNotIn("id", $filter_meta_ids)->where("filter_id", $filter->id)->delete();
            }

            return Redirect::route('filters')->with("flash_message", "Successfully update filter.");
        }
        else {
            return Redirect::back()->withErrors($validator)->with("flash_error", "Please check your inputs.");
        }
    }

    public function postDelete($id)
    {
        $filter = Filter::where("id", $id)->where("site_id", $this->active_site_id)->get()->first();

        if ($filter) {

            //check if the filter associated with any widget
            $number_of_widget_contain_this_filter = WidgetFilter::where("filter_id", $id)->get()->count();
            if ($number_of_widget_contain_this_filter > 0)
                return Redirect::route('filters')->with("flash_error", "This filter cannot be removed. There is a widget that still associated with this filter.");

            FilterMeta::where("filter_id", $filter->id)->delete();
            $filter->delete();
        }
        return Redirect::route('filters')->with("flash_message", "Filter has been successfully removed.");
    }

    function _validateArray($key, $inputs, $rules)
    {
        for ($i = 0; $i < count($inputs); $i++) {
            $validator = Validator::make(array($key => $inputs[$i]), $rules);
            if (!$validator->passes()) {
                return $validator;
            }
        }

        return true;
    }

}

/* End of file FiltersController.php */
/* Location: ./application/controllers/FiltersController.php */
