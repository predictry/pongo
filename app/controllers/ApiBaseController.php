<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 18, 2014 5:10:42 PM
 * File         : app/controllers/ApiBaseController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     :
 */

namespace App\Controllers;

define('LOKE_RESTAPI_URL', 'http://119.81.72.66');
define('EASYREC_RESTAPI_URL', 'http://demo.easyrec.org:8080/api/1.0/json/');
define('GUI_RESTAPI_URL', 'http://tapirus-lb-sg-788431201.ap-southeast-1.elb.amazonaws.com/predictry/api/v1/');
define("GUI_HTTP_USERNAME", "pongo");
define("GUI_HTTP_PASSWORD", "vventures");

class ApiBaseController extends \Controller
{

    public $predictry_server_api_key   = null;
    public $predictry_server_tenant_id = null;
    public $site                       = null;
    public $site_id                    = null;
    public $http_status                = 200;
    public $gui_domain_auth            = array();
    private $message                   = "";
    public $response                   = [
        'error'          => false,
        'status'         => 200,
        'message'        => '',
        "client_message" => ""
    ];

    public function __construct()
    {
        $this->beforeFilter('@filterRequests');
    }

    /**
     * Filter the incoming requests.
     */
    public function filterRequests($route, $request)
    {

        $this->site_id                    = false;
        $this->predictry_server_tenant_id = \Request::header("X-Predictry-Server-Tenant-ID");
        $this->predictry_server_api_key   = \Request::header("X-Predictry-Server-Api-Key");
        $api_credential                   = [];

        if (!empty($this->predictry_server_api_key) && !empty($this->predictry_server_tenant_id)) {

            $api_credential['tenant_id'] = $this->predictry_server_tenant_id;
            $api_credential['api_key']   = $this->predictry_server_api_key;

            try
            {
                $this->site_id = $this->validateApiKey($api_credential);
            }
            catch (Exception $ex)
            {
                \Log::error($ex->getMessage());
                return \Response::json($this->getErrorResponse("", "500", "", "System malfunction [code 5]", ""), "500");
            }
        }
        else {
            return \Response::json(array("message" => "Auth failed " . $this->message, "status" => "401"), "401");
        }

        if (!$this->site_id)
            return \Response::json(array("message" => "Auth failed " . $this->message, "status" => "401"), "401");
    }

    public function validateApiKey($api_credential)
    {
        try
        {
            $site = \App\Models\Site::where("name", $api_credential['tenant_id'])->where("api_key", $api_credential['api_key'])->first();
        }
        catch (\Exception $ex)
        {
            \Log::error($ex->getMessage());
            $this->message = "Site malfunction [code 5]";
            return false;
        }

        if (is_object($site)) {
            $site = (count($site->toArray()) > 0 && isset($site['url'])) ? $site->toArray() : false;
//            $site = (count($site->toArray()) > 0 && isset($site['url']) && (($site['url'] === "http://" . \Request::server("HTTP_ORIGIN")) || $site['url'] === \Request::server("HTTP_ORIGIN"))) ? $site->toArray() : false;
            if (!$site) {
                $this->message = "[unknown site]";
                return false;
            }
            $this->site = $site;
            return $site['id'];
        }
        else {
            $this->message = "[credential hasn't assigned or wrong]";
            return false;
        }
    }

    public function getErrorResponse($error_key, $http_status, $resources = "", $msg = "", $c_msg = "")
    {
        $response = array(
            'error'          => true, //true / false
            'message'        => '',
            'client_message' => '',
            'status'         => ''
        );

        $client_message = $message        = "";

        switch ($error_key) {
            case "credentialMissing":
                $message = "credential hasn't assigned or wrong";
                break;

            case "notFound":
                $message        = "Resources of {$resources} doesn't exists";
                $client_message = "The {$resources} doesn't exists. Please try again.";
                break;

            case "inputUnknown":
                $message        = "Input of {$resources} unknown";
                $client_message = "Your request cannot be proceed, due to unknown input. Please contact your site administrator.";
                break;

            case "errorValidator":
                $message        = $msg;
                $client_message = $c_msg;
                break;

            case "noResults":
                $message        = "No recommendation results available.";
                $client_message = "No recommendation results available in the moment. Please try again later.";
                break;

            default:
                $message        = $msg;
                $client_message = $c_msg;

                break;
        }

        $response['message']        = $message;
        $response['client_message'] = $client_message;
        $response['status']         = $http_status;
        $this->http_status          = $http_status;

        return $response;
    }

}
