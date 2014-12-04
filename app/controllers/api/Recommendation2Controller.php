<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Aug 14, 2014 5:47:40 PM
 * File         : app/controllers/Recommendation2Controller.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Controllers\Api;

use App\Controllers\ApiBaseController,
    App\Pongo\Repository\RecommendationRepository,
    Event,
    Gui,
    Input,
    Response;

class Recommendation2Controller extends ApiBaseController
{

    protected $response     = array();
    private $operator_types = array();
    protected $repository;

    function __construct(RecommendationRepository $repository)
    {
        parent::__construct();

        $this->repository     = $repository;
        $this->operator_types = array(
            'contain'            => 'Contains',
            'equal'              => 'Equals',
            'not_equal'          => 'Not Equals',
            'is_set'             => 'Is Set',
            'is_not_set'         => 'Is Not Set',
            'greater_than'       => 'Greater Than',
            'greater_than_equal' => 'Greater Than or Equal',
            'less_than'          => 'Less Than',
            'less_than_equal'    => 'Less Than or Equal'
        );

        $this->response = array(
            "error"          => false,
            "status"         => 200,
            "message"        => "",
            "client_message" => ""
        );

        $this->gui_domain_auth = array(
//            'appid'  => $this->predictry_server_api_key,
            'appid'  => "pongo", //hardcoded for temporary
            'domain' => $this->predictry_server_tenant_id
        );

        Gui::setUri(GUI_RESTAPI_URL);
        Gui::setCredential(GUI_HTTP_USERNAME, GUI_HTTP_PASSWORD);
        Gui::setDomainAuth($this->gui_domain_auth['appid'], $this->gui_domain_auth['domain']);
    }

    /**
     * Get Recommendation
     *
     * @return {json} 
     */
    public function index()
    {
        $input     = Input::only("item_id", "user_id", "session_id", "widget_id");
        $response  = $reco_data = array();

        if (isset($input['widget_id'])) {
            try
            {
                $reco_data = $this->repository->populateRecoData($this->site_id, $input);

                if (!isset($reco_data['error'])) {
                    //get recommendation
                    $response = $this->repository->getRecommendation($reco_data);

                    if (!is_null($response) && count($response) > 0) {
                        if ($response && isset($response->error)) {
                            $this->http_status = $response->status;
                        }
                        else {
                            if ($response->data->items && count($response->data->items) > 0) {
                                $widget_instance_id = $this->repository->createWidgetInstance($input['widget_id'], $input['session_id']); //no error found, then we have to create new widget instance
                                if ($widget_instance_id && count($response->data->item_ids) > 0) { //when the widget instance ready, then we record the result
                                    $response->data->widget_instance_id = $widget_instance_id; //widget_instance_id
                                    $response->data->items              = $this->repository->appendWidgetInstanceId($response->data->items, $widget_instance_id); //need to append predictry_src=widget_instance_id   
                                    Event::fire("recommendation.response_received", array($response->data->item_ids, $widget_instance_id));
                                }
                            }
                            unset($response->data->item_ids);
                        }
                    }
                    else {
                        $response                  = $this->getErrorResponse("noResults", 200);
                        $response['data']['items'] = [];
                    }
                }
                else
                    $response = $this->getErrorResponse($reco_data['data'][0], $reco_data['data'][1], $reco_data['data'][2]);
            }
            catch (Exception $ex)
            {
                \Log::error($ex->getMessage());
                $response = $this->getErrorResponse("", 400, "", "Malfunction system, please contact administrator", "");
            }
        }
        else
            $response = $this->getErrorResponse("inputUnknown", 400, "widget_id");

        return Response::json($response, $this->http_status);
    }

}

/* End of file Recommendation2Controller.php */