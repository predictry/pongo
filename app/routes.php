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
Route::pattern('type', '[A-Za-z0-9_]+');
Route::pattern('bar_type', '[A-Za-z0-9]+');
Route::pattern('date_unit', '[A-Za-z0-9]+');
Route::pattern('selected_comparison', '[A-Za-z0-9]+');
Route::pattern('dt_start', '^([0-9]{4})-([0-9]{2})-([0-9]{2})$');
Route::pattern('dt_end', '^([0-9]{4})-([0-9]{2})-([0-9]{2})$');
Route::pattern('numeric', '[0-9]+');

/*
  |--------------------------------------------------------------------------
  | Frontend Routes
  |--------------------------------------------------------------------------
 */
//Route::group(array('domain' => 'dashboard.{domain}', 'namespace' => 'App\Controllers'), function() {
Route::group(array('namespace' => 'App\Controllers'), function() {
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

    Route::get('tokenauth', 'TokenAuthenticationController@index');
});

/*
  |--------------------------------------------------------------------------
  | User Dashboard Routing
  |--------------------------------------------------------------------------
 */
//Route::group(array('domain' => 'dashboard.{domain}', 'before' => 'auth', 'namespace' => 'App\Controllers\User'), function() {
Route::group(array('before' => 'auth', 'namespace' => 'App\Controllers\User'), function() {

    #Dashboard
    $role = Session::get("role");

    Route::get('user', 'UserController@getDashboard');
    Route::get('home', array('as' => 'home', 'uses' => 'PanelController@index'));
    Route::get('home2', array('as' => 'home2', 'uses' => 'PanelController@index'));
//	Route::get('home2/{selected_comparison?}/{type?}/{bar_type?}/{type_by?}/{dt_start?}/{dt_end?}', array('as' => 'home2', 'uses' => 'PanelController@index2'));
//    Route::get('home/{selected_comparison?}/{type?}/{date_unit?}/{dt_start?}/{dt_end?}', array('as' => 'home', 'uses' => 'PanelController@index2'));
    Route::get('sites/wizard', array('as' => 'sites', 'uses' => 'SitesController@getSiteWizard'));
    Route::get('sites/getModal', array('as' => 'sites', 'uses' => 'SitesController@getModalCreate'));
    Route::post('sites/ajaxSubmitSite', array('as' => 'sites', 'uses' => 'SitesController@postCreate'));

    #Panel
    Route::post('panel/ajaxGraphComparison', array('as' => 'panel.ajaxGraphComparison', 'uses' => 'AjaxPanelController@comparisonGraph'));


    #Update Profile
    Route::get('profile', 'UserController@getProfile');
    Route::post('user/profile/submit', 'UserController@postProfile');
    Route::get('user/profile', array('as' => 'profile', 'uses' => 'UserController@getProfile'));

    #Update Password
    Route::get('password', 'UserController@getPassword');
    Route::post('user/password/submit', 'UserController@postPassword');
    Route::get('user/password', array('as' => 'password', 'uses' => 'UserController@getPassword'));
    Route::get("panel/stats", array('as' => 'panel.stats', 'uses' => 'PanelController@getShowStats'));

    if ($role === "admin") {
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
        if (Auth::user()->plan_id !== 3) {
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
        Route::get("items/key/{name}/metas", array("uses" => "ItemsController@getItemMetas"));

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

        #Widget Management
        Route::get("widgets", array("as" => "widgets", "uses" => "WidgetsController@index"));
        Route::get('widgets/{numeric}/view', array("as" => "widgets.{numeric}.view", "uses" => 'WidgetsController@getView'));
        Route::get('widgets/create', array("as" => "widgets.create", "uses" => 'WidgetsController@getCreate'));
        Route::post('widgets/submit', array("as" => "widgets.submit", "uses" => 'WidgetsController@postCreate'));
        Route::get('widgets/item', array("as" => "widgets.item", "uses" => 'WidgetsController@getItemWidgetRuleset'));
        Route::get('widgets/itemEdit', array("as" => "widgets.itemEdit", "uses" => 'WidgetsController@getItemEditWidgetRuleset'));
        Route::get('widgets/itemFilterEdit', array("as" => "widgets.itemFilterEdit", "uses" => 'WidgetsController@getItemEditWidgetFilter'));
        Route::get('widgets/{numeric}/edit', array("as" => "widgets.{numieric}.edit", "uses" => 'WidgetsController@getEdit'));
        Route::post('widgets/{numeric}/edit', array("as" => "widgets.update", "uses" => 'WidgetsController@postEdit'));
        Route::get('widgets/{numeric}/delete', 'WidgetsController@getDelete');
        Route::post('widgets/{numeric}/delete', 'WidgetsController@postDelete');

        Route::get('widgets/wizard', array("as" => "widgets.wizard", "uses" => 'WidgetsController@getWizard'));
        Route::post('widgets/ajaxSubmitWizardWidget', array("as" => "widgets.ajaxSubmitWizardWidget", "uses" => 'WidgetsController@postAjaxWizardWidget'));
        Route::post('widgets/ajaxSubmitWizardAddRuleset', array("as" => "widgets.ajaxSubmitWizardAddRuleset", "uses" => 'WidgetsController@postAjaxWizardAddRuleset'));
        Route::post('widgets/ajaxSubmitCompleteWizard', array("as" => "widgets.ajaxSubmitCompleteWizard", "uses" => 'WidgetsController@postAjaxWizardCompleteWidget'));

        Route::get("filters", array("as" => "filters", "uses" => "FiltersController@index"));
        Route::get('filters/create', array("as" => "filters.create", "uses" => 'FiltersController@getCreate'));
        Route::post('filters/submit', array("as" => "filters.submit", "uses" => 'FiltersController@postCreate'));
        Route::get('filters/item', array("as" => "filters.item", "uses" => 'FiltersController@getItem'));
        Route::get('filters/{numeric}/edit', array("as" => "filters.{numieric}.edit", "uses" => 'FiltersController@getEdit'));
        Route::get('filters/itemEdit', array("as" => "filters.itemEdit", "uses" => 'FiltersController@getItemEdit'));
        Route::post('filters/{numeric}/edit', array("as" => "filters.update", "uses" => 'FiltersController@postEdit'));
        Route::get('filters/{numeric}/delete', 'FiltersController@getDelete');
        Route::post('filters/{numeric}/delete', 'FiltersController@postDelete');
    }
    #logout
    Route::get('user/logout', 'UserController@logout');
});

/*
  |--------------------------------------------------------------------------
  | API Routinglocal
  |--------------------------------------------------------------------------
 */
Route::group(array('prefix' => 'v1', 'namespace' => 'App\Controllers\Api'), function() {
    //	 Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400'); // cache for 1 day
    }
    // Access-Control headers are received during OPTIONS requests
    if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')) {
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }

    Route::resource('predictry', 'ActionController', array("only" => array("index", "store", "show", "destroy")));
    Route::resource('recommendation', 'RecommendationController', array("only" => array("index")));
    Route::resource('activation', 'ItemActivationController', array("only" => array("index", "store")));
    Route::resource('item', 'ItemController', array("only" => array("store")));
});

