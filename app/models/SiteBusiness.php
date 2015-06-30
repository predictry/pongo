<?php

namespace App\Models;

class SiteBusiness extends \Eloquent
{

    public $table       = "site_business";
    protected $fillable = ['site_id', 'name', 'range_number_of_users', 'range_number_of_items', 'industry_id'];
    public $timestamps  = false;
    public $rules       = [
        'name'        => 'required|max:50',
        'industry_id' => 'required|exists:industries,id'
    ];

}
