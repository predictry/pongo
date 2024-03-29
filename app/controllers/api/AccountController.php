<?php

namespace App\Controllers\Api;

use App\Models\Account,
    App\Pongo\Repository\AccountRepository,
    App\Models\Site,
    Event,
    Input,
    Lang,
    Request,
    Response,
    Validator;

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

            $validator = $this->repository->validate(Input::all(), Account::$rules);

            if ($validator->passes()) {
                $input   = Input::all();
                $account = new Account();
                $site    = new Site();
  
                // define the accounts' params
                $account->name     = $input['name'];
                $account->email    = $input['email'];
                $account->password = $input['password'];
                $account->plan_id  = $input['plan_id'];

                // define the sites' params
                $site->name       = $input['site_name'];
                $site->url        = $input['site_url'];
                
                $this->repository->assignConfirmation($account);

                if ($this->repository->saveAccount($account)) {
                    $this->repository->assignUserRoleByEmail($input['email']); //assign user role
                    Event::fire("account.registration_confirmed", $account);  //send verification email (skip to confirmation)
                    $response = [
                        'error'   => false,
                        "data"    => [
                            'user' => $account
                        ],
                        'status'  => 200,
                        'message' => Lang::get('home.success.register')];
                }
                else
                  $response = $this->_getErrorResponse(
                    "", 400, "", "Unable to store the data", "We are unable to process the data. Please try again.");
            }
            else
                $response = $this->_getErrorResponse('errorValidator', 400, "", $validator->messages()->first());
        }
        else
          $response = $this->_getErrorResponse(
            "errorValidator", 400, "", "We only accept JSON, but pizza sounds good.", "Error on data type. Ask your administrator.");

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