/*
  |--------------------------------------------------------------------------
  | API Routing (can be removed later, testing purpose only)
  |--------------------------------------------------------------------------
 */
Route::group(array('prefix' => 'api/v1', 'namespace' => 'App\Controllers\Api'), function() {
    Route::resource('predictry', 'ActionController', array("only" => array("index", "store", "show", "destroy")));
    Route::resource('recommendation', 'RecommendationController', array("only" => array("index")));
    Route::resource('activation', 'ItemActivationController', array("only" => array("index", "store")));
    Route::resource('item', 'ItemController', array("only" => array("store")));
    Route::resource('cart', 'CartController', array("only" => array("store")));
    Route::resource('cartlog', 'CartLogController', array("only" => array("store")));
    Route::resource('widget', 'WidgetInstanceController', array("only" => array("store")));
});

Route::group(array('prefix' => 'api/v2', 'namespace' => 'App\Controllers\Api'), function() {
    Route::resource('actions', 'Action2Controller', array("only" => array("index", "store", "show", "destroy")));
    Route::resource('recommendation', 'Recommendation2Controller', array("only" => array("index")));
    Route::resource('item', 'ItemController', array("only" => array("store")));
    Route::resource('carts', 'CartController', array("only" => array("store")));
    Route::resource('cartlog', 'CartLogController', array("only" => array("store")));
    Route::resource('widget', 'WidgetInstanceController', array("only" => array("store")));

    //@TODO - Create route to fetch recent actions by site
    /**
     * api/v2/tenant/{tenant_id}/actions
     */
    Route::resource('tenant', 'TenantController');
    Route::resource('tenant.actions', 'TenantActionController', array("only" => array("index")));
});

Route::group(array('prefix' => 'api/v3', 'namespace' => 'App\Controllers\Api'), function() {
    Route::resource('actions', 'Action3Controller', array("only" => array("index", "store", "show", "destroy")));
    Route::resource('recommendation', 'Recommendation2Controller', array("only" => array("index")));
    Route::resource('item', 'ItemController', array("only" => array("store")));
    Route::resource('carts', 'CartController', array("only" => array("store")));
    Route::resource('cartlog', 'CartLogController', array("only" => array("store")));
    Route::resource('widget', 'WidgetInstanceController', array("only" => array("store")));

    //@TODO - Create route to fetch recent actions by site
    /**
     * api/v2/tenant/{tenant_id}/actions
     */
    Route::resource('tenant', 'TenantController');
    Route::resource('tenant.actions', 'TenantActionController', array("only" => array("index")));
});
