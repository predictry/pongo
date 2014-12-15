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
    App\Pongo\Repository\ActionRepository,
    Input,
    Response,
    Validator;

class Item2Controller extends ApiBaseController
{

    private $action_repository;

    function __construct(ActionRepository $repository)
    {
        parent::__construct();
        $this->action_repository = $repository;
    }

    public function store()
    {
        $is_new_item         = false;
        $item_data           = Input::get("item");
        $item_property_rules = array(
            "item_id"  => "required|alpha_num",
            "name"     => "required",
            "price"    => "required|numeric",
            "img_url"  => "required|url",
            "item_url" => "required|url"
        );

        $item_validator = Validator::make($item_data, $item_property_rules);

        if ($item_validator->passes()) {

            $item_id = false;
            $item    = Item::firstOrCreate([
                        'identifier' => $item_data['item_id'],
                        'name'       => $item_data['name'],
                        'site_id'    => $this->site_id
            ]);

            if (isset($item->id)) {
                foreach ($item_data as $key => $value) {
                    $item_meta          = new ItemMeta();
                    $item_meta->item_id = $item->id;
                    $item_meta->key     = $key;

                    if (is_array($value))
                        $value = json_encode($value);

                    $item_meta->value = $value;
                    $item_meta->save();
                }

                $is_new_item = $item_id     = $item->id;
            }

            $this->response['message'] = "Item successfully added";
            return Response::json($this->response, "200");
        }
        else
            return Response::json(array("status" => "failed", "message" => $item_validator->errors()->first()), "400");
    }

    public function update($identifier)
    {
        $item_data            = Input::get("item");
        $item_data['item_id'] = $identifier;

        $item_property_rules = array(
            "item_id"  => "required|alpha_num",
            "name"     => "required",
            "price"    => "required|numeric",
            "img_url"  => "required|url",
            "item_url" => "required|url"
        );

        $item_validator = Validator::make($item_data, $item_property_rules);

        if ($item_validator->passes()) {
            $item = Item::where("identifier", $identifier)->where("site_id", $this->site_id)->first();
            if ($item) {
                $item_id = $item->id;
                if (isset($item_data['name']) && $item_data['name'] !== "" && ($item->name !== $item_data['name'])) {
                    $item->name = $item_data['name'];
                    $item->update();
                }
                $this->action_repository->compareAndUpdateItemMetas($item->id, $item_data);
            }

            if ($item_id) {
                $this->response['message'] = "Item successfully updated";
                return Response::json($this->response, "200");
            }
            else {
                return Response::json($this->getErrorResponse("notFound", 400, "item_id"), "200");
            }
        }
        else
            return Response::json(array("status" => "failed", "message" => $item_validator->errors()->first()), "400");
    }

    public function destroy($identifier)
    {
        $validator = Validator::make(['identifier' => $identifier], ['identifier' => "required|exists:items,identifier,site_id,{$this->site_id}"]);

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
            $this->response = $this->getErrorResponse("errorValidator", 200, $validator->messages()->first());

        return Response::json($this->response);
    }

}

/* End of file ItemController.php */
/* Location: ./application/controllers/ItemController.php */
