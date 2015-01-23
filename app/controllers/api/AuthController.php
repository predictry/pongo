<?php

namespace App\Controllers\Api;

use ApiBaseController,
    App\Models\Account,
    Auth,
    Input,
    Response,
    Validator;

/**
 * Author       : Rifki Yandhi
 * Date Created : Oct 1, 2014 12:11:36 PM
 * File         : AuthController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class AuthController extends ApiBaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($email)
    {
        $account = Account::where('email', $email)->first(['name', 'email']);

        if (!is_null($account)) {

            $this->response['data']['profile'] = $account->toArray();
            return Response::json($this->response, $this->http_status);
        }
    }

    /**
     * API for Auth (Login)
     * 
     * @return json
     */
    public function store()
    {
        $rules = array(
            'email'    => 'required|email',
            'password' => 'required|min:8'
        );

        $input     = Input::all();
        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {
            $account = Account::where("email", $input['email'])->get()->first();
            if ($account) {
                if (Auth::attempt(array('email' => $input['email'], 'password' => $input['password']))) {
                    $input['user_id'] = Auth::user()->id;
                    $code             = \Hash::make(uniqid());

                    $this->response['data']['token'] = $code;
                    return Response::json($this->response);
                }
                else
                    $flash_error = 'error.login.failed';
            }
            else
                $flash_error = "error.email.doesnt.exists";

            $response = [
                'error'   => true,
                'status'  => 400,
                'message' => $flash_error
            ];
        }
        else
            $response = $this->getErrorResponse('errorValidator', 400, '', $validator->messages()->first());

        return Response::json($response);
    }

}

/* End of file AuthController.php */