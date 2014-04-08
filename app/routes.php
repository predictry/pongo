<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the Closure to execute when that URI is requested.
  |
 */


/*
  |--------------------------------------------------------------------------
  | Routes Pattern
  |--------------------------------------------------------------------------
 */
Route::pattern('token', '[A-Za-z0-9]+');
Route::pattern('numeric', '[0-9]+');

/*
  |--------------------------------------------------------------------------
  | Frontend Routes
  |--------------------------------------------------------------------------
 */

Route::get('/', 'HomeController@getLogin');
Route::get('/home', 'HomeController@getHome');

Route::get('login', 'HomeController@getLogin');
Route::post('login/submit', 'HomeController@postLogin');

Route::get('register', 'HomeController@getRegister');
Route::post('register/submit', 'HomeController@postRegister');

Route::get('forgot', 'HomeController@getForgotPassword');
Route::post('forgot/submit', 'HomeController@postForgotPassword');

Route::get('reset/{token}', 'HomeController@getReset');
Route::post('reset/submit', 'HomeController@postReset');

/*
 * User Routing
 */
Route::group(array('before' => 'auth'), function() {

	#Dashboard
	Route::get('user', 'UserController@getDashboard');
	Route::get('dashboard', array('as' => 'dashboard', 'uses' => 'user\PanelController@index'));

	#Update Profile
	Route::get('user/profile', 'UserController@getProfile');
	Route::post('user/profile/submit', 'UserController@postProfile');
	Route::get('user/profile', array('as' => 'profile', 'uses' => 'UserController@getProfile'));

	#Update Password
	Route::get('user/password', 'UserController@getPassword');
	Route::post('user/password/submit', 'UserController@postPassword');
	Route::get('user/password', array('as' => 'password', 'uses' => 'UserController@getPassword'));

	#Member Management
	Route::get('members/create', array("as" => "members.create", "uses" => 'user\MembersController@getCreate'));
	Route::post('members/submit', array("as" => "members.submit", "uses" => 'user\MembersController@postCreate'));
	Route::get('members/{numeric}/edit', array("as" => "members.{numieric}.edit", "uses" => 'user\MembersController@getEdit'));
	Route::post('members/{numeric}/edit', array("as" => "members.update", "uses" => 'user\MembersController@postEdit'));
	Route::get('members/{numeric}/delete', 'user\MembersController@getDelete');
	Route::post('members/{numeric}/delete', 'user\MembersController@postDelete');
	Route::get('members', array("as" => "members", "uses" => 'user\MembersController@index'));

	#Sites Management
	Route::get('sites/create', array("as" => "sites.create", "uses" => 'user\SitesController@getCreate'));
	Route::post('sites/submit', array("as" => "sites.submit", "uses" => 'user\SitesController@postCreate'));
	Route::get('sites/{numeric}/edit', array("as" => "sites.{numieric}.edit", "uses" => 'user\SitesController@getEdit'));
	Route::post('sites/{numeric}/edit', array("as" => "sites.update", "uses" => 'user\SitesController@postEdit'));
	Route::get('sites/{numeric}/delete', 'user\SitesController@getDelete');
	Route::post('sites/{numeric}/delete', 'user\SitesController@postDelete');
	Route::get('sites/{numeric}/default', 'user\SitesController@getDefault');
	Route::get("sites", array("as" => "sites", "uses" => "user\SitesController@index"));

	#Item Management
	Route::get('items/{numeric}/view', array("as" => "items.{numeric}.view", "uses" => 'user\ItemsController@getView'));
	Route::get('items/{numeric}/edit', array("as" => "items.{numieric}.edit", "uses" => 'user\ItemsController@getEdit'));
	Route::post('items/{numeric}/edit', array("as" => "items.update", "uses" => 'user\ItemsController@postEdit'));
	Route::get("items", array("as" => "items", "uses" => "user\ItemsController@index"));

	#Statistics
	Route::get("statistics", array("as" => "statistics", "uses" => "user\StatisticsController@index"));

	#logout
	Route::get('user/logout', 'UserController@logout');
});

Route::get('/authtest', array('before' => 'auth.basic', function() {
return View::make('hello');
}));

// Route group for API versioning
Route::group(array('prefix' => 'api/v1', 'before' => 'auth.basic'), function() {
	Route::resource('predictry', 'api\ActionController', array("only" => array("index", "store", "show", "destroy")));
});
