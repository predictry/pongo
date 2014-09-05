<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 18, 2014 5:10:42 PM
 * File         : app/controllers/HomeController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     :
 */

namespace App\Controllers;

use View,
    Auth,
    Input,
    Validator,
    Redirect,
    Hash,
    Config,
    Password,
    App;

class HomeController extends BaseController
{

    protected $layout = 'frontend.layouts.basic';
    protected $account_repository;

    public function __construct(App\Pongo\Repository\AccountRepository $account_repository)
    {
        parent::__construct();
        $this->account_repository = $account_repository;
    }

    /**
     * Display home view.
     * 
     * @return Response
     */
    public function getHome($id, $str)
    {
        return View::make('hello');
    }

    /**
     * Display login view.
     * 
     * @return Response
     */
    public function getLogin()
    {
        $user = Auth::user();
        if (!empty($user->id)) {
            return Redirect::to('home');
        }

        return View::make('frontend.common.login', array("pageTitle" => "Login"));
    }

    /**
     * Handle a POST request to logged in.
     * 
     * @return Response
     */
    public function postLogin()
    {
        $rules = array(
            'email'    => 'required|email',
            'password' => 'required|min:8'
        );

        $input       = Input::only("email", "username", "password", "remember");
        $validator   = $this->account_repository->validate($input, $rules);
        $flash_error = '';

        if ($validator->passes()) {
            $account = \App\Models\Account::where("email", $input['email'])->get()->first();

            if ($account) {
                if (Auth::attempt(array('email' => $input['email'], 'password' => $input['password']), ($input['remember']))) {
                    //validate if member or not
                    $is_member = $this->account_repository->isMember();
                    if (!$is_member)
                        \Session::set("role", "admin");

                    return Redirect::to('home');
                }

                $flash_error = 'error.login.failed';
            }
            else
                $flash_error = "error.email.doesnt.exists";

            return Redirect::to('login')->with('flash_error', $flash_error)->withInput();
        }
        else
            return Redirect::to('login')->withInput()->withErrors($validator);
    }

    /**
     * Display register view.
     * 
     * @return Response
     */
    public function getRegister()
    {
        $this->siteInfo['pageTitle'] = "signup.now";
        return View::make('frontend.common.register');
    }

    /**
     * Handle a POST request to register new account
     * 
     * @return Response
     */
    public function postRegister()
    {
        $input     = Input::only("name", "email", "password", "password_confirmation");
        $validator = $this->account_repository->validate($input);

        if ($validator->passes()) {
            // add necessary info for new account
            $input = array_add($input, 'plan_id', 1);
            $input = array_add($input, "confirmed", 1);
            $input = array_add($input, "confirmation_code", md5(microtime() . Config::get('app.key')));
            unset($input['password_confirmation']);   //we don't need password confirmation on account attributes

            $account = $this->account_repository->newInstance($input); //create new instance
            if ($this->account_repository->saveAccount($account))
                \Event::fire("account.registration_confirmed", $account);  //send verification email (skip to confirmation)
            else
                return Redirect::to('register')->withInput()->withErrors("We are unable to process the data. Please try again.");

            return Redirect::to('login')->with('flash_message', "home.success.register");
        }
        else {
            return Redirect::to('register')->withInput()->withErrors($validator);
        }
    }

    /**
     * Display forgot password view.
     * 
     * @return Response
     */
    public function getForgotPassword()
    {
        return View::make("frontend.common.forgot");
    }

    /**
     * Hanle a POST request to send forgot password email confirmation.
     * 
     * @return Response
     */
    public function postForgotPassword()
    {
        $input = array(
            'email' => Input::get('email')
        );

        $rules = array(
            'email' => 'required|email'
        );

        $validator = Validator::make($input, $rules);
        if ($validator->passes()) {
            $user_id = \App\Models\Account::where("email", $input['email'])->get(array('id'))->first();
            if (!$user_id)
                return Redirect::to('forgot')->with('flash_error', "error.email.doesnt.exists");
            else {
                $response = Password::remind(Input::only('email'), function($message) {
                            $message->subject = "subject.password.reminder";
                        });
                switch ($response) {
                    case Password::INVALID_USER:
                        return Redirect::back()->with('flash_message', \Lang::get($response));

                    case Password::REMINDER_SENT:
                        return Redirect::back()->with('flash_message', \Lang::get($response));
                }
            }
        }
        else {
            return Redirect::back()->withInput()->withErrors($validator);
        }
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  string  $token
     * @return Response
     */
    public function getReset($token = null)
    {
        if (is_null($token))
            App::abort(404);

        return View::make('frontend.common.reset')->with('token', $token);
    }

    /**
     * Handle a POST request to reset a user's password.
     *
     * @return Response
     */
    public function postReset()
    {
        $credentials = Input::only('email', 'password', 'password_confirmation', 'token');

        $rules = array(
            'email'                 => 'required|email',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
            'token'                 => 'required'
        );

        $validator = Validator::make($credentials, $rules);

        if ($validator->passes()) {
            $response = Password::reset($credentials, function($user, $password) {
                        $user->password = Hash::make($password);
                        $user->save();
                    });

            switch ($response) {
                case Password::INVALID_PASSWORD:
                case Password::INVALID_TOKEN:
                case Password::INVALID_USER:
                    return Redirect::back()->with('flash_error', Lang::get($response));
                case Password::PASSWORD_RESET:
                    return Redirect::to('login')->with('flash_message', "success.password.changed");
            }
        }

        $token = Input::get('token');
        return Redirect::to('reset/' . $token)->withInput()->withErrors($validator);
    }

}
