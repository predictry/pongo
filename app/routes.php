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
Route::group(array('namespace' => 'App\Controllers'), function() {
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
});


/*
 * User Routing
 */
Route::group(array('before' => 'auth', 'namespace' => 'App\Controllers\User'), function() {

	#Dashboard
	Route::get('user', 'UserController@getDashboard');
	Route::get('dashboard', array('as' => 'dashboard', 'uses' => 'PanelController@index'));

	#Update Profile
	Route::get('user/profile', 'UserController@getProfile');
	Route::post('user/profile/submit', 'UserController@postProfile');
	Route::get('user/profile', array('as' => 'profile', 'uses' => 'UserController@getProfile'));

	#Update Password
	Route::get('user/password', 'UserController@getPassword');
	Route::post('user/password/submit', 'UserController@postPassword');
	Route::get('user/password', array('as' => 'password', 'uses' => 'UserController@getPassword'));

	#Member Management
	Route::get('members/create', array("as" => "members.create", "uses" => 'MembersController@getCreate'));
	Route::post('members/submit', array("as" => "members.submit", "uses" => 'MembersController@postCreate'));
	Route::get('members/{numeric}/edit', array("as" => "members.{numieric}.edit", "uses" => 'MembersController@getEdit'));
	Route::post('members/{numeric}/edit', array("as" => "members.update", "uses" => 'MembersController@postEdit'));
	Route::get('members/{numeric}/delete', 'MembersController@getDelete');
	Route::post('members/{numeric}/delete', 'MembersController@postDelete');
	Route::get('members', array("as" => "members", "uses" => 'MembersController@index'));

	#Sites Management
	Route::get('sites/create', array("as" => "sites.create", "uses" => 'SitesController@getCreate'));
	Route::post('sites/submit', array("as" => "sites.submit", "uses" => 'SitesController@postCreate'));
	Route::get('sites/{numeric}/edit', array("as" => "sites.{numieric}.edit", "uses" => 'SitesController@getEdit'));
	Route::post('sites/{numeric}/edit', array("as" => "sites.update", "uses" => 'SitesController@postEdit'));
	Route::get('sites/{numeric}/delete', 'SitesController@getDelete');
	Route::post('sites/{numeric}/delete', 'SitesController@postDelete');
	Route::get('sites/{numeric}/default', 'SitesController@getDefault');
	Route::get("sites", array("as" => "sites", "uses" => "SitesController@index"));

	#Item Management
	Route::get('items/{numeric}/view', array("as" => "items.{numeric}.view", "uses" => 'ItemsController@getView'));
	Route::get('items/{numeric}/edit', array("as" => "items.{numieric}.edit", "uses" => 'ItemsController@getEdit'));
	Route::post('items/{numeric}/edit', array("as" => "items.update", "uses" => 'ItemsController@postEdit'));
	Route::get('items/{numeric}/delete', 'ItemsController@getDelete');
	Route::post('items/{numeric}/delete', 'ItemsController@postDelete');
	Route::get("items", array("as" => "items", "uses" => "ItemsController@index"));

	#Rules Management
	Route::get('rules/create', array("as" => "rules.create", "uses" => 'RulesController@getCreate'));
	Route::post('rules/submit', array("as" => "rules.submit", "uses" => 'RulesController@postCreate'));
	Route::get('rules/item', array("as" => "rules.item", "uses" => 'RulesController@getItemRule'));
	Route::get('rules/itemEdit', array("as" => "rules.itemEdit", "uses" => 'RulesController@getItemEditRule'));
	Route::post('rules/fetchItems', array("as" => "rules.fetchitem", "uses" => 'RulesController@postFetchItems'));
	Route::get('rules/{numeric}/view', array("as" => "rules.{numeric}.view", "uses" => 'RulesController@getView'));
	Route::get('rules/{numeric}/edit', array("as" => "rules.{numieric}.edit", "uses" => 'RulesController@getEdit'));
	Route::post('rules/{numeric}/edit', array("as" => "rules.update", "uses" => 'RulesController@postEdit'));
	Route::get('rules/{numeric}/delete', 'RulesController@getDelete');
	Route::post('rules/{numeric}/delete', 'RulesController@postDelete');
	Route::get("rules", array("as" => "rules", "uses" => "RulesController@index"));

	#Statistics
	Route::get("statistics", array("as" => "statistics", "uses" => "StatisticsController@index"));

	#Site Actions Managament
	Route::get('actions/create', array("as" => "actions.create", "uses" => 'ActionsController@getCreate'));
	Route::post('actions/submit', array("as" => "actions.submit", "uses" => 'ActionsController@postCreate'));
	Route::post('actions/submitSelector', array("as" => "actions.submitSelector", "uses" => 'ActionsController@postSelector'));
	Route::get("actions", array("as" => "actions", "uses" => "ActionsController@index"));


#logout
	Route::get('user/logout', 'UserController@logout');
});

// Route group for API versioning
Route::group(array('prefix' => 'api/v1', 'before' => 'auth.basic', 'namespace' => 'App\Controllers\Api'), function() {
	Route::resource('predictry', 'ActionController', array("only" => array("index", "store", "show", "destroy")));
});
