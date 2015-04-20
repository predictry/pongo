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
Route::pattern('confirmation_code', '[A-Za-z0-9]+');
Route::pattern('type', '[A-Za-z0-9_]+');
Route::pattern('bar_type', '[A-Za-z0-9]+');
Route::pattern('date_unit', '[A-Za-z0-9]+');
Route::pattern('selected_comparison', '[A-Za-z0-9]+');
Route::pattern('dt_start', '^([0-9]{4})-([0-9]{2})-([0-9]{2})$');
Route::pattern('dt_end', '^([0-9]{4})-([0-9]{2})-([0-9]{2})$');
Route::pattern('numeric', '[0-9]+');

#Sites
Route::pattern('tenant_id', '[A-Za-z0-9]+');
Route::pattern('action_name', '[A-Za-z0-9_]+');

#Items
Route::pattern('item_id', '[A-Za-z0-9]+');

/*
  |--------------------------------------------------------------------------
  | Frontend Routes V2
  |--------------------------------------------------------------------------
 */
Route::group(array('prefix' => 'v2', 'namespace' => 'App\Controllers'), function() {
    Route::get('login', 'Home2Controller@getLogin');
    Route::post('login/submit', 'Home2Controller@postLogin');

    Route::get('register', 'Home2Controller@getRegister');
    Route::post('register/submit', 'Home2Controller@postRegister');

    Route::get('forgot', 'Home2Controller@getForgotPassword');
    Route::get('password/reset/{token}', 'Home2Controller@getReset');
});

/*
  |------------------------------------ --------------------------------------
  | User Dashboard Routing V2
  |--------------------------------------------------------------------------
 */
