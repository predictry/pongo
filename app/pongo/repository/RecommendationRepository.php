<?php

namespace App\Pongo\Repository;

use App\Models\Filter,
    App\Models\Item,
    App\Models\Session,
    App\Models\Visitor,
    App\Models\Widget,
    App\Models\WidgetFilter,
    App\Models\WidgetInstance,
    Gui;

/**
 * Author       : Rifki Yandhi
 * Date Created : Aug 28, 2014 11:55:24 AM
 * File         : RecommendationRepository.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class RecommendationRepository
{

    protected $error_response = null;

    public function createWidgetInstance($widget_id, $session)
    {
        $visitor_session = Session::where("session", $session)->get()->first();
        $session_id      = ($visitor_session) ? $visitor_session->id : 1;

        $widget_instance = WidgetInstance::create(["widget_id" => $widget_id, "session_id" => $session_id]);
        if ($widget_instance) {
            return $widget_instance->id;
        }

        return false;
    }

    public function populateRecoData($site_id, $input, $limit = null)
    {
        $widget        = null;
        $gui_reco_data = array();

        if (!is_null($input['widget_id'])) {
            $widget = Widget::where("id", $input['widget_id'])->where("site_id", $site_id)->get()->first();
        }

        if (!is_null($widget)) {

            $gui_reco_data = array_add($gui_reco_data, 'widget_id', $widget->id);
            $gui_reco_data = array_add($gui_reco_data, 'type', $widget->reco_type);

            if (isset($input['user_id']) && $input['user_id'] > 0) {
                $visitor = Visitor::where("identifier", $input['user_id'])->get()->first();

                if ($visitor)
                    $gui_reco_data = array_add($gui_reco_data, "user_id", $visitor->id);
                else
                    return array("error" => true, 'data' => array("inputUnknown", 400, "visitor_id"));
            }

            if (isset($input['item_id']) && $input['item_id'] > 0) {
                $item = Item::where("identifier", $input['item_id'])->get()->first();

                if ($item)
                    $gui_reco_data = array_add($gui_reco_data, "item_id", $item->id);
                else
                    return array("error" => true, 'data' => array("inputUnknown", 400, "item_id"));
            }

            if (!is_null($limit))
                array_add($gui_reco_data, "limit", $limit);
        }

        return $gui_reco_data;
    }

    public function setLimit($reco_data, $limit = 6)
    {
        if (is_array($reco_data))
            return array_add($reco_data, "limit", $limit);

        return false;
    }

    public function getRecommendation($reco_data)
    {
        $fields   = []; //fields that requested in the moment are all
        $response = [];

        //get filters
        $widget_filter       = WidgetFilter::where("widget_id", $reco_data['widget_id'])->get()->first();
        $widget_filter_metas = ($widget_filter) ? Filter::find($widget_filter->filter_id)->metas()->get()->toArray() : [];
        unset($reco_data['widget_id']);

        switch ($reco_data['type']) {

            //since we only gui engine, we group the case
            case "oiv": //otheritemsviewed
            case "oivt": //otheritemsviewedtogether
            case "oip": //otheritemspurchased
            case "oipt": //otheritemspurchasedtogether
            case "trv": //topitemsviewed
            case "trp": //topitemspurchasedrecently
            case "trac": //toprecentadditionstocart
                $response = json_decode(Gui::getRecommended($reco_data['type'], $reco_data, $widget_filter_metas, $fields));
//                $response             = json_decode('{"status": 200, "data": {"items": [{"matches": 80, "id": 4491}, {"matches": 41, "id": 360}, {"matches": 40, "id": 4492}, {"matches": 40, "id": 356}, {"matches": 40, "id": 61}, {"matches": 40, "id": 63}, {"matches": 40, "id": 54}]}}');
                break;

            default:
                break;
        }

        //process of getting the details
        if (!is_null($response) && is_object($response) && !isset($response->error)) {
            if ($response->data->items && count($response->data->items) > 0) {
                $item_ids                 = [];
                $items_with_details       = $this->getRecoItemDetails($response->data->items, $item_ids);
                $response->data->item_ids = $item_ids;
                $response->data->items    = $items_with_details;
            }
        }

        return $response;
    }

    public function getRecoItemDetails($items, &$item_ids = [])
    {
        $complete_items = [];
        foreach ($items as $obj) {
            $item = Item::find($obj->id); // lookup from items using id
            if ($item) {
                $detail = [
                    'id'   => $item->identifier, //this is client item identifier
                    'name' => $item->name
                ];

                $metas     = $item->item_metas()->get()->lists("value", "key");
                $reco_item = array_merge($detail, $metas);

                array_push($complete_items, (object) $reco_item);
                array_push($item_ids, array('id' => $obj->id, 'identifier' => $item->identifier)); // need this information to store recommendation results
            }
        }

        return $complete_items;
    }

}

/* End of file RecommendationRepository.php */
    /* Location: RecommendationRepository.php */
    