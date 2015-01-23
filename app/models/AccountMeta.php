<?php

namespace App\Models;

class AccountMeta extends \Eloquent
{

    public $table        = "account_metas";
    protected $fillable  = ['account_id', 'key', 'value'];
    public static $rules = [
        'key'   => 'required|max:50',
        'value' => 'required|max:150'
    ];

}