Route::group(array('prefix' => 'v2', 'before' => 'auth', 'namespace' => 'App\Controllers\User'), function() {
    #Update Profile
    Route::get('profile', 'User2Controller@getProfile');

    #Update Password
    Route::get('password', 'User2Controller@getPassword');

    Route::group(array('before' => 'role.user|has.site'), function() {
        #Dashboard
        Route::get('home/{type?}/{dt_start?}/{dt_end?}', array('as' => 'home', 'uses' => 'Panel2Controller@index'));

        #Sites Management
        Route::get("sites", array("as" => "sites", "uses" => "Sites2Controller@index"));
        Route::get('sites/create', array("as" => "sites.create", "uses" => 'Sites2Controller@getCreate'));
        Route::get('sites/{numeric}/edit', array("as" => "sites.{numieric}.edit", "uses" => 'Sites2Controller@getEdit'));
        Route::get('sites/{numeric}/delete', 'Sites2Controller@getDelete');
        Route::get('sites/{numeric}/default', array("as" => "sites.{numieric}.edit", "uses" => 'Sites2Controller@getDefault'));

        #Items
        Route::get("items", array("as" => "items", "uses" => "Items2Controller@index"));
        Route::get('items/{numeric}/view', array("as" => "items.{numeric}.view", "uses" => 'Items2Controller@getView'));
        Route::get('items/{numeric}/edit', array("as" => "items.{numieric}.edit", "uses" => 'Items2Controller@getEdit'));
        Route::get('items/{numeric}/delete', 'Items2Controller@postDelete');
        Route::post('items/{numeric}/delete', 'Items2Controller@postDelete');

        #Widgets
        Route::get("widgets", array("as" => "widgets", "uses" => "Widgets2Controller@index"));
        Route::get('widgets/{numeric}/view', array("as" => "widgets.{numeric}.view", "uses" => 'Widgets2Controller@getView'));
        Route::get('widgets/create', array("as" => "widgets.create", "uses" => 'Widgets2Controller@getCreate'));
        Route::get('widgets/item', array("as" => "widgets.item", "uses" => 'Widgets2Controller@getItemWidgetRuleset'));
        Route::get('widgets/itemEdit', array("as" => "widgets.itemEdit", "uses" => 'Widgets2Controller@getItemEditWidgetRuleset'));
        Route::get('widgets/itemFilterEdit', array("as" => "widgets.itemFilterEdit", "uses" => 'Widgets2Controller@getItemEditWidgetFilter'));
        Route::get('widgets/{numeric}/edit', array("as" => "widgets.{numieric}.edit", "uses" => 'Widgets2Controller@getEdit'));
        Route::get('widgets/{numeric}/delete', 'Widgets2Controller@postDelete');

        #Filters 
        Route::get("filters", array("as" => "filters", "uses" => "Filters2Controller@index"));
        Route::get('filters/create', array("as" => "filters.create", "uses" => 'Filters2Controller@getCreate'));
        Route::post('filters/submit', array("as" => "filters.submit", "uses" => 'Filters2Controller@postCreate'));
        Route::get('filters/item', array("as" => "filters.item", "uses" => 'Filters2Controller@getItem'));
        Route::get('filters/{numeric}/edit', array("as" => "filters.{numieric}.edit", "uses" => 'Filters2Controller@getEdit'));
        Route::get('filters/itemEdit', array("as" => "filters.itemEdit", "uses" => 'Filters2Controller@getItemEdit'));
        Route::post('filters/{numeric}/edit', array("as" => "filters.update", "uses" => 'Filters2Controller@postEdit'));
        Route::get('filters/{numeric}/delete', 'Filters2Controller@postDelete');
        Route::post('filters/{numeric}/delete', 'Filters2Controller@postDelete');

        #Rules Management
        Route::get('rules/create', array("as" => "rules.create", "uses" => 'Rules2Controller@getCreate'));
        Route::get('rules/formCreate', array("as" => "rules.formCreate", "uses" => 'Rules2Controller@getFormCreate'));
        Route::post('rules/submit', array("as" => "rules.submit", "uses" => 'Rules2Controller@postCreate'));
        Route::get('rules/item', array("as" => "rules.item", "uses" => 'Rules2Controller@getItemRule'));
        Route::get('rules/modalItem', array("as" => "rules.modalItem", "uses" => 'Rules2Controller@getModalItemRule'));
        Route::get('rules/itemEdit', array("as" => "rules.itemEdit", "uses" => 'Rules2Controller@getItemEditRule'));
        Route::post('rules/fetchItems', array("as" => "rules.fetchitem", "uses" => 'Rules2Controller@postFetchItems'));
        Route::get('rules/{numeric}/view', array("as" => "rules.{numeric}.view", "uses" => 'Rules2Controller@getView'));
        Route::get('rules/{numeric}/edit', array("as" => "rules.{numieric}.edit", "uses" => 'Rules2Controller@getEdit'));
        Route::post('rules/{numeric}/edit', array("as" => "rules.update", "uses" => 'Rules2Controller@postEdit'));
        Route::get('rules/{numeric}/delete', 'Rules2Controller@getDelete');
        Route::post('rules/{numeric}/delete', 'Rules2Controller@postDelete');
        Route::get("rules", array("as" => "rules", "uses" => "Rules2Controller@index"));

        #Demo
        Route::get("demo", ['as' => 'demo', 'uses' => 'DemoController@index']);
        Route::get("demo/show/{id}", ['as' => 'demo.show.{id}', 'uses' => 'DemoController@show']);
    });

    # Data Collections
    Route::get("sites/{tenant_id}/integration", "Sites2Controller@getImplementationWizard");
    Route::get("sites/{tenant_id}/data_collection", "Sites2Controller@getDataCollection");

    Route::group(array('before' => 'site.ajax'), function() {
        Route::get("sites/{tenant_id}/actions/{action_name}/properties", "Sites2Controller@ajaxGetActionProperties");
        Route::get("sites/{tenant_id}/actions/{action_name}/snipped", "Sites2Controller@ajaxGetActionSnipped");
        Route::get("sites/{tenant_id}/actions/{action_name}/validate", "Sites2Controller@ajaxGetCheckIfActionImplemented");
        Route::post("sites/{tenant_id}/integration/submit", "Sites2Controller@ajaxPostImplementationWizard");
    });

    Route::get('user/logout', 'User2Controller@logout');
});

/*
  |------------------------------------ --------------------------------------
  | Admin Dashboard Routing V2
  |--------------------------------------------------------------------------
 */
Route::group(array('prefix' => 'v2/admin', 'before' => 'auth|role.admin', 'namespace' => 'App\Controllers\Admin'), function() {
    /*
     * Dashboard
     */
    Route::get('home/{type?}/{dt_start?}/{dt_end?}', ['as' => 'admin.home', 'uses' => 'PanelController@index']);
    Route::post('panel/ajaxSiteOverviewSummary/{type?}/{dt_start?}/{dt_end?}', ['as' => 'admin.dashboard', 'uses' => 'AjaxPanelController@postSiteOverviewSummary']);

    /*
     * Demo
     */
    Route::get('sites/demo', ['as' => 'admin.sites.demo', 'uses' => 'DemoController@index']);
    Route::get('sites/demo/{id}/view', ['as' => 'admin.sites.demo.{site_id}.show', 'uses' => 'DemoController@show']);
    Route::get('sites/demo/{id}/view/item/{item_id}', ['as' => 'admin.sites.demo.{site_id}.view.item.{item_id}', 'uses' => 'DemoController@getItemDetail']);
});

