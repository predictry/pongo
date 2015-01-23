<?php

namespace App\Models;

class SiteCategory extends \Eloquent
{

    protected $table    = 'site_categories';
    protected $fillable = ['name'];
    public $timestamps  = false;

}
