<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 18, 2014 5:10:42 PM
 * File         : app/controllers/ItemsController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     :
 */

namespace App\Controllers\User;

use View,
    Input,
    Redirect,
    Validator,
    Paginator;

class ItemsController extends \App\Controllers\BaseController
{

    public function __construct()
    {
        parent::__construct();
        View::share(array("ca" => get_class(), "moduleName" => "Item", "create" => false));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $this->model = new \App\Models\Item();
        $page        = Input::get('page', 1);
        $data        = $this->getByPage($page, $this->manageViewConfig['limit_per_page'], "site_id", $this->active_site_id);
        $message     = '';

        if (!is_array($data) && !is_object($data)) {
            $message   = $data;
            $paginator = null;
        }
        else {
            $paginator = Paginator::make($data->items, $data->totalItems, $data->limit);
        }

        $output = array(
            'paginator'    => $paginator,
            "str_message"  => $message,
            "pageTitle"    => "Manage Items",
            "table_header" => $this->model->manage_table_header,
            "page"         => $page,
            "modalTitle"   => "View Item"
        );
        return View::make("frontend.panels.manage", $output);
    }

    public function getEdit($id)
    {
        $item       = \App\Models\Item::find($id);
        $item_metas = \App\Models\ItemMeta::where("item_id", $id)->get();

        if ($item_metas) {
            foreach ($item_metas as $obj) {
                $item->{$obj->key} = $obj->value;
            }
        }

        $activated = ($item->active) ? true : false;
        return View::make("frontend.panels.items.form", array("item" => $item, "type" => "edit", 'pageTitle' => "Edit Item", "activated" => $activated));
    }

    public function postEdit($id)
    {
        $item  = \App\Models\Item::find($id);
        $input = Input::only("name", "item_url", "img_url", "active");
        $rules = array(
            'name' => $item->rules['name']
        );

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) { // validator for name and email
            $item->name   = Input::get("name");
            $item->active = Input::get("active");
            $item->update();
            return Redirect::route("items")->with("flash_message", "Data successfully updated.");
        }
        else {
            return Redirect::back()->withInput()->withErrors($validator);
        }
    }

    public function getView($id)
    {
        $item            = \App\Models\Item::find($id);
        if (is_object($item))
            $item_properties = \App\Models\ItemMeta::where("item_id", $item->id)->orderBy('created_at', 'ASC')->get();

        $activated = ($item->activte) ? true : false;
        return View::make("frontend.panels.items.viewmodalcontent", array("item" => $item, "properties" => $item_properties, "type" => "view", 'pageTitle' => "View Item", "columns" => $item->manage_table_header, "activated" => $activated));
    }

    public function postDelete($id)
    {
        \App\Models\Rule::where("item_id", $id)->delete();
        \App\Models\Item::find($id)->delete();
        return Redirect::back()->with("flash_message", "Data has been removed.");
    }

    function getItemMetas($key = null)
    {
        if (is_null($key)) {
            //something wrong
        }

        $items          = \App\Models\Item::with("item_metas")->where("site_id", $this->active_site_id)->get()->toArray();
        $key_values     = [];
        $key_properties = [];

        foreach ($items as $item) {
            $keys = array_fetch($item['item_metas'], "key");
            if (in_array($key, $keys)) {
                $index          = array_search($key, $keys);
                $key_values     = array_add($key_values, $item['id'], json_decode($item['item_metas'][$index]['value']));
                $key_properties = array_unique(array_merge($key_properties, array_keys((array) $key_values[$item['id']])));
            }
        }

        //@todo extract the locations to be unique value refer to CODE_TESTING/index2.php
        $data = [];
        foreach ($key_values as $key_value) {
            $arr = (array) $key_value;

            foreach ($key_properties as $property) {
                if (!isset($data[$property]))
                    $data[$property] = [];

                if (key_exists($property, $arr)) {
                    $data[$property] = array_unique(array_merge($data[$property], [ucwords($arr[$property])]));
                }
            }
        }

        return \Response::json(array(
                    "status"         => "success",
                    "key_properties" => $key_properties,
                    "data"           => $data
        ));
    }

}
