<?php

namespace App\Controllers\Api;

use App\Controllers\ApiBaseController,
    App\Models\ActionInstance,
    App\Models\Item,
    App\Models\Site,
    Response;

/**
 * Author       : Rifki Yandhi
 * Date Created : Oct 2, 2014 1:37:04 PM
 * File         : TenantActionController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class TenantActionController extends ApiBaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($tenant_id)
    {
        //Show recent actions 10

        if (!($this->predictry_server_tenant_id === $tenant_id)) {
            return Response::json([
                        'error'   => true,
                        'message' => 'Auth failed'
            ]);
        }

        $validator = \Validator::make(['tenant_id' => $tenant_id], ['tenant_id' => 'required|exists:sites,name']);
        if ($validator->passes()) {

            $actions          = Site::find($this->site_id)->actions();
            $action_instances = ActionInstance::whereIn("action_id", $actions->get(['id'])->lists("id"))->orderBy('id', 'DESC')->limit(10)->get(['id', 'action_id', 'item_id']);

            $action_instance_metas = [];

            foreach ($action_instances as $action_instance) {


                $action = $action_instance->action()->first();

                $action_instance_meta = $action_instance->action_instance_metas();
                $item                 = Item::find($action_instance->item_id);

                array_push($action_instance_metas, [
                    'action'            => trim($action->name),
                    'action_properties' => $action_instance_meta->get(['key', 'value'])->toArray(),
                    'action_item'       => ($item) ? [
                        'name'       => $item->name,
                        'created_at' => $item->created_at
                            ] : []
                ]);
            }

            return Response::json($action_instance_metas);
        }

        return Response::json([
                    'error'   => true,
                    'message' => $validator->messages()->first()
        ]);
    }

}

/* End of file TenantActionController.php */