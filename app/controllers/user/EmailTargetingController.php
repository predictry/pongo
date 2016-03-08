<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\CampaignDraft;
use App\Pongo\Repository\EmailTargetingRepository;
use View;
use Input;
use GuzzleHttp;


class EmailTargetingController extends BaseController
{
    private $repository;

    public function __construct(EmailTargetingRepository $repository)
    {
        parent::__construct();
        $this->repository  = $repository;
        $this->http_status = 200;
    }

    public function index()
    {
        $data = ['current_site' => 'test'];
        return View::make(getenv('FRONTEND_SKINS') . $this->theme . ".panels.email.emailtargeting", $data);

    }

    public function save()
    {
        $data = ['current_site' => 'test'];
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

            // save the campaign
            if ($this->repository->save($campaignDraft)) {

                // sent json data to oms
                $tenantId = \Session::get("active_site_name");
                $client = new GuzzleHttp\Client(['base_uri' => 'http://fisher.predictry.com:8090/oms/']);
                $response = $client->request('POST', "email_campaign/$tenantId", ['json' => [
                    'pongoUserId' => $this->active_site_id,
                    'campaignName' => $campaignDraft->campaignname,
                    'targets' => [
                        [
                            'action' => 'BUY',
                            'day' => 7
                        ]
                    ],
                    'mandrillAPIKey' => $campaignDraft->apikey,
                    'emailFrom' => $campaignDraft->usersname,
                    'emailSubject' => $campaignDraft->subject,
                    'template' => $campaignDraft->template
                ]]);

                // check response from oms
                $jsonResponse = json_decode($response->getBody());
                if ($jsonResponse->status == 'created') {
                    $data['message'] = 'Your campaign has been created.';
                } else if ($jsonResponse->status == 'error') {
                    $data['message'] = 'Error while processing your campaign: ' . $jsonResponse->message;
                } else {
                    $data['message'] = 'Unknown response';
                }
            } else {
                $data['message'] = 'There is a problem in saving your campaign.';
            }
        }else {
            $data['message'] = 'You entered invalid fields';
            $data['validationErrors'] = $validator->errors()->all();
        }
        return View::make(getenv('FRONTEND_SKINS') . $this->theme . '.panels.email.sent', $data);
    }

}