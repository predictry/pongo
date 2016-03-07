<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class CampaignDraft extends \Eloquent
{

    use SoftDeletingTrait;

    protected $table = 'draft';

    static $rules = array(
        'campaignname'          => 'required|min:4',
        'apikey'                => 'required',
        'usersname'             => 'required',
        'subject'               => 'required',
        'template'              => 'required'

    );



}