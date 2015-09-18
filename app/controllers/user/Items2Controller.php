<?php

namespace App\Controllers\User;

use App\Controllers\BaseController,
    App\Models\Item,
    App\Models\ItemMeta,
    App\Models\Rule,
    Guzzle\Service\Client,
    Form,
    Input,
    Paginator,
    Redirect,
    Response,
    Str,
    Validator,
    View;

class Items2Controller extends BaseController
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
        $client = new Client($_ENV['PREDICTRY_ANALYTICS_URL'] . 'items/');
        $current_site = \Session::get("active_site_name");

        $response = $client->get($current_site . "?size=100")->send();
        $arr_response = $response->json();

        
        $output = array(
          "items" => $arr_response
        );
        return View::make(getenv('FRONTEND_SKINS') . $this->theme . ".panels.manage", $output);
    }

    public function getEdit($id)
    {
        $item = Item::find($id);

        if ($item) {
            $item_metas = ItemMeta::where("item_id", $id)->get();

            if ($item_metas) {
                foreach ($item_metas as $obj) {
                    $item->{$obj->key} = $obj->value;
                }
            }

            $activated = ($item->active) ? true : false;
            return View::make(getenv('FRONTEND_SKINS') . $this->theme . ".panels.items.form", array("item" => $item, "type" => "edit", 'pageTitle' => "Edit Item", "activated" => $activated));
        }

        return Redirect::to('v2/items')->with("flash_error", "Item doesn't exist.");
    }

    public function postEdit($id)
    {
        $item  = Item::find($id);
        $input = Input::only("name", "item_url", "img_url", "active");
        $rules = array(
            'name'     => $item->rules['name'],
            "item_url" => 'required',
            'img_url'  => 'required'
        );

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) { // validator for name and email
            $item->name   = Input::get("name");
            $item->active = Input::get("active");
            $item->update();
            return Redirect::route("v2/items")->with("flash_message", "Data successfully updated.");
        }
        else {
            return Redirect::back()->withInput()->withErrors($validator);
        }
    }

    public function getView($id)
    {
        $item = Item::find($id);

        if (is_object($item))
            $item_properties = ItemMeta::where("item_id", $item->id)->orderBy('created_at', 'ASC')->get();

        $activated = ($item->activte) ? true : false;
        return View::make(getenv('FRONTEND_SKINS') . $this->theme . ".panels.items.viewmodalcontent", array("item" => $item, "properties" => $item_properties, "type" => "view", 'pageTitle' => "View Item", "columns" => $item->manage_table_header, "activated" => $activated));
    }

    public function postDelete($id)
    {
        ItemMeta::where("item_id", $id)->delete();
        Rule::where("item_id", $id)->delete();
        Item::find($id)->delete();
        return Redirect::back()->with("flash_message", "Data has been removed.");
    }

    function getItemMetas($key = null)
    {
        if (is_null($key)) {
            //something wrong
        }

        $is_dropdown = (!is_null(\Input::get("isDropDown"))) ? \Input::get("isDropDown") : false;
        $id          = (!is_null(\Input::get("id"))) ? \Input::get("id") : 1;
        $value       = (!is_null(\Input::get("val"))) ? \Input::get("val") : false;

        $items          = Item::with("item_metas")->where("site_id", $this->active_site_id)->get()->toArray();
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
        $data       = $data_group = [];
        foreach ($key_values as $key_value) {
            $arr = (array) $key_value;

            foreach ($key_properties as $property) {
                if (!isset($data[$property]))
                    $data[$property] = [];

                if (key_exists($property, $arr)) {
                    $data[$property] = array_unique(array_merge($data[$property], [ucwords($arr[$property])]));

                    foreach ($data[$property] as $index => $val) {
                        $data_group[$property][strtolower($property . '.' . Str::slug($val))] = ucwords($val);
                    }
                }
            }
        }

        if ($is_dropdown) {
            $key_properties_list = [];
            foreach ($key_properties as $val) {
                $key_properties_list[strtolower($val)] = ucwords($val);
            }
//            $view = \Form::select("propertyKey[]", $key_properties_list, ($value && $value !== "") ? $value[2] : head($key_properties_list), array('id' => 'propertyKey' . $id, 'class' => 'form-control chosen-select', 'onchange' => 'viewMetaDetail(' . $id . ',"' . $key . '");'));
            $view = Form::select("value[]", $data_group, ($value && $value !== "") ? $value : head($key_properties_list), array('id' => 'value' . $id, 'class' => 'form-control chosen-select'));
        }

        return Response::json(array(
                    "status"         => "success",
                    "key_properties" => $key_properties,
                    "data"           => $data,
                    "is_dropdown"    => $is_dropdown,
                    "view"           => $view,
                    "value"          => $value
        ));
    }

}
