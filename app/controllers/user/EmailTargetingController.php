<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\CampaignDraft;
use App\Pongo\Repository\EmailTargetingRepository;
use View;
use Input;
use Response;


class EmailTargetingController extends BaseController
{
    private $repository;

    public function __construct(EmailTargetingRepository $repository)
    {
        $this->repository  = $repository;
        $this->http_status = 200;
    }

    public function index()
    {
        $data = ['current_site' => 'test'];
        return View::make(getenv('FRONTEND_SKINS') . $this->theme . ".panels.email.emailtargeting", $data);

    }

    public function store()
    {
        $validator = $this->repository->validate(Input::all(), CampaignDraft::$rules);

        if ($validator->passes()) {
            $input   = Input::all();
            $campaignDraft = new CampaignDraft();

            // define the accounts' params
            $campaignDraft->campaignname     = $input['campaignname'];
            $campaignDraft->apikey    = $input['apikey'];
            $campaignDraft->usersname = $input['usersname'];
            $campaignDraft->subject  = $input['subject'];
            $campaignDraft->template = $input['template'];

            if ($this->repository->save($campaignDraft)) {
                $response = ['error'=> false];
            } else {
                $response = ['error'=> true];
            }
        }else {
            $response = ['error'=>true, 'description'=> 'validation failed!'];
        }
        return Response::json($response, 200);
    }

}