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
//Route::get('forgot/submit', 'HomeController@postForgotPassword');

/*
 * User Routing
 */
Route::resource('user/dashboard', 'UserController@getDashboard');
Route::resource('user/logout', 'UserController@logout');


Route::resource('password', 'UserPasswordController', array('only' => array('show', 'edit', 'update')));


