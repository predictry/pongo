<?php

use App\Models\Item,
    App\Models\WidgetInstanceItem;

/**
 * Author       : Rifki Yandhi
 * Date Created : Aug 28, 2014 12:05:34 PM
 * File         : RecommendationEventHandler.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class RecommendationEventHandler
{

    public function subscribe($events)
    {
        $events->listen('recommendation.response_received', 'RecommendationEventHandler@saveRecoResult');
    }

    /**
     * Record recommendation results.
     * 
     * @param array $reco_ids
     * @param int $widget_instance_id
     */
    public function saveRecoResult($reco_ids, $widget_instance_id)
    {
        //Added widget Instance Metas
        $new_records = array();
        foreach ($reco_ids as $id) {
            $record = array(
                "widget_instance_id" => $widget_instance_id,
                "item_id"            => $id
            );

            array_push($new_records, $record);
        }

        WidgetInstanceItem::insert($new_records);
    }

}

/* End of file RecommendationEventHandler.php */
