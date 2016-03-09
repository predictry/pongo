<?php

namespace App\Pongo\Repository;

use App\Models\CampaignDraft,
    Config,
    Illuminate\Support\Facades\Auth,
    Session,
    Validator;

class EmailTargetingRepository
{

    public function newInstance($attributes = array())
    {
        return new CampaignDraft($attributes);
    }

    public function validate($input, $rules = array())
    {
        $rules = count($rules) > 0 ? $rules : CampaignDraft::$rules;
        return Validator::make($input, $rules);
    }

    public function save(CampaignDraft $campaignDraft)
    {
        return $campaignDraft->save();
    }

    public function update(CampaignDraft $newCampaignDraft)
    {
        $campaignDraft = CampaignDraft::find($newCampaignDraft->id);
        $campaignDraft->campaignname = $newCampaignDraft->campaignname;
        $campaignDraft->apikey = $newCampaignDraft->apikey;
        $campaignDraft->usersname = $newCampaignDraft->usersname;
        $campaignDraft->template = $newCampaignDraft->template;
        $campaignDraft->timeframe = $newCampaignDraft->timeframe;
        $campaignDraft->subject = $newCampaignDraft->subject;
        return $campaignDraft->save();
    }

    public function updateRequestId(CampaignDraft $campaignDraft, $request_id)
    {
        $campaignDraft = CampaignDraft::find($campaignDraft->id);
        $campaignDraft->request_id = $request_id;
        return $campaignDraft->save();
    }

    public function updateRecipients(CampaignDraft $campaignDraft, $recipients)
    {
        $campaignDraft = CampaignDraft::find($campaignDraft->id);
        $campaignDraft->recipients = $recipients;
        $campaignDraft->status = 'delivered';
        return $campaignDraft->save();
    }


}