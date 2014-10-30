<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 18, 2014 5:10:42 PM
 * File         : app/controllers/HomeController.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     :
 */

namespace App\Controllers;

use App\Models\Account,
    App\Models\Plan,
    App\Models\SiteCategory,
    App\Pongo\Repository\AccountRepository,
    App\Pongo\Repository\SiteRepository,
    Auth,
    Carbon\Carbon,
    DateTime,
    Event,
    Input,
    Lang,
    Log,
    Password,
    Redirect,
    Response,
    Str,
    Validator,
    View;

class HomeController extends BaseController
{

    protected $layout = 'frontend.layouts.basic';
    protected $account_repository;

    public function __construct(AccountRepository $account_repository)
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

        if (gettype("2014-04-26 07:59:10") === "string") {
            if (DateTime::createFromFormat('Y-m-d G:i:s', "2014-04-26 07:59:10") !== FALSE) {
                $dt = new Carbon("2014-04-26 07:59:10");
                var_dump($dt->timestamp);

//                echo "it's a date";
            }
            else {
                echo "it's not a date";
            }
        }
        die;

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

            $account = Account::where("email", $input['email'])->get()->first();

            if ($account) {

                if (Auth::attempt(array('email' => $input['email'], 'password' => $input['password']), ($input['remember']))) {

                    if (!$account->confirmed) {
                        Auth::logout();
                        $flash_error = \Lang::get("home.error.account.have.not.confirmed");
                        return Redirect::to('login')->with('flash_error', $flash_error)->withInput();
                    }

                    $is_member = $this->account_repository->isMember();
                    if (!$is_member) //validate if member or not
                        \Session::set("role", "admin");

                    return Redirect::to('home');
                }

                $flash_error = \Lang::get("home.error.login.failed");
            }
            else
                $flash_error = \Lang::get("home.error.email.doesnt.exists");

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

        $site_categories = SiteCategory::orderBy('id', 'ASC')->get()->lists("name", "id");
        $plans           = Plan::orderBy('id', 'ASC')->get()->lists("name", "id");

        $output = [
            'site_categories'           => $site_categories,
            'plans'                     => $plans,
            'selected_site_category_id' => 1,
            'selected_plan_id'          => 4
        ];
        return View::make('frontend.common.register', $output);
    }

    /**
     * Handle a POST request to register new account
     * 
     * @return Response
     */
    public function postRegister()
    {
        $rules           = array_add(Account::$rules, 'site_url', 'required|unique:sites,url');
        $site_repository = new SiteRepository();
        $validator       = $this->account_repository->validate(Input::all(), $rules);

        if ($validator->passes()) {
            $input   = Input::all();
            $account = new Account();

            $account->name     = $input['name'];
            $account->email    = $input['email'];
            $account->password = $input['password'];
            $account->plan_id  = $input['plan_id'];
            $this->account_repository->assignConfirmation($account);

            if ($this->account_repository->saveAccount($account)) {

                $site_name = Str::random(6); //tenant_id
                $site      = $site_repository->createNewSite($account->id, $site_name, $input['site_url']);
                Event::fire("account.registered", $account);  //send verification email
                Event::fire("site.set_default_actions", [$site]);
                Event::fire("site.set_default_funnel_preferences", [$site]);
            }
            else
                return Redirect::to('register')->withInput()->withErrors("We are unable to process the data. Please try again.");

            return Redirect::to('login')->with('flash_message', \Lang::get('home.success.register'));
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
            $user_id = Account::where("email", $input['email'])->get(array('id'))->first();
            if (!$user_id)
                return Redirect::to('forgot')->with('flash_error', "error.email.doesnt.exists");
            else {
                $response = Password::remind(Input::only('email'), function($message) {
                            $message->subject = "subject.password.reminder";
                        });
                switch ($response) {
                    case Password::INVALID_USER:
                        return Redirect::back()->with('flash_message', Lang::get($response));

                    case Password::REMINDER_SENT:
                        return Redirect::back()->with('flash_message', Lang::get($response));
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
                        $user->password = $password;
                        $user->update();
                    });

            switch ($response) {
                case Password::INVALID_PASSWORD:
                    return Redirect::to('forgot')->with('flash_error', "Invalid Password");
                case Password::INVALID_TOKEN:
                    return Redirect::to('forgot')->with('flash_error', "Invalid Token");
                case Password::INVALID_USER:
                    return Redirect::back()->with('flash_error', Lang::get($response));
                case Password::PASSWORD_RESET:
                    return Redirect::to('login')->with('flash_message', "success.password.changed");
            }
        }

        $token = Input::get('token');
        return Redirect::to('reset/' . $token)->withInput()->withErrors($validator);
    }

    public function getConfirmation($confirmation_token = null)
    {
        if (is_null($confirmation_token))
            App::abort(404);

        $account = Account::where('confirmation_code', $confirmation_token)->first();

        if ($account) {
            $account->confirmed = 1;
            $account->update();
            Event::fire("account.registration_confirmed", $account);  //send confirmation email
            return Redirect::to('login')->with('flash_message', "success.confirmation.correct");
        }

        return Redirect::to('/');
    }

}
