<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : May 27, 2014 10:49:30 AM
 * File         : app/controllers/ItemController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Controllers\Api;

use App\Controllers\ApiBaseController,
    App\Models\Item,
    App\Models\ItemMeta,
    Input,
    Response,
    Validator;

class Item2Controller extends ApiBaseController
{

    function __construct()
    {
        parent::__construct();
    }

    public function store()
    {
        $item_id    = Input::get("item_id");
        $properties = Input::get("properties");

        $rules = array(
            'item_id' => 'required'
        );

        $validator = Validator::make(array('item_id' => $item_id, 'properties' => $properties), $rules);

        if ($validator->passes()) {
            $item = Item::where("identifier", $item_id)->get()->first();

            if ($item) {
                if (is_array($properties) && count($properties) > 0) {
                    $properties_keys = array_keys($properties);
                    $item_metas      = ItemMeta::where("item_id", $item->id)->get();
                    foreach ($item_metas as $meta) {
                        if (in_array($meta->key, $properties_keys)) {
                            if ($meta->value !== trim($properties[$meta->key])) {
                                $meta->value = trim($properties[$meta->key]);
                                $meta->update();
                            }
                            $index = array_search($meta->key, $properties_keys);
                            unset($properties_keys[$index]);
                        }
                        else
                            $meta->delete();
                    }

                    if (count($properties_keys) > 0) {//means have new additional properties
                        foreach ($properties_keys as $key) {
                            $item_meta          = new ItemMeta();
                            $item_meta->key     = $key;
                            $item_meta->value   = $properties[$key];
                            $item_meta->item_id = $item->id;
                            $item_meta->save();
                        }
                    }
                }

                return Response::json(array("status" => "success", "message" => "Item successfully updated"), "200");
            }
            else
                return Response::json(array('status' => 'failed', 'message' => 'Item not found'), "202");
        }
        else
            return Response::json(array("status" => "failed", "message" => $validator->errors()->first()), "400");
    }

    public function destroy($identifier)
    {
        $validator = Validator::make(['item_id' => $identifier], ['item_id' => "required|exists:items,identifier,site_id,{$this->site_id}"]);

        if ($validator->passes()) {

            $item = Item::where("identifier", $identifier)->where("site_id", $this->site_id)->first();
            if ($item) {
                if ($item->active) {
                    $item->active = false;
                    $item->update();
                }
                $this->response['message'] = "Item succesfully removed";
            }
            else
                $this->response = $this->getErrorResponse("notFound", 200, "item_id");
        }
        else
            $this->response = $this->getErrorResponse("errorValidator", 200);

        return Response::json($this->response);
    }

}

/* End of file ItemController.php */
/* Location: ./application/controllers/ItemController.php */
