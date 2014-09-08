<?php

use Carbon\Carbon;

/**
 * Author       : Rifki Yandhi
 * Date Created : Aug 21, 2014 3:01:11 PM
 * File         : Gui.php
 * Function     : 
 */
class Gui
{

    static $username, $password, $uri, $uriDomain, $appid, $domain;

    function __construct()
    {
        self::$username = null;
        self::$password = null;
        self::$uri = null;
        self::$uriDomain = null;
    }

    public static function setCredential($username, $password)
    {
        self::$username = $username;
        self::$password = $password;
    }

    public static function setDomainAuth($appid, $domain)
    {
        self::$appid = $appid;
        self::$domain = $domain;
    }

    public static function setUri($uri)
    {
        self::$uri = $uri;
    }

    public static function setResourcesUri($resources)
    {
        $item_resources_uri = self::$uri . "{$resources}";
        $domain_auth        = array(
            "appid"  => self::$appid,
            "domain" => self::$domain
        );

        $item_resources_uri_with_credential = $item_resources_uri . '/?' . http_build_query($domain_auth);
        self::$uriDomain = $item_resources_uri_with_credential;
    }

    public static function setAccess($uri, $credential, $domain_auth)
    {
        if ($uri !== "")
            self::setUri($uri);

        if (count($credential) > 0 && isset($credential['username']) && isset($credential['password']))
            self::setCredential($credential['username'], $credential['password']);

        if (count($domain_auth) > 0 && isset($domain_auth['appid']) && isset($domain_auth['domain']))
            self::setDomainAuth($domain_auth['appid'], $domain_auth['domain']);
    }

    /**
     * 
     * @param array $item_data
     * @param array $credential
     */
    public static function postItem($id, $item_data, $uri = "", $domain_auth = array(), $credential = array())
    {
        $params_allowed = array(
            "name", "brand", "model", "description", "tags", "price", "category",
            "sub_category", "date_added", "item_url", "image_url", "start_date", "end_date", "locations"
        );

        self::setAccess($uri, $credential, $domain_auth);

        $gui_item_data = self::filterKeys($params_allowed, $item_data);
        $gui_item_data = array_add($gui_item_data, "id", $id); //item id from items table

        self::setResourcesUri("items");
        Log::info("postItem gui_item_data: " . json_encode($gui_item_data));
        $response = self::send("post", self::$uriDomain, $gui_item_data);

        return $response;
    }

    public static function postUser($id, $user_data, $uri = "", $domain_auth = array(), $credential = array())
    {
        $params_allowed = array("email", "timestamp");

        self::setAccess($uri, $credential, $domain_auth);
        $gui_user_data = self::filterKeys($params_allowed, $user_data);
        $gui_user_data = array_add($gui_user_data, "id", $id); //item id from items table

        self::setResourcesUri("users");
        Log::info("postUser gui_user_data: " . json_encode($gui_user_data));
        $response = self::send("post", self::$uriDomain, $gui_user_data);
        return $response;
    }

    public static function postAction($id, $action_data, $uri = "", $domain_auth = array(), $credential = array())
    {
        $params_allowed = array(
            "user_id", "item_id", "type", "timestamp", "ip_address",
            "browser_id", "session_id", "guid", "agent", "quantum"
        );

        self::setAccess($uri, $credential, $domain_auth);
        $gui_action_data = self::filterKeys($params_allowed, $action_data);
        $gui_action_data = array_add($gui_action_data, "id", $id); //action instance id from action_instance table
        $gui_action_data = array_add($gui_action_data, "type", $action_data['name']); //type is action name

        self::setResourcesUri("actions");
        Log::info("postAction gui_action_data: " . json_encode($gui_action_data));
        $response = self::send("post", self::$uriDomain, $gui_action_data);
        return $response;
    }

    public static function filterKeys($allowed_params, $data)
    {
        $new_data = array();
        foreach ($data as $key => $val) {
            if (in_array($key, $allowed_params))
                $new_data = array_add($new_data, $key, $val);
        }

        return $new_data;
    }

    public static function send($method, $resources_uri, $data)
    {
        if (strtoupper($method) === "POST") {
            $ch = curl_init($resources_uri);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            Log::info("send resources_uri: " . $resources_uri);
        }
        else {
            $ch = curl_init($resources_uri . ($data ? '&' . http_build_query($data, NULL, '&') : ''));
            Log::info("send resources_uri: " . $resources_uri . ($data ? '&' . http_build_query($data, NULL, '&') : ''));
        }

        curl_setopt($ch, CURLOPT_USERPWD, self::$username . ':' . self::$password);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_VERBOSE, TRUE);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=UTF-8'
        ));
        curl_setopt($ch, CURLOPT_HEADER, false);

        $response = curl_exec($ch);
        Log::debug($response);
        //\Log::info(curl_getinfo($ch));
        Log::notice("---------------");
        Log::notice("---------------");
