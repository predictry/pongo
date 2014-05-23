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
if (App::isLocal())
	Route::pattern('domain', 'predictry.dev');
else
	Route::pattern('domain', 'predictry.com');


/*
  |--------------------------------------------------------------------------
  | Frontend Routes
  |--------------------------------------------------------------------------
 */
//Route::group(array('domain' => 'dashboard.{domain}', 'namespace' => 'App\Controllers'), function() {
Route::group(array('domain' => 'demo.predictry.dev', 'namespace' => 'App\Controllers'), function() {
	Route::get('/', 'HomeController@getLogin');
	Route::get('/home/{numeric}/{token}', 'HomeController@getHome');

	Route::get('login', 'HomeController@getLogin');
	Route::post('login/submit', 'HomeController@postLogin');

	Route::get('register', 'HomeController@getRegister');
	Route::post('register/submit', 'HomeController@postRegister');

	Route::get('forgot', 'HomeController@getForgotPassword');
	Route::post('forgot/submit', 'HomeController@postForgotPassword');

	Route::get('reset/{token}', 'HomeController@getReset');
	Route::post('reset/submit', 'HomeController@postReset');

	Route::get('datamigration', 'RedmartMigrationController@index');
});

/*
  |--------------------------------------------------------------------------
  | User Dashboard Routing
  |--------------------------------------------------------------------------
 */
