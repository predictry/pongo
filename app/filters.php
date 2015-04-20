<?php

/*
  |--------------------------------------------------------------------------
  | Application & Route Filters
  |--------------------------------------------------------------------------
  |
  | Below you will find the "before" and "after" events for the application
  | which may be used to do any work before or after a request into your
  | application. Here you may also register your custom route filters.
  |
 */

App::before(function($request) {
//
});


App::after(function($request, $response) {
//
});

/*
  |--------------------------------------------------------------------------
  | Authentication Filters
  |--------------------------------------------------------------------------
  |
  | The following filters are used to verify that the user of the current
  | session is logged into this application. The "basic" filter easily
  | integrates HTTP Basic authentication for quick, simple checking.
  |
 */
Route::filter('auth', function() {
    if (Auth::guest())
        return Redirect::guest('/');


    $site_exists    = false;
    $is_new_account = false;

    if (Session::get("active_site_id") !== null) {
        $site_exists = App\Models\Site::find(Session::get("active_site_id"))->count();
        View::share(array("activeSiteName" => Session::get("active_site_name")));
    }


    $site_id   = $tenant_id = null;
    if (Session::get("active_site_id") === null && !$site_exists) {
        $site = App\Models\Site::where("account_id", Auth::user()->id)->get(array('id', 'name'))->first();
        if ($site) {
            $site_id   = $site->id;
            $tenant_id = $site->name;

            Session::set("active_site_id", $site->id);
            Session::set("active_site_name", $site->name);
            Session::remove("default_action_view");

            $account_repository = new App\Pongo\Repository\AccountRepository();
            $is_new_account     = $account_repository->isNewAccount();
            Session::set('is_new_account', $is_new_account ? true : false);
        }
    }
    else {
        $site_id   = Session::get("active_site_id");
        $tenant_id = Session::get("active_site_name");
    }

    if ($is_new_account) {
        Session::remove('is_new_account');
        return Redirect::to("sites/{$tenant_id}/integration");
    }
});

Route::filter('auth.basic', function() {
    return Auth::basic();
});

/**
 * Filter of ajax request for data collection
 */
Route::filter('site.ajax', function($route) {

    if (!\Request::ajax()) {
        return Redirect::to('/');
    }

    $tenant_id = $route->parameter('tenant_id');

    if (is_null($tenant_id)) {
        return \Redirect::to('sites');
    }

    $validator = Validator::make(['name' => $tenant_id], ['name' => 'required|exists:sites,name']);
    if ($validator->passes()) {
        $repository = new \App\Pongo\Repository\SiteRepository();
        $site       = $repository->isBelongToHim($tenant_id);
        if (!$site)
            return Redirect::to('/');

        Session::set("active_site_id", $site->id);
        Session::set("active_site_name", $site->name);
    }
    else
        return Redirect::back()->withErrors($validator);
});

Route::filter('role.user', function() {
    if (Auth::check()) {
        if (!Auth::user()->hasRole('User'))
            return Response::view('frontend.errors.missing', [], 404);
    }
    else
        return Redirect::guest('/');
});

Route::filter('has.site', function () {
    if (Auth::check()) {
        $count_sites = App\Models\Account::find(Auth::user()->id)->sites()->count();
        if ($count_sites <= 0) {
            return "Signup site";
        }
    }
    else
        return Redirect::guest('/');
});

Route::filter('role.admin', function() {
    if (Auth::check()) {
        if (!Auth::user()->hasRole('Administrator')) {
            return Redirect::to('v2/home')->with("flash_error", "You are not allowed to access the page.");
        }
    }
    else
        return Redirect::guest('/');
});

App::missing(function($exception) {
    return Response::view('frontend.errors.missing', array('exception' => $exception), 404);
});


/*
  |--------------------------------------------------------------------------
  | Guest Filter
  |--------------------------------------------------------------------------
  |
  | The "guest" filter is the counterpart of the authentication filters as
  | it simply checks that the current user is not logged in. A redirect
  | response will be issued if they are, which you may freely change.
  |
 */

Route::filter('guest', function() {
    if (Auth::check())
        return Redirect::to('/');
});

/*
  |--------------------------------------------------------------------------
  | CSRF Protection Filter
  |--------------------------------------------------------------------------
  |
  | The CSRF filter is responsible for protecting your application against
  | cross-site request forgery attacks. If this special token in a user
  | session does not match the one given in this request, we'll bail.
  |
 */

Route::filter('csrf', function() {
    if (Session::token() !== Input::get('_token')) {
        throw new Illuminate\Session\TokenMismatchException;
    }
});