//
        return $response;
    }

    /**
     * Get Recommended Items
     * 
     * @param string $type
     * @param array $fields
     * @param array $filters
     * @param string $uri
     * @param array $domain_auth
     * @param array $credential
     * @return object
     */
    public static function getRecommended($type, $identifiers = array(), $filters = array(), $fields = array(), $limit = null, $uri = "", $domain_auth = array(), $credential = array())
    {
        $reco_data = array_merge(array(), $identifiers); //apply identifiers

        $str_filter_query = (count($filters) > 0) ? self::buildQuery($filters) : "";

        if ($str_filter_query !== "")
            $reco_data = array_add($reco_data, "q", $str_filter_query); //apply queries

        $str_fields = (count($fields) > 0) ? implode(",", $fields) : "";
        if ($str_fields !== "")
            $reco_data  = array_add($reco_data, "fields", $str_fields); //apply fields

        if ($limit !== null && is_int($limit))
            $reco_data = array_add($reco_data, "limit", $limit);

        $reco_data = array_add($reco_data, "type", $type); //apply type

        self::setAccess($uri, $credential, $domain_auth);
        self::setResourcesUri("recommend");
        Log::info("getRecommended reco_data: " . json_encode($reco_data));
        return self::send("get", self::$uriDomain, $reco_data);
    }

    //@todo Build standard query language for filtering purpose to get recommended items or any possible request that requires it.
    public static function buildQuery($filters)
    {
        // Sample Data
        // Operators such as e,gt,gte,lt,lte,ct,nct is optional for type number, string, date, list
        // Equal operator wouldn't work for list type [1,2] eq [2,1]
        // 
//                $filters = array(
//            array(
//                "property" => "price",
//                "operator" => "greater_than",
//                "type"     => "num",
//                "value"    => 100
//            ),
//            array(
//                "property" => "category",
//                "operator" => "contain",
//                "type"     => "str",
//                "value"    => "masak"
//            ),
//            array(
//                "property" => "end_date",
//                "operator" => "lte",
//                "type"     => "date",
//                "value"    => 123123131212
//            ),
//            array(
//                "property" => "end_date",
//                "operator" => "cti",
//                "type"     => "list",
//                "value"    => [100, 200, 300] | ["est", "test", "bla"]
//            )
//        );

        $operator_string_alias = array(
            "greater_than"       => "gt",
            "less_than"          => "lt",
            "greater_than_equal" => "gte",
            "less_than_equal"    => "lte",
            "not_equal"          => "ne",
            "equal"              => "e",
            "contain"            => "cti",
            "not_contain"        => "ncti"
        );

        $divider     = "$";
        $query_str   = $filter_type = '';

        foreach ($filters as $filter) {
            $is_list      = false;
            $filter_value = '';

            if ($filter['type'] === "date") {
                if (DateTime::createFromFormat('Y-m-d G:i:s', $filter['value']) !== FALSE) {
                    $dt           = new Carbon($filter['value']);
                    $filter_value = $dt->timestamp;
                    $filter_type  = "date";
                }
            }
            else if ($filter['type'] === "list") {
                $list = explode(',', $filter['value']);
                if (count($list) > 0) {
                    //get the first item on the list to determine the data type
                    foreach ($list as $val) {
                        if (gettype($val) === "string")
                            $filter_type = "str";
                        else if (gettype($val) === "integer")
                            $filter_type = "num";
                        break;
                    }
                    $is_list = true;
                }
            }

//            switch (gettype($filter['value'])) {
//                case "string":
//                    if (DateTime::createFromFormat('Y-m-d G:i:s', $filter['value']) !== FALSE) {
//                        $dt           = new Carbon($filter['value']);
//                        $filter_value = $dt->timestamp;
//                        $filter_type  = "date";
//                    }
//                    break;
//                case "integer":
//                    $filter_type = "num";
//                    break;
//                case "array":
//                    $filter_type = "list";
//                    break;
//                default:
//                    $filter_type = $filter['type'];
//                    break;
//            }
            $filter_value = ($filter_value !== "") ? $filter_value : $filter['value'];
            $filter_type  = ($filter_type !== "") ? $filter_type : $filter['type'];


            $query_str .= "{$filter['property']}"
                    . "{$divider}{$operator_string_alias[$filter['operator']]}"
                    . "{$divider}{$filter_value}"
                    . "{$divider}{$filter_type}";

            if ($is_list)
                $query_str .= "{$divider}ls";

            $query_str .= "|";
        }

        return substr($query_str, 0, strlen($query_str) - 1);
        //sample result 
        //$price$gt$100$num
        //$category$ct$masak$str
        //$tags$ct$electronics,sports$str$ls
    }

}