//Route::group(array('domain' => 'dashboard.{domain}', 'before' => 'auth', 'namespace' => 'App\Controllers\User'), function() {
Route::group(array('domain' => 'demo.predictry.dev', 'before' => 'auth', 'namespace' => 'App\Controllers\User'), function() {

	#Dashboard
	$role = Session::get("role");

	Route::get('user', 'UserController@getDashboard');
	Route::get('dashboard', array('as' => 'dashboard', 'uses' => 'PanelController@index'));
	Route::get('sites/wizard', array('as' => 'sites', 'uses' => 'SitesController@getSiteWizard'));
	Route::get('sites/getModal', array('as' => 'sites', 'uses' => 'SitesController@getModalCreate'));
	Route::post('sites/ajaxSubmitSite', array('as' => 'sites', 'uses' => 'SitesController@postCreate'));

	#Update Profile
	Route::get('profile', 'UserController@getProfile');
	Route::post('user/profile/submit', 'UserController@postProfile');
	Route::get('user/profile', array('as' => 'profile', 'uses' => 'UserController@getProfile'));

	#Update Password
	Route::get('password', 'UserController@getPassword');
	Route::post('user/password/submit', 'UserController@postPassword');
	Route::get('user/password', array('as' => 'password', 'uses' => 'UserController@getPassword'));

	if ($role === "admin")
	{
		#Panel
		Route::post('panel/submitFunel', array('as' => 'panel.submitFunel', 'uses' => 'PanelController@postCreateFunel'));
		Route::post('panel/submitSelector', array('as' => 'panel.submitSelector', 'uses' => 'PanelController@postDefaultFunel'));
		Route::get('panel/createFunel/{ismodal?}', array('as' => 'panel.createFunel', 'uses' => 'PanelController@getCreateFunel'));
		Route::get('panel/itemFunel', array('as' => 'panel.itemFunel', 'uses' => 'PanelController@getItemFunel'));
		Route::post('panel/deleteFunel', array('as' => 'panel.deleteFunel', 'uses' => 'PanelController@postDeleteFunel'));
		Route::post("panel/trends", array('as' => 'panel.trends', 'uses' => 'PanelController@getTrends'));

		#Member Management
		Route::get('members/create', array("as" => "members.create", "uses" => 'MembersController@getCreate'));
		Route::post('members/submit', array("as" => "members.submit", "uses" => 'MembersController@postCreate'));
		Route::get('members/{numeric}/edit', array("as" => "members.{numieric}.edit", "uses" => 'MembersController@getEdit'));
		Route::post('members/{numeric}/edit', array("as" => "members.update", "uses" => 'MembersController@postEdit'));
		Route::get('members/{numeric}/delete', 'MembersController@getDelete');
		Route::post('members/{numeric}/delete', 'MembersController@postDelete');
		Route::get('members', array("as" => "members", "uses" => 'MembersController@index'));

		#Sites Management
		if (Auth::user()->plan_id !== 3)
		{
			Route::get('sites/create', array("as" => "sites.create", "uses" => 'SitesController@getCreate'));
			Route::post('sites/submit', array("as" => "sites.submit", "uses" => 'SitesController@postCreate'));
			Route::get('sites/{numeric}/edit', array("as" => "sites.{numieric}.edit", "uses" => 'SitesController@getEdit'));
			Route::post('sites/{numeric}/edit', array("as" => "sites.update", "uses" => 'SitesController@postEdit'));
			Route::get('sites/{numeric}/delete', 'SitesController@getDelete');
			Route::post('sites/{numeric}/delete', 'SitesController@postDelete');
		}
		Route::get('sites/{numeric}/default', array("as" => "sites.{numieric}.edit", "uses" => 'SitesController@getDefault'));
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
		Route::get('rules/formCreate', array("as" => "rules.formCreate", "uses" => 'RulesController@getFormCreate'));
		Route::post('rules/submit', array("as" => "rules.submit", "uses" => 'RulesController@postCreate'));
		Route::get('rules/item', array("as" => "rules.item", "uses" => 'RulesController@getItemRule'));
		Route::get('rules/modalItem', array("as" => "rules.modalItem", "uses" => 'RulesController@getModalItemRule'));
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

		#Placement Management
		Route::get("placements", array("as" => "placements", "uses" => "PlacementsController@index"));
		Route::get('placements/{numeric}/view', array("as" => "placements.{numeric}.view", "uses" => 'PlacementsController@getView'));
		Route::get('placements/create', array("as" => "placements.create", "uses" => 'PlacementsController@getCreate'));
		Route::post('placements/submit', array("as" => "placements.submit", "uses" => 'PlacementsController@postCreate'));
		Route::get('placements/item', array("as" => "placements.item", "uses" => 'PlacementsController@getItemPlacementRuleset'));
		Route::get('placements/itemEdit', array("as" => "placements.itemEdit", "uses" => 'PlacementsController@getItemEditPlacementRuleset'));
		Route::get('placements/{numeric}/edit', array("as" => "placements.{numieric}.edit", "uses" => 'PlacementsController@getEdit'));
		Route::post('placements/{numeric}/edit', array("as" => "placements.update", "uses" => 'PlacementsController@postEdit'));
		Route::get('placements/{numeric}/delete', 'PlacementsController@getDelete');
		Route::post('placements/{numeric}/delete', 'PlacementsController@postDelete');

		Route::get('placements/wizard', array("as" => "placements.wizard", "uses" => 'PlacementsController@getWizard'));
		Route::post('placements/ajaxSubmitWizardPlacement', array("as" => "placements.ajaxSubmitWizardPlacement", "uses" => 'PlacementsController@postAjaxWizardPlacement'));
		Route::post('placements/ajaxSubmitWizardAddRuleset', array("as" => "placements.ajaxSubmitWizardAddRuleset", "uses" => 'PlacementsController@postAjaxWizardAddRuleset'));
		Route::post('placements/ajaxSubmitCompleteWizard', array("as" => "placements.ajaxSubmitCompleteWizard", "uses" => 'PlacementsController@postAjaxWizardCompletePlacement'));
	}
	#logout
	Route::get('user/logout', 'UserController@logout');
});

/*
  |--------------------------------------------------------------------------
  | API Routing
  |--------------------------------------------------------------------------
 */
Route::group(array('domain' => 'api.{domain}', 'prefix' => 'v1', 'namespace' => 'App\Controllers\Api'), function() {
	//	 Allow from any origin
	if (isset($_SERVER['HTTP_ORIGIN']))
	{
		header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
		header('Access-Control-Allow-Credentials: true');
		header('Access-Control-Max-Age: 86400'); // cache for 1 day
	}
	// Access-Control headers are received during OPTIONS requests
	if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'OPTIONS'))
	{
		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
			header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
			header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

		exit(0);
	}

	Route::resource('predictry', 'ActionController', array("only" => array("index", "store", "show", "destroy")));
	Route::resource('recommendation', 'RecommendationController', array("only" => array("index")));
});

/*
  |--------------------------------------------------------------------------
  | API Routing (can be removed later, testing purpose only)
  |--------------------------------------------------------------------------
 */
Route::group(array('prefix' => 'api/v1', 'namespace' => 'App\Controllers\Api'), function() {
	Route::resource('predictry', 'ActionController', array("only" => array("index", "store", "show", "destroy")));
	Route::resource('recommendation', 'RecommendationController', array("only" => array("index")));
});

