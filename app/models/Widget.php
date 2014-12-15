<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 17, 2014 11:06:40 AM
 * File         : app/models/Widget.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Widget extends \Eloquent
{

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table            = 'widgets';
    public $rules               = array(
        "name" => "required|max:64",
        "algo" => "required|exists:algorithms,name"
    );
    public $manage_table_header = array(
        "id"                 => "Widget ID",
        "name"               => "Name",
        "created_at"         => "Date Created",
        "number_of_rulesets" => "Number of Ruleset(s)"
    );

    public function widget_instances_and_items()
    {
        return $this->hasManyThrough("App\Models\WidgetInstanceItem", "App\Models\WidgetInstance");
    }

    public function widget_rule_sets()
    {
        return $this->hasMany('App\Models\WidgetRuleSet');
    }

}

/* End of file Widget.php */
/* Location: ./app/models/Widget.php */
