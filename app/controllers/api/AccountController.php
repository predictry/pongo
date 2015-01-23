<?php

namespace App\Controllers\Api;

use App\Models\Account,
    App\Pongo\Repository\AccountRepository,
    Event,
    Input,
    Lang,
    Request,
    Response,
    Validator;

/**
 * Author       : Rifki Yandhi
 * Date Created : Oct 1, 2014 4:10:20 PM
 * File         : AccountController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class AccountController extends \Controller
{

    private $repository;
    private $http_status;

    public function __construct(AccountRepository $repository)
    {
        $this->repository  = $repository;
        $this->http_status = 200;
    }

    public function store()
    {
        if (Request::isJson()) {

            $rules     = array_add(Account::$rules, 'site_url', 'required|unique:sites,url');
            $validator = $this->repository->validate(Input::all(), $rules);

            if ($validator->passes()) {
                $input   = Input::all();
                $account = new Account();

                $account->name     = $input['name'];
                $account->email    = $input['email'];
                $account->password = $input['password'];
                $account->plan_id  = $input['plan_id'];
                $this->repository->assignConfirmation($account);

                if ($this->repository->saveAccount($account)) {
                    Event::fire("account.registration_confirmed", $account);  //send verification email (skip to confirmation)
//                    $site             = new Site();
//                    $site->name       = Str::random(6);
//                    $site->api_key    = md5($input['url']);
//                    $site->api_secret = md5($input['url'] . uniqid(mt_rand(), true));
//                    $site->account_id = $account->id;
//                    $site->url        = $input['url'];
//                    $site->save();
//
//                    Event::fire("site.set_default_actions", [$site]);
//                    Event::fire("site.set_default_funnel_preferences", [$site]);

                    $response = [
                        'error'   => false,
                        'status'  => 200,
                        'message' => Lang::get('home.success.register')];
                }
                else
                    $response = $this->_getErrorResponse("", 400, "", "Unable to store the data", "We are unable to process the data. Please try again.");
            }
            else
                $response = $this->_getErrorResponse('errorValidator', 400, "", $validator->messages()->first());
        }
        else
            $response = $this->_getErrorResponse("errorValidator", 400, "", "We only accept JSON, but pizza sounds good.", "Error on data type. Ask your administrator.");

        return Response::json($response, $this->http_status);
    }

    public function show($email)
    {
        $validator = Validator::make(['email' => $email], ['email' => 'required|email|exists:accounts,email']);

        if ($validator->passes()) {
            $account = Account:: where('email', $email)->first([ 'name', 'email', 'plan_id']);
            return Response::json([
                        'error'   => false,
                        'message' => '',
                        'data'    => ['profile' => $account->toArray()]
            ]);
        }

        return Response::json($this->getErrorResponse('errorValidator', 200, '', $validator->messages()->first()));
    }

    public function _getErrorResponse($error_key, $http_status, $resources = "", $msg = "", $c_msg = "")
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
                break;
        }

        $response['message']        = $message;
        $response['client_message'] = $client_message;
        $response['status']         = $http_status;
        $this->http_status          = $http_status;

        return $response;
    }

}

/* End of file AccountController.php */