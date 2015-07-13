<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 18, 2014 5:10:42 PM
 * File         : app/models/Account.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     :
 */

namespace App\Models;

use Eloquent,
    Illuminate\Auth\Reminders\RemindableInterface,
    Illuminate\Auth\UserInterface,
    Illuminate\Database\Eloquent\SoftDeletingTrait,
    Illuminate\Support\Facades\Hash,
    Zizaco\Entrust\HasRole;

class Account extends Eloquent implements UserInterface, RemindableInterface
{

    use HasRole,
        SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'accounts';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden  = array('password');
    protected $guarded = array('id');
    static $rules      = array(
        'name'                  => 'required',
        'email'                 => 'required|email|unique:accounts',
        'password'              => 'required|min:8|confirmed',
        'password_confirmation' => 'required|min:8',
        'plan_id'               => 'required|exists:plans,id',
        'pricing_method'        => "required|in:CPA,CPC,FREE"
    );

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function getReminderEmail()
    {
        return $this->email;
    }

    /*
     * Relationship
     */

    public function metas()
    {
        return $this->hasMany("App\Models\AccountMeta");
    }

    public function sites()
    {
        return $this->hasMany("App\Models\Site");
    }

}

/* End of file Account.php */
/* Location: ./app/models/Account.php */
