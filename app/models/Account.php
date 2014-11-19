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
    Illuminate\Support\Facades\Hash;

class Account extends Eloquent implements UserInterface, RemindableInterface
{

    use SoftDeletingTrait;

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
        'password'              => 'required|min:8|confirmed',
        'password_confirmation' => 'required|min:8',
        'email'                 => 'required|email|unique:accounts'
    );

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
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

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

}

/* End of file Account.php */
/* Location: ./app/models/Account.php */
