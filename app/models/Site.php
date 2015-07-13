<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 18, 2014 5:35:03 PM
 * File         : app/models/Site.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Site extends \Eloquent
{

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table            = 'sites';
    public $manage_table_header = array(
        "name"    => "Tenant ID",
        "url"     => "URL Address",
        "api_key" => "API Key",
//      "api_secret" => "API Secret Key"
      );

    static $rules               = array(
        "name" => "required|regex:/^[a-zA-Z]{1}/|alpha_num|max:32",
        "url"  => "required|unique:sites"
    );

    public function actions()
    {
        return $this->hasMany("App\Models\Action");
    }

    public function members()
    {
        return $this->hasMany("Member");
    }

    public function siteCategory()
    {
        return $this->belongsTo("App\Models\SiteCategory", "site_category_id");
    }

    public function business()
    {
        return $this->hasOne("App\Models\SiteBusiness");
    }

    public function setApiKeyAttribute($value)
    {
        $this->attributes['api_key'] = md5($value);
    }

    public function setApiSecretAttribute($value)
    {
        $this->attributes['api_secret'] = md5($value . uniqid(mt_rand(), true));
    }

}

/* End of file Site.php */
/* Location: ./app/models/Site.php */
