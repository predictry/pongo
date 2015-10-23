<?php

namespace App\Controllers;

use App;
use Auth;
use Carbon\Carbon;
use Config;
use DateTime;
use Event;
use Input;
use Lang;
use Mail;
use Password;
use Redirect;
use Response;
use Session;
use App\Models\Site;
use Validator;
use View;
use Request;
use Guzzle\Service\Client;


class Home2Controller extends BaseController {

    protected $layout = 'frontend.layouts.basic';
    protected $account_repository;

    public function __construct(App\Pongo\Repository\AccountRepository $account_repository) {
        parent::__construct();
        $this->account_repository = $account_repository;
    }

    /**
     * Display home view.
     * 
     * @return Response
     */
    public function getHome($id, $str) {

        if (gettype("2014-04-26 07:59:10") === "string") {
            if (DateTime::createFromFormat('Y-m-d G:i:s', "2014-04-26 07:59:10") !== FALSE) {
                $dt = new Carbon("2014-04-26 07:59:10");
                var_dump($dt->timestamp);
            } else {
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
    public function getLogin() {
        $user = Auth::user();
        if (!empty($user->id)) {
            return Redirect::to('v2/home');
        }

        return View::make(getenv('FRONTEND_SKINS'). $this->theme . '.common.login', array("pageTitle" => \Lang::get("home.login")));
    }

    /**
     * Handle a POST request to logged in.
     * 
     * @return Response
     */
    public function postLogin() {
        $rules = array(
            'email' => 'required|email',
            'password' => 'required|min:8'
        );

        $input = Input::only("email", "username", "password", "remember");
        $validator = $this->account_repository->validate($input, $rules);
        $flash_error = '';

        if ($validator->passes()) {
            $account = \App\Models\Account::where("email", $input['email'])->get()->first();

            if ($account) {
                if (Auth::attempt(array('email' => $input['email'], 'password' => $input['password']), ($input['remember']))) {
                    //validate if member or not
                    $is_member = $this->account_repository->isMember();
                    if (!$is_member)
                        Session::set("role", "admin");

                    if (Auth::user()->hasRole("Administrator")) {
                        return Redirect::to('v2/admin/home');
                    }

                    return Redirect::to('v2/home');
                }

                $flash_error = \Lang::get("error.login.failed");
            } else
                $flash_error = \Lang::get("error.email.doesnt.exists");

            return Redirect::back()->with('flash_error', $flash_error)->withInput();
        } else
            return Redirect::back()->withInput()->withErrors($validator);
    }

    public function getRegister() {
        $pricing_method = \Input::get("pricing");

        if (!empty($pricing_method) && (strtoupper($pricing_method) !== "CPA" && strtoupper($pricing_method) !== "MFF")) {
            $pricing_method = "CPA";
        }

        $pricing_list = [
            'MFF' => 'Monthly Fixed Fee', 
            'CPA' => 'CPA – % of retail price only if product is sold by Predictry',
            'FREE' => 'I can’t decide now, just give me my FREE trial'
        ];

        $industries = App\Models\Industry::all()->lists("name", "id");
        $output = [
            'range_number_of_items' => ['choose' => 'Select the range of Page View Per Month', 'less_than_5k' => '< 20K', '20k_to_100k' => '20k to 100k', '100k_to_500k' => '100k to 500k', '500k_to_1M' => '500k to 1M', 'more_than_1mil' => '> 1M < 5M'],
            'selected_range_number_of_items' => '0_to_500',
            'selected_industry_id' => 1,
            'pricing_list' => $pricing_list,
            'pageTitle' => \Lang::get("home.signup.now"),
            'pricing_method' => strtoupper($pricing_method),
            'industries' => $industries
        ];

        return View::make(getenv('FRONTEND_SKINS') . $this->theme . '.common.register', $output);
    }

    public function postRegister() {
        $client = new Client($_ENV['ACTIVE_ENDPOINT']);
        $input = Input::only(
                      "name", 
                      "email", 
                      "password", 
                      "password_confirmation", 
                      "pricing_method", 
                      "url", 
                      "range_number_of_items", 
                      "industry_id");
      
        $input = array_add($input, 'plan_id', 1);

        if(mb_substr($input['url'], 0, 4) !== 'http') 
          $url = 'http://' . $input['url']; 
        else 
          $url = $input['url'];

        $site_host =  parse_url($url)['host'];
        $site_name = strtoupper(str_replace('.', '', $site_host));
        $input_site = array(
          'name' => $site_name,
          'url'  => $site_host
        );
        
        $email = array(
          'name'      => $input['name'],
          'email'     => $input['email'],
          'tenantID'  => $input_site['name'],
          'pricing'   => $input['pricing_method'],
          'skuCount'  => $input['range_number_of_items'],
          'industry'  => $input['industry_id']
        );
          
        $active_q = array( 
          'tenantId'  =>  $site_name ,
          'action'    => 'new'
        );

        $request = $client->post();
        $request->setBody(json_encode($active_q), 'application/json');
        $request->setAuth('admin', 'admin');
        $response = $request->send();

        $rules = array_merge(App\Models\Account::$rules, [
            'range_number_of_items' => 'required|in:less_than_5k,20k_to_100k,100k_to_500k,500k_to_1M,more_than_1mil',
        ]);
        
        $site_rules = App\Models\Site::$rules;
    
        $account_validator = $this->account_repository->validate($input, $rules); 
        $site_validator = $this->account_repository->siteValidate($input_site, $site_rules);

        
        if ( ($account_validator->passes()) && ($site_validator->passes()) ) {
            try {
                // add necessary info for new account
                $input = array_add($input, "confirmed", 1);
                $input = array_add($input, "confirmation_code", md5(microtime() . Config::get('app.key')));
                unset($input['password_confirmation']);   //we don't need password confirmation on account attributes

                $account = $this->account_repository->newInstance([
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'password' => $input['password'],
                    'confirmed' => 1,
                    'confirmation_code' => md5(microtime() . Config::get('app.key')),
                    'plan_id' => 1
                ]); //create new instance
        
                if ($this->account_repository->saveAccount($account)) {
                    $this->account_repository->assignUserRoleByEmail($input['email']); //assign user role
                    //create site
                    $site_repository = new App\Pongo\Repository\SiteRepository();
                    $site_id = $site_repository->createSiteBasedOnURL($account, $input['url'], 1);
                     
                    //update business
                    if ($site_id) {
                        $parse_url = parse_url($input['url']);
                        $host = null;

                        if (!empty($parse_url['host'])) {
                            $host = $parse_url['host'];
                        }

                        $site_business = \App\Models\SiteBusiness::firstOrCreate([
                                    'name' => (!empty($host) && !is_null($host)) ? strtoupper(str_replace('.', '', $host)) : '',
                                    'site_id' => $site_id,
                                    'range_number_of_users' => isset($input['range_number_of_users']) ? $input['range_number_of_users'] : '',
                                    'range_number_of_items' => isset($input['range_number_of_items']) ? $input['range_number_of_items'] : '',
                                    'industry_id' => $input['industry_id']
                        ]);
                    } else {
                        App\Models\Site::where('account_id', $account->id)->delete();
                        App\Models\Account::where('email', $input['email'])->delete();
                        return Redirect::to('v2/register')->withInput()->withErrors("We are unable to process the data. Please try again.");
                    }
                    
                    Event::fire("account.registration_confirmed", $account);  //send verification email (skip to confirmation)
                    Mail::send('emails.workers.noti', $email, function($message) {
                      $message->to('stewart@predictry.com', 'Stewart')->subject('New user on-boarded!');
                    });
                } else
                    return Redirect::to('v2/register')->withInput()->withErrors("We are unable to process the data. Please try again.");
                return Redirect::to('v2/login')->with('flash_message', \Lang::get("home.success.register"));
            } catch (Exception $ex) {
                \Log::info($ex->getMessage());
                return Redirect::to('v2/register')->withInput()->withErrors("We are unable to process the data. Please try again.");
            }
        } else
          $valudation_message = array_merge_recursive( 
            $account_validator->messages()->toArray(), 
            $site_validator->messages()->toArray()
          );
            return Redirect::back()->withInput()->withErrors($valudation_message);
    }

    public function getForgotPassword() {
        return View::make(getenv('FRONTEND_SKINS') . $this->theme . ".common.forgot", ['pageTitle' => \Lang::get("home.reset.password")]);
    }

    /**
     * Hanle a POST request to send forgot password email confirmation.
     * 
     * @return Response
     */
    public function postForgotPassword() {
        $input = array(
            'email' => Input::get('email')
        );

        $rules = array(
            'email' => 'required|email'
        );

        $validator = Validator::make($input, $rules);
        if ($validator->passes()) {
            $user_id = App\Models\Account::where("email", $input['email'])->get(array('id'))->first();
            if (!$user_id)
                return Redirect::to('v2/forgot')->with('flash_error', \Lang::get("error.email.doesnt.exists"));
            else {
                $response = Password::remind(Input::only('email'), function($message) {
                            $message->subject = \Lang::get("home.subject.password.reminder");
                        });
                switch ($response) {
                    case Password::INVALID_USER:
                        return Redirect::back()->with('flash_message', Lang::get($response));

                    case Password::REMINDER_SENT:
                        return Redirect::back()->with('flash_message', Lang::get($response));
                }
            }
        } else {
            return Redirect::back()->withInput()->withErrors($validator);
        }
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  string  $token
     * @return Response
     */
    public function getReset($token = null) {
        if (is_null($token))
            App::abort(404);

        return View::make(getenv('FRONTEND_SKINS') . $this->theme . '.common.reset')->with('token', $token);
    }

    /**
     * Handle a POST request to reset a user's password.
     *
     * @return Response
     */
    public function postReset() {
        $credentials = Input::only('email', 'password', 'password_confirmation', 'token');

        $rules = array(
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
            'token' => 'required'
        );

        $validator = Validator::make($credentials, $rules);

        if ($validator->passes()) {
            $response = Password::reset($credentials, function($user, $password) {
                        $user->password = $password;
                        $user->save();
                    });

            switch ($response) {
                case Password::INVALID_PASSWORD:
                case Password::INVALID_TOKEN:
                case Password::INVALID_USER:
                    return Redirect::back()->with('flash_error', Lang::get($response));
                case Password::PASSWORD_RESET:
                    return Redirect::to('/')->with('flash_message', \Lang::get("home.success.password.changed"));
            }
        }

        $token = Input::get('token');
        return Redirect::to('v2/password/reset/' . $token)->withInput()->withErrors($validator);
    }

    public function bucket($start, $end , $action, $interval, $value ="overall", $tenantID) {
      $client   = new Client($_ENV['PREDICTRY_ANALYTICS_URL'] . 'stat/combined');
      if ($tenantID) {
        $site         = Site::find($tenantID);
        $current_site = $site['name'];
      } else {
        $current_site =   \Session::get("active_site_name");
      }

      $response = $client->get("?tenantId=". $current_site . "&startDate=" . $start . "&endDate=" . $end . "&metric=". $action . "&interval=" .$interval . "&valueType=" . $value)->send(); 
      return $response->json();
    }
}