/*
 * API Routing
 */
Route::group(array('prefix' => 'api', 'namespace' => 'App\Controllers\Api'), function() {

    //Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400'); // cache for 1 day
    }
    // Access-Control headers are received during OPTIONS requests
    if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')) {
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }

    Route::group(array('prefix' => 'v1'), function() {

        /**
         * Registration End Point
         * 
         * POST - api/v2/user (create new user)
         * POST - api/v2/user/{email} {get information of the user}
         */
        Route::resource('account', 'AccountController', array("only" => array("store", "show")));

        /**
         * POST - {prefix}/auth (store)
         */
        Route::resource('auth', 'AuthController', ['only' => ['index', 'store']]);

        /**
         * AUTH FLOW : CLIENT CREDENTIALS
         * POST - api/v1/auth
         * @param string $email
         * @param string password
         */
        Route::post('auth', 'AuthController@postAuth');
    });
});

/*
 * UNUSED
 * Has been replaced with the V2
 */

/*
  |--------------------------------------------------------------------------
  | Frontend Routes
  |--------------------------------------------------------------------------
 */
Route::group(array('namespace' => 'App\Controllers'), function() {
    Route::get('/', 'Home2Controller@getLogin');
    Route::get('/home/{numeric}/{token}', 'HomeController@getHome');

    Route::get('login', 'HomeController@getLogin');
    Route::post('login/submit', 'HomeController@postLogin');

    Route::get('register', 'HomeController@getRegister');
    Route::post('register/submit', 'HomeController@postRegister');

    Route::get('forgot', 'HomeController@getForgotPassword');
    Route::post('forgot/submit', 'HomeController@postForgotPassword');

    Route::get('password/reset/{token}', 'HomeController@getReset');
    Route::post('password/reset/submit', 'HomeController@postReset');

    Route::get('verify/{confirmation_code}', 'HomeController@getConfirmation');
});

/*
  |--------------------------------------------------------------------------
  | User Dashboard Routing (Un used)
  |--------------------------------------------------------------------------
 */
Route::group(array('before' => 'auth', 'namespace' => 'App\Controllers\User'), function() {

    #Dashboard
    $role = Session::get("role");

    Route::get('user', 'UserController@getDashboard');
    Route::get('home2', array('as' => 'home2', 'uses' => 'PanelController@index'));
    Route::get('home/{selected_comparison?}/{type?}/{date_unit?}/{dt_start?}/{dt_end?}', array('as' => 'home', 'uses' => 'PanelController@index2'));
    Route::get('sites/wizard', array('as' => 'sites', 'uses' => 'SitesController@getSiteWizard'));
    Route::get('sites/getModal', array('as' => 'sites', 'uses' => 'SitesController@getModalCreate'));
    Route::post('sites/ajaxSubmitSite', array('as' => 'sites', 'uses' => 'SitesController@postCreate'));

    #Panel
    Route::post('panel/ajaxGraphComparison', array('as' => 'panel.ajaxGraphComparison', 'uses' => 'AjaxPanelController@comparisonGraph'));

    #Update Profile
    Route::get('profile', 'UserController@getProfile');
    Route::post('user/profile/submit', 'UserController@postProfile');
    Route::get('user/profile', array('as' => 'profile', 'uses' => 'UserController@getProfile'));

    #Update Business
    Route::get('sites/{name}/business', 'SitesController@getBusiness');
    Route::post('sites/{name}/business/submit', 'SitesController@postBusiness');

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
        if (!is_null(Auth::user()) && Auth::user()->plan_id !== 3) {
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

    # Data Collections
    Route::get("sites/{tenant_id}/integration", "SitesController@getImplementationWizard");
    Route::get("sites/{tenant_id}/data_collection", "SitesController@getDataCollection");
    Route::group(array('before' => 'site.ajax'), function() {
        Route::get("sites/{tenant_id}/actions/{action_name}/properties", "SitesController@ajaxGetActionProperties");
        Route::get("sites/{tenant_id}/actions/{action_name}/snipped", "SitesController@ajaxGetActionSnipped");
        Route::get("sites/{tenant_id}/actions/{action_name}/validate", "SitesController@ajaxGetCheckIfActionImplemented");
        Route::post("sites/{tenant_id}/integration/submit", "SitesController@ajaxPostImplementationWizard");
    });

    #Logout
    Route::get('user/logout', 'UserController@logout');
});
