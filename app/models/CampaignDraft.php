<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class CampaignDraft extends \Eloquent
{

    use SoftDeletingTrait;

    protected $table = 'draft';

    static $rules = array(
        'campaignname'          => 'required',
        'apikey'                => 'required',
        'usersname'             => 'required',
        'subject'               => 'required',
        'template'              => 'required',
        'status'                => 'in:draft,pending,delivered',
        'timeframe'             => 'required'
    );



}