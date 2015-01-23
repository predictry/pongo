<?php

namespace App\Models;

class Industry extends \Eloquent
{

    public $table       = "industries";
    protected $fillable = ['name'];
    public $timestamps  = false;

}
